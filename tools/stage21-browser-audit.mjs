import { createRequire } from "node:module";
import { mkdirSync } from "node:fs";
import { join } from "node:path";
import { tmpdir } from "node:os";

const requireBase = process.env.SHEMO_PLAYWRIGHT_REQUIRE_BASE || import.meta.url;
const require = createRequire(requireBase);
const { chromium } = require("playwright");

const base = "http://shemostudio.local";
const outDir = join(tmpdir(), "shemo-stage21-playwright");
mkdirSync(outDir, { recursive: true });

const checks = [
  { name: "home-ar-mobile", url: `${base}/`, width: 390, height: 844 },
  { name: "home-en-mobile", url: `${base}/en/`, width: 390, height: 844 },
  { name: "services-ar-mobile", url: `${base}/services/`, width: 390, height: 844 },
  { name: "work-en-mobile", url: `${base}/en/work/`, width: 390, height: 844 },
  { name: "case-en-mobile", url: `${base}/en/work/frame-pulse-launch-film-en/`, width: 390, height: 844 },
  { name: "quote-en-mobile", url: `${base}/en/request-a-quote-en/`, width: 390, height: 844 },
  { name: "start-ar-mobile", url: `${base}/start-a-project/`, width: 390, height: 844 },
  { name: "contact-en-mobile", url: `${base}/en/contact-en/`, width: 390, height: 844 },
  { name: "policy-ar-mobile", url: `${base}/deposit-policy/`, width: 390, height: 844 },
  { name: "home-ar-desktop", url: `${base}/`, width: 1440, height: 1000 },
  { name: "work-ar-desktop", url: `${base}/work/`, width: 1440, height: 1000 },
  { name: "packages-ar-desktop", url: `${base}/packages/`, width: 1440, height: 1000 },
];

const browser = await chromium.launch({ headless: true });
const context = await browser.newContext();
const failures = [];
const warnings = [];
const screenshots = [];

async function acceptCookies(page) {
  const accept = page.getByRole("button", { name: /^accept$/i });
  if (await accept.count()) {
    try {
      await accept.first().click({ timeout: 2000 });
      await page.waitForTimeout(300);
    } catch {
      // Some pages hide the banner after the first accepted cookie.
    }
  }
}

for (const check of checks) {
  const page = await context.newPage();
  await page.setViewportSize({ width: check.width, height: check.height });
  let response = await page.goto(check.url, { waitUntil: "load", timeout: 45000 });
  if (!response || response.status() !== 200) {
    await page.waitForTimeout(1200);
    response = await page.goto(check.url, { waitUntil: "load", timeout: 45000 });
  }
  if (!response || response.status() !== 200) {
    failures.push(`${check.name}: browser HTTP status ${response ? response.status() : "none"}`);
  }
  await page.waitForTimeout(900);
  await acceptCookies(page);

  const result = await page.evaluate(() => {
    function isVisible(el) {
      const style = getComputedStyle(el);
      const rect = el.getBoundingClientRect();
      return style.visibility !== "hidden" && style.display !== "none" && rect.width > 0 && rect.height > 0;
    }

    function parseColor(value) {
      const match = value.match(/rgba?\(([^)]+)\)/);
      if (!match) return null;
      const parts = match[1].split(",").map((part) => Number.parseFloat(part.trim()));
      return { r: parts[0], g: parts[1], b: parts[2], a: parts.length > 3 ? parts[3] : 1 };
    }

    function luminance(channel) {
      const value = channel / 255;
      return value <= 0.03928 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4;
    }

    function contrastRatio(fg, bg) {
      const l1 = 0.2126 * luminance(fg.r) + 0.7152 * luminance(fg.g) + 0.0722 * luminance(fg.b);
      const l2 = 0.2126 * luminance(bg.r) + 0.7152 * luminance(bg.g) + 0.0722 * luminance(bg.b);
      const lighter = Math.max(l1, l2);
      const darker = Math.min(l1, l2);
      return (lighter + 0.05) / (darker + 0.05);
    }

    function blend(top, bottom) {
      const alpha = top.a + bottom.a * (1 - top.a);
      if (alpha <= 0) return { r: 255, g: 255, b: 255, a: 1 };
      return {
        r: (top.r * top.a + bottom.r * bottom.a * (1 - top.a)) / alpha,
        g: (top.g * top.a + bottom.g * bottom.a * (1 - top.a)) / alpha,
        b: (top.b * top.a + bottom.b * bottom.a * (1 - top.a)) / alpha,
        a: alpha,
      };
    }

    function effectiveBackground(el) {
      const stack = [];
      let current = el;
      while (current) {
        const color = parseColor(getComputedStyle(current).backgroundColor);
        if (color && color.a > 0.01) stack.push(color);
        current = current.parentElement;
      }
      return stack.reverse().reduce((bg, color) => blend(color, bg), { r: 255, g: 255, b: 255, a: 1 });
    }

    const overflow = document.documentElement.scrollWidth - window.innerWidth;
    const unnamedInteractive = [...document.querySelectorAll("a[href], button, [role='button'], input[type='submit']")]
      .filter(isVisible)
      .filter((el) => !(el.textContent || "").trim() && !el.getAttribute("aria-label") && !el.getAttribute("title"))
      .slice(0, 5)
      .map((el) => el.outerHTML.slice(0, 120));

    const unlabeledFields = [...document.querySelectorAll("input, select, textarea")]
      .filter((el) => !["hidden", "submit", "button"].includes((el.getAttribute("type") || "").toLowerCase()))
      .filter(isVisible)
      .filter((el) => {
        if (el.labels && el.labels.length) return false;
        if (el.getAttribute("aria-label") || el.getAttribute("aria-labelledby") || el.getAttribute("placeholder")) return false;
        return true;
      })
      .slice(0, 5)
      .map((el) => el.outerHTML.slice(0, 120));

    const headings = [...document.querySelectorAll("h1, h2, h3, h4, h5, h6")]
      .filter(isVisible)
      .map((el) => ({ level: Number(el.tagName.slice(1)), text: (el.textContent || "").trim().slice(0, 60) }));
    const headingSkips = [];
    let previous = 0;
    for (const heading of headings) {
      if (previous && heading.level > previous + 1) headingSkips.push(`${previous}->${heading.level}: ${heading.text}`);
      previous = heading.level;
    }

    const lowContrast = [...document.querySelectorAll("main :is(p, a, span, li, dt, dd, label, summary, h1, h2, h3, h4, h5, h6, button)")]
      .filter(isVisible)
      .filter((el) => (el.textContent || "").trim().length > 0)
      .map((el) => {
        const style = getComputedStyle(el);
        const fg = parseColor(style.color);
        const bg = effectiveBackground(el);
        const ratio = fg && bg ? contrastRatio(fg, bg) : 99;
        const fontSize = Number.parseFloat(style.fontSize);
        const fontWeight = Number.parseFloat(style.fontWeight) || 400;
        const largeText = fontSize >= 24 || (fontSize >= 18.66 && fontWeight >= 700);
        const required = largeText ? 3 : 4.5;
        return { tag: el.tagName.toLowerCase(), text: (el.textContent || "").trim().slice(0, 70), ratio, required };
      })
      .filter((item) => item.ratio < item.required)
      .slice(0, 8);

    return {
      title: document.title,
      overflow,
      h1Count: headings.filter((heading) => heading.level === 1).length,
      headingSkips,
      unnamedInteractive,
      unlabeledFields,
      lowContrast,
    };
  });

  if (check.width <= 480 && result.overflow > 2) failures.push(`${check.name}: horizontal overflow ${result.overflow}px`);
  if (result.h1Count !== 1) failures.push(`${check.name}: expected one visible main h1, got ${result.h1Count}`);
  if (result.headingSkips.length) warnings.push(`${check.name}: heading skips ${result.headingSkips.join("; ")}`);
  if (result.unnamedInteractive.length) failures.push(`${check.name}: unnamed interactive ${result.unnamedInteractive.join(" | ")}`);
  if (result.unlabeledFields.length) failures.push(`${check.name}: unlabeled fields ${result.unlabeledFields.join(" | ")}`);
  if (result.lowContrast.length) {
    failures.push(`${check.name}: low contrast ${result.lowContrast.map((item) => `${item.ratio.toFixed(2)} ${item.tag} "${item.text}"`).join(" | ")}`);
  }

  const file = join(outDir, `${check.name}.png`);
  await page.screenshot({ path: file, fullPage: false });
  screenshots.push(file);
  await page.close();
}

await browser.close();

console.log(`browser_checks=${checks.length}`);
console.log(`screenshots_dir=${outDir}`);
for (const file of screenshots) console.log(`screenshot=${file}`);

if (warnings.length) {
  console.log("warnings:");
  for (const warning of warnings) console.log(`- ${warning}`);
}

if (failures.length) {
  console.error("failures:");
  for (const failure of failures) console.error(`- ${failure}`);
  process.exit(1);
}

console.log("Stage 21 browser responsive/accessibility audit complete.");

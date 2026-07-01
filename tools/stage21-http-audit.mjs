const base = "http://shemostudio.local";

const pages = [
  { path: "/", lang: "ar", dir: "rtl" },
  { path: "/en/", lang: "en-US", dir: "ltr" },
  { path: "/about/", lang: "ar", dir: "rtl" },
  { path: "/en/about-en/", lang: "en-US", dir: "ltr" },
  { path: "/services/", lang: "ar", dir: "rtl" },
  { path: "/en/services-en/", lang: "en-US", dir: "ltr" },
  { path: "/services/video-editing-motion/", lang: "ar", dir: "rtl" },
  { path: "/en/services-en/video-editing-motion-en/", lang: "en-US", dir: "ltr" },
  { path: "/services/graphic-design/", lang: "ar", dir: "rtl" },
  { path: "/en/services-en/graphic-design-en/", lang: "en-US", dir: "ltr" },
  { path: "/services/sketch-illustration/", lang: "ar", dir: "rtl" },
  { path: "/en/services-en/sketch-illustration-en/", lang: "en-US", dir: "ltr" },
  { path: "/services/storyboarding-creative-planning/", lang: "ar", dir: "rtl" },
  { path: "/en/services-en/storyboarding-creative-planning-en/", lang: "en-US", dir: "ltr" },
  { path: "/services/branding/", lang: "ar", dir: "rtl" },
  { path: "/en/services-en/branding-en/", lang: "en-US", dir: "ltr" },
  { path: "/services/creative-direction-custom/", lang: "ar", dir: "rtl" },
  { path: "/en/services-en/creative-direction-custom-en/", lang: "en-US", dir: "ltr" },
  { path: "/work/", lang: "ar", dir: "rtl" },
  { path: "/en/work/", lang: "en-US", dir: "ltr" },
  { path: "/work/frame-pulse-launch-film/", lang: "ar", dir: "rtl" },
  { path: "/en/work/frame-pulse-launch-film-en/", lang: "en-US", dir: "ltr" },
  { path: "/packages/", lang: "ar", dir: "rtl" },
  { path: "/en/packages-en/", lang: "en-US", dir: "ltr" },
  { path: "/request-a-quote/", lang: "ar", dir: "rtl" },
  { path: "/en/request-a-quote-en/", lang: "en-US", dir: "ltr" },
  { path: "/process/", lang: "ar", dir: "rtl" },
  { path: "/en/process-en/", lang: "en-US", dir: "ltr" },
  { path: "/testimonials/", lang: "ar", dir: "rtl" },
  { path: "/en/testimonials-en/", lang: "en-US", dir: "ltr" },
  { path: "/faq/", lang: "ar", dir: "rtl" },
  { path: "/en/faq-en/", lang: "en-US", dir: "ltr" },
  { path: "/start-a-project/", lang: "ar", dir: "rtl" },
  { path: "/en/start-a-project-en/", lang: "en-US", dir: "ltr" },
  { path: "/contact/", lang: "ar", dir: "rtl" },
  { path: "/en/contact-en/", lang: "en-US", dir: "ltr" },
  { path: "/terms/", lang: "ar", dir: "rtl" },
  { path: "/en/terms-en/", lang: "en-US", dir: "ltr" },
  { path: "/revision-policy/", lang: "ar", dir: "rtl" },
  { path: "/en/revision-policy-en/", lang: "en-US", dir: "ltr" },
  { path: "/deposit-policy/", lang: "ar", dir: "rtl" },
  { path: "/en/deposit-policy-en/", lang: "en-US", dir: "ltr" },
  { path: "/refund-policy/", lang: "ar", dir: "rtl" },
  { path: "/en/refund-policy-en/", lang: "en-US", dir: "ltr" },
  { path: "/delivery-policy/", lang: "ar", dir: "rtl" },
  { path: "/en/delivery-policy-en/", lang: "en-US", dir: "ltr" },
  { path: "/search/", lang: "ar", dir: "rtl" },
  { path: "/en/search-en/", lang: "en-US", dir: "ltr" },
];

const expected404 = ["/wp-sitemap.xml", "/sitemap_index.xml", "/stage21-missing-page-check/"];
const failures = [];
const warnings = [];
const localLinks = new Set();
const externalOrigins = new Set();
let totalHtmlBytes = 0;

function absolute(path) {
  return new URL(path, base).toString();
}

function findAll(regex, text) {
  const matches = [];
  let match;
  while ((match = regex.exec(text))) matches.push(match);
  return matches;
}

function normalizeLocalLink(href) {
  if (!href || href.startsWith("#") || href.startsWith("mailto:") || href.startsWith("tel:")) return null;
  const url = new URL(href, base);
  if (url.origin !== base) {
    externalOrigins.add(url.origin);
    return null;
  }
  url.hash = "";
  return url.toString();
}

async function head(url) {
  return fetch(url, { method: "HEAD", redirect: "manual" });
}

for (const page of pages) {
  const url = absolute(page.path);
  const res = await fetch(url, { redirect: "manual" });
  if (res.status !== 200) failures.push(`${page.path} expected 200 got ${res.status}`);
  if (res.headers.get("location")) failures.push(`${page.path} has unexpected Location header ${res.headers.get("location")}`);

  const html = await res.text();
  totalHtmlBytes += Buffer.byteLength(html);

  const htmlOpen = html.match(/<html[^>]*>/i)?.[0] ?? "";
  if (!new RegExp(`lang=["']${page.lang}["']`, "i").test(htmlOpen)) {
    failures.push(`${page.path} missing html lang ${page.lang}`);
  }
  if (!new RegExp(`dir=["']${page.dir}["']`, "i").test(htmlOpen)) {
    failures.push(`${page.path} missing html dir ${page.dir}`);
  }

  const canonical = html.match(/<link[^>]+rel=["']canonical["'][^>]+href=["']([^"']+)["']/i)?.[1];
  if (!canonical) {
    failures.push(`${page.path} missing canonical`);
  } else if (canonical !== url) {
    failures.push(`${page.path} canonical mismatch: ${canonical}`);
  }

  const alternateTags = findAll(/<link[^>]+>/gi, html)
    .map((match) => match[0])
    .filter((tag) => /rel=["']alternate["']/i.test(tag) && /hreflang=/i.test(tag));
  const hreflangs = new Set(
    alternateTags
      .map((tag) => tag.match(/hreflang=["']([^"']+)["']/i)?.[1])
      .filter(Boolean)
  );
  if (!hreflangs.has("ar") || !hreflangs.has("en")) {
    failures.push(`${page.path} missing ar/en hreflang`);
  }

  if (html.includes("/en/start-a-project/") || html.includes("/en/contact/")) {
    failures.push(`${page.path} contains old English CTA URL`);
  }

  const robotMeta = html.match(/<meta[^>]+name=["']robots["'][^>]+content=["']([^"']+)["']/i)?.[1] ?? "";
  if (!robotMeta.includes("noindex")) warnings.push(`${page.path} has no noindex robots meta in LocalWP`);

  for (const match of findAll(/<a[^>]+\shref=["']([^"']+)["']/gi, html)) {
    const link = normalizeLocalLink(match[1]);
    if (link) localLinks.add(link);
  }

  for (const match of findAll(/<(?:script|link|img|iframe)[^>]+\s(?:src|href)=["']([^"']+)["']/gi, html)) {
    normalizeLocalLink(match[1]);
  }
}

for (const link of localLinks) {
  const url = new URL(link);
  if (url.pathname.startsWith("/wp-admin/") || url.pathname.startsWith("/wp-login.php")) continue;
  const res = await head(link);
  if (res.status >= 400) failures.push(`broken internal link ${link} => ${res.status}`);
  if (res.status >= 300 && res.status < 400) warnings.push(`redirecting internal link ${link} => ${res.status} ${res.headers.get("location")}`);
}

for (const path of expected404) {
  const res = await head(absolute(path));
  if (res.status !== 404) failures.push(`${path} expected 404 got ${res.status}`);
}

console.log(`pages_checked=${pages.length}`);
console.log(`internal_links_checked=${localLinks.size}`);
console.log(`external_origins=${Array.from(externalOrigins).sort().join(",")}`);
console.log(`total_html_bytes=${totalHtmlBytes}`);

if (warnings.length) {
  console.log("warnings:");
  for (const warning of warnings) console.log(`- ${warning}`);
}

if (failures.length) {
  console.error("failures:");
  for (const failure of failures) console.error(`- ${failure}`);
  process.exit(1);
}

console.log("Stage 21 HTTP/HTML audit complete.");

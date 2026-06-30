# Shemo Studio — Complete WordPress Strategy and Implementation Plan

> **Brand:** Shemo Studio — *From Sketch to Screen* · **Status:** Planning only (nothing built/installed/migrated). For review → adjustment → approval before implementation.
>
> **Legend:** **[C]** Confirmed fact (sourced) · **[R]** Professional recommendation · **[A]** Assumption · **[F]** Future enhancement. Prices needing business input are marked `‹PLACEHOLDER›`. Version facts verified June 2026 (sources in §43).

---

## Plan Update v2 — Locked Decisions & Changes (2026-06-30)

> This section **supersedes** the corresponding parts of the original plan below, based on the client's §42 export and new context. Sections 1–43 remain as the full reference; where they conflict with v2, **v2 wins**.

**Status of §42 decisions (from client export):**

| # | Decision | Status | Action in v2 |
|---|---|---|---|
| 1 | Brand direction | ✅ Approved | Locked |
| 5 | Logo direction | ✅ Approved | Locked |
| 2 | Visual identity | ✏️ Provisional | **Gate:** moodboard + 2–3 homepage mockups + contrast test before final sign-off. Do **not** finalize Cinematic Noir from text alone. |
| 3 | Colour palette | ✏️ Provisional | Validate on real UI / images / video; **reduce Ember dominance** to a true accent (~5–10% of surface) so it never reads "technical/mathematical"; run contrast tests. |
| 4 | Typography | ✏️ Provisional | Inter = body (provisional). Choose Display font from **live samples**. **Add a matching Arabic typeface** (bilingual now). |
| 6 | Language | ✏️ Changed | **Bilingual AR + EN at launch** (not Phase 2). Default by primary market. See **Change L1**. |
| 15 | Launch sitemap | ✏️ Changed | **Leaner, stronger sitemap** — fewer pages, deeper content. See **Change S1**. |
| 7–14, 16–21 | architecture, builder, theme, portfolio, forms, selling, payment, pricing, packages, motion, scope, hosting, video, assumptions | ◻️ Not marked | Recommendations **stand provisionally** but are **NOT executed** until explicitly locked (guardrail #7). |

### Change L1 — Bilingual at launch (supersedes §26)
- AR + EN both live at launch. **Polylang Pro** moves Future → **Launch Essential** (added to §40 and §10).
- **Default language [R]:** Arabic at `/`, English at `/en/`, **RTL-first** design — primary market is Egypt + Gulf. *Sub-decision to confirm: if a premium-international signal is preferred, English-default + Arabic is equally valid.*
- **Ripple effects:** design system becomes **bidirectional / RTL-first**; content workload ≈ **×2** (every page, case study, service, form, email in both languages); SEO adds `hreflang`; **Bricks ↔ Polylang compatibility MUST be verified before the architecture phase** (guardrail). Updates §27 (content), §38 (scope), §39 (risks).

### Change S1 — Leaner launch sitemap (supersedes §12 launch set)
- **Home · About · Services** (one strong page, 6 categories as sections — not 6 thin pages) **· Work** (archive) **+ 4–6 deep case studies · Packages · Start a Project** (brief hub) **· Contact · Policies ·** utility (Thank-you, 404, Search).
- **Process / Testimonials / FAQ** → folded into Home + About + the Services page (not standalone at launch).
- 2–3 strongest service topics can graduate to standalone pages later, once content justifies them. **Rule: no page ships without strong content.**

### Business inputs received
- **Market & language:** Egypt + Gulf; bilingual AR + EN → updates **A2**.
- **Prices:** illustrative examples below (examples, **not** final market rates).
- **LocalWP:** site already exists → see **§11 Actual Environment** + guardrails.

**Example pricing (ILLUSTRATIVE ONLY — confirm real numbers).** USD shown; can display EGP/SAR. "From" anchors. These replace the `‹PLACEHOLDER›` markers **as examples only**.

| Package | Example "from" | |
|---|---|---|
| Reels Creator (4 reels) | ~$180 | example |
| Reels Pro (8 reels) | ~$320 | example |
| YouTube Creator | ~$150 | example |
| Social Growth (monthly) | ~$450/mo | example |
| Visual Campaign | ~$400 | example |
| Sketch Commission | ~$90 | example |
| Sketch-to-Digital | ~$160 | example |
| Storyboard | ~$200 | example |
| Visual Identity | ~$800 | example |
| Monthly Content Retainer | ~$700/mo | example |

### §11 update — Actual LocalWP environment (SOURCE OF TRUTH — do not recreate)

| Setting | Actual (keep) | Notes |
|---|---|---|
| Site name | Shemo Studio | keep |
| Local URL | https://shemo-studio.local | **keep this exact domain** (not `shemostudio.local`) |
| WordPress | 7.0 | keep |
| PHP | **8.4.16** | **keep** — do not downgrade to 8.3 unless a documented plugin conflict appears |
| Web server | Nginx 1.26.1 | keep |
| Database | MySQL 8.4.0 | keep credentials untouched |
| Multisite | No | keep |
| SSL + local domain | working | **do not touch SSL / Router Mode / Windows services** |
| Port 80 | freed (IIS/W3SVC stopped) | already resolved |
| Admin user | exists | keep; later verify non-`admin` + strong password + 2FA |

**Guardrails (client-mandated, apply to every step):** full backup before any change · audit before acting (never redo finished work) · don't touch SSL / Router Mode / Windows services / DB credentials · run architecture steps only after the related decision is locked · no design, no final pages, no paid-plugin installs before licenses are provided · report everything inspected or modified.

---

## Implementation Phases (LocalWP) — gradual & gated

Each phase: **backup → act minimally → report**. Nothing in a phase runs until its prerequisites are met.

**Phase 0 — Safety & Audit (READ-ONLY, no config changes).**
- Full LocalWP backup/snapshot + dated DB export.
- Read-only inventory: WP/PHP versions, active theme(s), active/inactive plugins, users & roles, existing pages/posts, current General/Reading/Permalinks/Discussion settings, indexing flag, `wp-config` flags, uploads.
- **Deliverable:** Environment Audit Report (what exists · what's already configured · what's default/removable). → review gate.

**Phase 1 — Baseline WordPress config (decision-independent; matches guardrail #5–6).** After backup:
- General: Site Title `Shemo Studio`, Tagline `From Sketch to Screen`, Timezone `Africa/Cairo`, date/time format.
- Reading: **Discourage search engines** ON (development).
- Permalinks: `/%postname%/`.
- Discussion: disable comments, pingbacks, trackbacks (existing + default-for-new).
- Cleanup: delete "Hello World" post, "Sample Page", default comment; remove Hello Dolly (and Akismet unless wanted); remove unused default themes, keep **one** current default as a safe fallback.
- Dev debug flags if missing (`WP_DEBUG`/`WP_DEBUG_LOG` on, `WP_DEBUG_DISPLAY` off).
- **Keep PHP 8.4.16 + domain.** → backup + Baseline Config Report → review gate.

**Phase 2 — Decision-lock & licensing gate (no execution).** Needed from client:
- Lock decisions **7–14**.
- Provide/confirm **licenses**: Bricks · ACF Pro · Fluent Forms Pro · SureCart · Polylang Pro.
- Confirm **default language** (Arabic vs English).
- Approve **visual proofs** (moodboard + 2–3 homepage mockups) for decisions 2/3/4.

**Phase 3 — Version control & scaffolding** (after 7–9 locked + licenses): Git init + `.gitignore`; install + activate Bricks; create empty `shemo-child` child theme; create `shemo-core` plugin skeleton (no CPT yet). Backup + report. **No design.**

**Phase 4 — Content model** (after 10 locked): `shemo-core` registers Projects CPT + taxonomies; ACF Pro field groups (case study / service / package / options). Backup + report.

**Phase 5 — Bilingual foundation** (after L1 default confirmed + Bricks↔Polylang verified): install Polylang Pro; configure AR + EN, default language, URL structure, string translation, RTL baseline. Backup + report.

**Phase 6 — Forms & commerce foundations** (after 11–14 locked + licenses): Fluent Forms Pro (Contact + Start-a-Project hub structure); SureCart in **test mode** (Stripe test keys only); install + baseline-config the essential free stack (Rank Math, Safe SVG, Turnstile, ShortPixel, Wordfence, Complianz). Backup + report.

**Phase 7 — Visual proofs & design system** (after 2/3/4 approved): deliver moodboard + 2–3 homepage mockups → sign-off → then design tokens (bidirectional), global styles, core components in Bricks.

**Phase 8+ — Pages → portfolio → content integration → responsive → motion → a11y → performance → SEO → security → QA** per original **§37**, each gated + backup + report.

**Final — Migration & launch** per **§35** (after hosting chosen).

> **Right now we are at the end of "Plan Update v2." Next executable step = Phase 0 (backup + read-only audit) — on the client's go-ahead.** Phases 3+ are blocked until Phase 2 (decisions 7–14, licenses, default language, visual proofs) is cleared.

---

## 1. Skills and Capabilities Applied

| Capability consulted | How it concretely shaped this plan |
|---|---|
| `ui-ux-pro-max:design-system` (read on disk) | Adopted its **three-layer token model** (primitive → semantic → component) as the literal structure of §23 |
| `frontend-design`, `ui-ux-pro-max:brand` | Anti-template visual direction, the three identity routes in §20 |
| `wordpress-pro` (managed skill — domain guidance) | Architecture, CPT/ACF modeling, hooks, hardening, query-loop portfolio |
| `woocommerce-backend-dev` (managed) | Drove the deliberate decision to **not** use WooCommerce at launch (§16/§40) |
| `accessibility` / `accessibility-compliance` | WCAG 2.2 plan, accessible accordions/sliders/forms (§31) |
| `performance` | Core Web Vitals budget and media strategy (§30) |
| `ui-animation`, `hyperframes-animation`, `framer-motion-animator` | Motion system + reduced-motion fallbacks (§24) and the hero (§14) |
| `documentation-writer` (Diátaxis) | Document structure and future client docs |
| `git-commit`, `review`, `security-review` | Git workflow (§11), QA + security gates (§36) |

Live verification was run against `wordpress.org`, `make.wordpress.org`, `bricksbuilder.io`, `advancedcustomfields.com`, `fluentforms.com`, `surecart.com`, `localwp.com`, and SEO/multilingual primary comparisons (sources in §43).

---

## 2. Executive Summary

Shemo Studio needs a **premium, cinematic studio site** that sells creative services and generates qualified leads — not a freelancer portfolio. The recommended build is **WordPress 7.0** **[C]** + **Bricks Builder** on a custom child theme, with **ACF Pro** for content modeling and a small in-house **`shemo-core`** plugin owning the Projects CPT (so portfolio data is portable forever). Lead capture runs on **Fluent Forms Pro** (multi-step, conditional, file uploads, save-and-resume). Selling is **quote-first with deposits**: custom work → brief → quote → 50% deposit via **SureCart** (far lighter than WooCommerce, native installments/subscriptions); packages display **"starting from"** pricing. Launch is **English-first**, architecturally Arabic-ready (Polylang Pro, Phase 2). Visual identity is **"Cinematic Noir"** with a sketch-line motif that enacts *From Sketch to Screen* in the UI. Motion is restrained and reduced-motion safe. Migration via **Duplicator**. Brand posture: **boutique studio with a visible founder.** Priority order followed literally: premium quality → performance → UX → accessibility → security → maintainability → content simplicity → scalability → minimal bloat.

---

## 3. Assumptions

| # | Assumption **[A]** | Why assumed | Impact if wrong |
|---|---|---|---|
| A1 | Solo / very small studio operating the site | Brief framing | Workflow weight (§17) |
| A2 | Market = MENA + international creators/brands; business language English first | Founder context + premium reach | Language strategy (§26) |
| A3 | Real prices not set → placeholders + "starting from" | Brief instruction | Pricing display (§16) |
| A4 | Hosting undecided → caching/CDN/object-cache deferred until chosen | Brief instruction | Perf timing (§30) |
| A5 | Video delivered via **Vimeo** (clean player), not self-hosted | Performance priority | Hero/portfolio media |
| A6 | Budget allows a few premium licenses (Bricks, ACF Pro, Fluent Forms Pro, SureCart Pro) | Premium positioning | Falls back to free tiers |
| A7 | Founder supplies portfolio assets + signed client permissions before dev | Standard practice | Launch blocker (weak portfolio) |
| A8 | One admin user at launch; Editor role added when delegating | Small team | Roles (§32) |
| A9 | Payment processing via **Stripe** (PayPal optional) | SureCart default, MENA-friendly | Checkout config |

---

## 4. Final Decision Summary

| Decision | Final choice | Type | One-line rationale |
|---|---|---|---|
| CMS version | WordPress 7.0 | [C] | Current stable since May 2026 |
| PHP | 8.3 | [R] | WP-recommended; core fully compatible 8.0–8.3 |
| Builder | Bricks Builder | [R] | Best perf among visual builders; query loops; lifetime license |
| Theme | Custom child theme over Bricks | [R] | CSS/JS/snippets under Git |
| Content model | ACF Pro + `shemo-core` plugin | [R] | Portable portfolio data |
| Portfolio | Projects CPT + ACF + taxonomies | [R] | Query-driven, editor-friendly |
| Forms | Fluent Forms Pro | [R] | Multi-step, conditional, uploads, lightweight |
| Selling | Quote-first + SureCart deposits; "from" pricing | [R] | Matches custom creative work; low bloat |
| SEO | Rank Math | [R] | Richer free schema than Yoast |
| Security | Wordfence Free + 2FA + hardening | [R] | One tool, malware + login |
| Images | ShortPixel/Imagify (WebP/AVIF) | [R] | Works locally, pre-hosting |
| Video | Vimeo + click-to-play | [R] | Perf + clean player |
| Migration | Duplicator | [R] | Reliable LocalWP→live package |
| Language | English-first; Arabic Phase 2 (Polylang Pro) | [R] | Trust + lower workload; RTL-ready |
| Visual direction | Cinematic Noir + sketch motif | [R] | Best embodiment of "Sketch to Screen" |
| Launch scope | "Recommended Launch" (§38) | [R] | Premium yet maintainable |

---

## 5. Strategic Brand Direction

**Three positioning directions**

| Direction | Strengths | Weaknesses | Audience fit | Scalability | Perception | Commercial impact |
|---|---|---|---|---|---|---|
| **1. Personal brand ("Shemo")** | Fast trust, authentic, creator-native | Caps at one person; hard to delegate/sell | Creators, small brands | Low | Talented individual | Good retainers, weak for agencies |
| **2. Pure creative studio** | Scales, hireable, agency-grade | Anonymous; loses founder's edge | SMBs, agencies | High | Established firm | Higher project values |
| **3. Hybrid studio + visible founder** ✅ | Trust *and* scale; premium yet personal | Needs disciplined brand voice | All tiers | High | Boutique with a signature | Best blended value |

**Final recommendation [R]: Direction 3 — boutique creative studio with a visible founder.** "Shemo Studio" reads as a studio (premium, scalable, sellable) while the founder's hand-drawn craft carries trust and originality. This is the only direction that supports retainers *and* agency/white-label work later.

- **Brand promise:** ideas that begin as a sketch and end as something people stop scrolling to watch.
- **Value proposition:** one studio takes the work from the first pencil line to the final cut — concept, design, and editing under one cinematic eye.
- **Differentiators:** (1) genuine drawing/illustration craft most editors lack; (2) end-to-end sketch→design→video pipeline; (3) cinematic, film-grade taste; (4) a *visible* process (the sketch-to-screen story itself becomes proof).
- **Personality / tone:** confident, calm, cinematic, precise, generous with craft, never hypey. Editorial voice — short, specific sentences; shows rather than boasts.
- **Avoiding price competition:** sell the *transformation and taste*, lead with before/after sketch→screen evidence, package outcomes (not hours), use quote-first so the conversation opens on fit, not cost.
- **"From Sketch to Screen" through the experience:** it is the H1, the order of the homepage narrative, the three-stage hero, the case-study spine (sketch → design → screen), the loading animation, and a recurring sketch-stroke UI motif.

Ready-to-use copy (finalized in §28): elevator pitch — *"Shemo Studio turns ideas into cinematic visuals — from the first sketch to the final screen."* · H1 — **"From Sketch to Screen."** · Primary CTA — **Start a Project** · Secondary — **View the Work** · Bio — *"Creative studio • Sketch → Design → Screen • Video, design & illustration with a cinematic eye."*

---

## 6. Target Customers

**Personas**

| Persona | Profile | Top need | Top fear | Budget | Likely buys | Decisive trust signal | What makes them leave | Preferred contact | Repeat/retainer |
|---|---|---|---|---|---|---|---|---|---|
| **P1 Creator/YouTuber** (primary, high-volume) | Personal brand, 10k–500k followers | Consistent on-brand video, fast | Generic editor, missed deadlines | Mid, value-aware | Reels/YouTube editing, thumbnails | Showreel + before/after + clear turnaround | Slow site, unclear pricing | WhatsApp + short form | **High** → monthly retainer |
| **P2 SMB / DTC brand** (high-value) | Founder / marketing lead | Premium-looking campaign | Looking cheap / off-brand | Mid–high | Campaign visuals, promo video, branding | Case studies with results | Thin portfolio, no proof | Email + brief | Medium → campaigns |
| **P3 Marketing agency** (high-value, white-label) | Producer / creative director | Reliable overflow craft, confidential | Quality slip, leak | Project-based | Editing, illustration, storyboards | NDA handling, consistency, process | No process/NDA clarity | Email | **High** → ongoing |
| **P4 Coach/consultant** | Solo authority | Authority-grade visuals | Time, complexity | Mid | Social packages, sketch portrait | Simple process | Friction | WhatsApp/form | Medium |
| **P5 Bespoke-art client** (niche, high-margin) | Individual / brand | Custom sketch/illustration | Won't match vision | Variable | Commissions, sketch-to-digital | Gallery + commission terms | Vague terms | Form | Low–medium |

**Information each needs before contacting:** relevant work, clear deliverables/turnaround, pricing signal (even "from"), process, contact options. **Buying journey (universal):** see work → trust craft → understand fit → quick quote/WhatsApp.

**Prioritization:** Primary = **P1**. High-value = **P2 + P3**. High-volume = **P1**. **Best launch audience = P1 + P2** (clearest journey, strongest portfolio evidence). P3/P5 nurtured post-launch.

---

## 7. Website Goals and KPIs

| Class | KPI | First 3 months **[R targets]** | First 6 months |
|---|---|---|---|
| **Business** | Qualified inquiries / mo | 8–15 | 20–35 |
| Business | Deposit-paid projects / mo | 2–5 | 6–12 |
| Business | Retainer inquiries | 1–3 total | 3–6 / mo |
| Business | Repeat-client rate | baseline | ≥ 25% |
| Business | Revenue indicator (deposits collected) | establish baseline | upward trend |
| **Marketing** | Primary-CTA click rate | 4–7% of sessions | 6–10% |
| Marketing | WhatsApp conversions / mo | 5–10 | 12–20 |
| Marketing | Email leads / mo | 5–12 | 15–30 |
| **UX** | Brief-form completion (start→submit) | ≥ 35% | ≥ 50% |
| UX | Portfolio → inquiry conversion | 2–4% | 4–6% |
| UX | Engaged sessions | ≥ 55% | ≥ 65% |
| UX | Case-study scroll depth | ≥ 60% | ≥ 70% |
| **Performance** | Mobile PageSpeed (field) | ≥ 75 | ≥ 85 |
| Performance | LCP / INP / CLS (mobile) | ≤2.5s / ≤200ms / ≤0.1 | ≤2.0s / ≤150ms / ≤0.05 |
| **SEO** | Indexed core service/case pages | all | + journal |
| SEO | Organic impressions (GSC) | baseline | +40–60% vs M3 |
| SEO | Ranking service keywords (top 20) | 3–6 | 10–15 |

Targets are realistic ranges, not guarantees; revisit after baseline data. Classes deliberately separated per the brief.

---

## 8. WordPress Architecture Comparison

| Option | Design freedom | Cinematic | Animation | Dynamic content | Portfolio | Selling | Forms | Responsive | A11y | Perf | Maintainability | Curve | Cost | Plugin dep | Lock-in | Editor UX | Scalability | Multilingual | Dev control |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Native Gutenberg | M | M | L-M | M | M | OK | OK | Good | Good | **High** | High | M | Free | Low | **Low** | Good | Good | Good | M |
| Full Site Editing | M | M | L-M | M | M | OK | OK | Good | Good | High | High | M-H | Free | Low | Low | M | Good | Good | M |
| **Bricks** ✅ | **High** | **High** | **High** | **High** | **High** | OK | OK | **High** | Good* | **High** | High | M | $79–599 one-off [C] | Med | Med | Good | High | Good* | **High** |
| Elementor Pro | High | High | High | M-H | M-H | OK | OK | High | Med | **Low-M** | Med | Low | ~$59+/yr | High | **High** | Good | Med | Good | Med |
| Breakdance | High | High | High | High | High | Good | OK | High | Med | M-H | Med | M | $ | Med | M-H | Good | High | Good | M-H |
| GeneratePress + GenerateBlocks | M-H | M | L-M | M-H | M-H | OK | OK | High | **High** | **Very High** | High | M | $ | Low | **Low** | Good | High | Good | High |
| Kadence | M | M | L-M | M | M | Good | OK | High | High | High | High | Low | $ | L-M | Low | Good | M-H | Good | Med |
| Custom lightweight theme | **High** | High | High (manual) | M (manual) | High (manual) | Manual | Manual | Manual | High | **Very High** | Med (dev-bound) | High | Dev time | Low | Low | **Poor** | High | Manual | **Very High** |
| Custom block theme | High | High | M-H | High | High | OK | OK | High | High | Very High | Med | High | Dev time | Low | Low | Med | High | Good | Very High |
| Hybrid custom + blocks | High | High | M-H | High | High | OK | OK | High | High | Very High | Med | High | Dev time | Low | Low | Med | High | Good | Very High |

\*Bricks a11y and multilingual quality depend on disciplined markup and a verified Polylang/Bricks pairing (§26).

**Decision [R]: Bricks Builder on a custom child theme.** It is the only option delivering cinematic design freedom *and* clean, performant output *and* native dynamic/query-loop content for the portfolio — without Elementor's bloat/lock-in or a hand-coded theme's poor editor UX. **Runner-up: GeneratePress + GenerateBlocks** (lighter, zero lock-in) — the fallback if a no-license / maximum-performance path is preferred, at the cost of more manual work for cinematic layouts. **Lock-in is mitigated because content lives in ACF/CPTs (portable), not inside the builder.** **[C]** Bricks is a one-time license ($79/1 site, $149/3, $249/unlimited, $599 lifetime; WooCommerce + popup builders included; 60-day refund).

---

## 9. Final Technical Architecture

- **Theme/builder [R]:** Bricks (acts as theme) + **`shemo-child`** child theme holding `style.css` token overrides, `functions.php`, enqueued GSAP/custom JS, template parts — all under Git.
- **Functionality plugin [R]:** **`shemo-core`** (custom) registers the Projects CPT, taxonomies, helper snippets, and security tweaks — so portfolio data survives any future theme/builder change. *Single most important portability decision.*
- **Dynamic content [R]:** Bricks query loops + ACF dynamic data.
- **Custom fields [R]:** **ACF Pro** (repeater, gallery, flexible content, options pages, ACF blocks). **[C]** WP Engine owns ACF (since 2022); ACF 6.1+ can register CPTs/taxonomies from its UI in both free and Pro.
- **CPT method [R]:** registered in code inside `shemo-core` (versioned, no CPT-UI plugin). ACF's post-type UI is the no-code fallback.
- **Templates [R]:** Bricks templates for archive, single-project, service, package, plus global header/footer and a reusable "sections" library.
- **Reusable components [R]:** Bricks **global classes** mapped to CSS custom-property tokens (§23) + a component template set (cards, CTA, before/after, testimonial).
- **CSS/JS strategy [R]:** design tokens as CSS variables in the child theme; component styles via Bricks global classes; custom JS enqueued **only on pages that use it** (hero).
- **Animation [R]:** GSAP (hero + scroll reveals) + Lottie (sketch line-draw) + Bricks native interactions elsewhere; all behind `prefers-reduced-motion`.
- **Child theme:** **required.** **Git [R]:** child theme + `shemo-core` versioned only (§11).

---

## 10. Plugin Strategy

Each plugin is justified, non-duplicating, and chosen against the priority order.

### 10.1 Core building
| Plugin | Purpose / Shemo use | Cost | Tier | Perf | Alt |
|---|---|---|---|---|---|
| **Bricks** | Builder/theme; all layouts | One-off [C] | Essential | Low cost (clean output) | Breakdance |
| **ACF Pro** | Custom fields for cases/services/packages/options | Paid | Essential | Negligible | Meta Box |
| **`shemo-core` (custom)** | Projects CPT, taxonomies, snippets | In-house | Essential | None | CPT-UI + Code Snippets (rejected — bloat) |
| **Safe SVG** | Sanitized SVG upload (logo, icons, sketch line art) | Free | Essential | None | manual sanitize |

### 10.2 Service selling — comparison
| Option | Fixed pkgs | "From" price | Quote-only | Deposits | Installments | Add-ons | Retainers | Bloat | Verdict |
|---|---|---|---|---|---|---|---|---|---|
| WooCommerce | ✅ | ✅ | via form | plugin | plugin | ✅ | Subscriptions ($) | **High** | Overkill at launch [F if shop] |
| **SureCart** ✅ | ✅ | ✅ | hybrid | ✅ [C] | ✅ [C] | ✅ | ✅ native [C] | **Low** | **Launch winner** |
| Easy Digital Downloads | ✅ | n/a | no | partial | ext | ✅ | ext | Med | Digital products only |
| Quote-only (forms) | n/a | display | ✅ | manual link | manual | manual | manual | None | Pairs with SureCart |

**[C] SureCart** supports one-time, subscriptions, trials, setup fees, **payment plans/installments**, pay-what-you-want; processors **Stripe/PayPal/Razorpay/Mollie**; free Launch plan (1.9% fee) or Pro from **$179/yr intro (renews $199)**. **Decision [R]: quote-first + SureCart for deposits and fixed-package checkout.** Custom work → brief → manual quote → SureCart link for 50% deposit; balance on delivery. **[F]** WooCommerce only if a template/preset shop is later added.

### 10.3 Forms — **Fluent Forms Pro** [R]
**[C]** Pro provides multi-step (with progress bar), conditional logic (fields/pages/emails/confirmations), file/image upload, **partial entries (save & resume)**, conditional pricing/calculations, PDF generation, 60+ integrations. **Alt:** Gravity Forms (more mature, heavier, pricier). **Large-file strategy [R]:** cap uploads ~25–50 MB; raw footage via **external-link field** (Drive/WeTransfer/Dropbox) — never push gigabytes through PHP.

### 10.4 Portfolio — no plugin needed [R]
Projects CPT (`shemo-core`) + ACF fields + Bricks query loops cover archive, single, before/after, related, Vimeo embeds, galleries. **Avoids portfolio/slider plugins entirely.**

### 10.5 Performance — split by dependency
- **[R, works locally] Images:** **ShortPixel or Imagify** (WebP/AVIF, compression). Native responsive `srcset`.
- **[R] Lazy load:** native + Bricks; video = click-to-play.
- **[A4 — hosting-dependent] Page/browser/object caching + CDN:** decide **after hosting** (likely WP Rocket + Cloudflare/Bunny + Redis). **Do not install caching on LocalWP** — meaningless locally and complicates migration.
- **[R] DB cleanup:** occasional WP-CLI; avoid heavy "optimizer" plugins.

### 10.6 SEO — **Rank Math** [R]
**[C]** Free tier: 20+ schema types, redirect manager, 404 monitor, image SEO, GSC integration, breadcrumbs, sitemap — richer than Yoast free/premium for our needs. Schemas used: Organization/**ProfessionalService**, **Service**, Article, VideoObject. One SEO plugin only. **Alt:** Yoast.

### 10.7 Security — **Wordfence Free** + 2FA + hardening [R]
Malware scan, login security, login-attempt limiting; plus least-privilege roles, login rename, MIME validation, Safe SVG, security headers (server/Cloudflare). **Alt:** Solid Security. **[F]** Cloudflare WAF post-hosting. One security plugin only.

### 10.8 Backups & migration
- **[R] Local:** LocalWP snapshots/blueprints + periodic exports.
- **[R] Migration LocalWP→live:** **Duplicator** (full package). **Alt:** WP Migrate / All-in-One WP Migration.
- **[A4] Live ongoing backups:** UpdraftPlus or host-native (decide with hosting).

### 10.9 Analytics & marketing
- **[R] GA4 + Google Search Console** via a single **GTM** container (GA4 + Meta Pixel; TikTok Pixel only if paid social justified).
- **[R] Cookie consent:** **Complianz** (GDPR + Consent Mode v2). **Alt:** CookieYes.
- **[R] Email/CRM:** form→email + a simple list (MailerLite/Brevo) via webhook; CRM later. **WhatsApp tracking:** GA4 event on click-to-chat.

### 10.10 Stacks
| Stack | Contents |
|---|---|
| **Minimal launch** | Bricks · ACF Pro · `shemo-core` · Fluent Forms Pro · Safe SVG · Rank Math · Cloudflare Turnstile · ShortPixel · Wordfence Free · Complianz · GA4 via GTM · Duplicator (migration) |
| **Recommended launch** | Minimal **+ SureCart** (deposits/packages) **+** UpdraftPlus (post-hosting) |
| **Optional advanced** | SureCart Subscriptions (retainers) · Fluent Forms partial-entries (save/resume) · WP Migrate |
| **Future [F]** | WooCommerce (only if shop) · Polylang Pro (Arabic) · client portal (SureMembers/custom) · WP Rocket + Cloudflare/Bunny (post-hosting) |
| **Avoid** | Elementor (lock-in) · multiple SEO/security/cache plugins · CPT-UI + Code Snippets (replaced by `shemo-core`) · portfolio/slider plugins · "all-in-one" mega-plugins · reCAPTCHA (use Turnstile) |

---

## 11. LocalWP Development Plan

| Setting | Recommendation |
|---|---|
| Site name | `Shemo Studio` |
| Local domain | `shemostudio.local` |
| WordPress | **7.0** (current stable [C]) |
| PHP | **8.3** ([C] WP-recommended; LocalWP hot-swaps PHP 5.6–8.x) |
| Web server | **Nginx** (LocalWP, hot-swappable [C]) |
| Database | MySQL, UTF8MB4 |
| Local SSL | LocalWP trusted SSL on [C] |
| Email testing | **Mailpit** capture (built into LocalWP [C] — no real sends, works offline) |
| Admin user | Non-`admin` username, strong unique password, single admin |
| Debug | `WP_DEBUG=true`, `WP_DEBUG_LOG=true`, `WP_DEBUG_DISPLAY=false`, `SCRIPT_DEBUG=true`; log at `wp-content/debug.log` |
| Dev plugin | Query Monitor (dev only — never on prod) |
| Theme/plugin | `shemo-child` child theme + `shemo-core` plugin |
| Secrets/API keys | In `wp-config-local.php` / env — **never committed** |
| Media | Descriptive filenames + optional folders later; keep uploads out of Git |
| Blueprint | Save a LocalWP **blueprint** once stack is set, for clean re-spin |

**Git [R]:** version **child theme + `shemo-core` only**. **Branches:** `main` + `feature/*`; **conventional commits** (`git-commit` skill). **`.gitignore`:** `/wp/`, `wp-config.php`, `/wp-content/uploads/`, all plugins except `shemo-core`, `*.log`, `node_modules/`, `.env`, LocalWP files. **Commit:** `themes/shemo-child/`, `plugins/shemo-core/`, docs.

**Development workflow (sequence — execute later, not now):** Discovery → Requirements → Content collection → Sitemap → Journeys → Wireframes → Visual identity → Design system → LocalWP setup → Theme/plugins → Content modeling → Global styles → Components → Pages → Portfolio → Forms → Selling → Content integration → Responsive → A11y → Performance → Security → SEO → Migration → Launch → Monitoring.

---

## 12. Sitemap

**Launch sitemap [R]:** Home · About · Services (overview) · Service detail ×6 (Video Editing & Motion, Graphic Design, Sketch & Illustration, Storyboarding & Creative Planning, Branding, Creative Direction/Custom) · Packages · Work (portfolio archive) · Case Study (single) · Process · Testimonials · FAQ · Start a Project (brief hub) · Request a Quote · Contact · Policies (Privacy, Cookie, Terms, Revision, Deposit, Refund, Delivery) · Thank-you pages · 404 · Search.

**Future expansion sitemap [F]:** per-service landings (Reels, YouTube, Sketch-to-Digital) · portfolio sub-archives (Video / Graphic / Sketch / Storyboard / Before-after) · Journal/Blog · Retainers page · Client Portal · Payment pages · Resource/template shop · Arabic (`/ar/`) tree.

---

## 13. Page Plans

| Page | Purpose | Target visitor | Main sections | Primary / Secondary CTA | Visual treatment | SEO objective | Launch priority |
|---|---|---|---|---|---|---|---|
| **Home** | Convert + orient | All | Hero, showreel, selected work, services, sketch-to-screen, process, packages, testimonials, FAQ, final CTA | Start a Project / View Work | Cinematic, full-bleed | Brand + "creative studio" terms | **P0** |
| **About** | Trust + founder story | P2/P3/P5 | Story, founder, craft, tools, values | Start a Project / Contact | Portrait + editorial | Brand/About | **P0** |
| **Services overview** | Route to service | All | Category grid, "how it works" | View service / Quote | Grid, restrained | Service hub | **P0** |
| **Service detail ×6** | Sell one service | Buyer | Problem, deliverables, process, evidence, packages, FAQ | Request a Quote / Start | Media-led | "{service} for {audience}" | **P0** |
| **Packages** | Compare offers | P1/P4 | Tiers, add-ons, deposit note, FAQ | Start a Project | Premium cards | "{service} packages/pricing" | **P0** |
| **Work (archive)** | Prove craft | All | Filterable grid, featured | Open case / Start | Gallery, hover reveal | Portfolio | **P0** |
| **Case study (single)** | Deep proof | P2/P3 | §15 16-part structure | Start a Project | Cinematic story | Long-tail project terms | **P0** |
| **Process** | Lower risk | P2/P3 | Sketch→Design→Screen steps | Start a Project | Step reveal | "creative process" | **P1** |
| **Testimonials** | Social proof | All | Quotes, logos | Start a Project | Quote cards | Reviews | **P1** |
| **FAQ** | Remove objections | All | Grouped accordions | Contact / Start | Clean accordion | FAQ schema | **P1** |
| **Start a Project (hub)** | Brief intake | Buyer | Service picker → routed brief | Submit brief | Calm form UX | Conversion | **P0** |
| **Request a Quote** | Fast lead | Buyer | Short form | Submit / WhatsApp | Minimal | Conversion | **P0** |
| **Contact** | Direct line | All | WhatsApp, email, form, hours | WhatsApp / Email | Simple | Local/contact | **P0** |
| **Policies** | Trust/legal | Buyer | T&Cs, privacy, cookie, revision, deposit, refund, delivery | — | Readable long-form | Legal | **P0** |
| **Thank-you / 404 / Search** | UX | All | Confirmation / recovery / results | Relevant next step | On-brand | — | **P0** |

---

## 14. Homepage Plan

| # | Section | Objective | Content | Layout & hierarchy | Interaction / Motion | Mobile behavior | A11y / Perf |
|---|---|---|---|---|---|---|---|
| 1 | Availability bar (optional) | Signal capacity / offer | "Booking ‹month›" | Slim top bar | Dismissible | Collapses | No focus auto-shift |
| 2 | Header/nav | Orientation | Logo, slim nav, CTA | Logo L · nav C/R · CTA R | Sticky on scroll-up | Slide-over menu | Keyboard + focus trap |
| 3 | **Cinematic hero** | Hook + promise | H1 "From Sketch to Screen", sub, CTA | Full-bleed, large display | Sketch→design→video sequence | Poster + static | Reduced-motion static; no autoplay sound |
| 4 | Showreel | Prove range | 60–90s reel | Centered player + poster | Click-to-play (Vimeo) | Tap-to-play | Lazy, captions |
| 5 | Brand statement | Position | One editorial line | Centered, generous space | Scroll reveal | Stacked | — |
| 6 | Selected work | Craft proof | 3–6 featured projects | Asymmetric grid | Hover video preview/reveal | Swipe carousel | Alt text, focusable |
| 7 | Services overview | Route | 6 categories | Card grid | Subtle hover lift | 1-col | Semantic links |
| 8 | **Sketch-to-Screen** | Story | 3-stage transformation | Before/after stages | Scroll-driven mask | Tap stages | Reduced-motion = static set |
| 9 | Process | Lower risk | Sketch · Design · Screen | 3–5 steps | Step reveal | Vertical list | Ordered list semantics |
| 10 | Featured case study | Depth proof | One flagship + result | Big visual + stat | Parallax-lite | Stacked | — |
| 11 | Packages | Commercial | Tier cards + "from" | 3-up cards | Hover lift | Swipe/accordion | Accessible tables |
| 12 | Retainers teaser | Recurring | Monthly content banner | Full-width band | — | Stacked | — |
| 13 | Testimonials + logos | Trust | Quotes + client logos | Slider + logo row | Accessible slider (pause) | Swipe | ARIA, no auto-advance trap |
| 14 | Tools/expertise | Credibility | Final Cut Pro, etc. | Logo/skill row | — | Wrap | Alt text |
| 15 | FAQ | Objections | 6–8 Qs | Accordion | `aria-expanded` accordion | Full-width | Keyboard operable |
| 16 | Final CTA | Convert | "From sketch to screen — let's start" | Bold band | Magnetic button (desktop) | Static | Visible focus |
| 17 | Footer | Nav + contact | Nav, WhatsApp, email, social, policies | Multi-col | — | Stacked | Landmarks |

**Hero concept — building it without a slow site**

| Technique | Quality | Perf cost | Verdict [R] |
|---|---|---|---|
| Lightweight looping video | High | Med (size-bound) | Final "screen" stage, ≤2 MB WebM/MP4 |
| Image sequence | High | High (many requests) | Avoid |
| CSS masks | M-H | Low | Sketch→design reveal |
| **SVG line animation** | High | **Low** | Sketch stroke draw |
| **Lottie** | High | L-M | Sketch→design morph |
| Canvas / WebGL | Very high | High | **[F]** only |
| Pre-rendered showreel | High | Med | Full reel = click-to-play |

**Recommended launch hero (hybrid):** SVG/Lottie sketch line-draws → CSS-mask reveal to a refined design frame → cut to a short muted **≤2 MB** loop that reads as "screen" → wordmark resolves → **Start a Project** CTA. Full showreel = click-to-play (Vimeo).
- **Desktop:** full sequence · **Tablet:** sequence, lighter video · **Mobile:** poster + wordmark + CTA (sequence on tap or skipped) · **Reduced-motion:** static composed frame · **Low-bandwidth (Save-Data):** poster only.

---

## 15. Services Structure

**Primary categories (launch):** 1) Video Editing & Motion · 2) Graphic Design · 3) Sketch & Illustration · 4) Storyboarding & Creative Planning · 5) Branding · 6) Creative Direction / Custom Projects.

**Add-ons:** rush delivery · extra revision · extra format/aspect ratio · extra length · commercial/print license · source files · licensed music · subtitles/voiceover.
**Quote-only:** branding systems · custom illustration · retainers · agency white-label.
**Package-based:** social/reels/YouTube editing · social design · sketch commission · storyboard.
**Retainer:** monthly content · agency support.

**Per-service detail** (each independent; pricing model and CTA noted):

**1. Video Editing & Motion** — *Scroll-stopping edits: reels, YouTube, promos, commercials.* Ideal: P1/P2. Problem: inconsistent, slow, generic video. Deliverables: edited master + platform cuts, captions, color, audio cleanup, titles. Inputs: raw footage (link), script/brief, brand assets, references. Workflow: brief → assembly → draft → revisions → delivery. Turnaround: tiered `‹PLACEHOLDER business days›`. Revisions: 2 rounds. Add-ons: rush, extra cuts, subtitles, motion titles. Pricing: **packages "from" + quote for custom**. CTA: Request a Quote. Evidence: before/after, reel cuts. Related/upsell: thumbnails, branding, retainer.

**2. Graphic Design** — *Campaign-grade visuals, social, thumbnails, print.* Ideal: P2/P1. Deliverables: source + export formats, sized variants. Inputs: brand assets, copy, references, dimensions. Turnaround tiered. Revisions: 2. Add-ons: extra sizes, print-ready, source files. Pricing: packages "from" + quote. CTA: Start a Project. Evidence: campaign galleries. Upsell: branding, video.

**3. Sketch & Illustration** — *Custom sketches, portraits, product/concept art, sketch-to-digital.* Ideal: P5/P2. Deliverables: high-res artwork + usage license. Inputs: subject, style/medium, references, usage. **Quote-first.** Revisions: 2 (concept-locked). Add-ons: extra concepts, commercial license, print. CTA: Request a Quote. Evidence: sketch gallery, before/after. Upsell: brand kit, video.

**4. Storyboarding & Creative Planning** — *Storyboards, moodboards, campaign concepts, pre-production.* Ideal: P2/P3. Deliverables: frames/boards, shot notes. Inputs: script/concept, aspect ratio, style. Quote-first/package. Revisions: 2. CTA: Request a Quote. Evidence: storyboard sets. Upsell: video production.

**5. Branding** — *Logo concepts, identity systems, guidelines, launch kits.* Ideal: P2. **Quote-first, milestone-billed.** Deliverables: logo suite, system, guidelines. Inputs: industry, competitors, values. Revisions: per milestone. CTA: Request a Quote. Evidence: identity case studies. Upsell: retainer, content.

**6. Creative Direction / Custom Projects** — *Bespoke and mixed campaigns, retainers, white-label.* Ideal: P2/P3. **Quote-only, scoped.** Deliverables: per scope. CTA: Start a Project. Evidence: flagship cases. Upsell: ongoing retainer.

---

## 16. Packages and Pricing

**Display-model comparison**

| Model | Pros | Cons | Verdict |
|---|---|---|---|
| Fully visible | Transparent, filters tire-kickers | Anchors low, invites price-shopping, dates fast | No |
| Quote-only | Premium, custom | Friction, slower | Partial |
| **"Starting from" + quote** ✅ | Premium feel, sets a floor, still converts | Needs good copy | **Recommended [R]** |
| Hybrid (fixed small / quote big) | Flexible | More to maintain | Acceptable variant |

**Final pricing model [R]: "Starting from" with a quote-first CTA; 50% deposit via SureCart.** All figures `‹PLACEHOLDER — business input required›`.

| Package | Ideal | Includes | Concepts | Revisions | Turnaround | Add-ons | Deposit / milestones | CTA | Upgrade path |
|---|---|---|---|---|---|---|---|---|---|
| Social Starter | P1/P4 | X reels + Y posts | — | 1 | `‹PH›` | extra posts | 50% | Start | → Growth |
| Social Growth | P1/P2 | more volume + thumbnails | — | 2 | `‹PH›` | extra cuts | 50% | Start | → Retainer |
| Reels Creator / Pro | P1 | 4 / 8 reels | — | 1 / 2 | `‹PH›` | subtitles | 50% | Start | → Retainer |
| YouTube Creator | P1 | long-form + thumbnail | — | 2 | `‹PH›` | chapters | 50% | Start | → Retainer |
| Visual Campaign | P2 | multi-asset set | 2 | 2 | `‹PH›` | print-ready | 50% | Quote | → Branding |
| Sketch Commission | P5 | 1 custom sketch | 2 | 2 | `‹PH›` | extra concept, license | 50% | Quote | → Sketch-to-Digital |
| Sketch-to-Digital | P5/P2 | sketch→vector/illustration | 2 | 2 | `‹PH›` | commercial license | 50% | Quote | → Brand kit |
| Storyboard | P2/P3 | N frames | 1 | 2 | `‹PH›` | extra frames | 50% | Quote | → Video |
| Brand Content | P2 | templates + assets | 2 | 2 | `‹PH›` | extra templates | 50% | Quote | → Identity |
| Visual Identity | P2 | logo + system + guidelines | 3 | 2 | `‹PH›` | stationery, social kit | **milestones** | Quote | → Retainer |
| Monthly Content Retainer | P1/P2 | set monthly output | — | rolling | monthly | extra deliverables | **recurring** | Quote | scale tier |
| Agency Support | P3 | white-label block | — | per project | rolling | dedicated slot | **recurring** | Quote | dedicated |
| Custom Creative Project | all | bespoke | — | scoped | scoped | — | **milestones** | Quote | — |

**Preventing scope creep / cheap look [R]:** define **revision rounds** (never "unlimited"); list **exact deliverables + formats**; state turnaround as **business days from asset receipt**; cap included length/quantity; price every add-on; require deposit before work; premium card design (generous spacing, single accent, no fake gold). Keep **≤6 visible packages per page** to avoid choice overload.

---

## 17. Sales Workflow

| Step | Launch (where) [R] | Advanced future [F] |
|---|---|---|
| 1 Discover | Website / social | — |
| 2 Understand positioning | Home / About | — |
| 3 Explore services | Service pages | — |
| 4 View relevant work | Portfolio / case study | — |
| 5 Choose package / custom | Packages page | Configurator |
| 6 Complete brief | Fluent Forms brief | Client portal |
| 7 Upload references | Form upload / external link | Secure portal upload |
| 8 Studio reviews | Email / WhatsApp | CRM |
| 9 Confirm scope | Email / WhatsApp | Portal |
| 10 Issue quote | Email / SureCart link | Auto-quote |
| 11 Pay deposit | **SureCart** (Stripe) | Installments |
| 12 Onboarding | Email + cloud folder | Automated onboarding |
| 13–17 Work, drafts, revisions, approval | Email/WhatsApp + cloud links | Portal approvals + revision tracking |
| 18 Final payment | SureCart link | Auto-charge |
| 19 Deliver files | Cloud link | Secure delivery |
| 20 Testimonial | Testimonial form | Automated request |
| 21 Upsell related | Email | Automated |
| 22 Offer retainer | Email/WhatsApp | SureCart subscription |

**Three workflow tiers:** **Simple launch** = forms + WhatsApp + one SureCart deposit link + email. **Recommended launch** = the table above. **Advanced future** = client portal with approvals, secure delivery, subscriptions, CRM/email automation. Launch stays deliberately manageable for a small studio.

---

## 18. Creative Brief Forms

**Architecture [R]:** one **"Start a Project" hub** → service picker → routes to the matching multi-step brief via Fluent Forms **conditional logic** (single smart form preferred for maintenance; dedicated forms acceptable). Save-and-resume via partial entries.

**Shared steps (all briefs):**
1. **Contact** — name, business, email, phone/WhatsApp, country, preferred language, preferred contact method.
2. **Project basics** — service type, package (if any), project title, objective, business objective, target audience, platform.
3. **Specs** — *service-conditional (below)*.
4. **Assets** — brand assets, references, competitors, file upload **or** external link.
5. **Logistics** — deadline (fixed/flexible), budget range, required formats, number of versions, commercial/print use.
6. **Consent** — terms acceptance, portfolio-use permission, privacy consent.

**Service-conditional fields**

| Brief | Required | Optional |
|---|---|---|
| **Video / Reels / YouTube** | service, platform, video duration, # videos, aspect ratio, raw-footage link, deadline | script, music (licensed/provided), voiceover, subtitles + language, motion/titles, references |
| **Graphic / Social** | # designs, dimensions, print vs digital, deadline | brand assets, style/mood, copy provided, references |
| **Sketch / Illustration** | subject, style/medium, B/W vs color, usage (personal/commercial/print), deadline | reference photos, size, # concepts |
| **Storyboard** | # frames/scenes, script/concept, aspect ratio | style (rough/clean), references |
| **Branding** | scope (logo/system/guidelines), industry, values/keywords | competitors, existing assets, references |
| **Custom** | objective, scope description, budget range | references, timeline notes |

**Per-form rules [R]:** client-side + server validation; whitelist file types (jpg/png/pdf/mp4/zip); size cap ~25–50 MB else external link required; confirmation message + dedicated thank-you page; **admin email** (full brief) + **client email** (acknowledgement, expected reply window); **Cloudflare Turnstile + honeypot** spam protection; data-retention policy stated; accessible labels/instructions/inline error recovery. **Testimonial form** separate: name, role/business, quote, rating, permission to publish, optional photo/logo.

---

## 19. Portfolio and Case-Study System

**CPT [R]:** `project` → URL `/work/{slug}`, archive `/work/`, custom single template.
**Taxonomies:** Service · Project type · Industry · Platform · Tool · Content format · Client type · Visual style; **Featured** = field flag (not a taxonomy).
**ACF fields:** client/confidential label · title · short summary · project date · service · industry · tools used · deliverables · project goal · challenge · creative direction · **sketch** · **before** (image/video) · **after** (image/video) · main video (Vimeo) · gallery · results · testimonial · credits · external link · featured (bool) · related projects (relationship).

**Case-study structure [R]:** 1 Hero → 2 Project summary → 3 Client/context → 4 Challenge → 5 Creative direction → 6 **Sketch / initial concept** → 7 Process → 8 **Before & after** → 9 Final result → 10 Video / gallery → 11 Deliverables → 12 Tools used → 13 Outcome → 14 Testimonial → 15 Related projects → 16 CTA.

**Handling sensitive / varied work [R]:**
- **Confidential / NDA:** "Confidential client" label, no name/logo, representative or blurred visuals, written permission before publishing.
- **Unnamed client:** describe sector + outcome only.
- **Personal / concept / student:** clearly tagged via Client-type taxonomy.
- **Collaborative / agency:** honest role credits.
- **Video-heavy:** Vimeo + poster, click-to-play. **Image-heavy:** optimized lazy-loaded gallery.
- **Governance gate:** no project goes live without a recorded client permission (§22 checklist).

---

## 20. Visual Identity Options (three genuinely distinct directions)

**Direction 1 — "Cinematic Noir"** (✅ recommended). Strategic idea: the dark canvas *is* the screen; ivory sketch strokes drawn onto it enact sketch→screen. Mood: dramatic, film-grade, premium, confident. Palette: Obsidian `#0E0F12`, Graphite `#1C1E22`, Surface `#23262B`, Ivory `#F5F2EC`, Muted `#A7A39B`, **Ember `#FF5A2C`** accent, optional warm-gold `#C9A36A` hairlines. Type: cinematic display grotesk/serif (e.g. Clash Display / Editorial New — license at build) + Inter/General Sans body; Arabic [F]: IBM Plex Sans Arabic / Tajawal. Logo: confident wordmark + SS monogram; sketch-stroke mark accent. Icons: fine line. Illustration: hand-drawn line over dark. Photography/video: high-contrast, filmic grade, grain. Buttons: ember solid CTA. Cards/forms: graphite surfaces, ember focus. Portfolio: film-frame corners. Spacing: generous, editorial. Motion: slow cinematic reveals. Cursor: minimal dot + magnetic on CTA. Loading: pencil-to-screen morph. Social: dark, ember accent, sketch motif.

**Direction 2 — "Blueprint to Render."** Strategic idea: the designer's desk — pencil/blueprint becoming a rendered frame. Mood: technical-artistic, precise. Palette: Ink Blue `#16243B`, Paper `#F2EEE4`, Graphite, **Signal Yellow `#F2C14E`** accent. Type: technical serif + mono-influenced sans. Texture: blueprint grid + pencil lines. Motion: line-draw / plotting. Strength: ownable "process" story; weakness: lighter/less cinematic; risk of looking "engineering" not "premium creative."

**Direction 3 — "Gallery Editorial."** Strategic idea: white-cube gallery; the work is the hero. Mood: airy, magazine, calm-premium. Palette: Gallery White `#FAFAF8`, Ink `#111111`, one bold accent (Cobalt `#2B59FF`). Type: high-contrast serif + neutral grotesk. Texture: whitespace, hairlines. Motion: crisp, minimal. Strength: lets portfolio shine, very accessible; weakness: less cinematic, more common in premium space.

**Comparison**

| Criterion | 1 Noir ✅ | 2 Blueprint | 3 Gallery |
|---|---|---|---|
| Cinematic | **High** | Med | Med |
| "Sketch to Screen" fit | **High** | High | Med |
| Differentiation | High | High | Med |
| Media pop | **High** | Med | High |
| Accessibility ease | Med (dark needs care) | High | **High** |
| Premium perception | **High** | Med-H | High |

---

## 21. Recommended Visual Direction

**Cinematic Noir, with the sketch-line motif from Direction 2.** **Why it wins "From Sketch to Screen":** a dark, film-grade canvas reads as the *screen* instantly, and ivory **sketch strokes** drawn onto that frame literally perform sketch→screen in the interface — underlines, dividers, the hero, the loading animation, and case-study transitions all reuse one coherent gesture. It differentiates sharply from bright freelancer sites and makes video/portfolio media pop. Accessibility risk (dark theme contrast) is explicitly managed in §31.

**Final tokens:** bg Obsidian `#0E0F12`, surface Graphite `#1C1E22`/`#23262B`, text Ivory `#F5F2EC`, muted `#A7A39B`, **accent Ember `#FF5A2C`**, success `#3FB984`, warning `#E9A23B`, error `#E5484D`, hairline gold `#C9A36A`. Display = cinematic grotesk/serif; body = Inter/General Sans. Signature: animated sketch-stroke accents, subtle grain, ember focus rings, film-frame corners on featured media.

---

## 22. Logo Strategy

| Direction | Originality | Readability | Scalability | Avatar | Video watermark | Final Cut export | Favicon | Verdict |
|---|---|---|---|---|---|---|---|---|
| **Wordmark "Shemo Studio"** | Med | High | High | weak | ok | ok | weak | **Primary [R]** |
| **SS monogram** | High | High | High | ✅ | **✅** | **✅** | **✅** | **Secondary [R]** |
| Sketch-inspired mark | High | Med | Med | ok | ok | ok | med | Accent element |
| Frame-inspired mark | Med | High | High | ok | ok | ok | ok | Optional |
| Editing-timeline mark | Med | Med | High | ok | ok | ok | med | Too niche alone |
| Pencil-to-screen morph | High | Low (static) | Med | — | — | poor | poor | **Loading animation only [R]** |
| Signature mark | High | Low | Low | — | — | poor | poor | Avoid as primary |
| Abstract motion mark | High | Low | Med | ok | ok | ok | med | **[F]** |

**Recommendation [R]:** Primary = **clean wordmark**; Secondary = **SS monogram** (avatar, watermark, Final Cut export, favicon, embroidery/print); pencil-to-screen morph lives as the **loading/animation** expression. Provide **light + dark** versions; safe-area = cap-height padding; minimum sizes (wordmark ≥120px web / monogram ≥24px); favicon from monogram. **Logo not finalized here — direction only.**

---

## 23. Web Design System

Built on the **three-layer token model** (primitive → semantic → component), expressed as CSS custom properties and mirrored into Bricks global colors/classes.

**Colors (semantic tokens):** `--bg` (obsidian), `--surface`, `--surface-2`, `--text` (ivory), `--text-muted`, `--accent` (ember), `--border`, `--success/--warning/--error`, `--focus` (ember). Component tokens example: `--btn-bg: var(--accent)`, `--card-bg: var(--surface)`.

**Typography:** fluid scale via `clamp()` — Display-1 (hero) → H1–H4 → Body-L/M/S → Caption/Label/Button. Line-heights 1.05 (display) → 1.6 (body); tightened display tracking. Mobile drops one step. Arabic [F]: line-height +0.1, font-feature tuning.

**Layout:** 12-col grid; container max ~1280–1440px; reading max ~720–800px; full-bleed media sections. **Breakpoints:** ≥1280 desktop · 768–1279 tablet · <768 mobile. **Spacing scale** (4/8 base): 4,8,12,16,24,32,48,64,96,128; section padding generous desktop / compressed mobile; honor safe-area insets.

**Components:** header · slide-over mobile nav · buttons (primary ember / secondary outline / ghost / link) · text links · cards (service/package/portfolio) · case-study blocks · testimonials · FAQ accordion · forms (input/select/upload/checkbox) · notices · video player · gallery · **before/after slider** · modal/lightbox · footer.

**Component spec pattern (buttons):**

| Property | Default | Hover | Active | Focus | Disabled |
|---|---|---|---|---|---|
| Background | accent | accent-dark | accent-darker | accent | muted |
| Text | obsidian | obsidian | obsidian | obsidian | muted-fg |
| Outline | none | none | none | **2px ember ring** | none |

**States:** default · hover · active · **focus (visible ember ring, never removed)** · disabled · loading (skeleton) · success · error · empty · reduced-motion.
**Tokens:** radius 4/8/16 · subtle shadows on dark · image ratios 16:9 / 4:5 / 1:1 · video 16:9 / 9:16 · consistent line-icon set · **touch targets ≥44px** · focus indicators always present.

---

## 24. Animation and Interaction Strategy

| Interaction | Location | Purpose / value | Desktop | Mobile | Reduced-motion | Perf risk | Priority |
|---|---|---|---|---|---|---|---|
| Hero sketch→screen | Home hero | Brand promise | Full sequence | Poster/static | Static frame | Med | **Launch** |
| Scroll text reveals | Sections | Polish | Yes | Light | Off | Low | Launch |
| Image / mask reveals | Work, sketch-to-screen | Storytelling | Yes | Light | Off | Low | Launch |
| Hover video preview | Work cards | Engagement | Yes | Tap | Off | Med | Launch |
| Before/after slider | Case study / home | Proof | Drag | Drag | Static pair | Low | Launch |
| Accessible accordion/slider | FAQ / testimonials | UX | Yes | Yes | Instant | Low | Launch |
| Magnetic button | Final CTA | Delight | Yes | Off | Off | Low | Optional |
| Custom cursor | Global | Signature | Yes | Off | Off | Low | Optional |
| Loading animation | First load | Brand | Yes | Light | Static logo | Low | Optional |
| Horizontal portfolio | Work | Showcase | Maybe | Off | Off | Med | **Postpone** |
| Parallax (heavy) | — | — | — | — | — | High | **Avoid** |
| Full page transitions | Global | Premium | — | — | — | M-H | **Postpone** |

**Launch motion set [R]:** hero sequence + scroll/mask reveals + hover previews + before/after + accessible accordions/sliders. **Engine:** GSAP (hero/reveals) + Lottie (sketch morph) + Bricks native interactions. **Every effect honors `prefers-reduced-motion`; no autoplay with sound.** **Avoid:** heavy parallax, WebGL (→ [F]), animation without purpose.

---

## 25. Mobile-First UX

Mobile is **designed, not shrunk.** **Header:** logo + menu + sticky CTA. **Hero:** poster + wordmark + CTA (sequence on tap). **Showreel/video:** click-to-play, 9:16 where natural. **Portfolio:** swipeable cards. **Packages:** stacked with horizontal-compare or accordion. **Before/after:** touch-drag. **Forms:** single-column, large touch targets, OS file picker + external-link fallback. **Persistent CTA [R]: yes** — bottom bar (Start a Project + WhatsApp) that auto-hides on scroll-down, returns on scroll-up, never traps focus. **WhatsApp + contact shortcuts** prominent. **Typography** one step down; **spacing** generous; **image/video ratios** fixed to prevent CLS. **Reduced-data / low-end:** poster-only hero, deferred non-critical JS, smaller images. **Mobile PageSpeed ≥75 and mobile a11y are launch gates.**

---

## 26. Language and Localization Strategy

| Option | Market fit | Trust | SEO | Maintenance | RTL | Verdict |
|---|---|---|---|---|---|---|
| English only | Broad/intl | High | Good | Low | n/a | Launch baseline |
| Arabic only | Local | High local | Local | Low | Yes | Limits reach |
| AR + EN at launch | Both | High | Double | **High** | Yes | Too heavy for launch |
| **EN first, AR Phase 2** ✅ | Both over time | High | Phased | Phased | Ready | **Recommended [R]** |

**Decision [R]: English-first launch; Arabic in Phase 2** once content/portfolio are stable. Architecturally Arabic-ready now (no hard-coded strings, logical heading order, token-based layout). **Phase 2 setup [F]:** default EN, URL `/ar/`, **Polylang Pro** (**[C]** lightweight "language-as-taxonomy", <5% overhead, auto `rtl.css`; WPML is the heavier alternative with integrated WooCommerce). **Verify [R]:** Polylang↔**Bricks** compatibility at Phase 2 (Polylang has a documented Elementor gap — confirm Bricks before committing; WPML or TranslatePress are fallbacks). Translate pages/portfolio/forms/emails; add `hreflang`; assign a content-governance owner for parity; full RTL stylesheet. Do **not** ship bilingual merely because it is possible — the content workload is real.

---

## 27. Content Requirements

| Item | Format | Recommended dims/notes | Owner | Status | Priority | Phase needed |
|---|---|---|---|---|---|---|
| Brand story / bio | Text | — | Founder | ☐ | High | Before design |
| Founder + studio photos | Image | ≥2000px, retina | Founder | ☐ | High | Before design |
| Showreel | Video | Vimeo, ≤90s, 16:9 + 9:16 | Founder | ☐ | High | Before dev |
| Video portfolio (6–10) | Video | Vimeo + poster | Founder | ☐ | High | Before dev |
| Graphic portfolio | Image | optimized, fixed ratios | Founder | ☐ | High | Before dev |
| Sketches / illustrations | Image | high-res scans | Founder | ☐ | High | Before dev |
| Storyboards | Image/PDF | — | Founder | ☐ | Med | Before launch |
| Before/after sets | Image/Video | paired | Founder | ☐ | High | Before dev |
| Case-study data ×6–10 | Text+media | §19 fields | Founder | ☐ | High | Before dev |
| Testimonials (3–6) | Text(+photo) | with permission | Founder | ☐ | High | Before launch |
| Client logos | SVG/PNG | with permission | Founder | ☐ | Med | Before launch |
| Tools/expertise | List/logos | Final Cut Pro etc. | Founder | ☐ | Med | Before launch |
| Service + package copy | Text | — | Founder/Copy | ☐ | High | Before dev |
| Process + FAQ | Text | — | Founder | ☐ | Med | Before launch |
| Policies | Text | legal-reviewed | Founder/Lawyer | ☐ | High | Before launch |
| Contact + WhatsApp + socials | Data | — | Founder | ☐ | High | Before dev |
| Brand assets / logo / fonts | Files | licensed | Founder | ☐ | High | Before design |
| Video poster images | Image | per video | Founder | ☐ | High | Before dev |
| **Client permissions** | Signed | per project | Founder | ☐ | **Blocker** | Before launch |

**Buckets:** *Before design* (brand, photos, assets) · *Before development* (services/packages copy, portfolio media + case data) · *Before launch* (testimonials, logos, policies, permissions) · *After launch* (journal, more cases).

---

## 28. Copywriting Strategy

**Final tone [R]:** editorial, confident, specific, calm, human; short sentences; show > tell; no clichés, superlatives, or unrealistic promises.

**Options + final picks**

| Slot | Option A (✅ final) | Option B | Option C |
|---|---|---|---|
| Hero H1 | **"From Sketch to Screen."** | "Ideas, drawn and directed." | "Where ideas get made." |
| Hero sub | **"A boutique creative studio for brands and creators — concept, design, and editing crafted under one cinematic eye."** | "We sketch the idea, design the look, and edit it into something worth watching." | "Video, design, and illustration with a cinematic eye." |
| Primary CTA | **Start a Project** | Begin a Brief | Work With Us |
| Secondary CTA | **View the Work** | See the Reel | Explore Projects |
| About intro | **"Shemo Studio began with a pencil. Today it's where ideas are sketched, designed, and edited into work that moves."** | "A studio built on craft and a cinematic eye." | — |
| Services intro | **"Six ways we take ideas from sketch to screen."** | "Pick a service, or tell us about something bigger." | — |
| Portfolio intro | **"Selected work — from first line to final cut."** | "Proof, not promises." | — |
| Sketch-to-screen | **"Every project starts with a line. We sketch the idea, design the look, and edit it into something worth watching."** | — | — |
| Process | **"Sketch. Design. Screen. A simple path to work that moves."** | — | — |
| Package intro | **"Clear scopes, premium craft. Start from a package or tell us about something bigger."** | — | — |
| Quote CTA | **"Tell us about your project — we'll send a tailored quote."** | — | — |
| Final homepage CTA | **"Have something to make? Let's take it from sketch to screen."** | — | — |
| Contact intro | **"Fastest on WhatsApp. Tell us what you're making."** | — | — |
| Thank-you | **"Got it — your brief is in. We'll review and reply, usually within 1–2 business days."** | — | — |
| 404 | **"This frame didn't render. Let's get you back to the work."** | — | — |

**Final messaging direction [R]:** lead with "From Sketch to Screen" as both promise and structural spine; everything supports the three-beat sketch→design→screen narrative.

---

## 29. SEO Strategy

- **Search intent:** transactional ("hire video editor for reels", "{service} for creators/brands"), navigational (brand), informational (journal [F]).
- **Keyword themes:** core service ("video editing", "reels editor", "thumbnail design", "custom illustration", "storyboard artist", "brand visuals"); audience-modified long-tail; optional local ("creative studio {city}").
- **Page structure:** service pages problem→deliverables→evidence→CTA; case studies with descriptive slugs.
- **Image SEO:** named files, alt text, captions, dimensions. **Video SEO:** VideoObject schema, transcripts where useful, Vimeo embeds.
- **Technical (Rank Math):** titles/meta/canonicals, XML sitemap, robots, breadcrumbs, OG/social previews; 404 monitor + redirects; internal linking service↔case↔package.
- **Schema [R]:** Organization/**ProfessionalService**, **Service**, Article (journal), VideoObject, FAQ. **[C]** Rank Math free covers all these.
- **Core Web Vitals** treated as SEO (§30). **Multilingual SEO [F]:** `hreflang` in Phase 2.
- **Journal — conditional [R]:** recommend **only** with a realistic cadence (1–2 posts/month). Topics: "How we take a reel from sketch to screen" · "What makes a thumbnail get clicked" · "Briefing a video editor: a simple checklist" · "Storyboarding a campaign" · "Sketch vs AI: why hand-drawn still wins" · "Choosing aspect ratios for every platform." **If cadence isn't realistic, defer the blog.**

---

## 30. Performance Strategy

- **Video [R]:** **Vimeo** (clean player, no recommendations), poster images, **click-to-play**, no autoplay-with-sound; mobile = poster-first. Self-hosted hero loop only if ≤2 MB WebM/MP4, else Vimeo.
- **Images:** WebP/AVIF via ShortPixel/Imagify, correct dimensions, responsive `srcset`, lazy-load below fold, fixed ratios (no CLS).
- **Fonts:** self-host, subset, `font-display: swap`, preload 1–2 display weights only.
- **CSS/JS:** Bricks outputs lean CSS; defer/conditionally enqueue GSAP/Lottie (hero pages only); minimize third-party scripts; **single GTM** container; pixels load **post-consent**.
- **Caching/CDN/object cache [A4 — hosting-dependent]:** configure after provider chosen (likely WP Rocket + Cloudflare/Bunny + Redis). Not on LocalWP.
- **DB/DOM:** shallow DOM, periodic DB cleanup, quarterly plugin audit.

**Targets** (field/mobile unless noted):

| Metric | **Hard requirement (launch gate)** | Aspirational |
|---|---|---|
| LCP | ≤ 2.5s | ≤ 2.0s |
| INP | ≤ 200ms | ≤ 150ms |
| CLS | ≤ 0.1 | ≤ 0.05 |
| FCP | ≤ 1.8s | ≤ 1.2s |
| TTFB | ≤ 0.8s (host-bound) | ≤ 0.5s |
| Mobile PageSpeed | ≥ 75 | ≥ 90 |
| Desktop PageSpeed | ≥ 90 | ≥ 98 |
| Page weight (typical) | ≤ 2.5 MB | ≤ 1.5 MB |
| Hero media | ≤ 2 MB | ≤ 1 MB |
| Portfolio image | ≤ 300 KB | ≤ 150 KB |

**Hard requirements:** LCP/INP/CLS + mobile PageSpeed ≥75. The rest are aspirational.

---

## 31. Accessibility Strategy (WCAG 2.2)

- **Contrast:** ivory-on-obsidian and ember interactions verified ≥4.5:1 body / ≥3:1 large + UI; never color alone (dark theme audited explicitly).
- **Structure:** semantic landmarks, single H1, logical heading order, skip link.
- **Keyboard:** full operability; visible ember focus rings (never removed); focus trapped+restored in modals/menus; `:focus-visible`.
- **Media:** meaningful-image alt text, empty alt for decorative, **captions + transcripts** for video, audio controls, **no autoplay with sound**.
- **Motion:** `prefers-reduced-motion` everywhere (§24).
- **Forms:** programmatic labels, instructions, inline error identification + recovery, accessible file upload, ≥44px targets.
- **Components:** ARIA-correct accordions, sliders (pause control), galleries, lightboxes.
- **Language / RTL [F]:** `lang` + `dir` attributes in Phase 2.
- **WCAG 2.2 specifics:** target size (min), focus appearance, dragging alternatives (before/after slider also operable by buttons/keys), consistent help, accessible authentication (no cognitive-test CAPTCHA → Turnstile).
- **QA procedure [R]:** automated (axe + Lighthouse) **+ manual keyboard pass + screen-reader spot check (NVDA/VoiceOver)** on Home, one Service, one Case study, and each form before launch. Audit Bricks markup for div-soup.

---

## 32. Security and Privacy

**Security [R]:** least-privilege roles; single named admin (no `admin`); strong unique passwords; **2FA**; login-attempt limiting + login rename; Wordfence scanning; timely core/theme/plugin updates **with pre-update backup**; Fluent Forms nonce/CSRF + Turnstile + honeypot; **file-upload MIME/type/size validation**; Safe SVG sanitization; uploads not publicly browsable; security headers (CSP/HSTS/X-Frame via server/Cloudflare); SSL everywhere; **payments handled by SureCart/Stripe — no card data on-site**; activity logging if justified; documented incident-response + restore.

**Privacy [R]:** store client briefs/uploads minimally; **retention policy** auto-purges old form entries/files; client files never public; consent-based analytics/pixels (Complianz + Consent Mode v2); cookie disclosure; honor data-access/deletion requests; secure transactional email (SMTP post-hosting); secrets/API keys in env, never committed.

---

## 33. Legal and Commercial Policies

| Policy | Must clarify |
|---|---|
| Terms & Conditions | Scope, process, IP, liability, governing law |
| Privacy | Data collected, use, retention, rights |
| Cookie | Cookies used, consent, opt-out |
| Revision | Included rounds, what counts, extra-revision cost |
| Cancellation | When/how, effect on deposit |
| Refund | Deposit conditions (typically non-refundable post-work start) |
| Deposit | % required, timing, what it secures |
| Payment | Methods (Stripe), schedule, milestones |
| Late-payment | Fees, work pause |
| Delivery | Formats, timelines, channels |
| Rush-delivery | Surcharge, availability |
| File-retention / source-file | How long files kept, source-file fees |
| Commercial-use / copyright / license transfer | Ownership transfers on final payment; usage rights |
| Portfolio-use permission | Right to showcase unless NDA |
| Third-party / music / stock licensing | Who licenses, responsibility |
| Client-supplied content & responsibility | Client warrants rights, timely feedback |
| Late feedback / project inactivity | Timeline impact, hold rules |
| Scope changes | Change-request + re-quote |
| Final approval / archiving / confidentiality | Sign-off, archival window, NDA handling |

**Required:** Terms, Privacy, Cookie, Revision, Cancellation, Refund, Deposit, Payment, Delivery, Commercial-use/Copyright, Portfolio-use, Confidentiality. **All final legal text must be reviewed by a qualified lawyer in the relevant jurisdiction.** **[A/Disclaimer]**

---

## 34. Hosting Requirements

| Criterion | Minimum | **Recommended [R]** | Advanced [F] | Red flags |
|---|---|---|---|---|
| Type | Quality shared/VPS | **Managed WordPress** | Managed + autoscale | Overcrowded shared |
| PHP | 8.2 | **8.3** | latest supported | EOL PHP only |
| Memory | 256 MB | 512 MB | 1 GB+ | hard 128 MB cap |
| Storage | 10 GB SSD | 20 GB+ NVMe | scalable | tiny quota |
| Bandwidth | adequate | generous | CDN-fronted | metered/throttled |
| SSL | Free/LE | Free/LE | + WAF | paid-only SSL |
| Staging | — | **Yes** | Git deploy | no staging |
| Backups/restore | Daily | Daily + on-demand + 1-click restore | Hourly | opaque backups |
| Object cache | — | **Redis** | Redis | none available |
| Email | SMTP-capable | Transactional SMTP | Dedicated | poor deliverability |
| Cron | Real cron | Real cron | Real cron | WP-pseudo-cron only |
| Server location | Near audience | Near audience + CDN | Multi-region | far/unknown region |
| Support / uptime | 99.9% | 99.95%, 24/7 | SLA | no support |
| Upload limits | ≥64 MB | ≥256 MB | configurable | tiny upload cap |

**Pick a provider only when there's a project-specific reason** (budget, region, support). Separate video hosting (Vimeo) from web hosting regardless.

---

## 35. Deployment and Launch Plan

1 Pre-migration audit → 2 Full local backup → 3 DB backup → 4 Media backup → 5 Plugin/license audit → 6 Staging setup → 7 Hosting env (PHP 8.3/memory/SSL) → 8 Domain → 9 DNS prep (low TTL) → 10 SSL → 11 File migration (**Duplicator**) → 12 DB migration → 13 URL search-replace (serialized-safe) → 14 Verify serialized data → 15 Media verification → 16 Permalinks flush → 17 Form tests → 18 Email/SMTP tests → 19 Upload tests → 20 SureCart checkout test → 21 Payment (Stripe test→live) → 22 Mobile → 23 Browsers → 24 Performance + CWV → 25 A11y → 26 Security → 27 SEO settings (de-index OFF) → 28 Analytics → 29 Search Console → 30 Sitemap submit → 31 Indexing on → 32 Redirects → 33 Cookie consent live → 34 Legal pages live → 35 Final backup → 36 Launch (DNS cutover) → 37 Post-launch checks → 38 Monitoring (uptime/CWV/forms) → 39 Rollback ready.

**Launch-day owners:** founder (content, payments, legal sign-off); developer (technical cutover + tests). **Rollback triggers:** broken checkout/forms, payment failure, major layout break, staging indexed, SSL failure → restore last backup, revert DNS.

---

## 36. Quality Assurance

**Domains:** brand consistency · layout · typography · color · spacing · responsive (mobile/tablet/desktop) · nav · footer · content · grammar · links · buttons · forms + validation + emails + uploads + spam · packages · quotes · checkout · payments · portfolio · case studies · galleries · videos · poster images · before/after · browser compat · device compat · RTL [Phase 2] · performance + CWV · SEO/metadata/schema/sitemap · a11y (keyboard + screen reader) · security · backups · analytics + conversion tracking · cookies · legal pages · 404 · thank-you pages.

**Severity classes:** **Launch blocker** (checkout/forms/security/CWV gate/broken core page) · **High** (visual breakage, a11y on key flows) · **Medium** (minor responsive/content) · **Low** (polish) · **Future enhancement**. Run `review` + `security-review` skills at this gate.

---

## 37. Project Phases and Approval Gates

| Phase | Goal | Key deliverable | Dependency | Approval gate | Main risk |
|---|---|---|---|---|---|
| 0 Discovery | Align scope | Signed brief | — | Scope sign-off | Scope creep |
| 1 Brand strategy | Positioning | Brand direction | 0 | Brand sign-off | Indecision |
| 2 Content collection | Gather assets | Content + permissions | 1 | Content ready | Thin portfolio |
| 3 IA | Structure | Sitemap | 1 | IA sign-off | — |
| 4 Journeys | Flows | Journey maps | 3 | — | — |
| 5 Wireframes | Skeleton | Wireframes | 3 | Wireframe sign-off | — |
| 6 Visual identity | Look | Design direction | 1,5 | **Visual sign-off** | Subjectivity |
| 7 WP architecture | Tech base | §9 decisions | 6 | Architecture sign-off | — |
| 8 LocalWP setup | Env | Working local + Git | 7 | — | Config |
| 9 Content modeling | CPT/fields | Projects CPT + ACF | 8 | Model sign-off | Late rework |
| 10 Design system | Tokens/components | Bricks globals + tokens | 6,8 | DS sign-off | — |
| 11 Core components | Reusables | Component library | 10 | — | — |
| 12 Core pages | Home/About/etc | Built pages | 11 | Page sign-off | — |
| 13 Portfolio system | Cases | Archive + single | 9,11 | Portfolio sign-off | — |
| 14 Service pages | Sell | Service templates | 11 | — | — |
| 15 Forms | Capture | All briefs | 11 | Form sign-off | Spam/uploads |
| 16 Selling workflow | Deposits | SureCart flow | 15 | **Payment sign-off** | Payment fail |
| 17 Content integration | Fill | Real content in | 12–16 | — | Late content |
| 18 Responsive | Mobile polish | Device pass | 17 | — | — |
| 19 Motion | Interactions | Launch motion set | 17 | — | Perf |
| 20 Accessibility | WCAG 2.2 | A11y pass | 18,19 | **A11y gate** | — |
| 21 Performance | CWV | Targets met | 18,19 | **Perf gate** | Media weight |
| 22 SEO | Visibility | SEO config | 17 | — | — |
| 23 Security | Hardening | Security pass | 16 | Security gate | — |
| 24 QA | Quality | QA report | all | **Launch readiness** | Blockers |
| 25 Migration | Go-live prep | Staging verified | 24 | Migration sign-off | Data |
| 26 Launch | Live | Live site | 25 | **Final sign-off** | DNS |
| 27 Post-launch | Optimize | Monitoring + iterations | 26 | — | Neglect |

No time estimates (per brief) — sequence, dependencies, approvals only.

---

## 38. Scope Options

| Aspect | Minimum Premium Launch | **Recommended Launch** ✅ | Advanced Future [F] |
|---|---|---|---|
| Pages | Home, About, Services, Packages, Work+Case, Contact, Start-a-Project, Policies, 404 | + Service details ×6, Process, Testimonials, FAQ, Request-a-Quote, Thank-you pages | + Journal, per-service landings, Retainers, Client Portal, Payment pages, AR tree |
| Portfolio | 4–6 projects | 6–10 full case studies | Sub-archives, deep filtering |
| Forms | Contact + 1 smart brief | Contact, Quote, hub + service briefs, testimonial | Save/resume, configurator |
| Selling | Quote-only + manual deposit link | Quote-first + **SureCart** deposits/packages | Subscriptions, installments, portal payments |
| Motion | Hero + basic reveals | Launch motion set (§24) | WebGL, page transitions |
| SEO/Analytics | Rank Math + GA4 | + schema, GSC, GTM, events | Content engine, intl SEO |
| Security/A11y | Baseline + 2FA + WCAG core | Full §31/§32 | WAF, periodic audits |
| Benefit | Fast, lean, polished | **Best quality/effort balance** | Highest commercial value |
| Complexity / Cost / Maintenance | Low | Medium | High |
| Risk | Low | Low-Med | Med-High |
| Launch suitability | OK | **Ideal** | Not for v1 |

**Recommended launch scope [R] = "Recommended Launch."** Advanced items (client portal/dashboard, project status, secure delivery, online approval, revision tracking, automated quoting, installments, retainer subscriptions, booking, CRM, automated onboarding/email, resource shop, templates, digital products, courses, membership, multi-user team, white-label portal) form the **future backlog (§41)**, not v1.

---

## 39. Risks and Mistakes Register

| Risk | Likelihood | Impact | Prevention | Detection | Response |
|---|---|---|---|---|---|
| Plugin overload/duplication | Med | High | Curated stack (§10), one-tool-per-job | Plugin audit | Remove/replace |
| Slow video / heavy hero | Med | High | Vimeo + click-to-play + poster + ≤2 MB loop | CWV/PageSpeed | Lighter media |
| Builder lock-in | Med | Med | Data in ACF/CPT, clean child theme | Migration test | Export content, rebuild shell |
| Excessive custom code / animation | Med | Med | Launch motion set only, reduced-motion | Perf budget, review | Trim |
| Weak mobile experience | Med | High | Mobile-first §25 | QA | Block launch |
| Weak portfolio / poor case studies | Med | High | Content gate §27 | Pre-launch audit | Strengthen before launch |
| Missing client permissions | Med | High | Permission checklist gate | Audit | Hold project from portfolio |
| Unclear boundaries / unlimited revisions / scope creep | High | High | Defined rounds, add-ons, policies §33 | Project tracking | Re-quote |
| Complicated checkout / too many packages | Med | Med | SureCart simplicity, ≤6 packages | Drop-off analytics | Simplify |
| Cheap-looking pricing | Med | Med | "From" + premium cards | Design review | Redesign |
| Weak policies | Med | High | Legal review §33 | Pre-launch | Add/fix |
| Inconsistent branding / too many fonts | Med | Med | Token system §23 (2 families) | QA | Enforce tokens |
| Large images / public client files | Med | High | Compression + private uploads §32 | Security/perf scan | Lock down |
| Insecure uploads | Low | High | MIME/size + Safe SVG + Turnstile | Security review | Patch |
| Poor backup/migration | Med | High | Duplicator + pre-update backups | Migration test | Restore |
| Weak analytics / no conversion tracking | Med | Med | GA4 + events at launch | QA | Add events |
| Multilingual complexity | Med | Med | Defer AR to Phase 2, verify Bricks+Polylang | — | Phased rollout |
| Oversized launch scope | Med | High | Recommended scope §38 | Phase gates | Cut to backlog |
| Difficult content management | Med | Med | ACF + clean editor templates | Editor feedback | Simplify fields |
| Dependence on unsupported plugins | Low | High | Maintained tools (verified §43) | Update logs | Replace |

---

## 40. Final Recommended Stack

| Layer | Choice | Tier |
|---|---|---|
| CMS | WordPress 7.0 [C] | Essential |
| PHP | 8.3 [C-recommended] | Essential |
| Builder | Bricks (one-off license) | Essential |
| Theme | `shemo-child` child theme | Essential |
| Custom fields | ACF Pro | Essential |
| CPT/portfolio | `shemo-core` (Projects CPT + taxonomies) | Essential |
| Dynamic content | Bricks query loops + ACF | Essential |
| Forms | Fluent Forms Pro | Essential |
| Selling / quotes / deposits | Quote-first + SureCart (Stripe) | Essential |
| SEO | Rank Math | Essential |
| Images | ShortPixel/Imagify (WebP/AVIF) | Essential |
| Video | Vimeo + click-to-play | Essential |
| SVG | Safe SVG | Essential |
| Spam | Cloudflare Turnstile | Essential |
| Security | Wordfence Free + 2FA + hardening | Essential |
| Backup/migration | Duplicator (+ UpdraftPlus post-hosting) | Essential |
| Analytics/consent | GA4 + GTM + GSC + Complianz | Essential |
| Animation | GSAP + Lottie + Bricks interactions | Essential |
| Git | child theme + `shemo-core` versioned | Essential |
| LocalWP | Nginx, PHP 8.3, SSL, Mailpit, debug on | Essential |
| Caching/CDN/object cache | WP Rocket + Cloudflare/Bunny + Redis | **Hosting-dependent** |
| Live backups | UpdraftPlus or host-native | Hosting-dependent |
| Retainers/subscriptions | SureCart Subscriptions | Future |
| Arabic | Polylang Pro + RTL (verify Bricks compat) | Future |
| Client portal | SureMembers / custom | Future |
| Shop | WooCommerce | Future (only if needed) |
| **Not recommended** | Elementor; multiple SEO/security/cache plugins; CPT-UI + Code Snippets; reCAPTCHA; slider/portfolio plugins; mega all-in-ones | — |

No duplicate-function plugins; one tool per job throughout.

---

## 41. Exact Execution Roadmap

**Order, first decision → launch:** Approve §42 → LocalWP + Git setup (§11) → install essential stack (§40) → build `shemo-core` Projects CPT + ACF groups → design system / global tokens (§23) → components → Home + core pages → portfolio archive/single → service pages → Fluent Forms briefs → SureCart deposits → integrate real content → responsive → motion → a11y → performance → SEO → security → QA → staging/migration (Duplicator) → launch → monitor/optimize.

**Embedded reference tables:** Decision table §4 · Architecture comparison §8 · Plugin table §10/§40 · Page table §13 · Services §15 · Packages §16 · Content checklist §27 · LocalWP checklist §11 · Phases §37 · QA §36 · Launch §35 · Risks §39 · Assumptions §3 · Approvals §42.

**Future-features backlog [F]:** client portal · dashboard · project status · secure file delivery · online approval · revision tracking · automated quotation · installments · retainer subscriptions · appointment booking · CRM integration · automated onboarding · email automation · resource/template shop · digital products · courses · membership · multi-user team · agency white-label portal · Arabic site.

---

## 42. Approval Decisions

| # | Decision | Recommendation | Confirm |
|---|---|---|---|
| 1 | Brand direction | Hybrid studio + visible founder | ☐ |
| 2 | Visual identity | Cinematic Noir + sketch motif | ☐ |
| 3 | Color palette | Obsidian/Graphite/Ivory + Ember `#FF5A2C` | ☐ |
| 4 | Typography | Cinematic display + Inter/General Sans | ☐ |
| 5 | Logo direction | Wordmark primary + SS monogram | ☐ |
| 6 | Language strategy | English-first, Arabic Phase 2 | ☐ |
| 7 | WordPress architecture | WP 7.0 + Bricks + child theme + `shemo-core` | ☐ |
| 8 | Builder | Bricks | ☐ |
| 9 | Theme | Custom child theme | ☐ |
| 10 | Portfolio structure | Projects CPT + ACF + taxonomies | ☐ |
| 11 | Form system | Fluent Forms Pro | ☐ |
| 12 | Selling model | Quote-first + SureCart deposits | ☐ |
| 13 | Payment strategy | 50% deposit / milestones, Stripe | ☐ |
| 14 | Pricing display | "Starting from" + quote | ☐ |
| 15 | Launch sitemap | §12 launch set | ☐ |
| 16 | Packages | §16 set (placeholder prices) | ☐ |
| 17 | Motion level | Launch motion set (§24) | ☐ |
| 18 | Launch scope | Recommended Launch (§38) | ☐ |
| 19 | Hosting criteria | Managed WP, PHP 8.3, staging, Redis, daily backups | ☐ |
| 20 | Video host | Vimeo | ☐ |
| 21 | Assumptions A1–A9 (§3) | As stated | ☐ |

---

## 43. Decision-Critical Sources

Verified during this planning pass (June 2026):

- WordPress current release & roadmap — WordPress News/Releases (wordpress.org/news), WP 7.0 schedule (make.wordpress.org), endoflife.date/wordpress
- PHP compatibility & recommended version — PHP Compatibility and WordPress Versions (make.wordpress.org handbook), Dropping PHP 7.2/7.3 (make.wordpress.org), php.net/supported-versions
- Bricks Builder pricing/licensing/features — bricksbuilder.io/pricing
- ACF Pro + CPT registration + ownership — advancedcustomfields.com/pro, Registering a CPT with ACF, WP Engine: ACF 6.1 CPT/taxonomy registration
- Fluent Forms Pro features — fluentforms.com/features, Free vs Pro
- SureCart features/pricing/processors — surecart.com/features (subscriptions, payments), wordpress.org/plugins/surecart
- LocalWP features (Nginx/PHP/Mailpit/SSL/blueprints) — localwp.com/features, Mailpit docs
- SEO plugin comparison — Rank Math vs Yoast 2026
- Multilingual/RTL — Polylang vs WPML 2026, WordPress RTL support (Jetpack)
- Standards — WCAG 2.2 (W3C Recommendation, w3.org/TR/WCAG22), Core Web Vitals (web.dev/vitals)

**Note:** Bricks↔Polylang compatibility for the Phase 2 Arabic build was **not** independently verified in this pass and is flagged for confirmation before that phase (§26).

---

*Prepared for review. No implementation, installation, or LocalWP site creation has been performed. Final legal text requires review by a qualified professional in the relevant jurisdiction.*

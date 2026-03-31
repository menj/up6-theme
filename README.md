# UP6 — WordPress Child Theme

**Version:** 2.8.0 · **Parent theme:** Twenty Twenty-Five · **Requires WP:** 6.4+ · **Requires PHP:** 8.2+ · **License:** GPLv2+

A zero-plugin editorial news child theme for the UP6 Malaysian news portal. Built on Twenty Twenty-Five with a deep blue / accent red / beige / gold design system representing the Jalur Gemilang, full Bahasa Melayu translation (583 strings), and a self-contained suite of editorial, SEO, and content-management features.

**Key characteristics:** no plugin dependencies for core functionality, classic PHP templates (not FSE block templates), full dark mode, Hijri date support (locked at publish time), structured data (JSON-LD), hidden tag filtering, post view counter (cookie-deduplicated), article voting (thumbs up/down), social share bar (10 platforms), subtitle/dek meta box, pin-to-homepage system, automated diverse editor's picks sidebar, festive occasion icons (17 SVG), optional content copy protection, clean search permalinks, built-in theme security scanner (18 malicious code patterns, SHA-256 integrity checking, email alerts), LCP-optimised hero image (`fetchpriority="high"`, `srcset`), minified CSS/JS assets (33% reduction), and a responsive mobile-first CSS patch layer.

---

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Design System](#design-system)
4. [Configuration](#configuration)
5. [Features](#features)
6. [Template Files](#template-files)
7. [Asset Structure](#asset-structure)
8. [CSS Architecture](#css-architecture)
9. [PHP Helper Functions](#php-helper-functions)
10. [Custom Post Types](#custom-post-types)
11. [Structured Data & SEO](#structured-data--seo)
12. [Translations & i18n](#translations--i18n)
13. [Development Standards](#development-standards)
14. [Adding a New Page Template](#adding-a-new-page-template)
15. [Theme Security Scanner](#theme-security-scanner)
16. [Versioning](#versioning)
17. [Changelog](#changelog)
18. [Upgrading](#upgrading)

---

## Requirements

| Dependency | Minimum | Notes |
|---|---|---|
| WordPress | 6.4 | Tested up to 6.9.3 |
| PHP | 8.2 | Uses typed properties, union types, `str_contains()`, arrow functions |
| Parent theme | Twenty Twenty-Five | Must be installed (does not need to be active) |
| PHP `calendar` extension | — | Required for `gregoriantojd()` in Hijri conversion; enabled by default on all standard hosts |
| MySQL / MariaDB | 5.7+ / 10.3+ | Standard WordPress requirement |

No plugins are required. Optional integrations:

| Plugin | Integration |
|---|---|
| Contact Form 7 | Contact page template renders CF7 form via shortcode if form ID is configured in Theme Options |
| Cipher Gate | Hidden tag filtering defers to the plugin when `CG_VERSION` constant is detected |
| Yoast / RankMath | Canonical URL and `noindex` directives are skipped automatically when either plugin is detected. Open Graph and JSON-LD are always output by the theme — disable duplicate output in the SEO plugin's settings |

---

## Installation

1. Ensure **Twenty Twenty-Five** is installed (Appearance → Themes → search "Twenty Twenty-Five").
2. Upload `up6-2.8.0.zip` via **Appearance → Themes → Add New → Upload Theme**.
3. Activate **UP6**.
4. Set site language: **Settings → General → Site Language → Bahasa Melayu** (for ms_MY translation).
5. Configure: **Appearance → Theme Options** (tabbed admin page) and **Appearance → Customize** (site identity, footer, social).

### WP-CLI installation

```bash
wp theme install twentytwentyfive --activate
wp theme install up6-2.8.0.zip --activate
wp option update WPLANG ms_MY
```

---

## Design System

### Colour Tokens

Defined as CSS custom properties in `:root` (`style.css` lines 21–37):

| Token | Variable | Value | Usage |
|---|---|---|---|
| Deep Blue | `--up6-deep` | `#1B3C53` | Primary text, headers, nav backgrounds |
| Mid Blue | `--up6-mid` | `#2E5871` | Secondary text, hover states |
| Dark Blue | `--up6-dark` | `#162f42` | Nav row background |
| Accent Red | `--up6-red` | `#C0392B` | CTAs, badges, active states, section dots |
| Red Dark | `--up6-red-dk` | `#a8302a` | Hover state for red elements |
| Red Light | `--up6-red-lt` | `#d4564a` | "SUARA SEMASA" subtitle in header and footer |
| Gold | `--up6-gold` | `#D4A017` | Jalur Gemilang yellow: Hijri crescent icon, ornamental diamond, footer stripe |
| Gold Dark | `--up6-gold-dk` | `#E8C84A` | Dark mode variant of gold |
| Beige | `--up6-beige` | `#C4B5A5` | Accent numerals, avatar backgrounds |
| Background | `--up6-bg` | `#F4F2EF` | Page background |
| White | `--up6-white` | `#ffffff` | Cards, inputs |
| Border | `--up6-border` | `rgba(79,111,134,.12)` | Dividers, card borders |
| Shadow | `--up6-shadow` | `rgba(27,60,83,.07)` | Card box-shadows |

### Dark Mode Overrides

When `html.up6-dark` is active, the tokens are remapped:

| Token | Dark value |
|---|---|
| `--up6-bg` | `#0f1923` |
| `--up6-deep` | `#c8dae6` |
| `--up6-mid` | `#90b4c8` |
| `--up6-border` | `rgba(255,255,255,.08)` |
| `--up6-shadow` | `rgba(0,0,0,.25)` |
| `--up6-red-lt` | `#e05a4e` |
| `--up6-gold-dk` | `#E8C84A` |

100 dark mode selectors cover all components.

### Typography

| Role | Family | Weight | Source |
|---|---|---|---|
| Headings, UI, nav | DM Sans | 900, 700 | Google Fonts |
| Body text, excerpts | Source Serif 4 | 400, 600, 700 | Google Fonts |

CSS variables: `--up6-sans` and `--up6-serif`.

### Layout

| Variable | Value | Purpose |
|---|---|---|
| `--up6-max` | `75rem` (1200px) | Maximum content width |
| `--up6-pad` | `1.5rem` | Horizontal page padding (reduces to 1.25rem at ≤540px, 1rem at ≤480px) |

### Custom Image Sizes

| Name | Dimensions | Crop | Usage |
|---|---|---|---|
| `ss-card` | 640 × 360 | Hard | Article card thumbnails |
| `ss-hero` | 1200 × 675 | Hard | Homepage hero, RSS feed, OG image |
| `ss-single` | 1200 × 560 | Hard | Single post featured image |

---

## Configuration

### Site Identity

**Appearance → Customize → Site Identity**

- Site title and tagline: read from **Settings → General** (native WordPress).
- Custom logo: upload to replace the red circle SVG icon. The text title always remains visible beside it.
- Logo dimensions: `height: 2.25rem`, `max-width: 3.5rem`, `flex-height: true`, `flex-width: true`.

### Footer Identity

**Appearance → Theme Options → Footer tab**

| Field | Sanitiser | Notes |
|---|---|---|
| Footer Tagline / Description | `wp_kses_post` | Shown below brand name in footer. Supports HTML (`<a>`, `<strong>`, `<br>`) |
| Contact Address | `sanitize_textarea_field` | Multiline; silently omitted if empty |
| Contact Phone | `sanitize_text_field` | Renders as `tel:` link in footer |
| Contact Email | `sanitize_email` | ROT13-obfuscated in HTML source; decoded by inline JS |
| Copyright Line | `wp_kses_post` | Supports HTML — `<a>`, `<strong>`, `<em>` |
| Legal / Ownership Notice | `wp_kses_post` | Shown in the dark band below the footer bar. Leave blank to hide |

### Social Media

**Appearance → Theme Options → Social Media tab**

All six social URL fields use `esc_url_raw` sanitisation. Icons render in the footer only when at least one URL is non-empty.

| Field | Key |
|---|---|
| Facebook URL | `ss_social_facebook` |
| X (Twitter) URL | `ss_social_x` |
| Instagram URL | `ss_social_instagram` |
| Threads URL | `ss_social_threads` |
| Telegram URL | `ss_social_telegram` |
| WhatsApp URL | `ss_social_whatsapp` |

### Navigation Menus

**Appearance → Menus**

| Location | ID | Purpose | Fallback |
|---|---|---|---|
| Primary Navigation | `primary` | Header nav bar (category links), mobile drawer | Auto-generates from first 8 categories |
| Secondary (Footer Bar) | `secondary` | Footer bar links, mobile drawer utility section | Hardcoded About / Contact / Advertise / Archives links |

Both menus support sub-menus (dropdowns on desktop, accordion on mobile).

### Theme Options

**Appearance → Theme Options** — tabbed admin page (`includes/theme-options.php`). All values stored as `theme_mod` and synced with the Customizer.

#### Footer tab

See Footer Identity above for the full field list. All footer fields are managed here exclusively.

#### Social Media tab

All six social URL fields (Facebook, X, Telegram, Instagram, Threads, WhatsApp).

#### Homepage tab

| Setting | Key | Default | Range | Sanitiser |
|---|---|---|---|---|
| Category sections | `up6_homepage_cat_count` | 4 | 1–20 | `absint` |
| Posts per category | `up6_homepage_posts_per_cat` | 3 | 1–12 | `absint` |
| Most Recent posts | `up6_homepage_recent_count` | 5 | 0–50 | `absint` |
| Excerpt length (words) | `up6_excerpt_length` | 35 | 10–80 | `absint` |
| Sidebar categories | `up6_sidebar_cat_count` | 6 | 1–20 | `absint` |
| Related News cards | `up6_related_count` | 4 | 3–12 | `absint` |
| Show empty categories | `up6_homepage_show_empty_cats` | off | checkbox | `absint` |

#### Contact tab

| Setting | Key | Default | Notes |
|---|---|---|---|
| CF7 Form ID | `up6_cf7_form_id` | 0 | Numeric ID from Contact → Contact Forms |
| Google Maps API Key | `up6_maps_api_key` | (empty) | Requires Maps Embed API enabled in Google Cloud Console |
| Google Maps Place ID | `up6_maps_place_id` | (empty) | Starts with `ChIJ` |

#### General tab

| Setting | Key | Default | Range | Notes |
|---|---|---|---|---|
| Hijri Date Offset | `up6_hijri_offset` | 0 | -1, 0, +1 | Corrects for moon-sighting vs astronomical calculation |
| Most Viewed Day Range | `up6_most_viewed_days` | 5 | 1–30 | Lookback window for Most Viewed sidebar panel and View Stats page |
| Enable content copy protection | `up6_copy_protect` | off | checkbox | Disables right-click, text selection, Ctrl+C/A/S for non-admin visitors |
| noindex search results and policy pages | `up6_noindex_search_policy` | on | checkbox | Adds `noindex` to search results and Privacy Policy, Disclaimer, Corrections page templates. Skipped when Yoast SEO or RankMath is active |
| Festive Occasion | `up6_festive_occasion` | *(none)* | dropdown | Shows a colourful SVG icon beside the header logo. 17 Malaysian occasions available. Select "None" to hide |
| Show from | `up6_festive_from` | *(empty)* | date `YYYY-MM-DD` | Optional start date — icon appears from this date inclusive. Leave blank for no start gate |
| Show until | `up6_festive_until` | *(empty)* | date `YYYY-MM-DD` | Optional end date — icon disappears after this date. Leave both blank to show until manually changed |
| Enable article voting | `up6_vote_enabled` | on | checkbox | Shows thumbs up/down vote buttons below article content |
| Vote count display threshold | `up6_vote_threshold` | 1 | 1–100 | Vote counts hidden until this many total votes are reached |
| Vote prompt label | `up6_vote_label` | *(empty)* | text | Optional text beside vote buttons. Leave blank for no label (recommended for news) |

### Festive Occasion Icons

A colourful inline SVG icon can be displayed beside the site logo in the header to mark Malaysian public holidays. Controlled from **Theme Options → General**. Hybrid behaviour: the editor selects the occasion manually from a dropdown, and optionally sets a date range for automatic show/hide. The selection persists in the database — reusable each year by updating the dates.

17 occasions are available: Hari Raya Aidilfitri, Hari Raya Haji, Ramadan, Maal Hijrah, Israk & Mikraj, Nuzul al-Quran, Maulid Nabi, Hari Kebangsaan, Hari Malaysia, Hari Keputeraan YDP Agong, Tahun Baru Cina, Deepavali, Thaipusam, Hari Wesak, Krismas, Tahun Baharu, Hari Pekerja.

Icons are stored as self-contained SVGs in the `/icons/` directory. Each uses embedded colours (not CSS variables) and renders at 5rem with a subtle fade-in animation. Hidden on mobile below 480px. Header only — does not appear in the footer.

#### Hidden Tags tab

Checkbox list of all tags. Selected tags are stored as comma-separated term IDs in the `up6_hidden_tags` option. When the Cipher Gate plugin is active, this tab shows a notice and defers to the plugin.

#### Security tab (v2.7.2+)

Read-only dashboard (outside the save form, AJAX-only). Four status cards: scanner status, installed themes count, flagged themes count, detection pattern count. Manual "Scan Now" button scans all installed themes and verifies UP6 file integrity. Scan history log shows the last 20 blocked activations, deleted uploads, and manual scans. UP6 File Integrity checker compares all PHP files against a SHA-256 baseline generated on theme activation. "Regenerate Baseline" button for post-update rebaselining. Email alerts sent to admin on every blocked activation or upload.

### View Stats

**Appearance → View Stats** — admin page showing a ranked table of posts by view count within the configured day range. Provides global "Reset All" button (with confirmation) and per-post "Reset" button in both the stats table and the post edit sidebar meta box.

---

## Features

### Single Post Layout (v2.6+)
Single posts render full-width with no sidebar. A stepped editorial hierarchy creates visual weight: the entry header (category badge, title, standfirst excerpt, author/date meta bar) spans a wide column (`64rem` / ~1024px), while the featured image, body content, abstract/summary boxes, cite blocks, topic tags, related news, and comments sit in a narrower centred reading column (`54rem` / ~864px). The sidebar (Pilihan panel, Most Viewed, Most Active categories) is shown only on the homepage and archive pages.

### Subtitle / Dek Line (v2.6.14+)
Posts can have an optional subtitle stored in `_up6_subtitle` post meta, entered via a "Subtitle" meta box in the editor sidebar. Displayed between the headline (`h1.entry-title`) and the excerpt on single posts. Rendered in italic Source Serif 4, mid-blue colour, responsive font size (`clamp(1.1rem, 2.5vw, 1.35rem)`). Retrieve programmatically with `up6_get_subtitle( $post_id )`. Save handler is nonce-verified with capability check.

### Search Permalink Rewrite (v2.6.14+)
Rewrites WordPress default `/?s=query` to clean URLs: `/carian/query` (Malay) or `/search/query` (English). The search base is translatable via the `.po` file (`__( 'search', 'up6' )` → `carian`). Two hooks: `init` sets `$wp_rewrite->search_base`, `template_redirect` redirects old-style query-string URLs. Replaces the Pretty Search Permalinks plugin.

### Content Copy Protection (v2.6.14+, optional)
Disables right-click, text selection, and copy keyboard shortcuts for non-admin visitors. Controlled by a toggle at **Theme Options → General → Enable content copy protection**. Disabled by default. Skips logged-in editors and administrators. Allows selection in form inputs and contenteditable elements. Inline CSS (`user-select: none`) + JS (contextmenu, keydown, dragstart listeners). This is a deterrent only — it cannot prevent technically determined copying. Replaces the WP Content Copy Protector plugin.

### Social Share Bar (v2.6.5+)
Horizontal row of share buttons positioned below the byline on single posts. Platforms in order: WhatsApp, Telegram, Facebook, X, Threads, LinkedIn, Reddit, Pinterest, Email, Copy Link — ordered for Malaysian sharing behaviour with global platforms following. Zero-plugin implementation using native platform share URLs and inline SVG icons. "Copy link" button uses `navigator.clipboard` with visual tick confirmation (2-second reset). Platform-coloured hover states (WhatsApp green, Telegram blue, Facebook blue, X/Threads black, LinkedIn blue, Reddit orange, Pinterest red), full dark mode support, slightly larger touch targets on mobile, flex-wrap for graceful line-breaking on narrow screens. All 12 labels translatable.

### Article Voting (v2.7.9+)
Thumbs up / thumbs down buttons between article content and topic tags. Configurable via Theme Options → General: enable/disable toggle, vote count display threshold (counts hidden until N total votes reached, default 1), and optional prompt label (blank by default — recommended for news). AJAX via `wp_ajax_up6_vote` / `wp_ajax_nopriv_up6_vote` with nonce verification. Deduplication: logged-in users tracked via user meta (`_up6_voted_{post_id}`), guests via httpOnly cookie (24h TTL). Vote data stored as post meta (`_up6_votes_up`, `_up6_votes_down`) — no custom database tables. Visual states: default (grey outline), hover (green for up, red for down), voted (filled), dimmed unvoted button. Full dark mode support. Mobile stacks vertically with 44px touch targets. Replaces the Vote It Up plugin (2010).

### Pin to Homepage
Any post can be pinned as the homepage hero card from the editor sidebar. A "Pin to Homepage" meta box in the editor sidebar wraps native WordPress `stick_post()` / `unstick_post()`. A red "📌 Sedang disemat" indicator shows when a post is currently pinned. If multiple posts are pinned, the most recent takes precedence. Pinned posts also appear with a 📌 column in the admin posts list.

### Pilihan Editor (Editor's Picks)
The sidebar panel that replaced the old "Featured / Pilihan" section. Powered by `up6_get_editor_picks()` — selects the single most recent post from each unique primary category (max 5), guaranteeing editorial diversity without manual curation. Editor pick post IDs are automatically excluded from all homepage category grids and the Most Recent section to prevent duplication.

### Dark Mode
Toggle button in the header (moon/sun icon). Preference persisted in `localStorage` under key `up6_theme`. An inline `<script>` in `<head>` applies the `up6-dark` class before first paint — zero flash. The logo bounce animation is disabled on mobile (≤768px) to avoid unintentional animation on tap.

### Dual CE + Hijri Date
Navigation bar displays both Gregorian and Hijri dates separated by a red dot. Hijri conversion uses the Julian Day Number astronomical algorithm (`up6_hijri_date()`). Month names are in Malay (Muharram through Zulhijjah). Configurable ±1 day offset for moon-sighting correction via Theme Options → General. Article bylines display the Hijri date corresponding to the post's publication date — stored as `_up6_hijri_formatted` post meta at publish time so future offset changes do not retroactively alter historical articles. `up6_get_hijri( $post_id )` reads stored meta first, falls back to live computation for pre-2.7.7 posts. A one-time backfill populates the meta for all existing posts on upgrade.

### Post View Counter
Self-contained, zero-plugin. Increments `_up6_views` post meta on each singular post view. Skips logged-in admins and common bot user-agents (7 patterns). Cookie-deduplicated: each visitor receives a `up6_viewed_{post_id}` httpOnly cookie (24h TTL) — repeat visits within 24 hours are not counted. Powers the Most Viewed sidebar panel and the View Stats admin page.

### Hidden Tags
Posts assigned a hidden tag are excluded from: homepage queries, archive pages, search results, REST API responses, XML sitemaps, tag cloud widgets, and rendered tag link output. Hidden tag archives return empty rather than 404. When the Cipher Gate plugin is active (`CG_VERSION` constant), the theme's built-in filters stand down to avoid double-filtering.

### Reading Time Estimator
Displayed in the single post meta bar: `📖 N minit`. Based on 200 words per minute, minimum 1 minute. Uses `up6_reading_time()` — reusable anywhere in the theme.

### Related News
Shown below every single post. Pulls from the same categories, excludes the current post. Card count configurable via Theme Options → Homepage → Related News cards (3–12).

### Load More Pagination
Archive and search pages replace native WP pagination with a Load More button. Cards fetched via `$.get()` and appended with fade-in animation. Button states: Load More → Loading… → All caught up!

### Masonry Layout
Applied to Related News grid on single posts via jQuery Masonry + imagesLoaded. Cards reposition after all images are loaded, preventing layout jumps. CSS grid fallback for no-JS environments. Clearfix applied via `::after` pseudo-element.

### Ornamental Section Divider
Between every homepage category section. SVG motif (abstract Nusantaran floral diamond with branching strokes) flanked by thin horizontal rules. Dark-mode aware (opacity-adjusted).

### Email Obfuscation
Footer email is ROT13-encoded in HTML source. Inline JS decodes and populates the visible `<a>` element at render time. A fallback `onclick` handler decodes for users with delayed JS execution.

### Theme Security Scanner (v2.7.1+)
Built-in defence against malicious themes. Scans every `.php` file in any theme being activated or uploaded against 18 known backdoor signatures (shell execution, eval injection, obfuscation chains, security bypasses, web shell command parameters, arbitrary file upload/write patterns). Three layers of protection: activation-time interception (blocks the switch before it happens), upload-time scanning (deletes malicious themes on upload), and background visual flagging (red overlay + disabled Activate button on the Themes page). All blocked activations are logged to the PHP error log with file names, line numbers, and matched patterns. Pattern library tuned for zero false positives against legitimate themes — tested against the Twenty Twenty-Five parent and commercial themes. No plugin required. See [Theme Security Scanner](#theme-security-scanner) for full details.

### Brand Inline Stylisation
`up6_brand_inline()` content filter replaces every occurrence of "UP6 Suara Semasa" (case-insensitive) in post content with a branded inline chip mirroring the header logo styling. Applied to `the_content`, `the_excerpt`, `widget_text_content`. Negative lookbehind prevents re-processing.

### Author Avatar System
Three-tier fallback: custom upload (user meta `up6_avatar`, managed via profile page media uploader) → Gravatar (with `d=404` and `onerror` fallback) → CSS initials span.

### RSS Feed Enrichment
`media:content` namespace with featured images and `<category>` elements per item. Compatible with Telegram channel syndication and standard aggregators.

### Scroll Progress Bar
Single posts only. 2px red line fixed at top of viewport, width driven by `requestAnimationFrame` scroll handler. Hidden in print.

---

## Template Files

| File | Layout | Sidebar | Description |
|---|---|---|---|
| `home.php` | — | — | Blog posts index (overrides parent block template, loads `index.php`) |
| `front-page.php` | — | — | Front page router (latest posts or static page) |
| `index.php` | 2-column grid | Yes | Homepage: hero card + category grids + Most Recent list |
| `archive.php` | 2-column grid | Yes | Category, tag, date, author archives with hero banner |
| `single.php` | Full-width, 64rem header / 54rem body | **No** | Single post: full article, tags, related news, comments |
| `page.php` | Default | — | Static pages |
| `search.php` | Full-width | No | Search results with hero banner; empty state with inline search form |
| `404.php` | Centred | No | Page not found |
| `sidebar.php` | — | — | Sidebar partial: Pilihan (Featured) panel + Most Viewed + Most Active categories |
| `header.php` | — | — | Sticky 2-row header: brand row (logo, search, icons) + nav row (primary menu, dual date) |
| `footer.php` | — | — | 2-column footer (brand/social + contact), footer bar (secondary nav + copyright), legal notice |
| `comments.php` | — | — | Native WordPress comment thread |

### Custom Page Templates

All assigned via the **Page Template** meta box in the editor sidebar.

| File | Template Name | Description |
|---|---|---|
| `template-faq.php` | FAQ Page | Accordion layout using `up6_faq` CPT items; FAQPage JSON-LD schema |
| `template-about.php` | About | Editorial about page |
| `template-meaning-of-6.php` | The Meaning of 6 | Mission/identity page: Six Voices, Six Lenses, Six Commitments (Akujanji), SUARA acronym |
| `template-editorial-policy.php` | Editorial Policy | Policy page with press freedom section |
| `template-privacy-policy.php` | Privacy Policy | PDPA-compliant privacy policy |
| `template-disclaimer.php` | Disclaimer | Legal disclaimer |
| `template-contact.php` | Contact | Two-column: NAP + CF7 form + Google Maps embed; NewsMediaOrganization JSON-LD |
| `template-corrections.php` | Corrections | Corrections and right-of-reply policy |

All policy-style page templates share the `.policy-*` CSS class system. FAQ and Contact have their own CSS sections.

---

## Asset Structure

### Stylesheets

| Path | Media | Lines | Purpose |
|---|---|---|---|
| `style.css` | screen | 3,371 | Main stylesheet: design tokens, all components, dark mode, responsive breakpoints |
| `css/mobile-patch.css` | screen | 357 | Supplementary mobile-first fixes: overflow protection, touch targets, font floor, focus states, logo wiggle disable |
| `css/editor-style.css` | editor | 77 | Block editor visual parity with front-end typography |
| `css/admin-options.css` | admin | 286 | Theme Options admin page UI styling |
| `css/print.css` | print | 357 | Print stylesheet: strips chrome, preserves article body, appends source URL footer |

Enqueue order: parent `style.css` → child `style.css` → `mobile-patch.css` → `print.css` (print media only).

### Scripts

| Path | Dependencies | Loaded on | Purpose |
|---|---|---|---|
| `js/navigation.js` | None | All pages | Mobile drawer, desktop dropdown menus, mobile search toggle, dark mode toggle, scroll progress bar |
| `js/up6-grid.js` | jQuery, Masonry, imagesLoaded | Archive, search, single | Load More pagination on archives + Masonry on related news grid |
| `js/jquery.masonry.min.js` | jQuery | Archive, search, single | Masonry layout library (v3) |
| `js/jquery.imagesloaded.min.js` | jQuery | Archive, search, single | imagesLoaded library (v5) — Masonry dependency |

All theme CSS and JS files have corresponding `.min` versions (e.g. `style.min.css`, `js/navigation.min.js`). The minified versions are loaded by default. Set `define( 'SCRIPT_DEBUG', true )` in `wp-config.php` to load unminified source files for development.
| `js/admin-options.js` | None | Admin: Theme Options | Tab switching on the Theme Options page |

All scripts loaded in footer (`true` in `wp_enqueue_script`). `navigation.js` is vanilla JS (no jQuery dependency). Grid/masonry scripts are conditional: enqueued only on `is_archive() || is_search() || is_single()`.

### Includes

| Path | Purpose |
|---|---|
| `includes/theme-options.php` | Admin page registration, save handler with per-key sanitisers and numeric clamping, `up6_opt()` helper, tabbed UI rendering |
| `includes/hidden-tags.php` | Hidden tag ID/slug helpers (static-cached), `UP6HiddenTagFilters` class (query, REST, sitemap, tag cloud, output filters), Cipher Gate compatibility guard, body class stripping |
| `includes/theme-security-scanner.php` | Theme activation scanner: 18 malicious code patterns, activation interception, upload-time scanning, background visual flagging, PHP error logging |

### Icons

| Path | Purpose |
|---|---|
| `icons/aidilfitri.svg` | Hari Raya Aidilfitri — hanging ketupat with woven grid, ribbon tails, bead strings, and stars in green and gold |
| `icons/aidiladha.svg` | Hari Raya Haji — mosque dome with gold crescent |
| `icons/ramadan.svg` | Ramadan — simplified hanging lantern (fanous) in purple, gold, and red |
| `icons/maal-hijrah.svg` | Maal Hijrah — green crescent with gold star |
| `icons/israk-mikraj.svg` | Israk & Mikraj — gold crescent and stars with teal triple mihrab arch |
| `icons/nuzul-quran.svg` | Nuzul al-Quran — open book in green and gold |
| `icons/mawlid.svg` | Maulid Nabi — green dome of Masjid an-Nabawi |
| `icons/merdeka.svg` | Hari Kebangsaan — raised fist with flag ribbon and crescent-star badge |
| `icons/malaysia-day.svg` | Hari Malaysia — Petronas Twin Towers with flag backdrop |
| `icons/agong-birthday.svg` | Hari Keputeraan YDP Agong — tengkolok diraja with royal star brooch |
| `icons/cny.svg` | Tahun Baru Cina — red lantern with gold tassels |
| `icons/deepavali.svg` | Deepavali — orange-gold oil lamp (diya) |
| `icons/thaipusam.svg` | Thaipusam — gold vel (spear) |
| `icons/wesak.svg` | Hari Wesak — pink and gold lotus flower |
| `icons/christmas.svg` | Krismas — decorated Christmas tree with holly and berries |
| `icons/new-year.svg` | Tahun Baharu — calendar page showing JAN 1 |
| `icons/labour-day.svg` | Hari Pekerja — raised fist gripping a wrench |

All SVGs use embedded colours (no CSS variable dependency) with viewBox dimensions appropriate to each icon. Loaded by `up6_festive_icon()` via `file_get_contents()`.

---

## CSS Architecture

### Strategy

The main `style.css` uses a **desktop-first** approach (47 `max-width` queries). The supplementary `css/mobile-patch.css` adds **mobile-first** fixes as an additive layer without rewriting the base. Both are loaded on all pages.

### Breakpoints

| Width | Context |
|---|---|
| 380px | Very narrow mobile: section header wraps, hero title clamped |
| 480px | Small mobile: `--up6-pad` reduces to 1rem, hero min-height reduced, policy font reduced |
| 540px | Mobile: card padding compact, meta bar compact, font size floor, footer bar stacks, `--up6-pad` step to 1.25rem |
| 640px | Mobile–tablet: Meaning of 6 grids stack to single column |
| 768px | Tablet: header collapses to mobile (hamburger, mobile search), footer grid stacks, logo wiggle disabled |
| 900px | Tablet–desktop: nav date hidden, contact page stacks, article card grid switches to 2-col |
| 960px | Desktop: sidebar collapses below content, nav gets horizontal scroll |

### Reusable CSS Class Systems

**`.policy-*`** — shared across all policy/legal page templates:

| Class | Purpose |
|---|---|
| `.policy-main` | `<main>` wrapper with vertical padding |
| `.policy-header` | Header block with bottom border |
| `.policy-header-label` | Red kicker label (dot + "Policy & Standards") |
| `.policy-updated` | "Last updated: …" metadata line |
| `.policy-content` | Body prose: Source Serif 4, `1.125rem`, `line-height: 1.85`, justified text (left-aligned on mobile) |
| `.policy-content h2` | Section heading: uppercase DM Sans 900, bottom border |
| `.policy-content a` | Red link, hover transitions to deep blue |

Dark mode variants defined for all `.policy-*` classes.

**`.single-article`** — single post wrapper (`max-width: 64rem`); header spans full width, content children constrained to `54rem`.

**`.article-card`** / `.card-*` — reusable card components: homepage grids, archive grids, search results, related news.

---

## PHP Helper Functions

Defined in `functions.php`, available globally in the theme:

| Function | Signature | Returns | Purpose |
|---|---|---|---|
| `up6_logo()` | `up6_logo()` | `void` (echoes) | Site title with beige accent on trailing digits, red subtitle |
| `up6_author_avatar()` | `up6_author_avatar( $author_id = null )` | `string` (HTML) | Three-tier avatar: custom upload → Gravatar → initials |
| `up6_social_url()` | `up6_social_url( $key )` | `string` | Sanitised social URL or empty string |
| `up6_breadcrumb()` | `up6_breadcrumb()` | `void` (echoes) | Accessible breadcrumb trail (Home → Category → Title) |
| `up6_hijri_date()` | `up6_hijri_date( $timestamp = null )` | `array` | Gregorian → Hijri: keys `day`, `month`, `month_name`, `year`, `formatted` |
| `up6_reading_time()` | `up6_reading_time( $post_id = null )` | `int` | Estimated reading time in minutes (200 wpm, min 1) |
| `up6_opt()` | `up6_opt( $key )` | `mixed` | Theme Options value with default fallback via `get_theme_mod()` |
| `up6_hidden_tag_ids()` | `up6_hidden_tag_ids()` | `int[]` | Hidden tag term IDs (static-cached per request) |
| `up6_hidden_tag_slugs()` | `up6_hidden_tag_slugs()` | `string[]` | Hidden tag slugs resolved from IDs (static-cached) |
| `up6_is_hidden_tag()` | `up6_is_hidden_tag( int\|string $term )` | `bool` | Check if a tag ID or slug is designated hidden |
| `up6_save_hidden_tag_ids()` | `up6_save_hidden_tag_ids( array $ids )` | `void` | Save hidden tag IDs to option |
| `up6_brand_inline()` | `up6_brand_inline( $content )` | `string` | Replace "UP6 Suara Semasa" with branded inline chip |
| `up6_get_most_viewed_posts()` | `up6_get_most_viewed_posts( $count = 5 )` | `WP_Post[]` | Most-viewed posts within configured day range |
| `up6_increment_post_views()` | `up6_increment_post_views()` | `void` | Increments `_up6_views` meta on singular post views (skips admins and bots) |
| `up6_head_meta()` | `up6_head_meta()` | `void` (echoes) | Open Graph, Twitter Card, JSON-LD NewsArticle + BreadcrumbList |
| `up6_contact_schema_json_ld()` | `up6_contact_schema_json_ld()` | `void` (echoes) | NewsMediaOrganization JSON-LD (contact page only) |
| `up6_get_subtitle()` | `up6_get_subtitle( $post_id = null )` | `string` | Returns the subtitle for a post, or empty string if not set |
| `up6_get_editor_picks()` | `up6_get_editor_picks( $count = 5 )` | `WP_Post[]` | One post per unique primary category, ordered by date — powers Pilihan Editor sidebar |
| `up6_get_editor_pick_ids()` | `up6_get_editor_pick_ids( $count = 5 )` | `int[]` | Post IDs from editor picks — used to exclude these from homepage grids |
| `up6_festive_occasions()` | `up6_festive_occasions()` | `array` | Returns all 17 occasion slugs mapped to their display labels |
| `up6_festive_icon()` | `up6_festive_icon()` | `void` (echoes) | Outputs the active festive SVG icon inline; checks date range and validates slug |
| `up6_scanner_scan_theme()` | `up6_scanner_scan_theme( WP_Theme $theme )` | `array` | Scans all .php files in a theme for 18 malicious code patterns; returns `is_blocked`, `hits`, `files_scanned` |
| `up6_scanner_get_php_files()` | `up6_scanner_get_php_files( string $dir )` | `string[]` | Recursively discovers all .php files under a directory |
| `up6_get_votes()` | `up6_get_votes( $post_id = null )` | `array` | Vote counts: `['up' => int, 'down' => int, 'total' => int]` |
| `up6_user_has_voted()` | `up6_user_has_voted( $post_id )` | `string\|false` | Returns `'up'`, `'down'`, or `false` — checks user meta (logged-in) or cookie (guest) |
| `up6_get_hijri()` | `up6_get_hijri( $post_id = null )` | `string` | Formatted Hijri date for a post — reads stored meta first, falls back to live computation |
| `up6_handle_vote()` | *(AJAX handler)* | `void` | Processes `wp_ajax_up6_vote` / `wp_ajax_nopriv_up6_vote` with nonce verification and dedup |

---

## Custom Post Types

| CPT | Slug | Public | Gutenberg | Menu Icon | Purpose |
|---|---|---|---|---|---|
| FAQ Items | `up6_faq` | No (admin UI only) | No (`show_in_rest: false` — non-public CPT; REST endpoint would expose content) | `dashicons-editor-help` | FAQ accordion items; `title` = question, `content` = answer; ordered by `menu_order` |

---

## Structured Data & SEO

All generated without plugins, hooked to `wp_head`:

| Schema Type | Template | Priority | Content |
|---|---|---|---|
| `NewsArticle` | Single posts | 5 | headline, description, dates, author (Person with `name` + `url`), publisher (Organization + logo), image array, articleSection, keywords, inLanguage |
| `BreadcrumbList` | Single posts | 5 | Home → Category → Post Title (mirrors visible breadcrumb) |
| `NewsMediaOrganization` | Contact page | 6 | name, url, contactPoint, address (PostalAddress), telephone, email, sameAs (all social URLs), logo |
| `FAQPage` | FAQ template | — | Question/Answer entities from `up6_faq` CPT (Google Rich Results eligible) |

Additional meta tags output on all pages: `<meta name="description">`, Open Graph (`og:site_name`, `og:title`, `og:description`, `og:url`, `og:type`, `og:locale`, `og:image`), Twitter Card (`twitter:card`, `twitter:title`, `twitter:description`, `twitter:image`). On article pages: `article:published_time`, `article:modified_time`, `article:author`, `article:section`, `article:tag`.

### Canonical URL

`<link rel="canonical">` emitted on all front-end pages via `wp_get_canonical_url()` at `wp_head` priority 5. Skipped automatically when Yoast SEO (`wpseo_init`) or RankMath (`rankmath`) is detected.

### Robots directives

`noindex` is added via the `wp_robots` filter, controlled by **Theme Options → General → noindex search results and policy pages** (on by default). When enabled, applies to:

- All search result pages — search results carry no SEO value and risk near-duplicate content signals
- Privacy Policy, Disclaimer, and Corrections page templates — legal pages consume crawl budget with no ranking return

Entirely skipped when Yoast SEO or RankMath is active — those plugins manage `noindex` themselves.

### Pagination signals

`<link rel="prev">` and `<link rel="next">` output at `wp_head` priority 5 on paginated archives, search results, and the blog index. Only emitted when a previous or next page actually exists.

### Google Fonts performance

`<link rel="preconnect">` hints for `fonts.googleapis.com` and `fonts.gstatic.com` (with `crossorigin`) output at `wp_head` priority 1 — before the font stylesheet is resolved. Eliminates one DNS + TCP round-trip on first visit.

---

## Translations & i18n

| File | Purpose | Entries |
|---|---|---|
| `languages/up6.pot` | Translation template (source of truth for `msgid` entries) | — |
| `languages/ms_MY.po` | Bahasa Melayu translation source | 515 |
| `languages/ms_MY.mo` | Compiled binary (must be recompiled after any `.po` change) | 354 compiled (511 translated) |

### Plural Forms

The `.po` header declares `nplurals=1; plural=0;` (Malay has no grammatical plural). All plural entries only need `msgstr[0]`.

### Recompiling

```bash
# Preferred: WP-CLI
wp i18n make-mo languages/ms_MY.po

# Alternative: Python Babel
python3 -c "
from babel.messages.pofile import read_po
from babel.messages.mofile import write_mo
with open('languages/ms_MY.po', 'rb') as f:
    cat = read_po(f)
with open('languages/ms_MY.mo', 'wb') as f:
    write_mo(f, cat)
"
```

**Critical:** the `.mo` binary must use a 7-field header (28 bytes). A 6-field header silently shifts string offsets by 4 bytes, breaking all translations with no visible error. Always recompile after editing the `.po` — a stale `.mo` causes WordPress to fall back to English source strings for any entries added after the last compile.

### Adding a New Language

1. Copy `languages/up6.pot` → `languages/{locale}.po` (e.g. `id_ID.po`).
2. Translate all `msgstr` entries.
3. Compile: `wp i18n make-mo languages/{locale}.po`.
4. Set site language in **Settings → General → Site Language**.

---

## Development Standards

### File Naming Convention

| Artefact | Convention | Example |
|---|---|---|
| PHP template files | **en-US** | `template-editorial-policy.php` |
| CSS / JS files | **en-US** | `admin-options.css`, `navigation.js` |
| WordPress page slugs | **ms-MY**, set manually in WP admin | `dasar-editorial` |
| i18n `msgid` strings | **en-US** | `"Press Freedom"` |
| i18n `msgstr` translations | **ms-MY** | `"Kebebasan Akhbar"` |

This separation is **non-negotiable**. PHP contains only English source strings wrapped in `esc_html_e()` / `esc_html__()`. Malay output is produced at runtime by `ms_MY.po` / `ms_MY.mo`.

### Security

- All form handlers use nonce verification (`wp_nonce_field` / `wp_verify_nonce`).
- All admin actions gated behind capability checks (`current_user_can`).
- All inputs sanitised via explicit per-key sanitiser functions with numeric clamping.
- All outputs escaped (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`) — 593 escaping calls across the codebase.
- 10 nonce verification points across form submission handlers.
- Built-in theme security scanner (`includes/theme-security-scanner.php`) blocks activation of themes containing malicious code — 18 detection patterns, zero false positives against legitimate themes. See [Theme Security Scanner](#theme-security-scanner).

### Accessibility

- Skip-to-content link (`<a class="skip-link" href="#content">`).
- 101 ARIA attribute instances across templates.
- Keyboard navigation: Escape closes drawers and dropdowns, focus returns to trigger element.
- `aria-expanded`, `aria-hidden`, `aria-controls` on mobile nav, mobile search, dropdown menus.
- `role` attributes: `banner`, `main`, `complementary`, `contentinfo`, `search`, `progressbar`, `list`, `listitem`.
- `focus-visible` outlines on all interactive elements (added via `mobile-patch.css`).

---

## Adding a New Page Template

1. **Create** `template-{en-us-name}.php` in the theme root with required headers:
   ```php
   <?php
   /**
    * Template Name: My Page Name
    * Template Post Type: page
    *
    * File naming: en-US per UP6 convention.
    * Content strings: en-US source, translated via ms_MY.po.
    *
    * @package UP6
    */
   ```
   `Template Post Type: page` is **mandatory** — TT25 is a block theme and will not register PHP templates without it.

2. **Wrap all user-facing strings** in `esc_html_e( 'English string', 'up6' )` or `esc_html__( 'English string', 'up6' )`. **Never hardcode Malay or any other language directly in PHP.**

3. **Add translations** to `languages/ms_MY.po` and mirror new `msgid` entries in `languages/up6.pot` (with empty `msgstr ""`).

4. **Recompile** `ms_MY.mo` (see [Translations & i18n](#translations--i18n) for commands).

5. **Register in `functions.php`** in three places:
   ```php
   // 1. theme_page_templates filter:
   $templates['template-my-page.php'] = __( 'My Page Name', 'up6' );

   // 2. up6_page_template_meta_box $templates array:
   'template-my-page.php' => __( 'My Page Name', 'up6' ),

   // 3. save_post_page $allowed array:
   'template-my-page.php',
   ```

6. **Set the template** on a page via the **Page Template** meta box in the editor sidebar.

7. **Set the page slug** in WordPress admin (ms-MY, your choice).

8. **Bump the version** in all four locations (see [Versioning](#versioning)).

---

## Theme Security Scanner

**Since:** 2.7.1 · **File:** `includes/theme-security-scanner.php` · **No plugin dependency**

Built-in protection against malicious themes containing backdoors, web shells, and obfuscated code. The scanner runs entirely within the UP6 child theme — no plugin required.

### How It Works

The scanner provides three layers of defence:

**Layer 1 — Activation interception.** When an admin clicks "Activate" on any theme, WordPress processes that request while UP6's `functions.php` is still loaded. The scanner hooks into `admin_init` at priority 1, detects the activation request, scans every `.php` file in the target theme against 18 malicious code patterns, and if any match, redirects back to the Themes page before the switch occurs. The activation is silently blocked and a detailed admin notice is shown.

**Layer 2 — Upload-time scanning.** Hooks into `upgrader_post_install` so that when a theme is uploaded via the admin (Appearance → Themes → Add New → Upload Theme), it's scanned immediately. If malicious patterns are found, the theme is deleted on the spot and an error message is shown to the uploader.

**Layer 3 — Background visual flagging.** Every time an admin visits the Themes page, a background scan runs across all installed themes (cached for 10 minutes via transient). Flagged themes receive a red overlay on their screenshot reading "MALICIOUS CODE DETECTED" and their Activate button is hidden via CSS and blocked via JavaScript.

### Detection Patterns

18 regex patterns targeting known malicious signatures. Each pattern is specifically tuned to match backdoor behaviour — not legitimate theme code. Tested against the Twenty Twenty-Five parent theme (0 false positives), the ConsultStreet commercial theme (66 PHP files, 0 false positives), and a confirmed web shell backdoor (multiple hits).

| Category | Patterns | Examples |
|---|---|---|
| Shell execution | 2 | `shell_exec()`, `passthru()`, `proc_open()`, `popen()`, `pcntl_exec()`, `system()` / `exec()` with `$_GET` / `$_POST` input |
| Eval injection | 3 | `eval()` with `base64_decode` / `$_REQUEST`, `assert()` with user input, `create_function()` with obfuscated body |
| Obfuscation chains | 3 | Nested `base64_decode`, `gzinflate( base64_decode(...) )`, `str_rot13( base64_decode(...) )` |
| Security bypasses | 3 | `open_basedir` set to NULL, `disable_functions` cleared, Suhosin executor bypass |
| Web shell indicators | 2 | `$_GET['cmd']` / `$_GET['exec']` command parameters, `@error_reporting(0)` + `@ini_set('display_errors', ...)` suppression chain |
| File operations | 2 | `move_uploaded_file()` / `file_put_contents()` with user-supplied paths |
| Backdoor signatures | 1 | Function-alternative mapping arrays (exact pattern from scanned malware) |
| Network exfiltration | 2 | `fsockopen()` / `curl_exec()` with `$_GET` / `$_POST` input |

### Configuration

Constants defined at the top of `includes/theme-security-scanner.php`:

| Constant | Default | Purpose |
|---|---|---|
| `UP6_SCANNER_PATTERNS` | 18 patterns | Regex → label map of malicious signatures |
| `UP6_SCANNER_THRESHOLD` | `1` | Minimum hits to block (1 = strict) |
| `UP6_SCANNER_LOG` | `true` | Log blocked activations to PHP error log |

### Logging

When `UP6_SCANNER_LOG` is `true`, all blocked activations are written to the PHP error log:

```
[UP6 Security Scanner] BLOCKED activation of "FitnessBase" — 3 malicious pattern(s) in 5 file(s).
  → about.php (line ~13): open_basedir bypass attempt
  → about.php (line ~21): Function-alternative mapping (backdoor signature)
  → about.php (line ~4): Error suppression pattern (web shell signature)
```

### Limitations

Because UP6 is a child theme, the scanner only runs while UP6 is the active theme. If someone switches away from UP6 entirely (to a theme that is not a child of UP6), `functions.php` stops loading and the scanner goes with it. This is an inherent limitation of a theme-only solution versus a plugin. However, as long as UP6 is active, no malicious theme can be activated in its place.

### False Positive Risk

All 18 patterns target code constructs that have no legitimate use in a WordPress theme context. The two patterns with the highest theoretical false-positive risk (`create_function` and web shell command parameters) have been specifically narrowed: `create_function` only triggers when combined with user input or obfuscation functions, and the command parameter pattern excludes common legitimate keys like `action` and single-letter variables.

---

## Performance

### Asset Minification (v2.7.18+)
All theme CSS and JS files ship with minified `.min` versions. Enqueues load minified by default. Set `define( 'SCRIPT_DEBUG', true )` in `wp-config.php` to load unminified source files. Front-end total: 129KB → 86KB (33% reduction across 5 CSS + 3 JS files).

### LCP Optimisation (v2.7.16+)
The homepage hero image uses a proper `<img>` tag with `fetchpriority="high"`, `loading="eager"`, and `decoding="async"` instead of a CSS `background-image`. This allows the browser's preload scanner to discover the image during HTML parsing — before CSS is downloaded. WordPress auto-generates `srcset` and `sizes` so mobile devices receive appropriately sized images. The hero image is also now crawlable by Google Image Search and Discover.

### View Counter Deduplication (v2.7.15+)
Each visitor receives a `up6_viewed_{post_id}` httpOnly cookie (24h TTL) on first view. Repeat visits within 24 hours are not counted. Prevents inflated view counts from page refreshes, back-button navigation, and bots that bypass UA filtering.

### Conditional Script Loading
jQuery, Masonry, and imagesLoaded are only enqueued on archive, search, and single post pages. `navigation.js` (vanilla JS, no dependencies) loads on all pages. Admin assets (`admin-options.css`, `admin-options.js`) only load on the Theme Options page.

---

## Versioning

Every change to the theme **must** include a version bump. The version string appears in four locations that must all be updated:

| File | Location | Format |
|---|---|---|
| `style.css` | Theme header line 8 | `Version: X.Y.Z` |
| `README.md` | First paragraph | `**Version:** X.Y.Z` |
| `readme.txt` | Near bottom | `Current version: X.Y.Z` |
| `CHANGELOG.md` | New entry at top | `## [X.Y.Z] — YYYY-MM-DD` |

WordPress reads the version from `style.css` via `wp_get_theme()->get('Version')`. This value is used for cache-busting on all enqueued stylesheets and scripts. **If the version is not bumped, browsers will serve cached copies of old CSS/JS files.**

### Scheme

- **Major** (3.0): breaking changes, structural rewrites
- **Minor** (2.7): new features, template additions, layout changes
- **Patch** (2.6.3): bug fixes, translation fixes, CSS adjustments

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for the full version history.

## Upgrading

See [UPGRADING.md](UPGRADING.md) for notes on breaking changes between versions.

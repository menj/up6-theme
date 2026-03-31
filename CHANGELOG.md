# Changelog

All notable changes to UP6 are documented here.
Format: [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versions: Major (breaking/structural), Minor (new features), Patch (fixes/translations).

## [2.7.32] — 2026-03-28

Consolidates all changes from v2.7.1 through v2.7.18 into a thematic summary. Per-version detail preserved below.

### New Features

**Theme Security Scanner (v2.7.1)**
- Built-in protection against malicious themes: 18 regex patterns for backdoors, shell execution, obfuscation, web shells
- Three layers: activation-time interception, upload-time scanning, background visual flagging
- Scan results cached via transient (10-minute TTL); all blocked activations logged to PHP error log

**Security Dashboard (v2.7.2)**
- Theme Options → Security tab: status cards, manual "Scan Now" button, scan history log (last 20 events)
- UP6 file integrity checker: SHA-256 baseline generated on activation, verified on demand
- "Regenerate Baseline" button; email alerts on every blocked activation or upload

**Article Voting (v2.7.9)**
- Thumbs up / thumbs down buttons between content and topic tags
- AJAX-powered with nonce verification; cookie dedup (guests, 24h) and user meta dedup (logged-in)
- Vote data in post meta (`_up6_votes_up`, `_up6_votes_down`) — no custom tables
- Configurable: enable/disable, count display threshold, optional prompt label (Theme Options → General)
- Full dark mode; mobile column stack with 44px touch targets
- Replaces Vote It Up plugin (2010)

**Hijri Date Immutability (v2.7.7–v2.7.8)**
- Hijri date locked as `_up6_hijri_formatted` post meta at publish time
- Future offset changes only affect nav bar + new posts, not historical articles
- One-time backfill for all existing posts on upgrade
- `up6_get_hijri( $post_id )` helper reads stored meta first, falls back to live computation

### Performance

**LCP Optimisation (v2.7.16)**
- Hero image converted from CSS `background-image` to proper `<img>` with `fetchpriority="high"`, `loading="eager"`, `decoding="async"`
- Browser preload scanner discovers image during HTML parsing (was: after all CSS downloaded)
- WordPress auto-generates `srcset`; mobile devices receive smaller images
- Hero image now crawlable by Google Image Search and Discover

**Asset Minification (v2.7.18)**
- 8 minified `.min` files for all CSS and JS; loaded by default
- Front-end total: 129KB → 86KB (33% reduction)
- `SCRIPT_DEBUG` constant switches to unminified source files for development

**View Counter Dedup (v2.7.15)**
- Cookie-based deduplication: `up6_viewed_{post_id}` httpOnly cookie (24h TTL)
- Repeat visits within 24 hours no longer counted

### Responsive & Print Fixes

**Tablet/Mobile (v2.7.11–v2.7.12)**
- Sidebar intermediate breakpoint at ≤1100px (20rem → 16rem)
- Footer equalised to 1fr/1fr on tablets (769–960px)
- Voting section constrained to 54rem reading column
- Vote button 44px touch targets
- Footer social `flex-wrap` for overflow on mobile
- `theme.json` contentSize corrected to 54rem
- Dead `jquery.infinitescroll.min.js` removed

**Print Stylesheet Rewrite (v2.7.13)**
- Complete rewrite to match v2.7 markup classes
- Byline prints as: author · date · updated (inline, no SVG icons)
- Hidden: share bar, vote buttons, mobile drawer, footer legal, scroll progress, section dividers
- Stepped layout removed in print (full page width)

### Translation & Terminology

**Security Scanner (v2.7.3)** — 47 PHP + 12 JS strings translated to ms_MY
**Voting (v2.7.9–v2.7.10)** — 11 strings for buttons, settings, hints
**Terminology (v2.7.4–v2.7.5)** — "hash" → "cincang", "checksum" → "hasil tambah semak" per Kamus Teknologi Maklumat (DBP)
**Total:** 583 strings, 0 empty

### Bug Fixes

- Theme Options tabs disproportionate: `flex: 1` → `flex: 0 1 auto` (v2.7.3)
- Save button visible on Security tab: hidden via JS when `target === 'security'` (v2.7.17)
- Hijri date showing today instead of publication date (v2.7.6)
- `current_time('timestamp')` deprecated — replaced with `DateTimeImmutable` + `wp_timezone()` and `time()` (v2.7.15)
- Hardcoded URLs in `template-about.php` — replaced with dynamic `get_category_by_slug()` (v2.7.15)

---

### Detailed History (v2.7.1–v2.7.18)

For granular per-version changes, see the entries below.

---

## [2.7.18] — 2026-03-24

### Added
- **Minified CSS and JS** — 8 `.min` files generated for all front-end and admin assets; all `wp_enqueue_style` / `wp_enqueue_script` calls updated to load `.min` versions by default; original source files retained for development; set `define( 'SCRIPT_DEBUG', true )` in `wp-config.php` to load unminified versions
  - `style.min.css` — 95KB → 69KB (27.9%%)
  - `css/mobile-patch.min.css` — 9.3KB → 3.1KB (66.6%%)
  - `css/print.min.css` — 9.2KB → 5.4KB (41.1%%)
  - `css/editor-style.min.css` — 2.0KB → 1.3KB (33.7%%)
  - `css/admin-options.min.css` — 7.9KB → 5.7KB (27.9%%)
  - `js/navigation.min.js` — 12.5KB → 7.9KB (37.4%%)
  - `js/up6-grid.min.js` — 2.4KB → 1.3KB (44.8%%)
  - `js/admin-options.min.js` — 9.8KB → 5.8KB (40.1%%)
  - **Front-end total: 129KB → 86KB (33%% reduction)**

---

## [2.7.17] — 2026-03-24

### Fixed
- **Save button visible on Security tab** — the "SIMPAN PERUBAHAN" button sits inside the `<form>` but outside any tab panel; the Security tab is rendered outside the `<form>` (read-only, AJAX-only); when Security was active, all form panels were hidden but the save button remained visible, floating at the top above the security content; fixed by toggling `.up6-options-footer` display in the tab switching JS — hidden when target is `security`, shown for all other tabs; also handles initial page load (if Security tab was the last active tab before a save/reload)

---

## [2.7.16] — 2026-03-24

### Changed
- **Hero image: LCP optimisation** — replaced CSS `background-image` with a proper `<img>` tag using `the_post_thumbnail()` + `object-fit: cover`; the browser's preload scanner now discovers the hero image during HTML parsing (was: only after all 5 CSS files were downloaded and parsed); attributes: `fetchpriority="high"` (tells browser to prioritise over other resources), `loading="eager"` (no lazy-loading the LCP element), `decoding="async"` (non-blocking decode); WordPress auto-generates `srcset` and `sizes` so mobile devices receive smaller images; hero image is now indexable by Google Image Search and Discover (CSS `background-image` is not crawlable); visual output is identical — `object-fit: cover` is the `<img>` equivalent of `background-size: cover`; expected LCP improvement: 300–600ms
- Removed unused `$thumb_url` variable from `index.php`

---

## [2.7.15] — 2026-03-24

### Fixed
- **View counter dedup** — added `up6_viewed_{post_id}` httpOnly cookie (24h TTL) to `up6_increment_post_views()`; same visitor refreshing, navigating back, or re-opening the article within 24 hours is no longer counted; same pattern as the article voting cookie; prevents inflated view counts from repeat visits and bots that slip past UA filtering
- **`current_time('timestamp')` deprecated** — all 3 instances replaced across 2 files:
  - `functions.php` Hijri nav bar: now uses `DateTimeImmutable( 'now', wp_timezone() )` — timezone-correct without the deprecated function; handles offset via `->modify()` instead of timestamp arithmetic
  - `sidebar.php` Pilihan Editor: `get_post_time( 'U', true )` + `time()` — both UTC, compatible with `human_time_diff()`
  - `sidebar.php` Most Read: same UTC pair
- **Hardcoded URLs in `template-about.php`** — all 8 `up6.org/...` category URLs replaced with dynamic `get_category_by_slug()` + `get_category_link()` lookups via a `$cat_link()` helper closure; if a category slug is renamed, the links follow automatically; `#` fallback if the slug doesn't exist

---

## [2.7.14] — 2026-03-24

### Fixed
- **View counter dedup** — added cookie-based deduplication to `up6_increment_post_views()`; each visitor now gets a `up6_viewed_{post_id}` httpOnly cookie (24h TTL) after their first view; subsequent visits within 24 hours are not counted; same pattern as the article voting system; prevents inflated counts from page refreshes, back-button navigation, and bots that slip past the UA filter

---

## [2.7.13] — 2026-03-24

### Fixed
- **Print stylesheet rewritten for v2.7 markup** — the print CSS referenced class names from pre-v2.6 markup that no longer exist: `.entry-meta-bar` (now `.entry-byline`), `.entry-author-info` (now `.entry-byline-body`), `.entry-date` (now `.meta-item--published time`), `.entry-reading-time` (now `.meta-item--reading-time`); the entire byline section was printing with raw screen layout including SVG icons and flex gaps; breadcrumb was contradictory (hidden in strip chrome, then styled with `display: flex` below)
- **Print byline now clean** — author name + published date + updated date rendered inline with Georgia serif; avatar hidden; all SVG `.meta-icon` elements hidden; Hijri date, view count, and reading time hidden (not meaningful in print); CSS middot separators replaced with `content` separators
- **Additional print exclusions** — `.mobile-nav-drawer`, `.footer-legal`, `.section-divider-ornament`, `#up6-scroll-progress` added to strip chrome block
- **Stepped layout removed in print** — `.single-article` and all child containers get `max-width: 100% !important` so content fills the page width
- **Subtitle styled for print** — `.entry-subtitle` gets 12pt, #333 colour
- **Breaking badge styled for print** — `.is-breaking` stripped of background/border, rendered as red text

---

## [2.7.12] — 2026-03-24

### Fixed
- **Footer social icons overflow on mobile** — `.footer-social` was missing `flex-wrap: wrap`; with all 6 social URLs filled (Facebook, X, Instagram, Threads, Telegram, WhatsApp), the icon row exceeded 375px viewport width; now wraps gracefully to a second row
- **Share bar and vote buttons visible in print** — `.entry-share` and `.entry-vote` were not hidden in `print.css`; both interactive elements now excluded alongside related news, comments, and reading time

---

## [2.7.11] — 2026-03-24

### Fixed
- **Voting section unconstrained width** — `.entry-vote` was missing from the 54rem reading column constraint in the stepped layout; on wide screens it spanned the full 64rem article width, breaking the visual step-down between header and content; added `.single-article .entry-vote` to the constraint block alongside `.entry-thumbnail`, `.entry-content`, `.entry-tags`
- **`theme.json` contentSize mismatch** — was `780px` but the actual reading column is `54rem` (864px); block editor showed content at a narrower width than the front-end; corrected to `54rem`
- **Sidebar squeezed on narrow desktops** — added intermediate breakpoint at ≤1100px that narrows the sidebar from 20rem to 16rem and reduces gap from 2rem to 1.5rem; prevents content column from being crushed between 961px and 1100px before the full 1fr collapse at ≤960px
- **Footer cramped on tablets (769–960px)** — the `1.6fr 1fr` grid with 4rem gap left the contact column very narrow on 900px screens; added tablet-specific rule equalising to `1fr 1fr` with 2rem gap
- **Vote buttons lacked touch targets** — added `min-height: 44px` for `.vote-btn` in the touch target section of `mobile-patch.css` at ≤768px

### Removed
- **Dead file: `js/jquery.infinitescroll.min.js`** — bundled since v2.5.9 but never enqueued; Masonry and Load More use `jquery.masonry.min.js` and `up6-grid.js` instead; file count 62 → 61

---

## [2.7.10] — 2026-03-24

### Added
- **Voting controls in Theme Options → General** — three new settings:
  - **Enable article voting** — checkbox to turn voting on/off site-wide (default: on)
  - **Vote count display threshold** — minimum total votes before counts are shown (range 1–100, default 1); prevents bare "0 / 0" on new articles; vote buttons still appear but counts are hidden until threshold is reached; after a vote pushes the total past the threshold, JS creates the count spans dynamically
  - **Vote prompt label** — custom text shown beside the vote buttons; leave blank for no label (recommended for news sites where "Was this article useful?" doesn't fit); any custom text is escaped and rendered as-is

### Changed
- Voting UI now conditional: entire `.entry-vote` block only renders when `up6_vote_enabled` is on
- Vote label removed from default — empty by default (was "Adakah artikel ini berguna?" which doesn't suit news articles)
- JS updated to dynamically create `.vote-count` spans after first vote if they weren't in the DOM (below-threshold case)
- 8 new ms_MY translations for voting settings; `.mo` recompiled (580 strings)

---

## [2.7.9] — 2026-03-24

### Added
- **Article voting** — thumbs up / thumbs down buttons at the bottom of every article, between the content and topic tags
  - "Adakah artikel ini berguna?" label + two pill buttons with thumbs-up/down SVG icons and live vote counts
  - AJAX via `wp_ajax_up6_vote` / `wp_ajax_nopriv_up6_vote` with nonce verification
  - Deduplication: logged-in users tracked via user meta (`_up6_voted_{post_id}`), guests via httpOnly cookie (24h TTL)
  - Vote data stored as post meta (`_up6_votes_up`, `_up6_votes_down`) — no custom database tables
  - `up6_get_votes( $post_id )` helper returns `['up', 'down', 'total']`
  - `up6_user_has_voted( $post_id )` returns `'up'`, `'down'`, or `false`
  - Visual states: default (grey outline), hover (green for up, red for down), voted (filled background), dimmed unvoted button after voting
  - Full dark mode support; mobile stacks vertically with larger touch targets
  - 3 new ms_MY translations; `.mo` recompiled (572 strings)
  - Replaces Vote It Up plugin (2010, custom DB table, no nonces, raw XHR, SQL injection risks)

---

## [2.7.8] — 2026-03-24

### Added
- **Hijri date backfill** — one-time migration on `admin_init` that stores `_up6_hijri_formatted` for all existing published posts; queries posts where the meta key does not exist, computes the Hijri date from each post's publication timestamp using the current offset, and saves it; runs once (keyed by `up6_hijri_backfill_done` option); after this runs, all posts — old and new — have immutable Hijri dates that are unaffected by future offset changes

---

## [2.7.7] — 2026-03-24

### Fixed
- **Hijri date now locked at publish time** — the formatted Hijri date is stored as `_up6_hijri_formatted` post meta when a post transitions to `publish` status; this prevents future Theme Options offset changes from retroactively altering historical articles; `up6_get_hijri( $post_id )` helper reads from stored meta first, falls back to live computation for pre-2.7.7 posts; the nav bar header continues to compute today's Hijri date live with the current offset (correct behaviour — it shows "what day is it today")

---

## [2.7.6] — 2026-03-24

### Fixed
- **Hijri date showing today instead of publication date** — `up6_hijri_date()` in `single.php` was called without a timestamp, defaulting to `current_time()` (today); all articles showed the same Hijri date regardless of when they were published; fixed by passing `get_post_time( 'U' )` so each article displays the Hijri date corresponding to its Gregorian publication date; the nav bar header correctly continues to show today's Hijri date

---

## [2.7.5] — 2026-03-24

### Fixed
- **Terminology: checksum** — "jumlah semak" → "hasil tambah semak" per Kamus Teknologi Maklumat (DBP): "hasil tambah semak" (Ig. checksum; Br. hasil tambah semak); `.mo` recompiled

---

## [2.7.4] — 2026-03-24

### Fixed
- **Terminology: proper Malay IT terms per Kamus Teknologi Maklumat** — "hash" → "cincang" (e.g. "garis asas cincang SHA-256"), "hashed" → "dicincang" (e.g. "fail dicincang"), "checksum" → "jumlah semak" (e.g. "jumlah semak garis asas"); references: Kamus Teknologi Maklumat (DBP), "cincang sehala" = one-way hash; `.mo` recompiled (569 strings)

---

## [2.7.3] — 2026-03-24

### Fixed
- **Theme Options tabs disproportionate** — changed `.up6-tab` from `flex: 1` (equal width) to `flex: 0 1 auto` (content-sized); each tab now sizes to its label width; horizontal padding increased from `0.5rem` to `1rem`; font-size reduced to `0.6rem` with tighter letter-spacing (`0.08em`); `.up6-tabs` container gets `overflow-x: auto` for narrow admin screens
- **Security tab untranslated** — added 47 ms_MY translations for the security scanner: 35 PHP-rendered strings (`_e()` / `__()` in `theme-security-scanner.php`) and 12 JS-rendered strings moved from hardcoded English to `wp_localize_script` via the `up6Scanner` object; AJAX scan results, integrity status, baseline updates, scan log labels, and all status cards now fully translated; `.mo` recompiled (569 strings, 0 empty)

---

## [2.7.2] — 2026-03-24

### New Features

**Security tab in Theme Options** (`Appearance → Theme Options → Security`)
- Read-only security dashboard with four status cards: scanner status, installed themes count, flagged themes count, detection pattern count
- Manual "Scan Now" button — AJAX-powered, scans all installed themes and verifies UP6 file integrity in one click; results rendered inline with expandable per-theme detail tables
- Scan history log — persistent table of the last 20 blocked activations, deleted uploads, and manual scans with timestamps, theme names, action badges, hit counts, and usernames
- UP6 file integrity checker — SHA-256 checksum baseline generated on theme activation and version updates; verifies all UP6 PHP files against baseline with modified/added/removed file reporting; "Regenerate Baseline" button for post-update rebaselining
- Email notification — sends an alert to the site admin email on every blocked activation or upload, including full pattern match details, username, and timestamp

### Changed
- `includes/theme-security-scanner.php`: added scan log (`up6_scanner_log` option), email alert (`up6_scanner_send_alert()`), checksum integrity (`up6_scanner_generate_checksums()` / `up6_scanner_verify_checksums()`), AJAX handlers (`up6_scanner_scan_now` / `up6_scanner_regen_checksums`), security tab renderer (`up6_scanner_render_security_tab()`)
- `includes/theme-options.php`: added Security tab to tab list, security tab panel (outside form), AJAX nonce localization via `wp_localize_script`
- `js/admin-options.js`: added AJAX handlers for Scan Now and Regenerate Baseline buttons
- `css/admin-options.css`: added security tab card grid, section, and integrity status styles

## [2.7.1] — 2026-03-24

### New Features

**Theme Security Scanner** (`includes/theme-security-scanner.php`)
- Built-in protection against malicious themes containing backdoors, web shells, and obfuscated code
- Three layers: activation-time interception (blocks switch before it happens), upload-time scanning (deletes malicious themes on upload), background visual flagging (red overlay + disabled Activate button on Themes page)
- 18 regex patterns targeting shell execution, eval injection, obfuscation chains, security bypasses, web shell indicators, arbitrary file operations, backdoor function-mapping signatures, and network exfiltration
- All blocked activations logged to PHP error log with file names, line numbers, and matched pattern labels
- Configurable threshold (`UP6_SCANNER_THRESHOLD`) and logging toggle (`UP6_SCANNER_LOG`)
- Zero false positives against legitimate themes (tested against TT25 parent and commercial themes)
- Scan results cached via transient (10-minute TTL) to avoid performance impact on repeated Themes page visits

### Changed
- `functions.php`: added `require_once` for `includes/theme-security-scanner.php`

## [2.7.0] — 2026-03-20

Major feature release. Consolidates all changes from v2.6.3 through v2.6.82.

### New Features

**Single post redesign**
- Stepped editorial hierarchy: entry header (badge, title, subtitle, byline) at 64rem, reading column (image, body, tags, related, comments) at 54rem
- Enhanced byline with custom SVG icons: published date + time (calendar), Hijri date (gold crescent), last-updated timestamp (rotating arrows, shown >24h after publication), view count (eye, shown ≥100 views), reading time (open book)
- Compact single-row layout: avatar + author name on first line, all metadata on second line, share bar directly below
- Red-tinted middot separators between metadata items; "KONGSI" share label in accent red
- Subtitle / dek line meta box in the editor sidebar; stored as `_up6_subtitle` post meta; displayed in italic Source Serif 4 below the headline; `up6_get_subtitle()` helper available globally

**Social share bar**
- 10 platforms: WhatsApp, Telegram, Facebook, X, Threads, LinkedIn, Reddit, Pinterest, Email, Copy Link — ordered for Malaysian sharing behaviour
- Zero-plugin: native platform share URLs, inline SVG icons, `navigator.clipboard` for copy link with tick confirmation
- Platform-coloured hover states, full dark mode support, flex-wrap on desktop, column stack on mobile

**Pin to Homepage**
- "Semat di Laman Utama" meta box in the editor sidebar; checkbox toggles native WordPress `stick_post()` / `unstick_post()`
- Red "📌 Sedang disemat" status indicator when pinned
- 📌 column in admin posts list for at-a-glance pinned post visibility
- Pinned posts appear as the hero card; most recent pinned post takes precedence

**Pilihan Editor — automated diverse sidebar**
- Renamed from "Featured" / "Pilihan" to "Editor's Pick" / "Pilihan Editor"
- `up6_get_editor_picks()`: selects the most recent post from each unique primary category (max 5), guaranteeing editorial diversity
- Editor pick posts automatically excluded from all homepage category grids and Most Recent section — no content duplication
- Layout matches Most Read: title-left, thumbnail-right, single category label, relative time

**Plugin replacements (3 plugins eliminated)**
- **Search permalink rewrite** — rewrites `/?s=query` to `/carian/query` (translatable); replaces Pretty Search Permalinks plugin; 15 lines of theme code; auto-flushes rewrite rules on theme activation and version updates
- **Subtitle / dek line** — replaces Secondary Title plugin (7.5MB, 350 files, 2,240 lines) with ~40 lines of theme code
- **Content copy protection** — optional toggle at Theme Options → General; disables right-click, selection, copy shortcuts for non-admin visitors; skips logged-in editors; replaces WP Content Copy Protector plugin

**Malaysian flag gold accent**
- `--up6-gold: #D4A017` design token completing all four Jalur Gemilang colours (blue, red, white, gold)
- Hijri crescent icon in gold, ornamental section divider diamond in gold, footer top border as 4px red / 4px gold dual stripe

**Festive occasion icon system**
- 17 hand-crafted colourful SVG icons for Malaysian public holidays (Aidilfitri, Aidiladha, Ramadan, Maal Hijrah, Israk & Mikraj, Nuzul al-Quran, Maulid Nabi, Hari Kebangsaan, Hari Malaysia, Agong Birthday, Chinese New Year, Deepavali, Thaipusam, Wesak, Christmas, New Year, Labour Day)
- Theme Options → General: occasion dropdown + date range pickers for automatic visibility
- Icon displayed beside the header logo at 5rem; hidden below 480px

**SEO layer**
- `<link rel="preconnect">` for Google Fonts (priority 1)
- `<link rel="canonical">` on all pages (skips when Yoast/RankMath active)
- `noindex` on search results and policy pages (toggleable, skips with SEO plugins)
- `<link rel="prev/next">` on paginated archives
- NewsMediaOrganization JSON-LD on contact page

**RTL stylesheet** — `rtl.css` added for right-to-left language support

### Improvements

**Sidebar panels**
- Most Read (Paling Dibaca) redesigned: bold red rank numbers (01–05), title-first layout, view count with eye icon, relative time, hover scale on thumbnails
- Pilihan Editor restyled to match Most Read visual language

**Related News section**
- Cards now use identical markup to homepage cards (single uppercase category, author + date, excerpt)
- 3-line clamp on titles and excerpts for balanced card heights
- Red section border, red dot on "BERITA BERKAITAN" header, red-tinted card separators

**Category badges** — single posts now show all assigned categories as separate pill badges

**Search page** — removed duplicate inline search form (header search is sufficient); corrected misleading "browse by category" message; widened empty-state container; demoted back-home button to text link

**Abstract Box plugin compatibility** — scoped CSS reset prevents theme heading margins from creating gaps inside plugin boxes

**Code quality** — `$clamps` array in theme-options.php; consistent JS indentation; view counter moved from `wp_head` to `wp`; share URLs escaped with `esc_attr()`

### Mobile Fixes

- Header: dark mode toggle no longer hidden off-screen; branding shrinks to fit icons; gap reduced; title font scaled down
- Share bar: KONGSI label stacks above buttons instead of wrapping mid-row
- Metadata: middot separators hidden on mobile; icons provide visual separation
- Mobile drawer: dropdown chevrons now red; secondary nav in one horizontal row
- Footer legal notice: left-aligned on mobile instead of centred
- SUARA SEMASA subtitle: `white-space: nowrap` prevents awkward line break

### Translations

- Full ms_MY coverage: 513 strings filled, 0 empty
- Key translations: "Pilihan Editor", "Paling Dibaca %d Hari Terkini", "Semat di Laman Utama", "%s capaian", all share platform labels, all festive occasion labels, all policy/legal prose
- View count: "bacaan" → "capaian" (correct analytics term)
- Most Read: "terakhir" → "terkini" (latest, not final)

### Bug Fixes

- Brand colour circular self-references (`--up6-red-lt`, `--up6-gold-dk`) fixed
- Stale `ms_MY.mo` recompiled (was missing ~100 translations)
- CSS syntax error at `style.css` line 1099 (stray `}` breaking downstream rules)
- JS scope leak in `navigation.js` (6 globals leaked outside IIFE)
- Mobile search placeholder mojibake (double-encoded UTF-8)
- Logo wiggle disabled on touch devices to prevent unintentional animation on tap
- `up6_faq` CPT `show_in_rest` set to `false` for security
- Search permalink 404 resolved via automatic rewrite rules flush

### Documentation

- **README.md** — comprehensive technical reference: requirements, design system, configuration guide, features, template files, asset structure, CSS architecture, PHP helpers, CPT registry, structured data reference, i18n workflow, development standards, versioning policy
- **readme.txt** — general audience: plain-language features, getting started guide, expanded FAQ

---

### Detailed History (v2.6.3–v2.6.82)

For granular per-version changes, see the entries below.

---

## [2.6.82] — 2026-03-20
### Fixed
- **Mobile drawer: dropdown arrows now red** — chevron `border-top` changed from `rgba(255,255,255,.45)` to `var(--up6-red)`; matches the theme accent colour and is now visible against the dark drawer background
- **Mobile drawer: secondary nav in one row** — added `display: flex; flex-wrap: wrap` on `.mobile-utility-list` so MENGENAI, MAKSUD 6, EDITORIAL, FAQ, XML sit in a single horizontal row instead of stacking vertically; `white-space: nowrap` prevents individual items from breaking

---

## [2.6.81] — 2026-03-20
### Added
- **Pin to Homepage** — "Pin to Homepage" / "Semat di Laman Utama" meta box in the editor sidebar (same position and style as Subtitle box); checkbox toggles WordPress native `stick_post()` / `unstick_post()`; red "📌 Sedang disemat" status indicator shown when post is currently pinned; pinned posts appear as the hero card on the homepage (existing hero query reads `get_option('sticky_posts')` — no changes needed); description explains that the most recent pinned post takes precedence
- **Admin posts list 📌 column** — narrow column after the checkbox showing a red 📌 pin icon for any currently sticky post; column header is a pin emoji with "Pinned to Homepage" tooltip; allows editors to see all pinned posts at a glance without opening each one
- 5 new ms_MY translations; `.mo` recompiled (513 strings)

---

## [2.6.80] — 2026-03-20
### Changed
- **Pilihan Editor — automated diverse curation** — sidebar panel renamed from "Featured" / "Pilihan" to "Editor's Pick" / "Pilihan Editor"; posts now selected by `up6_get_editor_picks()` which picks the most recent post from each unique primary category (max 5), guaranteeing editorial diversity by recency; editor pick post IDs excluded from all homepage category grids and the Most Recent section via `up6_get_editor_pick_ids()` merged into `$exclude_ids` in `index.php` — no post appears in both the sidebar and the main content; helper function is static-cached so sidebar and index share one query

### Fixed
- **Most Read heading translation** — changed "Paling Dibaca %d Hari Terakhir" to "Paling Dibaca %d Hari Terkini" ("terkini" = most recent/latest vs "terakhir" = final/last)
- Added "Editor's Pick" → "Pilihan Editor" translation; `.mo` recompiled (508 strings)

---

## [2.6.79] — 2026-03-20
### Changed
- **Pilihan panel restyled to match Most Read** — layout flipped to title-left / thumbnail-right (was thumbnail-left / title-right); multi-category kicker with red dots replaced by single primary category label; date format changed from absolute (Mac 20, 2026) to relative (`human_time_diff` — "3 hari yang lalu"); thumbnail enlarged from 3.5×2.625rem to 4.5×3.375rem with hover scale; items separated by bottom borders instead of top borders; dead `.featured-panel-kicker`, `.featured-panel-cat-item`, `.featured-panel-content` CSS removed; dark mode overrides added for all new elements

### Fixed
- **Most Read heading untranslated** — `msgstr[0]` for "Most Read Last %d Day/Days" was empty in the `.po` file; filled in as "Paling Dibaca %d Hari Terakhir"; `.mo` recompiled (507 strings)

---

## [2.6.78] — 2026-03-20
### Fixed
- **Single post: category badges show all categories** — the entry badges area now loops through all categories assigned to the post instead of displaying only the primary one; each category renders as a separate linked pill badge

---

## [2.6.77] — 2026-03-20
### Fixed
- **Mobile footer: legal notice left-aligned** — `.footer-legal-inner` and `.footer-legal-text` changed from `text-align: center` / `align-items: center` to `text-align: left` / `align-items: flex-start` on ≤540px; centred legal text on narrow screens looked unintentional and made multi-line registration details harder to read

---

## [2.6.76] — 2026-03-20
### Fixed
- **Mobile header: dark mode icon hidden** — `.site-branding` with `flex: 1` was expanding to fill all available width, pushing the dark mode toggle off-screen; fixed by adding `min-width: 0` and `overflow: hidden` on the branding so it shrinks when needed, `flex-shrink: 0` on `.header-icons` so icons always have room, reduced header gap from 1.5rem to 0.625rem on ≤768px, and scaled down the title font from 1.35rem to 1.1rem on mobile
- **Reverted breadcrumb truncation** — CSS truncation of the breadcrumb current-page title removed; keeping full titles for SEO value

---

## [2.6.75] — 2026-03-20
### Fixed
- **Mobile: share bar KONGSI label trapped mid-row** — on screens ≤540px, `.entry-share` switches to `flex-direction: column` so the label sits above the button grid instead of wrapping into the middle of it
- **Mobile: metadata orphaned middot** — CSS middot separators (`::before`) hidden on ≤540px; icons alone provide sufficient visual separation between items, preventing a lone floating dot before a wrapped last item
- **Mobile: breadcrumb full title overflow** — `.breadcrumb .current` truncated to `max-width: 18ch` with `text-overflow: ellipsis` on ≤540px; the title already appears as the h1 directly below, so repeating it in full was redundant and consumed 2–3 lines
- **Header: SUARA SEMASA line break** — added `white-space: nowrap` to `.site-title-sub` preventing the brand subtitle from splitting across two lines on narrow viewports

---

## [2.6.65] — 2026-03-19
### Changed
- Rewrote `aidilfitri.svg`: auto-traced ketupat silhouette from reference — green body with woven grid cutouts and ribbon tails, gold hanging bead strings, stars, and sparkle (10KB)

---

## [2.6.63] — 2026-03-19
### Changed
- Rewrote `ramadan.svg` as a clean purpose-built icon: solid purple lantern body with gold arch window, red flame, gold hanging ring, gold accent circles and flanking stars — replaces 78KB line-art trace that was unreadable at icon size (now 3KB)
- Festive icon display size increased from 4.2rem to 5rem for better visibility

### Updated
- README: festive icon size, ramadan description, viewBox documentation
- CHANGELOG: entries for v2.6.58–v2.6.63

---

## [2.6.61] — 2026-03-19
### Changed
- Rewrote `israk-mikraj.svg`: gold crescent (upper-left) and five gold stars (upper-right) above triple nested teal mihrab arches — built from user YAML specification with improvements

---

## [2.6.60] — 2026-03-19
### Changed
- Optimised `agong-birthday.svg` (tengkolok diraja): RDP simplification (epsilon 4.0 fabric, 2.5 brooch) + Catmull-Rom smoothing + SVGO pass — 33,385 → 6,569 bytes (80% reduction). Removed `shape-rendering="geometricPrecision"`. Fixes jagged edges and hairline cracks at small display sizes

---

## [2.6.59] — 2026-03-19
### Changed
- Rewrote `agong-birthday.svg` using user-provided SVG trace of tengkolok diraja icon, colourised from beige/grey to royal gold palette (6 colour mappings)

---

## [2.6.58] — 2026-03-18
### Changed
- Rewrote `agong-birthday.svg` as hand-drawn tengkolok with royal gold and navy colour scheme — replaced by user-provided trace in v2.6.59

---

## [2.6.45] — 2026-03-18
### Changed
- Rewrote `agong-birthday.svg` based on the tengkolok diraja (royal Malay headgear): gold and black brocade songket fabric in a curved boat shape with upturned tips, silver multi-pointed royal star brooch with crescent moon and blue centre medallion, diamond-dot surround — replaces previous generic crown icon

---

## [2.6.43] — 2026-03-18
### Fixed
- Festive icon size increased from 1.5rem to 2.8rem — was barely visible beside the header title at the previous size
- Margin increased from 0.5rem to 0.75rem for better spacing at the larger size

---

## [2.6.42] — 2026-03-18
### Changed
- Rewrote `ramadan.svg` based on reference: purple and gold hanging lantern (fanous) with arched windows and four-pointed sparkle stars
- Rewrote `aidilfitri.svg` based on reference: green and gold mosque sitting inside a crescent moon, with minarets, doorway arches, and gold stars
- Rewrote `aidiladha.svg` based on reference: blue and gold mosque with large dome, two minarets with gold finials, three doorway arches, and gold stars

---

## [2.6.41] — 2026-03-18
### Fixed
- Complete ms-MY translation coverage: 501 strings filled, 0 empty (was 367 filled, 132 empty)
- Translated all 86 editorial prose paragraphs from policy/legal templates (privacy policy, disclaimer, editorial policy, corrections, about page, meaning-of-6)
- Translated all 49 UI strings (admin hints, field descriptions, list items, view stats labels)
- Added 2 strings that were missing from the `.po` file entirely (`What happens here…`, `What makes us who we are…`)
- Recompiled `ms_MY.mo` (504 entries, 79 KB)

---

## [2.6.40] — 2026-03-18
### Changed
- Rewrote `merdeka.svg` and `malaysia-day.svg` to match the actual Jalur Gemilang: 14 red/white stripes, dark blue canton (#010066), yellow crescent and 14-pointed star (#fc0) — based on official flag reference

---

## [2.6.39] — 2026-03-18
### Fixed
- **Brand colour consistency**: `--up6-red-lt` in `:root` was a circular self-reference (`var(--up6-red-lt)`) that resolved to nothing in light mode — "SUARA SEMASA" appeared invisible in the header and inline brand marks; fixed to `#e05a4e`
- **`--up6-gold-dk`** had the same circular self-reference bug; fixed to `#B8880F` in both `:root` and dark mode blocks
- Consolidated 5 hardcoded `#e05a4e` values across footer and dark mode overrides into `var(--up6-red-lt)` — single source of truth for the brand subtitle colour
- Added brand colour contract documentation comment in CSS design tokens section

### Added
- **Festive occasion icon system**: colourful SVG icon beside the header logo for Malaysian public holidays
- 17 hand-crafted colourful SVG icons in new `/icons/` directory: Hari Raya Aidilfitri, Hari Raya Haji, Ramadan, Maal Hijrah, Israk & Mikraj, Nuzul al-Quran, Maulid Nabi, Hari Kebangsaan, Hari Malaysia, Hari Keputeraan YDP Agong, Tahun Baru Cina, Deepavali, Thaipusam, Hari Wesak, Krismas, Tahun Baharu, Hari Pekerja
- Theme Options → General tab: "Festive Occasion" dropdown (17 options + None), optional "Show from" and "Show until" date pickers for automatic date-range visibility
- `up6_festive_occasions()` helper returning the full keyed occasion list
- `up6_festive_icon()` helper: reads theme_mod, validates slug against whitelist, checks date range in site timezone, loads SVG from `/icons/`, outputs inside `<span class="festive-icon">`
- `.festive-icon` CSS: inline-flex alignment, 1.5rem sizing, subtle scale-in animation, hidden below 480px
- Complete ms-MY translations for all new strings (17 occasion labels + 6 UI labels)

---

## [2.6.36] — 2026-03-17
### Changed
- Documentation updated: README General tab, SEO section, and Yoast/RankMath compatibility note updated to reflect all changes since v2.6.21; CHANGELOG entries added for v2.6.23–2.6.35

---

## [2.6.35] — 2026-03-17
### Fixed
- `wp_robots` filter now skips when Yoast SEO or RankMath is active — previously the theme's `noindex` logic would run alongside the plugin's own robots handling, risking silent conflicts

---

## [2.6.34] — 2026-03-17
### Added
- **noindex toggle** — new **Theme Options → General → noindex search results and policy pages** checkbox (on by default); controls whether the `wp_robots` filter applies `noindex` to search result pages and the Privacy Policy, Disclaimer, and Corrections page templates; skipped automatically when Yoast SEO or RankMath is active

---

## [2.6.33] — 2026-03-17
### Added
- **Stage 2 — SEO additions:**
  - `<link rel="preconnect">` hints for `fonts.googleapis.com` and `fonts.gstatic.com` at `wp_head` priority 1 — eliminates one DNS + TCP round-trip on first visit
  - `<link rel="canonical">` on all front-end pages via `wp_get_canonical_url()` at priority 5; skipped when Yoast SEO or RankMath is detected
  - `noindex` via `wp_robots` filter on search result pages and Privacy Policy, Disclaimer, Corrections page templates
  - `<link rel="prev">` / `<link rel="next">` on paginated archives, search results, and blog index
- **Stage 4 — Code quality:**
  - `$clamps` array in `includes/theme-options.php` replaces 8 individual `if` blocks — same behaviour, one-line to add a new clamped option
  - `js/up6-grid.js` indentation converted from tabs to 4 spaces (consistent with `navigation.js`)

---

## [2.6.32] — 2026-03-17
### Fixed
- Rebuilt clean from 2.6.21 base; applied 2.6.23 changes plus the four confirmed-safe fixes:
  - View counter hook moved from `wp_head` (priority 1) to `wp`
  - Share URL variables in `single.php` wrapped in `esc_attr()`
  - `up6_faq` CPT `show_in_rest` set to `false`
  - `$author_url` variable wired into JSON-LD author schema `url` field

---

## [2.6.23] — 2026-03-16
### Changed
- **Most Viewed sidebar panel redesigned:**
  - Rank number: large dim ghost numeral → zero-padded bold red `01`–`05`
  - Layout: title first, metadata row below, thumbnail floated right (absent gracefully when no image)
  - View count with eye icon and formatted number (`1.2k` style) added beside time
  - Time string: `human_time_diff . ' ' . 'ago'` → `printf('%s ago')` translating correctly as `%s yang lalu`
  - Heading: red left-border accent added
  - Category kicker removed from items
- Translation: "Most Viewed Last %d Day/Days" → "Most Read Last %d Day/Days"; `%s ago` / `%s yang lalu`; `views` / `tontonan` added

---

## [2.6.18] — 2026-03-14
### Fixed
- **Search no-results Malay translation refined** — changed from "Tiada artikel sepadan dengan carian anda. Cuba ejaan berbeza atau istilah yang lebih luas." to "Tiada artikel yang sepadan dengan carian anda. Cuba gunakan ejaan yang berbeza atau istilah yang lebih luas." (added "yang" and "gunakan" for more natural Malay phrasing)

---

## [2.6.21] — 2026-03-14
### Added
- **Malaysian flag gold accent** — introduced `--up6-gold: #D4A017` design token representing the Jalur Gemilang yellow (crescent and star), completing all four flag colours in the theme palette (blue, red, white, gold); applied in three places:
  - **Hijri crescent icon** — byline metadata crescent moon changed from red to gold (`opacity: 0.85`), symbolically matching the flag's crescent; dark mode: `#E8C84A`
  - **Ornamental section divider diamond** — the central diamond `<path>` in the Nusantaran motif now strokes in gold (`opacity: 0.6`), giving it a manuscript-illumination quality; branching lines and terminal dots remain in muted blue; dark mode: `#E8C84A` at `0.4`
  - **Footer top border** — changed from `8px solid red` to a dual stripe via `border-image: linear-gradient(to bottom, red 50%, gold 50%)` — 4px red over 4px gold, echoing the flag's red-and-yellow striping; visible on every page

---

## [2.6.20] — 2026-03-14
### Fixed
- **Search no-results — text cramped** — widened `.search-empty` container from `max-width: 480px` to `640px`; the message now sits on one or two lines instead of wrapping into a narrow three-line block

---

## [2.6.19] — 2026-03-14
### Fixed
- **Search no-results — Malay translation refined** — updated to owner's preferred wording: "Tiada artikel yang sepadan dengan carian anda. Cuba gunakan ejaan yang berbeza atau istilah yang lebih luas penggunaannya."

---

## [2.6.17] — 2026-03-14
### Fixed
- **Search no-results — misleading message** — old text said "browse by category below" but no categories were shown on the page; replaced with "No articles matched your search. Try a different spelling or a broader term." / "Tiada artikel sepadan dengan carian anda. Cuba ejaan berbeza atau istilah yang lebih luas."; `.mo` recompiled (482 strings)

---

## [2.6.16] — 2026-03-14
### Fixed
- **Search no-results — duplicate search box** — removed the inline search form from the empty state; the header search bar already contains the query and is visible on every page, making the second form redundant; "Back to Home" demoted from a large block button (`error-404-btn`) to a subtle text link (`search-empty-home`); dead `.search-empty .search-form` CSS removed (~40 lines); spacing tightened

---

## [2.6.15] — 2026-03-14
### Fixed
- **Search permalink 404** — WordPress needs a rewrite rules flush to register the new `/carian/` search base; added `flush_rewrite_rules()` on `after_switch_theme` (fires on theme activation) and a version-keyed one-time flush on `init` (fires automatically on first page load after a theme update, using `up6_flush_{version}` option to prevent repeat flushes)

---

## [2.6.14] — 2026-03-14
### Added
- **Search permalink rewrite** — replaces the Pretty Search Permalinks plugin; rewrites `/?s=query` to `/carian/query` (translatable via `.po` — English: `search`, Malay: `carian`); 15 lines of theme code replacing a standalone plugin; two hooks: `init` sets `$wp_rewrite->search_base`, `template_redirect` redirects old-style URLs
- **Subtitle / dek line** — replaces the Secondary Title plugin (7.5MB, 350 files); adds a "Subtitle" meta box in the post editor sidebar; stores value in `_up6_subtitle` post meta; displayed between the headline and the excerpt on single posts in italic Source Serif 4; `up6_get_subtitle()` helper function available globally; nonce-verified save handler with capability check; ~40 lines replacing 2,240 lines of plugin code
- **Content copy protection (optional)** — replaces WP Content Copy Protector plugin; toggle at Theme Options → General → "Enable content copy protection"; disabled by default; when enabled: disables right-click context menu, text selection (`user-select: none`), and Ctrl+C/A/U/S keyboard shortcuts for non-admin visitors; skips logged-in editors and administrators; allows selection in form inputs and contenteditable elements; inline CSS + JS output (no external files); all labels translatable
- 6 new ms_MY translations; `.mo` recompiled (475 → 481 strings)

---

## [2.6.13] — 2026-03-14
### Fixed
- **View count translation** — changed Malay translation of "%s reads" from "bacaan" (reading material) to "capaian" (views/reach), which is the correct analytics term
- **View count minimum threshold** — view count now only displays when ≥100 views; low single/double-digit counts undermined social proof rather than enhancing it

---

## [2.6.12] — 2026-03-14
### Changed
- **Single post header — collapsed byline** — restructured from six visual layers (breadcrumb → badge → title → author → metadata → share) into three (badge+title → byline → share); author avatar, name, and all metadata items (date, Hijri, updated, views, reading time) now sit in a single compact block aligned to the avatar; share bar sits directly beneath with minimal gap; removed `max-width: 54rem` constraint from byline and share bar — both now span the full 64rem header width, aligning left edges with the title; eliminates the visual disconnection between the wide title and the indented byline; featured image moves significantly higher above the fold

---

## [2.6.11] — 2026-03-14
### Fixed
- **Related News card balance** — added 3-line clamp on `.card-excerpt` (`-webkit-line-clamp: 3`) and `.article-card h3` so all cards in a row render at equal height regardless of title or excerpt length; eliminates the ragged bottom-edge across card rows

### Changed
- **Related News red accents** — section top border changed from 1px neutral grey to 2px accent red; section header now includes the red dot (`section-dot`) matching homepage section headers (● BERITA BERKAITAN); card author/date middot separator changed from neutral `#bbb` to red-tinted `rgba(192,57,43,.35)` matching byline separators; card title and excerpt line-clamped for visual balance

---

## [2.6.10] — 2026-03-14
### Changed
- **Byline red accents** — metadata middot separators changed from neutral grey (`#ccc`) to red-tinted (`rgba(192,57,43,.35)`), echoing the nav bar date separator; "Share" label changed from grey to accent red (`--up6-red`), matching the red-dot section header vocabulary; dark mode variants updated to `rgba(224,90,78,.3)` for separators and `#e05a4e` for the share label

---

## [2.6.9] — 2026-03-14
### Changed
- **Single post byline — enhanced metadata block** — redesigned the meta bar into a two-row byline: author avatar + name on row 1, metadata items with custom SVG icons on row 2; new data points: published date with time (calendar icon), Hijri date (crescent moon icon), last-updated timestamp shown only when modified >24h after publication (rotating arrows icon), view count from `_up6_views` shown when >0 (eye icon), reading time (open book SVG replacing 📖 emoji); all items separated by CSS middot separators; meta bar and share bar constrained to 54rem reading column width (matching content); per-icon accent tints (red for Hijri, green for updated, blue for views); full dark mode support; cleaned up dead `.entry-date` and `.entry-reading-time` responsive overrides; 2 new ms_MY translations ("Dikemaskini %s", "%s bacaan"); `.mo` recompiled (473 → 475 strings)

---

## [2.6.8] — 2026-03-14
### Fixed
- **Abstract Box plugin — title gap** — the theme's `.entry-content h2/h3` rule (`margin: 2rem 0 1rem`) had higher specificity than the plugin's `.abstract-box__title` (`margin: 0 0 6px`), causing a ~32px gap between the box border and the title; added scoped reset for all heading levels and paragraph margins inside `.abstract-box` when rendered within `.entry-content`

---

## [2.6.7] — 2026-03-14
### Fixed
- **Related News card design mismatch** — cards in the Related News section now use identical markup to homepage category cards: single primary category in uppercase with `card-category` class, `ss-card` image size (was `medium`), `<div>` wrapper for thumbnail (was bare `<a>`), author name + middot separator + date in meta line (was date only), excerpt paragraph (was missing); visual language is now consistent across homepage, archives, and related news

---

## [2.6.6] — 2026-03-14
### Added
- **Social share bar — 4 additional platforms** — added Threads, LinkedIn, Reddit, and Pinterest to the share bar (now 10 buttons total: WhatsApp, Telegram, Facebook, X, Threads, LinkedIn, Reddit, Pinterest, Email, Copy Link); brand-coloured hover states for each (Threads black, LinkedIn #0A66C2, Reddit #FF4500, Pinterest #E60023); share button row now flex-wraps gracefully on narrow screens; 4 new ms_MY translations ("Kongsi di Threads/LinkedIn/Reddit/Pinterest"); `.mo` recompiled (469 → 473 strings)

---

## [2.6.5] — 2026-03-14
### Added
- **Social share bar** — horizontal row of share buttons below the byline on single posts: WhatsApp, Telegram, Facebook, X, Email, Copy Link; zero-plugin implementation using native platform share URLs and inline SVG icons; "Copy link" uses `navigator.clipboard` with visual tick confirmation; platform-coloured hover states; full dark mode support; all labels translatable (8 new ms_MY strings: "Kongsi", "Kongsi di WhatsApp", etc.); slightly larger touch targets on mobile (2.5rem); `ms_MY.mo` recompiled (461 → 469 strings)

---

## [2.6.4] — 2026-03-14
### Changed
- **Single post layout — stepped editorial hierarchy** — the entry header (category badge, title, excerpt, author/date meta bar) now spans a wider column (64rem / ~1024px) while the featured image, body content, tags, related news, and comments remain at the narrower reading width (54rem / ~864px); this creates a visual step-down from the commanding headline into the focused reading column, a pattern common in premium editorial design

---

## [2.6.3] — 2026-03-14
### Changed
- **README.md** — complete rewrite as a comprehensive technical reference for developers, web hosting, and deployment: requirements table with PHP/WP/MySQL versions, WP-CLI installation commands, full design system reference (colour tokens, dark mode overrides, typography, layout variables, image sizes), complete configuration guide with every Theme Options field/key/default/range/sanitiser, all features documented with implementation detail, full template file listing with layout/sidebar columns, asset structure with line counts and dependency chains, CSS architecture strategy and breakpoint reference, PHP helper function signatures and return types, structured data and SEO output reference, i18n workflow with recompile commands, development standards (security, accessibility, file naming), page template creation recipe, and versioning policy
- **readme.txt** — rewritten for general audience: plain-language feature list, step-by-step getting started guide, expanded FAQ with practical answers for non-technical users

---

## [2.6.2] — 2026-03-14
### Changed
- **Single post column width unified** — replaced the split layout (780px body + 960px image breakout) with a single 54rem (~864px) column shared by all elements: featured image, entry header, body text, abstract/summary boxes, cite blocks, topic tags, related news, and comments all align to the same width

---

## [2.6.1] — 2026-03-14
### Fixed
- **Logo wiggle disabled on mobile** — the bounce animation on the header logo icon and custom logo image now only fires on pointer devices (desktop); on touch screens (≤768px) it is suppressed to avoid unintentional animation on tap

---

## [2.6] — 2026-03-14
### Changed
- **Single post layout** — removed sidebar from single posts; article now renders full-width with a centred 780px reading column; featured image breaks out to 960px for visual impact; related news and comments match article width; removed redundant Pilihan exclusion query (saves one `WP_Query` per page load)

### Fixed
- **CSS syntax error** — removed stray closing brace at `style.css` line 1099 that silently broke rules after `.article-card h3` (card headings, excerpts, dates, and downstream selectors affected)
- **JS scope leak** — dark mode toggle and scroll progress bar code in `navigation.js` was outside the IIFE, leaking 6 variables (`themeToggle`, `html`, `STORAGE_KEY`, `progressBar`, `ticking`, `updateProgress`) into global scope; now properly enclosed
- **Mobile search placeholder mojibake** — fixed double-encoded UTF-8 ellipsis (`â¦` → `…`) in `header.php` mobile search input
- **Stale `ms_MY.mo`** — recompiled binary translation file from 360 → 461 strings; ~100 translations added since the last compile (including Most Viewed sidebar heading) were present in `ms_MY.po` but missing from the binary, causing English fallback on affected strings
- **Translation: "Six Commitments"** — changed Malay translation from "Enam Komitmen" to "Enam Akujanji" (heading and running text in Meaning of 6 page)

### Added
- **`css/mobile-patch.css`** — supplementary mobile-first stylesheet loaded after `style.css`; contains scoped responsive fixes identified in full mobile-first audit:
  - Content overflow protection (`overflow-wrap: break-word`, horizontal-scroll tables/code/iframes)
  - Comment nesting overflow fix (reduces indent at 540px and 380px)
  - Mobile font size floor (bumps 13 sub-10px declarations to readable minimums)
  - Touch target improvements (`min-height: 44px` on nav links, footer links, tags, buttons)
  - Entry content mobile parity (font reduction on single posts, matching policy pages)
  - Progressive `--up6-pad` reduction at 540px
  - Masonry float clearfix
  - `focus-visible` keyboard navigation outlines on all interactive elements
  - Hero card refinement at 380px
  - Sidebar unstick below 960px (static position when stacked below content)
  - Print exclusion (patch styles don't bleed into print output)

---

## [2.5.112] — 2026-03-13
### Fixed
- **Search no-results layout** — removed all hacky `body.search-no-results` overrides; `search.php` now splits into two clean branches: results-found (hero + breadcrumb + grid, unchanged) and no-results (uses `div.site-content-inner.error-404-content` exactly like `404.php`); footer gap resolves naturally because the 404 layout already works correctly
- **Search no-results — search form styling** — added `.error-404-content .search-form` CSS block so the re-used search form inherits correct UP6 input styling (Source Serif 4, correct border radius, dark mode, focus ring)
- Removed all dead `.search-no-results`, `body.search-no-results` CSS that accumulated across 2.5.107–2.5.111

---

## [2.5.111] — 2026-03-13
### Fixed
- **Search no-results footer gap (attempt 3)** — added `body.search-no-results .site-content-inner { padding-bottom:0 }` and `body.search-no-results .search-main { padding-bottom:0 }` to zero ~96px dead space below card; partially resolved but superseded by full rewrite in 2.5.112

---

## [2.5.110] — 2026-03-13
### Fixed
- **Search no-results footer gap (attempt 2)** — added `body.search-no-results #content { flex:none }` to collapse the global `flex:1` that was pushing the footer to the bottom of the viewport on sparse pages; WordPress automatically adds `search-no-results` body class on empty searches

---

## [2.5.109] — 2026-03-13
### Fixed
- **Search no-results layout broken (regression from 2.5.108)** — reverted `:has(.search-no-results)` flex rule which was halving card width; restored simple `margin: 2rem auto` centering

---

## [2.5.108] — 2026-03-13
### Fixed
- **Search no-results footer gap (attempt 1)** — replaced `min-height:40vh` with `:has(.search-no-results)` flex rule on `.search-main`; introduced card width regression, reverted in 2.5.109

---

## [2.5.107] — 2026-03-13
### Added
- `template-corrections.php` — Pembetulan corrections policy page; policy statement only (no live log); seven sections: Our Commitment, What We Correct, How Corrections Are Made, Significant Corrections, How to Submit, Right of Reply, Editorial Accountability; registered in `theme_page_templates` filter, meta box list, and `save_post_page` allowlist
- All seven secondary-nav page templates now complete: Mengenai, Maksud 6, Dasar Editorial, Dasar Privasi, Penafian, Hubungi Kami, Pembetulan
### Fixed
- **Search no-results state** — rebuilt as a centred card with red search icon, failed query shown in message, dark mode, box-shadow; `min-height:40vh` added to `.search-main` to prevent footer floating mid-page on sparse results (approach later superseded in 2.5.110–2.5.112)

---

## [2.5.106] — 2026-03-13
### Added
- `template-contact.php` — Hubungi Kami contact page: two-column layout (intro + NAP left, CF7 form right), full-width Google Maps embed below, admin-only notices when CF7 or map credentials are missing; stacks to single column at ≤900px
- **Theme Options → Contact tab** — three new fields: CF7 Form ID (`up6_cf7_form_id`, absint), Google Maps Embed API Key (`up6_maps_api_key`, text), Google Maps Place ID (`up6_maps_place_id`, text); tab inserted between Homepage and General
- **NewsMediaOrganization JSON-LD** — `up6_contact_schema_json_ld()` fires on `wp_head` at priority 6, only on `template-contact.php`; outputs `@id`, name, url, address (PostalAddress), telephone, email, contactPoint (customer support, areaServed MY), logo (ImageObject), sameAs (all non-empty social URLs from Theme Options)
- NAP block uses microdata `itemscope/itemprop` as secondary schema layer alongside JSON-LD
- Template registered in `theme_page_templates` filter, meta box list, and `save_post_page` allowlist

---

## [2.5.105] — 2026-03-13
### Added
- `template-disclaimer.php` — Penafian legal disclaimer page; registered in all three required locations in `functions.php`

---

## [2.5.70] — 2026-03-13
### Changed
- Copyright Line and Legal / Ownership Notice fields now support basic HTML (`<a>`, `<strong>`, `<em>`, and other tags permitted by `wp_kses_post`)
  - Sanitisation changed from `sanitize_text_field` (strips all HTML) to `wp_kses_post` (allows safe subset)
  - Output in `footer.php` changed from `esc_html()` to `wp_kses_post()` for both fields
  - Admin fields changed from `<input type="text">` to `<textarea>` (2 rows copyright, 3 rows legal) to give room for markup
  - Hint text on both fields updated to state HTML is supported with examples

---

## [2.5.75] — 2026-03-13
### Added
- `template-dasar-editorial.php` — custom page template for the Editorial Policy page; all content is hardcoded in PHP so it is version-controlled with the theme rather than stored in the database
- Assign via Page Attributes → Template: "Dasar Editorial" on any static page
- Includes WebPage JSON-LD schema block
- `.policy-*` CSS block in `style.css` — shared base for all future policy page templates (Dasar Privasi, Terma Penggunaan, etc.)
- 14 new translatable strings added to `ms_MY.po` / `up6.pot`; `ms_MY.mo` recompiled (150 strings)

---

## [2.5.85] — 2026-03-13
### Fixed
- **Pilihan sidebar — wide line spacing on wrapped titles** — `.featured-panel-title` was an inline `<span>`; inline elements don't control their own line box height so `line-height: 1.25` was being overridden by the inherited body line-height; changed to `display: block` so `line-height: 1.3` applies correctly, restoring compact title appearance
- **Orphaned CSS** — missing closing brace on `.featured-panel-title` left `transition: color 0.15s` and a stray `}` floating as invalid CSS; moved transition inside the rule

---

## [2.5.84] — 2026-03-13
### Fixed
- **CSS corruption** — `.policy-content h2` rule was missing its body and closing brace (introduced by a bad str_replace in 2.5.83); all h2 styles (uppercase DM Sans 900, border-bottom, etc.) were silently dropped; fully restored
- **Inconsistent typography vs About page** — `.policy-content p` and `.entry-content p` now both use `text-align: justify` to match the About page body text treatment
- **Policy title smaller than page titles** — `.policy-header .entry-title` was capped at `2.5rem`; changed to match standard `clamp(1.75rem, 4vw, 3rem)`
- **Paragraph margin inconsistency** — `.policy-content p` margin-bottom aligned to `1.5rem` to match `.entry-content p`
- Policy CSS block comment updated to reflect current file naming convention

---

## [2.5.83] — 2026-03-13
### Fixed
- **Policy pages wrong font** — `.policy-content` had no `font-family` set, falling back to the body sans-serif instead of Source Serif 4; added `.policy-content { font-family: var(--up6-serif); font-size: 1.125rem; line-height: 1.85; color: rgba(27,60,83,.82) }` to match `page.php` / `.entry-content` rendering
- **Policy pages not full width** — `.policy-main` had `max-width: 740px` which constrained the layout inside `.site-content-inner`; removed the inner max-width so policy pages fill the same content column as standard pages
- Added dark mode rule `html.up6-dark .policy-content` for base text colour

---

## [2.5.82] — 2026-03-13
### Fixed
- **PHP page templates still not selectable in block editor** — the block editor's "Change template" UI in a block theme only lists `.html` block templates; it does not use `theme_page_templates` filter output for its switcher UI regardless of WordPress version
- Added `up6_page_template_meta_box` — a classic meta box on the page sidebar (position: side/high) with a `<select>` listing all UP6 PHP templates; writes directly to `_wp_page_template` post meta (the key WordPress resolves at `template_include` time); verified with nonce, capability check, and allowlist sanitisation
- Meta box appears in both the block editor sidebar and the classic editor; works independently of block theme template infrastructure

---

## [2.5.81] — 2026-03-13
### Fixed
- **PHP page templates not appearing in block editor** — TT25 is a block theme; its block editor template UI manages `.html` block templates only and does not surface PHP `Template Name` headers through the normal dropdown; fixed by registering all three PHP templates explicitly via the `theme_page_templates` filter in `functions.php`; they now appear under Template → Change template in the page sidebar
### Note
- `Template Post Type: page` in the file header remains correct and should be kept — it is still used by the classic editor and by `WP_Theme::get_page_templates()`; the filter is additive, not a replacement

---

## [2.5.80] — 2026-03-13
### Changed
- Press Freedom section in `template-editorial-policy.php` now closes with a "Further reading" link to *Polis Raja di Malaysia* (langgamfikir.my) — the intellectual grounding for the section
- 2 new strings added to `ms_MY.po`; `ms_MY.mo` recompiled (246 strings)

---

## [2.5.79] — 2026-03-13
### Added
- **Press Freedom section** added to `template-editorial-policy.php` (approved wording); 3 new strings added to `ms_MY.po` with Malay translations; `ms_MY.mo` recompiled (244 strings)
### Documented
- `README.md` — Development Standards section updated: `Template Post Type: page` added to template creation guide; MO compiler 7-field header requirement documented with correct `struct.pack` format
- `UPGRADING.md` — v2.5.77–2.5.79 upgrade note expanded with full standards reference: file naming table, template header requirements, i18n PHP convention with correct/wrong examples, MO compiler warning with code sample
- Standards in both documents now explicitly cover all future template and translation work

---

## [2.5.78] — 2026-03-13
### Fixed
- **MO compiler bug** — header was packed with `'<IIIIII'` (6 fields = 24 bytes) instead of the correct `'<IIIIIII'` (7 fields = 28 bytes); the missing `hash_offset` field shifted every string offset by 4 bytes, silently corrupting all translations; recompiled `ms_MY.mo` (244 strings, verified)
- **Page templates not appearing in dropdown** — all three template files (`template-faq.php`, `template-editorial-policy.php`, `template-privacy-policy.php`) were missing the `Template Post Type: page` file header; required by WordPress when the parent theme is a block theme (TT25); added to all three

---

## [2.5.77] — 2026-03-13
### Fixed
- `template-editorial-policy.php` and `template-privacy-policy.php` had Malay hardcoded as source strings instead of English — violated the en-US source / ms-MY translation convention; both templates rewritten with English `msgid` strings
- Removed stale `template-dasar-editorial.php` entries (Malay-sourced) from `ms_MY.po`
### Changed
- `ms_MY.po` / `up6.pot` updated with correct en-US source strings and Malay translations for all editorial policy and privacy policy content (240 strings total)
- `ms_MY.mo` recompiled

---

## [2.5.76] — 2026-03-13
### Added
- `template-privacy-policy.php` — PDPA-compliant privacy policy page template; covers data collection, cookies, third-party sharing, user rights under PDPA 2010, data retention, children's policy, and amendments; links to `/contact/`

---

## [2.5.75] — 2026-03-13
### Added
- `template-editorial-policy.php` — page template for `/editorial-policy/`; uses existing `.policy-*` CSS; full Malay editorial policy content with i18n strings; links to `/contact/`; shows last-modified date via `get_the_modified_date()`

---

## [2.5.74] — 2026-03-13
### Fixed
- **Card dot misalignment** — `.article-card .card-category { width: 100% }` was forcing the category link onto a new flex row, leaving the dot stranded alone above the text; replaced with `line-height: 1` so dot and category stay on the same row
- **Section header dot misalignment** ("N ARTIKEL") — `<h2>` was inheriting body `line-height: 1.75`, causing `align-items: center` to centre on the full line box rather than the cap-height; fixed with `line-height: 1` on `.section-header h1, h2`
- Added `align-self: center` to `.card-dot-sm` and `line-height: 1` to `.card-category` globally for consistent dot-to-text optical alignment

---

## [2.5.73] — 2026-03-13
### Changed
- Primary nav dropdown arrowheads changed from `rgba(255,255,255,.5)` to `var(--up6-red)` in both resting and open states

---

## [2.5.72] — 2026-03-13
### Fixed
- Anchor colours unreadable in `.footer-copyright` and `.footer-legal-text` — no `a` rules existed, so links inherited the theme default (deep blue or red), both invisible on the dark footer background
- Both areas now get explicit link styling: `rgba(255,255,255,.5)` / `rgba(255,255,255,.55)` resting, beige on hover, with `text-decoration: underline` and `text-underline-offset: 2px` for legibility

---

## [2.5.71] — 2026-03-13
### Fixed
- **Copyright Line** and **Legal / Ownership Notice** hint text was stale — the old msgids no longer matched the strings in the PHP, so the hints were falling through in English even on Malay installs
- Both hint strings rewritten to explicitly state HTML support and list the permitted tags (`<a href="...">, <strong>, <em>, <br>`)
- `ms_MY.po` and `up6.pot` updated with new msgids; Malay translations added for both; `ms_MY.mo` recompiled (136 strings, unchanged count — replaced two stale entries)
### Confirmed
- Both fields use `wp_kses_post` on save and output — HTML is fully supported

---

## [2.5.70] — 2026-03-13
### Fixed
- Missing Malay translations for the three social URL fields added in v2.5.65 — "Instagram URL" → "URL Instagram", "Threads URL" → "URL Threads", "WhatsApp URL" → "URL WhatsApp"
- `ms_MY.po`, `up6.pot`, and compiled `ms_MY.mo` updated — string count 133 → 136

---

## [2.5.69] — 2026-03-13
### Changed
- Legal notice bar (`.footer-legal`) restyled — Option A + Option D
  - **Option A:** `background: rgba(0,0,0,.25)` creates a visibly darker tinted band, distinct from the footer bar above; padding extended to `0.75rem` with full horizontal gutter alignment via `var(--up6-pad)`
  - **Option D:** Small scales-of-justice SVG icon (13×13, stroke style) prepended to the notice text via `.footer-legal-icon`; icon uses `color: var(--up6-beige)` at 35% opacity to stay understated
  - Added `.footer-legal-inner` flex wrapper to centre icon + text as a unit, constrained to `var(--up6-max)` for alignment with the rest of the footer

---

## [2.5.68] — 2026-03-13
### Fixed
- Footer bar nav and copyright not vertically centred within the bar
  - Moved padding from `.footer-bar` onto `.footer-bar-inner` (`padding: 0.875rem var(--up6-pad)`) so the flex container owns its own breathing room and `align-items: center` operates correctly
  - Added `min-height: 2.75rem` to `.footer-bar-inner` to guarantee a consistent bar height
  - Added explicit `margin: 0; padding: 0; list-style: none` to `.footer-utility-nav li` — browser and WP default `<li>` margins were offsetting the flex alignment
  - Added `line-height: 1; display: inline-block` to nav links and `line-height: 1` to copyright to ensure both text nodes sit on the same optical baseline

---

## [2.5.67] — 2026-03-13
### Changed
- Theme Options header digit accent (e.g. "6") changed from red to beige `#C4B5A5` with `font-weight: 900` — matches front-end colour while remaining legible on the white admin background

---

## [2.5.66] — 2026-03-13
### Fixed
- Theme Options page header title now visually matches the front-end site title treatment
  - `@import` for DM Sans weight 900 added to `css/admin-options.css` (was falling back to Inter/system fonts)
  - `h1` updated: `font-weight: 900`, `text-transform: uppercase`, `letter-spacing: -0.03em`, `font-size: 1.35rem` — matching front-end `.site-title` rules exactly
  - Subtitle ("SUARA SEMASA") now renders in `#d4564a` — same coral-red as front-end `.site-title-sub`
  - Digit accent ("6") now renders in `#C0392B` (brand red) — beige `#C4B5A5` is the front-end value but is unreadable on the white admin background; red preserves the same visual purpose

---

## [2.5.65] — 2026-03-13
### Added
- Instagram, Threads, and WhatsApp social link fields in Theme Options → Social Media tab
- Corresponding SVG icons in `footer.php` social icon row (stroke-based, consistent with existing set)
### Removed
- RSS Feed URL field from Theme Options → Social Media tab (RSS is auto-generated by WordPress; a manual URL field here was redundant)
- `ss_social_rss` option key removed from defaults and sanitisation map in `includes/theme-options.php`
### Changed
- Social links order in footer: Facebook → X → Instagram → Threads → Telegram → WhatsApp

---

## [2.5.64] — 2026-03-13
### Changed
- Theme Options page header title now renders the live site name (`get_bloginfo('name')`) instead of the hardcoded string "UP6 Theme Options", matching the front-end logo treatment — first word with trailing digits accented in red (`#C0392B`), remainder in a subdued weight; uses inline styles since wp-admin does not load theme CSS variables

---

## [2.5.63] — 2026-03-13
### Fixed
- Theme Options tab bar alignment and colour hierarchy (`css/admin-options.css`)
  - `flex: 0 1 auto` → `flex: 1` so all five tabs share the row width equally instead of huddling left
  - `margin-bottom: -1px` on each tab so the active red underline sits flush on top of the container border rather than doubling it
  - Inactive tab colour changed from `--up6a-mid` (#2E5871, strong blue) to `#9aacb8` (soft slate) — reduces visual noise and makes the active state read clearly without competition
  - Font size `0.7rem` → `0.65rem`; letter-spacing `0.08em` → `0.1em` to keep all-caps labels readable at narrower per-tab widths

---

## [2.5.62] — 2026-03-13
### Fixed
- **Site editor (`site-editor.php`) broken/inaccessible** — root cause: UP6 is a child of TT25 (a block/FSE theme), so WordPress exposes the site editor by default, but UP6 has no `templates/` directory and uses only classic PHP templates; the editor therefore showed TT25's irrelevant block templates or errored
### Changed
- `Appearance → Editor` menu item removed via `remove_submenu_page` (priority 999, after all plugins run)
- Direct navigation to `site-editor.php` redirects to the Customizer (`customize.php`) via `current_screen` hook
- Admin notice displayed on redirect landing explaining why the editor is unavailable and pointing to the Customizer and standard post editor as the correct tools

---

## [2.5.61] — 2026-03-13
### Fixed
- Corrected hardcoded site URL in `css/print.css` from `up6.com.my` to `up6.org` — affected both the masthead line above the article and the "Dicetak dari UP6 Suara Semasa" fixed footer on every printed page

---

## [2.5.60] — 2026-03-13
### Added
- **Print: "Dicetak dari UP6 Suara Semasa" footer line** — `body::after` fixed to bottom of every printed page; `@page` bottom margin extended to `2.8cm` to clear it
- **JSON-LD BreadcrumbList schema** — emitted on every single post in `<head>` alongside the existing NewsArticle schema; mirrors the visible breadcrumb (Home › Category › Post Title); uses `$cat_objects` already resolved by the NewsArticle block, zero extra queries
- **RSS feed enrichment** — `rss2_ns` hook declares `xmlns:media` namespace; `rss2_item` hook adds `<media:content>` with image URL, dimensions, title, and alt text (when available), plus `<category domain="...">` elements for each WP category; makes feed richer for aggregators and Telegram channels
- **Scroll progress bar** — 2px red `#up6-scroll-progress` bar fixed to top of viewport on single posts; width driven by `requestAnimationFrame`-throttled scroll listener in `navigation.js`; `passive: true` scroll event; initialises on load for anchor-linked pages; hidden in print via `css/print.css`
### Changed
- `single.php` — `<div id="up6-scroll-progress">` injected immediately after `get_header()`
- `css/print.css` — `@page` bottom margin `2cm` → `2.8cm`; `body::after` added for print footer
- `js/navigation.js` — scroll progress listener appended
- `css/style.css` — `#up6-scroll-progress` rule added

---

## [2.5.59] — 2026-03-13
### Added
- `css/print.css` — dedicated print stylesheet, loaded with `media="print"` (zero screen rendering cost)
- `@page` margins set to `2cm 2.5cm` for consistent output across browsers
- Masthead attribution line above article via `::before` — site name + URL, red rule separator
- Source URL appended below article via `::after` on `article[data-permalink]` — reads canonical URL from `data-permalink` attribute, prefixed "Sumber:"
- `data-permalink` attribute added to `<article>` in `single.php`
### Changed
- All chrome stripped in print: `.site-header`, `.header-brand-row`, `.header-nav-row`, `#secondary`, `.site-footer`, `.footer-bar`, `.related-news`, `.comments-area`, `.entry-reading-time`
- Layout forced to single-column full-width; `content-area-wrap` grid disabled
- Article structure preserved: breadcrumb (plain text), category badge, title, standfirst, byline (author + date, avatar hidden), featured image (capped at 280pt height), body content, topic tags
- `page-break-inside: avoid` on standfirst, byline, featured image, blockquotes; `page-break-after: avoid` on headings
- `orphans: 3; widows: 3` on body paragraphs
- Inline link URL expansion suppressed in body content — source URL printed once at bottom instead
- All background colours, box shadows, border-radius, transitions stripped globally
- Body links rendered in `#111` with underline; red accents (category badge, standfirst rule, blockquote rule, breadcrumb separator) preserved as they survive monochrome printing

---

## [2.5.58] — 2026-03-13
### Fixed
- Tab buttons in Theme Options now auto-size to content width (`flex: 0 1 auto` + `white-space: nowrap`) instead of equal-width — prevents long labels like "Tag Tersembunyi" from wrapping onto two lines
- Complete ms-MY translation coverage: 133 strings (was 113) — added translations for FAQ CPT labels, author avatar fields, hidden tags admin, Hijri offset hints, excerpt length, dark mode toggle, load more, and all v2.5.x hint text
- Regenerated `up6.pot` and recompiled `ms_MY.mo`

---

## [2.5.57] — 2026-03-13
### Added
- Excerpt word count control in Theme Options → Homepage tab ("Excerpt length (words)", min 10, max 80, default 35)
- `excerpt_length` filter now reads from `up6_excerpt_length` theme_mod instead of hardcoded value

---

## [2.5.56] — 2026-03-13
### Fixed
- Removed CSS `-webkit-line-clamp` from `.card-excerpt` — excerpts no longer cut mid-word at the pixel level
- Increased `excerpt_length` from 28 to 35 words for slightly longer, more readable card excerpts
- Changed `excerpt_more` from HTML entity `&hellip;` to UTF-8 `…` character — prevents double-encoding through `esc_html()`
- Excerpts now always end cleanly at a full word boundary, followed by ` …`

---

## [2.5.55] — 2026-03-13
### Fixed
- Removed `text-overflow: ellipsis`, `white-space: nowrap`, and `max-width` truncation from breadcrumb post titles — full title now always rendered for SEO

---

## [2.5.54] — 2026-03-13
### Fixed
- Removed CSS `-webkit-line-clamp` truncation from article card titles (`.article-card h3`), recent list titles (`.recent-title`), and featured panel titles (`.featured-panel-title`) — titles now render in full, preventing SEO-harmful ellipsis in headings

---

## [2.5.53] — 2026-03-12
### Added
- `includes/hidden-tags.php` — Hidden Tags architecture for suppressing tagged content from all public discovery
- `up6_hidden_tag_ids()` — returns configured hidden tag term IDs (static-cached per request)
- `up6_hidden_tag_slugs()` — resolves IDs to slugs (static-cached per request)
- `up6_is_hidden_tag( $term )` — checks whether a tag ID or slug is hidden
- `up6_save_hidden_tag_ids( $ids )` — saves IDs to `up6_hidden_tags` option and busts caches
- `UP6HiddenTagFilters` class — covers all public exclusion surfaces:
  - `pre_get_posts` — main queries and widget/secondary queries
  - `rest_post_query` — public REST API (editors bypass)
  - `wp_sitemaps_posts_query_args` — XML sitemaps
  - `widget_tag_cloud_args` — tag cloud widget
  - `the_tags` — strips hidden tag links from rendered post output, cleans orphaned separators
- `body_class` filter — strips `tag-{slug}` classes from hidden tags (always active, even with Cipher Gate plugin)
- Admin save handler at `admin_init` priority 5 for Theme Options → Hidden Tags tab
### Notes
- Cipher Gate plugin compatibility guard: all front-end filters stand down automatically when `CG_VERSION` is defined or `cg_hidden_tag_ids()` exists — plugin takes full ownership
- Hidden tag archives are silently suppressed (treated as empty) rather than 404ing
- Option key: `up6_hidden_tags` (comma-separated term IDs)

---

## [2.5.52] — 2026-03-12
### Changed
- Breadcrumb separator `›` colour changed from `#ddd` (light grey) to `var(--up6-red)` — consistent with the site's red accent language

---

## [2.5.51] — 2026-03-12
### Changed
- Ornamental divider spacing tightened — top margin reduced from `0.75rem` to `0.25rem` so the motif sits close to the card grid above it
- `.category-section` bottom margin reduced from `0.5rem` to `0` — all whitespace now falls below the divider toward the next section heading, where the eye needs breathing room

---

## [2.5.50] — 2026-03-12
### Changed
- Homepage refinement pass — no layout changes, spacing/hierarchy/consistency improvements throughout
- **Hero top spacing:** `site-content-inner` padding-top raised to `2.75rem`; hero feels more elevated off the navigation bar
- **Hero overlay:** replaced uniform dark wash with a layered dual gradient — angled `105deg` wash darkens the left text zone while preserving upper-right image visibility; additional bottom-up lift anchors the text floor
- **Hero border-radius:** `0.75rem` → `0.625rem` (10px); more editorial edge, less soft card feel
- **Hero body width:** constrained to `max-width: 62%` on desktop to prevent headline running full image width; full-width on mobile
- **Hero title line-height:** `1.2` → `1.08`; stronger but less blocky wrapping
- **Hero category pill:** now shows primary category only — no competing multi-category pills
- **Hero category-to-headline spacing:** `margin-bottom` on `.hero-category` increased from `0.75rem` to `0.875rem`
- **Hero headline-to-date spacing:** `.hero-title` bottom margin reduced from `0.75rem` to `0.625rem`; date reads as secondary
- **Hero transition:** `0.2s` → `0.15s`; snappier hover response
- **Card hover:** removed `translateY(-2px)` lift; shadow transition `0.2s` → `0.15s`; image scale `1.04` → `1.035`; h3 link hover adds underline with `2px offset`
- **Card h3 line-clamp:** headings now clamped to 2 lines — consistent card height within each row
- **Section header gap:** `margin-bottom` `1.25rem` → `0.875rem`; stronger grouping between heading and grid
- **Pilihan item padding:** reduced from `0.875rem` to `0.625rem` top/bottom; increased density without harming readability
- **Pilihan thumbnail:** reduced from `4×3rem` to `3.5×2.625rem`; consistent with tighter row density
- **Pilihan title line-height:** `1.35` → `1.25`
### Added
- Ornamental section divider system (`section-divider-ornament`) — replaces plain `<hr>` between category sections on homepage
- SVG motif: 40×22px abstract floral — open diamond core, 6 branching strokes, 6 terminal dots — restrained Nusantaran editorial accent
- Motif colour: brand indigo at 28% opacity; dark-mode variant at 22% opacity
- `.sdo-line` flanking lines inherit `var(--up6-border)`; dark-mode variant at 8% white

---

## [2.5.43] — 2026-03-12
### Changed
- Theme Options usage notes improved across all Footer tab fields — Footer Description now explicitly states HTML and shortcode support; Contact Address notes line-break formatting; Phone and Email show format examples; Copyright shows a full example string

---

## [2.5.42] — 2026-03-12
### Changed
- Footer description now supports HTML (`<a>`, `<strong>`, `<br>`, etc.) and WordPress shortcodes — output wrapped with `do_shortcode()` and `wp_kses_post()`
- Footer description sanitiser updated from `sanitize_textarea_field` (stripped all tags) to `wp_kses_post` so HTML is preserved on save
- Footer description wrapper changed from `<p>` to `<div>` to allow block-level HTML children
- Added CSS for `<p>`, `<a>`, `<strong>` inside `.footer-description` styled for the dark footer background

---

## [2.5.41] — 2026-03-12
### Fixed
- "SUARA SEMASA" was rendering pink (`#f2b8b2`) — replaced with `#d4564a`, a proper lighter tint of the brand red `#C0392B`, applied to header, footer, and hover state

---

## [2.5.40] — 2026-03-12
### Changed
- Site tagline font size increased from `0.625rem` to `0.7rem` for legibility

---

## [2.5.39] — 2026-03-12
### Changed
- "SUARA SEMASA" colour changed from dim white to `#f2b8b2` (light red tint) in header and footer
- Site tagline colour changed from accent red to near-white `rgba(255,255,255,.85)`
- Hover stability rules updated to match new colours

---

## [2.5.38] — 2026-03-12
### Changed
- `Requires PHP` updated to `8.2` in `style.css` and `readme.txt`
- `Tested up to` updated to `6.9.3` in `readme.txt`

---

## [2.5.37] — 2026-03-12
### Fixed
- Footer `.site-title-sub` ("SUARA SEMASA") was still set to `var(--up6-red)` — updated to `rgba(255,255,255,.6)` to match header

---

## [2.5.36] — 2026-03-12
### Changed
- Site title colour scheme: "UP" white, "6" beige `#C4B5A5`, "SUARA SEMASA" dim white `rgba(255,255,255,.6)`
- Site tagline colour changed from faint white to accent red `var(--up6-red)`
- Hover rules updated to keep accent and sub colours stable

---

## [2.5.35] — 2026-03-12
### Fixed
- Logo bounce animation (`up6-logo-bounce`) now targets `.custom-logo` (the uploaded image via Appearance → Customize → Site Identity) in addition to the default red icon circle
### Changed
- `Requires at least` and `Requires PHP` added to `style.css` theme header (`6.4` and `7.4` respectively)

---

## [2.5.34] — 2026-03-12
### Added
- Logo hover animation — rubber-band bounce on the red icon circle (`up6-logo-bounce` keyframe): scales up, tilts, oscillates and settles in 0.55s; red drop shadow blooms during animation
- Site title text lifts 1px on hover as a paired micro-interaction
- Pure CSS, no JavaScript; honours `prefers-reduced-motion` automatically

---

## [2.5.33] — 2026-03-12
### Fixed
- Breadcrumb wrapping badly on mobile — "Laman Utama" was breaking onto its own line
- Added `flex-wrap: wrap` so crumbs reflow cleanly across lines
- Last crumb (current page title) now truncates with ellipsis at `11rem` on small phones and `18rem` on wider screens; full title is still in the `<h1>` immediately below
- Separator dots marked `flex-shrink: 0` so they never collapse

---

## [2.5.32] — 2026-03-12
### Fixed
- Dropdown still not appearing despite arrows showing — root cause: CSS spec forbids `overflow-x: auto` + `overflow-y: visible` on the same element; browser silently upgrades `overflow-y` to `auto`, clipping the dropdown
- Removed `overflow` from `.primary-nav-scroll` on desktop entirely; `overflow-x: auto` re-applied only at 769–960px via media query where the nav may be tight

---

## [2.5.31] — 2026-03-12
### Fixed
- Submenus not rendering — `wp_nav_menu()` had `depth: 1` which prevented WordPress from outputting sub-menu HTML at all; changed to `depth: 0` (unlimited)
- `overflow-x: auto` on `<ul>` was implicitly setting `overflow-y: hidden`, clipping absolutely-positioned dropdowns — moved horizontal scrolling to a new `.primary-nav-scroll` wrapper `<div>`
### Changed
- Nav `<ul>` wrapped in `.primary-nav-scroll` div in `header.php`

---

## [2.5.30] — 2026-03-12
### Fixed
- Adjacent nav items appeared joined — the `::after` red bar ran `left: 0; right: 0` (full link width including padding), causing bars on neighbouring items to touch
- Bar now inset `0.5rem` each side with `border-radius: 2px 2px 0 0`
### Added
- Explicit hover `::after` rule — hover bar at 60% opacity, active bar at 100%, so current page vs hovered state are visually distinct

---

## [2.5.29] — 2026-03-12
### Changed
- Author avatar profile field in WordPress admin redesigned to match native WP admin conventions: `<h3>` → `<h2>`, Upload button is now `button-primary`, Remove is `button-link-delete`
- Preview is a 96px circle with `#c3c4c7` border ring (turns WP blue when photo set), with person-icon placeholder when empty
- Button label toggles between "Upload Photo" and "Change Photo" based on state
- Remove button always in the DOM, toggled via JavaScript rather than PHP conditional

---

## [2.5.28] — 2026-03-12
### Added
- `template-faq.php` — WordPress page template (assign via Page Attributes → Template → FAQ Page)
- `up6_faq` custom post type — FAQ items managed at FAQ Items in the admin; title = question, content = answer; ordered by Menu Order
- FAQPage JSON-LD schema — generated from the CPT query, Google Rich Results eligible; output in `<head>` before page content
- Accordion UI using native HTML `<details>`/`<summary>` — zero JavaScript, keyboard accessible, animates open/close via CSS
- Numbered questions (01, 02…) in beige, active item turns red, chevron rotates on open
- Full dark mode support for all FAQ components
- FAQ CSS block appended to `style.css` (~90 lines)
### Notes
- Assign the FAQ Page template to any static page via Page Attributes → Template
- Add FAQ items at FAQ Items → Add New in the WordPress admin
- Control display order via Page Attributes → Order on each FAQ item (lower = first)
- Page excerpt (if set) renders as an intro paragraph below the page title

---

## [2.5.27] — 2026-03-12
### Added
- Custom author avatar upload — each author can set a personal avatar image via Users → Profile → UP6 Author Avatar
- Uses WordPress media library uploader (no plugin required)
- Avatar URL stored in user meta key `up6_avatar`
- Remove button clears the avatar and reverts to fallback
- Gravatar fallback — if no custom avatar is set, the theme checks for a Gravatar registered to the author's email (`d=404`); if no Gravatar exists the image silently swaps to initials via `onerror`
- Initials remain the final fallback as before
### Changed
- `up6_author_avatar()` now accepts `$author_id` (int) instead of `$author_name` (string) — call in `single.php` updated accordingly
- Added `.entry-author-avatar--img` CSS modifier for image avatars (`object-fit: cover`, `font-size: 0`)

---

## [2.5.26] — 2026-03-12
### Fixed
- Excessive gap between RSS and dark mode toggle icons — both were independent flex siblings in `.header-inner` inheriting the `1.5rem` row gap
- Wrapped both icons in a `.header-icons` container with `gap: 0.375rem`; the container itself still participates in the header row with the standard gap
### Changed
- Mobile hide rule updated to target `.header-icons` wrapper instead of individual `.header-icon-btn` elements

---

## [2.5.25] — 2026-03-12
### Fixed
- Mobile header misalignment — on screens ≤768px the search bar and icon buttons are hidden, leaving `justify-content: space-between` with only the hamburger and branding, which pushed the logo to the left with empty space on the right
- `.site-branding` now gets `flex: 1; justify-content: center` on mobile so the logo and site title are centred between the hamburger and the right edge

---

## [2.5.24] — 2026-03-12
### Fixed
- Card meta (author · date) layout broken on narrow cards when author name is long — switched `.card-meta` from `display: flex` to `display: block` so author and date flow as inline text
- Date no longer wraps across two lines — added `white-space: nowrap` to `.card-meta .card-kicker-date`
- Removed redundant bare `.card-kicker-sep` and `.card-kicker-date` rules superseded by scoped `.card-meta` versions

---

## [2.5.23] — 2026-03-12
### Changed
- Article card layout: headline (`<h3>`) now appears above the author name and date on all homepage category grid cards
- Author and date moved out of `.card-kicker` into a new `.card-meta` element placed between the headline and excerpt
- Card order is now: category → headline → author · date → excerpt

---

## [2.5.22] — 2026-03-12
### Fixed
- Card kicker category label now always occupies its own line — added `width: 100%` to `.card-category`
- Previously, short category names (e.g. "GLOBAL", "POLITIK") stayed inline with the author and date, while long names (e.g. "MASYARAKAT") accidentally wrapped — layout was inconsistent across cards
- Author name and date now consistently sit on the second line of the kicker on every card regardless of category name length

---

## [2.5.21] — 2026-03-12
### Added
- Hijri Date Offset setting — Appearance → Theme Options → General
- Dropdown with three options: -1 (one day behind), 0 (astronomical default), +1 (one day ahead)
- Corrects for the ±1 day difference between astronomical calculation and Malaysia's official moon-sighting declaration
- `up6_hijri_offset` option stored as theme_mod, default 0
### Changed
- `up6_hijri_date()` now reads the offset and shifts the timestamp by `offset × DAY_IN_SECONDS` before computing the Hijri date
- Theme Options now has a fourth tab: General
### Notes
- Set to -1 if the displayed Hijri date is one day ahead of the date announced by Malaysian authorities (JAKIM)
- The astronomical algorithm itself is unchanged — the offset is applied to the input timestamp only

---

## [2.5.20] — 2026-03-12
### Changed
- `theme.json`: `defaultPalette` set to `true` — TT5's default colour palette now coexists alongside UP6's brand colours in the block editor colour picker
- Editors can now access TT5's neutral greys, whites, and blacks for post content without developer intervention (callout boxes, pull quotes, table backgrounds, etc.)
- UP6 brand colours remain fully intact and appear first in the picker
### Notes
- No front-end visual change — this only affects the block editor colour picker
- Zero risk change: UP6's CSS uses `--up6-*` tokens exclusively and is unaffected by the palette setting

---

## [2.5.19] — 2026-03-12
### Fixed
- Site title accent ("6" in UP6) not rendering in beige in the footer — `.footer-brand-name .site-title-link .site-title` had specificity `(0,3,0)` which outranked the accent rule at `(0,2,0)`, causing the digit to inherit white
- Header accent rule hardened — now covered by explicit selectors for all nesting contexts, preventing future cascade fragility
### Changed
- Accent selector expanded to four explicit rules covering header and footer in both logo and non-logo states

---

## [2.5.18] — 2026-03-12
### Added
- `NewsArticle` JSON-LD schema on all single posts — `headline`, `description`, `url`, `datePublished`, `dateModified`, `inLanguage`, `author` (Person), `publisher` (Organization + logo), `image` (all registered sizes), `articleSection`, `keywords`, `mainEntityOfPage`
- Open Graph meta tags on all pages — `og:title`, `og:description`, `og:url`, `og:type`, `og:image` (1200×675), `og:locale`, `og:site_name`
- Open Graph article tags on single posts — `article:published_time`, `article:modified_time`, `article:author`, `article:section`, `article:tag` (one per WP tag)
- Twitter Card meta tags — `summary_large_image` when featured image present, `summary` otherwise
- `<meta name="description">` on all pages — from post excerpt, or `wp_trim_words` fallback for posts; tagline for non-post pages
- All meta output via `up6_head_meta()` hooked to `wp_head` at priority 5 (before third-party plugins)
### Notes
- No plugin required — all schema is generated natively from core WP data
- `publisher.logo` populated automatically when a custom logo is uploaded via Appearance → Customize → Site Identity
- Schema passes Google Rich Results Test for `NewsArticle`

---

## [2.5.17] — 2026-03-12
### Added
- Reading time estimator in single post meta bar — format: 📖 5 minit
- `up6_reading_time()` helper in `functions.php` — reusable anywhere in the theme
- ms_MY translations: "minit", "Anggaran masa membaca" (90 strings total)
### Notes
- Based on 200 words per minute; minimum displayed is 1 minit
- Appears flush-right in the meta bar, opposite the author/date block
- Dark mode aware

---

## [2.5.16] — 2026-03-12
### Fixed
- Berita Berkaitan (Related News) no longer duplicates posts already shown in the Pilihan sidebar
- Related News query now fetches Pilihan post IDs and excludes them via `post__not_in`
- Fully automated — no editorial configuration needed

---

## [2.5.15] — 2026-03-12
### Changed
- Search results page completely rewritten — sidebar removed, full-width layout
- Search result cards now match archive page style (category kicker, title, excerpt, date)
- No-results state now matches 404 page style — centred, clean search form, Back to Home button
- Added article count header and breadcrumb to search results
### Removed
- Obsolete `.card-meta`, `.card-dot`, `.card-author` CSS (only used by old search template)

---

## [2.5.14] — 2026-03-12
### Changed
- Reverted Masonry layout on archive and search pages — restored original uniform CSS grid
- Masonry now only active on Related News grid (single posts)
- Load More button retained on archive and search pages
### Removed
- Masonry CSS overrides for `.articles-grid`

---

## [2.5.13] — 2026-03-12
### Fixed
- Footer unreadable in dark mode — `--up6-deep` remapped to light blue in dark mode but used as footer background; pinned to `#0d1a25` explicitly

---

## [2.5.12] — 2026-03-12
### Fixed
- Hijri date styling now matches CE date — removed italic and brightness difference
- Search submit button icon misalignment — `align-self: stretch` replaces fixed padding so icon centres correctly

---

## [2.5.11] — 2026-03-12
### Added
- Hijri date displayed alongside CE date in the nav bar
- Gregorian → Hijri conversion via PHP astronomical algorithm — no plugin, no API
- Hijri month names in Malay (Muharram … Zulhijjah)
- `up6_hijri_date()` helper in `functions.php`
### Notes
- Format: `Khamis, 12 Mac 2026 ● 12 Ramadan 1447H`
- Red dot separator consistent with theme's dot accent language

---

## [2.5.10] — 2026-03-12
### Fixed
- Breadcrumb "Home" string not translated — `"Home"` → `"Laman Utama"` added to `up6.pot` and `ms_MY.po`

---

## [2.5.9] — 2026-03-12
### Added
- jQuery Masonry, imagesLoaded, Infinite Scroll bundled into `js/`
- Masonry layout on Related News grid and archive/search grids
- Load More button replaces native WP pagination on archive and search (not auto-scroll)
- Load More button states: default, loading, all loaded, error — all translatable
- ms_MY translations for all Load More states
### Notes
- Scripts only enqueued on archive, search, and single templates
- `.masonry-active` class applied by JS — CSS grid fallback intact for no-JS

---

## [2.5.8] — 2026-03-12
### Added
- Related News section at the bottom of single posts — same categories, excludes current post
- Related News card count: Appearance → Theme Options → Homepage (min 3, max 12, default 4)
### Removed
- Prev/Next post navigation from single posts (covered by breadcrumbs)
- Orphaned `.post-navigation` / `.nav-direction` CSS
- ms_MY translations: "Berita Berkaitan", "Kad Berita Berkaitan"

---

## [2.5.7] — 2026-03-12
### Fixed
- Dark mode toggle icon and all white text/icons unreadable — `--up6-white` was remapped to `#1e2d3d` in dark mode, breaking every element using the token for colour
- Removed `--up6-white` from dark mode token overrides
- Added explicit surface-level dark overrides for cards, panels, widgets, search field, comment form
- `.header-icon-btn:hover` colour hardcoded to `#ffffff`

---

## [2.5.6] — 2026-03-12
### Changed
- Sidebar categories panel renamed "Categories" → "Most Active" (ms_MY: "Paling Aktif")
### Fixed
- Full translation sweep — rebuilt `up6.pot` and `ms_MY.po` from scratch (80 strings)
- Corrected ms_MY: "Secondary (Footer Bar)" was "Sekunder (Bar Atas)" → "Sekunder (Bar Pengaki)"
- Added ~15 previously missing translations
### Removed
- Stale strings: "RSS Feed URL", "Footer Column %d"

---

## [2.5.5] — 2026-03-12
### Changed
- Footer redesigned as 2-column layout: brand/social left, contact details right
- Contact column (address, phone, email) pulls from Customizer — each field silently skipped if empty
### Removed
- Unused footer widget sidebar areas `footer-col-1`, `footer-col-2`
- Orphaned footer widget CSS

---

## [2.5.4] — 2026-03-12
### Removed
- Unused `footer` menu location ("Footer Links" / "Pautan Pengaki") — registered but never rendered

---

## [2.5.3] — 2026-03-12
### Changed
- Social icons in footer now only render when their URL field is filled
- Entire `.footer-social` block suppressed when no social URLs are configured
### Removed
- RSS icon from footer social links (RSS now in header only)
- `ss_social_rss` Customizer field

---

## [2.5.2] — 2026-03-12
### Added
- RSS feed icon button in header (right of search bar)
- Dark mode toggle button in header (right of RSS icon)
- Dark mode via `up6-dark` class on `<html>`, persisted in `localStorage`
- Inline `<script>` in `<head>` prevents flash on load
- Moon icon (light mode) / sun icon (dark mode)
- Dark mode overrides for colour tokens and key component backgrounds

---

## [2.5.1] — 2026-03-12
### Changed
- Secondary menu location label: "Secondary (Top Bar)" → "Secondary (Footer Bar)"

---

## [2.5.0] — 2026-03-12
### Added
- Sidebar category count configurable via Theme Options → Homepage → Sidebar categories (default 6, range 1–20)

---

## [2.4.9] — 2026-03-12
### Changed
- Date moved from utility bar into primary nav row (flush right)
- Secondary nav (About, Contact, Advertise, Archives) moved to footer bar
- Footer bar changed to flex row: utility nav left, copyright right
### Removed
- Utility/secondary nav row from header
- Orphaned `.header-utility-row` and `.utility-nav` CSS

---

## [2.4.8] — 2026-03-12
### Fixed
- Removed `border-radius: 50%` from custom logo — logo now displays in its original shape

---

## [2.4.7] — 2026-03-12
### Fixed
- Custom logo now replaces the red icon only — site title text and tagline always remain visible
- `up6_logo()` always outputs text title regardless of custom logo upload

---

## [2.4.6] — 2026-03-12
### Fixed
- Restored beige accent on trailing digits of site title (e.g. "UP**6**") via regex
- Custom logo auto-resizes to `2.5rem` height

---

## [2.4.5] — 2026-03-12
### Changed
- Site title now reads from Settings → General → Site Title (native WordPress)
- Removed redundant `Logo Text` Customizer section

---

## [2.4.4] — 2026-03-12
### Added
- Dropdown sub-menu support in primary navigation
- Desktop: hover or click to open, Escape/outside-click to close
- Mobile: accordion expand/collapse inside drawer
- Full keyboard and ARIA accessibility

---

## [2.4.3] — 2026-03-12
### Fixed
- Categories panel hidden on single post pages

---

## [2.4.2] — 2026-03-12
### Removed
- `dynamic_sidebar()` from `sidebar.php`

---

## [2.4.1] — 2026-03-12
### Added
- Full `ms_MY.po` Malay translation (88 strings) with compiled `ms_MY.mo`
- `load_theme_textdomain()` in `up6_setup()`
- Regenerated `up6.pot` translation template
### Changed
- All theme strings now English by default

---

## [2.4.0] — 2026-03-12
### Added
- Persistent right-rail sidebar across full homepage
- Categories sorted by post count descending
- Sidebar categories widget with post counts
### Changed
- Default category limit reduced to 4
- Empty categories hidden by default

---

## [2.3.1] — 2026-03-12
### Added
- `home.php` — overrides parent block template for blog index
- `front-page.php` — routes to blog or static page per Reading settings

---

## [2.3.0] — 2026-03-12
### Added
- Tabbed Theme Options page (Appearance → Theme Options) — Footer, Social Media, Homepage tabs
- Homepage settings: category count, posts per category, Most Recent count, show/hide empty categories
- Admin assets: `css/admin-options.css`, `js/admin-options.js`

---

## [2.2.1] — 2026-03-12
### Changed
- Restored per-category sections on homepage
- Card kicker shows category, author, and date inline

---

## [2.2.0] — 2026-03-12
### Changed
- Footer redesigned to 3-column grid
- Hero card displays all categories joined by " · "

---

## [2.1.0] — 2026-03-12
### Added
- `comments.php` with full native comment support
- `/js/navigation.js` (extracted from inline)
- `/css/editor-style.css` for block editor parity
- `screenshot.png`, `rtl.css`
### Fixed
- Multiple CSS, escaping, and accessibility fixes (see README for full list)

---

## [2.0.0] — 2026-03-12
### Added
- Initial release as Twenty Twenty-Five child theme

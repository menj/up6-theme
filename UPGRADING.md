# UP6 — Enhancement Roadmap & Future Planning

This document captures planned improvements, open architectural decisions, and the upgrade procedure.

---

## Roadmap

### Near-term (next minor version)

**template-parts/card.php — article card partial**
The article card markup (thumbnail, category kicker, title, excerpt, meta) is currently duplicated in four files: `archive.php`, `search.php`, `single.php` (Related News grid), and `index.php`. Consolidating into a single `template-parts/card.php` partial via `get_template_part()` is the highest-leverage refactor in the codebase. Every card redesign currently requires four coordinated edits; the partial reduces that to one. This is the first item on the near-term list because card markup has changed at least six times across the theme's lifespan.

**template-advertise.php**
The footer secondary navigation includes a hardcoded Advertise link pointing to `/advertise`, but no corresponding page template exists. A lightweight media kit / advertising rates template is the natural completion of the secondary nav set established by the eight existing templates.

**Homepage fragment caching**
The homepage runs 4+ WP_Query calls per load (hero, N category grids, recent, editor picks, most viewed) with no transient caching. Acceptable at current traffic but will scale poorly under load. Wrapping category grid output in transients (keyed by category ID, invalidated on `save_post`) would reduce database queries significantly.

---

### Medium-term

**Split functions.php by responsibility**
At 2,167 lines, `functions.php` has outgrown its role as a single file. The pattern established by `includes/hidden-tags.php`, `includes/theme-options.php`, and `includes/theme-security-scanner.php` should be extended:

- `includes/schema.php` — all JSON-LD and SEO output (`up6_head_meta`, `up6_contact_schema_json_ld`, canonical, robots, prev/next)
- `includes/view-stats.php` — view counter, `up6_get_most_viewed_posts`, the View Stats admin page
- `includes/admin.php` — site editor redirect, page template meta box, subtitle meta box, pin-to-homepage meta box

`functions.php` would become an orchestrator of `require_once` calls and the core helpers that don't belong anywhere else. No behaviour change — purely an organisation improvement that makes the file scannable.

**CSP nonce on dark mode script**
The flash-prevention `<script>` in `header.php` (applies `up6-dark` class before first paint) has no `nonce` attribute. This is benign today — there is no Content Security Policy deployed. When a CSP is introduced, this script would be blocked and dark mode would flash on every load. The fix is to generate a nonce via WordPress and pass it to the script at the same time as the CSP header is set up.

**Author archive template (author.php)**
Author archives currently fall through to `archive.php`, rendering a generic article grid with no author identity. A dedicated `author.php` could display the author's avatar, bio, article count, and a filtered article grid. The custom avatar system (`up6_avatar`, `up6_author_avatar()`) and author URL in JSON-LD schema are already in place — the template is the missing piece.

---

### Long-term / exploratory

**Search within categories**
The current search is site-wide. A category-scoped search (passed as a hidden field on archive pages) would improve navigation for readers browsing a specific topic.

**Reading history / saved articles**
A `localStorage`-based read/saved list would give returning readers a lightweight personalisation layer without requiring login. Zero server-side storage — fully client-side.

**Automated Hijri date range for festive icons**
The festive icon system currently requires manual date entry. An annually-maintained mapping of Hijri dates to Gregorian ranges (accounting for Malaysia's moon-sighting calendar) could populate the Show From / Show Until fields automatically, with the editor only needing to select the occasion.

**Related News by tag**
The current related news query matches by category only. Adding a weighted tag-match fallback (posts sharing the most tags with the current article) would surface more semantically relevant suggestions, especially for articles that span multiple categories.

---

## Architecture notes and known trade-offs

**Customizer sections removed (v2.6.24)**
The Customizer's Footer Identity and Social Media sections were removed because they used `sanitize_text_field` on fields that Theme Options correctly treats with `wp_kses_post`. Any save from the Customizer would silently strip HTML. All footer and social configuration now lives exclusively in Theme Options. The Customizer retains only WordPress's native Site Identity section (logo, title, tagline).

**static $cache vs module-level globals in hidden-tags.php**
The two cache helpers (`up6_hidden_tag_ids()`, `up6_hidden_tag_slugs()`) use `static $cache`. The cache bust in `up6_save_hidden_tag_ids()` targets the correct variables. In practice, saves happen in admin requests where the cache has not yet been populated by a read — the bust is defensive rather than corrective. If save and read ever occur in the same request (e.g. a future REST endpoint), the static approach would be revisited in favour of module-level globals.

**wp_robots compatibility**
The `noindex` robots filter uses `add_filter('wp_robots', ...)` which was introduced in WordPress 5.7. The theme requires WordPress 6.4, so this is always available. The filter is wrapped in a Yoast/RankMath detection guard — if either plugin is active, the theme's noindex logic stands down entirely to avoid conflict.

**jQuery dependency**
`up6-grid.js` (Load More + Masonry) depends on jQuery and is only enqueued on archive, search, and single pages. `navigation.js` (drawer, dropdowns, dark mode, scroll progress, copy link, voting) is vanilla JS with no dependencies and loads on all pages. The jQuery dependency is a candidate for elimination in a future refactor — Load More is the primary blocker, as it uses `$.get()` for fetching pagination pages.

---

## Recently Completed

Items from previous roadmap versions that have been implemented:

| Item | Version | Notes |
|---|---|---|
| View counter deduplication | v2.7.15 | Cookie-based (`up6_viewed_{post_id}`, 24h TTL) |
| Article voting | v2.7.9–v2.7.10 | Thumbs up/down, AJAX, configurable threshold/label |
| Theme security scanner | v2.7.1–v2.7.2 | 18 patterns, SHA-256 integrity, email alerts, admin dashboard |
| Hijri date immutability | v2.7.7–v2.7.8 | Locked at publish time + one-time backfill |
| LCP hero optimisation | v2.7.16 | `<img>` with `fetchpriority="high"` replaces CSS `background-image` |
| Asset minification | v2.7.18 | 8 `.min` files, 33% front-end reduction |
| Print stylesheet rewrite | v2.7.13 | Aligned to v2.7 markup, all interactive elements hidden |
| Deprecated `current_time('timestamp')` | v2.7.15 | Replaced with `DateTimeImmutable` + `wp_timezone()` |
| Hardcoded URLs | v2.7.15 | Dynamic `get_category_by_slug()` in template-about.php |

---

## Upgrade procedure

All UP6 versions share the same settings storage format (WordPress theme mods). There are no database migrations between any versions.

1. **Back up** your database and theme files before upgrading.
2. Go to **Appearance → Themes**.
3. Click **Add New → Upload Theme**.
4. Upload the new zip file and click **Install Now**.
5. When prompted, choose **Replace active with uploaded**.
6. **Purge** any caching plugin or CDN cache.
7. **Verify** your settings are intact at **Appearance → Theme Options**.

---

## Version history of this document

| Version | Change |
|---|---|
| 2.7.32 | Added Recently Completed table; updated functions.php line count (2,167); marked view counter dedup as done; added homepage fragment caching to near-term; updated jQuery dependency note |
| 2.7.0 | Repurposed from breaking-changes log to enhancement roadmap and planning document |
| 2.6.24 | Customizer removal noted; first formal roadmap entries added |
| 2.5.x | Original breaking-changes log format (archived) |

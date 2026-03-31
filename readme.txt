=== UP6 ===
Contributors: up6
Requires at least: 6.4
Tested up to: 6.9.3
Requires PHP: 7.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Template: twentytwentyfive

A complete editorial news theme for Malaysian digital journalism, built on Twenty Twenty-Five.

== Description ==

UP6 is a clean, fast, and fully self-contained news theme designed for the UP6 editorial portal. It runs on top of WordPress's own Twenty Twenty-Five theme and comes with everything a Malaysian newsroom needs — no extra plugins required.

Set your site language to Bahasa Melayu and the entire interface — article bylines, share buttons, sidebar panels, menus, and admin labels — switches to Malay automatically.

= What's included =

* Full Bahasa Melayu (ms_MY) translation — 583 strings, zero untranslated
* Dual Gregorian + Islamic (Hijri) date display — in the navigation bar and on every article, with Malay month names and a correction offset for Malaysia's official moon-sighting calendar; Hijri dates locked at publish time so offset changes don't alter historical articles
* Festive occasion icons — 17 hand-crafted colourful icons for Malaysian public holidays (Aidilfitri, Raya Haji, Ramadan, Maal Hijrah, CNY, Deepavali, Thaipusam, Hari Kebangsaan, and more), displayed beside the logo during the selected period
* Dark mode — one click in the header, preference remembered between visits
* Pin to Homepage — tick a checkbox in the editor to make any post the hero card at the top of the homepage
* Pilihan Editor — the sidebar automatically picks the most recent post from each category, so you always get a diverse selection without manual curation
* Paling Dibaca (Most Read) — ranked list of popular articles with view counts, cookie-deduplicated so repeat visits don't inflate numbers
* Article voting — thumbs up / thumbs down buttons on every article; configurable threshold, optional label, AJAX-powered with cookie and user-meta deduplication
* Social share bar on every article — WhatsApp, Telegram, Facebook, X, Threads, LinkedIn, Reddit, Pinterest, Email, and Copy Link
* Subtitle / dek line — a short explanatory line below the headline, set from the editor sidebar
* Clean search URLs — /carian/query instead of /?s=query
* Related news at the bottom of every article — pulled automatically from the same category
* Load More button on archive and search pages — no full page reload
* Reading time, published date with time, last-updated indicator, and view count in every article byline
* Hidden Tags — mark certain tags to keep their articles out of public listings while still accessible by direct link
* Built-in SEO — structured data for Google News, Open Graph tags for social sharing, canonical URLs, and noindex on pages that should not be indexed
* Built-in security scanner — scans installed themes for 18 malicious code patterns, blocks activation of compromised themes, SHA-256 file integrity checking, email alerts, scan history log
* FAQ page with accordion layout, eligible for Google Rich Results
* Eight ready-made page templates: About, Editorial Policy, Privacy Policy, Disclaimer, Contact, FAQ, Corrections, and The Meaning of 6
* Optional content copy protection — disable right-click and text selection for non-logged-in visitors
* Email address in the footer is protected against spam bots automatically
* Performance optimised — LCP hero image with fetchpriority, all CSS/JS minified (33% smaller), conditional script loading
* Print-friendly layout — strips the header, sidebar, share bar, and vote buttons; adds the article URL at the bottom
* Fully responsive — tested across desktop, tablet, and mobile with intermediate breakpoints and 44px touch targets
* Malaysian flag design — all four Jalur Gemilang colours (blue, red, white, gold) are represented in the design system
* Zero plugin dependency — everything works straight away after activation

== Installation ==

1. Make sure Twenty Twenty-Five is installed on your WordPress site. It does not need to be active — just installed. You can find it at Appearance → Themes → Add New by searching "Twenty Twenty-Five".
2. Go to Appearance → Themes → Add New → Upload Theme.
3. Upload the UP6 zip file and click Install Now.
4. Click Activate.
5. To use Bahasa Melayu, go to Settings → General and set Site Language to Bahasa Melayu.

== Getting Started ==

After activating the theme:

1. Go to Appearance → Customize to set your site title, tagline, and logo (Site Identity section).
2. Go to Appearance → Theme Options to configure footer text, contact details, social media links, homepage layout, festive icons, and all other settings.
3. Go to Appearance → Menus to set up your main navigation menu (Primary Navigation) and footer links (Secondary / Footer Bar).
4. Create pages and assign the built-in templates — About, Contact, FAQ, and so on — using the Page Template selector in the editor sidebar.
5. To set a post as the homepage hero, open it in the editor and tick the Pin to Homepage checkbox.

== Frequently Asked Questions ==

= Does this theme require any plugins? =
No. News layout, dark mode, Hijri date, SEO tags, reading time, hidden tags, related news, and social sharing are all built in. Contact Form 7 and Google Maps are optionally supported on the Contact page if you choose to install them.

= How do I switch to dark mode? =
Click the moon icon in the top-right corner of the header. Your preference is saved automatically.

= How does the Hijri date work? =
The theme converts today's date to the Islamic calendar using Malay month names. If the date appears one day off from Malaysia's official (JAKIM) announcement, go to Appearance → Theme Options → General → Hijri Date Offset and set it to -1.

= How do I pin a post to the homepage hero? =
Open the post in the editor, find the "Pin to Homepage" box in the right sidebar, and tick the checkbox. If multiple posts are pinned, the most recent one is shown as the hero.

= How does Pilihan Editor work? =
It is fully automatic. The sidebar selects the most recent post from each different category, guaranteeing editorial diversity — one politics story, one economy story, one international story, and so on. Pinned posts are excluded so there is no duplication with the hero card.

= How do I add a subtitle to an article? =
Open the post in the editor and find the "Subtitle" box in the right sidebar. Type a short explanatory line — it appears in italic below the headline on the article page.

= How do I set the festive occasion icon? =
Go to Appearance → Theme Options → General → Festive Occasion. Pick the occasion from the dropdown and optionally set Show From and Show Until dates. The icon appears beside the logo during the selected period.

= How do I change the number of articles on the homepage? =
Go to Appearance → Theme Options → Homepage. You can adjust the number of category sections, posts per category, and Most Recent articles shown.

= What are Hidden Tags? =
Hidden Tags let you tag articles that should not appear anywhere on the public site — not on the homepage, not in search results, not in the RSS feed. The articles still exist and are accessible by direct link, but they are invisible to browsing visitors. Set them up at Appearance → Theme Options → Hidden Tags.

= How do I set up the Contact page? =
Create a new page, assign the "Contact" template from the Page Template selector in the editor sidebar, and configure the Contact Form 7 form ID and Google Maps details at Appearance → Theme Options → Contact.

= How do I add another language? =
The theme includes a translation template at languages/up6.pot. Copy it, rename it to your language code (e.g. id_ID.po), translate the strings using Poedit, compile the .mo file, and place both files in the languages/ folder.

= Can I use my own logo? =
Yes. Go to Appearance → Customize → Site Identity and upload your logo image. The text title remains visible beside it.

= How does content copy protection work? =
Go to Appearance → Theme Options → General and tick "Enable content copy protection". This disables right-click, text selection, and copy keyboard shortcuts for visitors who are not logged in. Editors and administrators are not affected. This is a deterrent, not absolute protection.

= How does article voting work? =
Thumbs up and thumbs down buttons appear below every article. Visitors can vote once per article — logged-in users are tracked by their account, guests by a 24-hour cookie. You can turn voting on or off, set a minimum number of votes before counts are displayed, and optionally add a prompt label, all from Appearance → Theme Options → General.

= What is the security scanner? =
UP6 includes a built-in scanner that checks every installed theme for malicious code patterns (backdoors, shell access, obfuscated code). It runs automatically when a theme is activated or uploaded, and you can also run a manual scan from Appearance → Theme Options → Security. If a malicious theme is detected, activation is blocked and you receive an email alert. It also verifies the integrity of UP6's own PHP files against a SHA-256 baseline.

= How do I enable unminified CSS/JS for development? =
Add `define( 'SCRIPT_DEBUG', true );` to your `wp-config.php`. The theme will load the unminified source files instead of the `.min` versions. Remove the line (or set to `false`) for production.

== Changelog ==

See CHANGELOG.md inside the theme folder for the full version history.

Current version: 2.8.0

== Upgrade Notice ==

See UPGRADING.md inside the theme folder for the enhancement roadmap and future plans.

== Credits ==

* Parent theme: Twenty Twenty-Five by Automattic (GPLv2)
* Fonts: DM Sans and Source Serif 4 via Google Fonts (SIL Open Font License)
* Icons: Inline SVG — no icon font dependencies
* Libraries: jQuery Masonry, imagesLoaded (MIT License)

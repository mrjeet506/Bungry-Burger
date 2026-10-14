# Changelog

## 0.1.0 (unreleased)

First build of the Bungry Burger theme.

### Added
- Classic PHP theme scaffold (`style.css`, `functions.php`, `theme.json`, `inc/`, `elementor/widgets/`, `template-parts/`, `assets/`).
- Design tokens from the Figma style guide: colours, Bakbak One / Montserrat type scale, buttons, placeholder tags, 84px desktop and 68px mobile header, 4-column footer.
- Self-hosted fonts (WOFF2, `font-display: swap`).
- `bungry_menu` custom post type, `bungry_category` taxonomy, meta fields (diet, price + TBC flag, allergen and verification notes, order URL override, availability, illustration) and the five seeded burgers.
- "Bungry Settings" page: logo URLs, announcement strip, ordering URL and label, business details, social links, footer blurb, copyright, client-to-confirm tag toggle, cookie/analytics notice. All business facts default to empty.
- Navigation walker with the orange active bar; full-screen mobile menu with keyboard support and focus trap.
- Elementor kit setup on activation: global colours, typography (H1–H5, Body L, Body, Small, Button, Eyebrow, Nav, Label, desktop and mobile sizes), container width 1280, breakpoints 768 / 1025.
- "Bungry" Elementor widget category and 8 widgets: Button, Placeholder Tag, Section Heading, Menu Cards, Notice, FAQ (native `<details>`, optional FAQPage JSON-LD), Info Rows, CTA Band.
- "Import Bungry demo pages & menu" tool: seven pages (Privacy and Terms as drafts), Home as front page, Primary and Footer menus, five menu items.
- Per-page meta description field; Open Graph tags.
- Admin notice recommending Elementor 3.20+. The theme runs without Elementor.
- `scripts/fetch-figma-assets.sh` and `scripts/fetch-fonts.sh` to rebuild the bundled assets.

### Not yet done
- Elementor page JSON templates for the seven pages (the demo tool creates the pages with placeholder copy only).
- Widgets 9–20 from the brief (Pillars, Feature split, Map, Allergen table, Social strip, Page hero, Hero, Signature explainer, Menu tile line-up, Legal layout, Contact form, Logo/nav/mobile-menu widgets).
- Contact form fallback (AJAX + nonce + honeypot + `wp_mail`).
- Bundled SVG artwork: must be fetched with `scripts/fetch-figma-assets.sh` before logos, icons and illustrations appear.
- Mobile type sizes marked "derived" in `inc/elementor-kit.php` need checking against the 390 Figma frames.
- Verified in a live WordPress + Elementor install: not yet done.

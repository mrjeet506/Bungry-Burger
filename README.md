# Bungry Burger theme

Written and developed by **mementotech**.

Classic WordPress theme for Bungry Burger ("Fresh. Sealed. Different."), built for Elementor. Tokens come from the Figma file `BgDWNsMfmJ9VwO8AD0iRdM`. Business facts are not invented: anything unconfirmed is empty, and the UI hides it or shows a placeholder tag.

**Status: 0.1.0 foundation.** The header, footer, settings, menu data, Elementor kit and 8 widgets are built. Page templates, widgets 9–20 and the contact form are not done yet. See [CHANGELOG.md](CHANGELOG.md) for the full list.

## Requirements
- WordPress 6.4+, PHP 8.0+
- Elementor 3.20+ (Free is enough). Without Elementor the coded header, footer and pages still render.

## Install
1. Copy this folder to `wp-content/themes/bungry-burger` (or upload `bungry-burger.zip`).
2. Fetch the bundled artwork (logos, icons, burger illustrations). They are not in git because the Figma CDN URLs expire:
   ```bash
   FIGMA_TOKEN=figd_xxx ./scripts/fetch-figma-assets.sh
   ```
   Without this step the theme still works. Logos fall back to a text wordmark and artwork is omitted.
3. Activate **Bungry Burger**. Activation applies the Elementor kit (if Elementor is active) and seeds the five menu items.
4. Go to **Appearance > Bungry Settings** and fill in what has been confirmed.
5. Optional: **Appearance > Bungry Demo** creates the seven pages, sets Home as the front page, and builds the menus.

## Elementor setup
- **Site Settings > Global Colors / Typography** hold the brand tokens (`Bungry forest`, `Bungry orange`, and so on). Widgets and theme CSS read them through `--e-global-*` variables, so changing a colour there retheme the site.
- **Theme Builder** header and footer locations are supported. Assign the Bungry header or footer and the coded versions step aside.
- Widgets are in the **Bungry** category: Button, Placeholder Tag, Section Heading, Menu Cards, Notice, FAQ, Info Rows, CTA Band.

### Importing the page templates
1. In Elementor, go to **Templates > Saved Templates > Import Templates** and upload the seven files from `elementor/templates/` (`bungry-home.json`, `bungry-menu.json`, `bungry-our-story.json`, `bungry-find-us.json`, `bungry-allergens-faqs.json`, `bungry-privacy.json`, `bungry-terms.json`).
2. Create a page for each template, open it in Elementor, and use **Insert Template** to add the saved template.
3. Rebuild the templates from source after editing copy: `python3 scripts/build-elementor-templates.py`.

Photos are styled slots until real photography is supplied. Unconfirmed details are written as `[CLIENT TO CONFIRM: ...]` text, so remove those when confirmed. Unlike the tag widget, that text does not follow the global tag toggle.

## Editing content
| What | Where |
|---|---|
| Menu items (name, description, diet, price, allergen notes, availability) | Menu items (`bungry_menu`) |
| Menu order | Page attributes > Order on each menu item |
| Header and footer details, ordering URL, social links, announcement, copyright | Appearance > Bungry Settings |
| Page meta description | "Bungry SEO" box on each page |
| Home, Menu, Find Us and Allergens copy | Elementor pages (demo pages hold placeholder copy until templates are built) |

## Placeholders and launch
- A **Client to confirm** tag shows wherever a value is empty and the toggle is on (Bungry Settings > Launch & compliance). Turn it off at launch. Empty fields then hide.
- The Order button, order links and menu order buttons only appear when an ordering URL is set.
- The announcement strip only shows when it is switched on and has text.
- Privacy and Terms pages are created as **drafts** and marked as outlines, not legal advice.
- Structured data (FAQPage) is only emitted when the FAQ widget's schema switch is on. Restaurant and Menu schema are not emitted until the client supplies confirmed data.

Launch checklist (from the content brief): confirm trading name, address, phone, monitored email, hours, ordering provider and URL, final menu and prices, ingredient and allergen sheets, claim approval (free-range, vegetarian/vegan, fresh), real photography, privacy and analytics setup. Then remove the placeholders and turn off the tag toggle.

## Development
- `phpcs.xml.dist` runs WordPress Coding Standards: `phpcs --standard=phpcs.xml.dist`.
- `scripts/fetch-fonts.sh` regenerates the self-hosted WOFF2 files.
- JavaScript is vanilla (`assets/js/nav.js`). No jQuery.

## Known gaps
- Elementor page templates (JSON) for all seven pages, and the one-click template import.
- Widgets 9–20 from the brief.
- Contact form fallback.
- Live browser QA against Figma screenshots, axe, Lighthouse, and testing with Elementor Free/Pro and without Elementor. Not run in this build.

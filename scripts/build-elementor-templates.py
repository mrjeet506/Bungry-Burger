#!/usr/bin/env python3
"""Builds Elementor import files for the Bungry Burger site, matched to the Figma design.

Design width is 1440px and counts as 100% (full width). Every section is full width.
The theme's boxed mode (70%) frames the whole site, so these files work in both modes.

Output: elementor/templates/*.json
- Pages: Templates > Saved Templates > Import Templates (hide_title is on).
- Header and footer: Templates > Theme Builder (Elementor Pro).

Colours reference the Bungry kit globals, so retheming in Site Settings flows through.
Unconfirmed details are written as "[CLIENT TO CONFIRM: ...]" text or placeholder tags.
Run:  python3 scripts/build-elementor-templates.py
"""
import hashlib
import json
import pathlib

OUT = pathlib.Path(__file__).resolve().parent.parent / "elementor" / "templates"

_seq = [0]


def _id() -> str:
    """Stable 7-character element id so re-running the script gives identical files."""
    _seq[0] += 1
    return hashlib.md5(str(_seq[0]).encode()).hexdigest()[:7]


def g(kind: str, slug: str) -> str:
    return f"globals/{kind}?id={slug}"


FOREST = g("colors", "bungryforest")
FOREST_DARK = g("colors", "bungryforestdark")
CREAM = g("colors", "bungrycream")
CREAM_DARK = g("colors", "bungrycreamdark")
ORANGE = g("colors", "bungryorange")
ORANGE_DARK = g("colors", "bungryorangedark")
OLIVE = g("colors", "bungryolive")
DEEP_OLIVE = g("colors", "bungrydeepolive")
INK = g("colors", "bungryink")
WHITE = g("colors", "bungrywhite")


# ---------------------------------------------------------------- element builders
def widget(widget_type: str, settings: dict, css: str = "", globals_: dict | None = None) -> dict:
    settings = dict(settings)
    if css:
        settings["_css_classes"] = css
    if globals_:
        settings["__globals__"] = globals_
    return {"id": _id(), "elType": "widget", "widgetType": widget_type, "settings": settings, "elements": []}


def container(children: list, *, row: bool = False, bg: str | None = None, css: str = "",
              gap: int = 40, pad: tuple = (0, 0), side: int = 0, width: int | None = None) -> dict:
    """Full-width flex container. pad = (top, bottom) px. side = left/right px. width = % inside a row."""
    top, bottom = pad
    settings = {
        "flex_direction": "row" if row else "column",
        "flex_direction_mobile": "column",
        "content_width": "full",
        "flex_gap": {"column": str(gap), "row": str(gap), "unit": "px", "size": gap},
        "padding": {"unit": "px", "top": str(top), "right": str(side), "bottom": str(bottom), "left": str(side), "isLinked": False},
        "padding_mobile": {"unit": "px", "top": str(min(top, 56)), "right": "24", "bottom": str(min(bottom, 56)), "left": "24", "isLinked": False},
    }
    if bg:
        settings["background_background"] = "classic"
        settings["background_color"] = "#F6F7EE"
        settings["__globals__"] = {"background_color": bg}
    if width:
        settings["width"] = {"unit": "%", "size": width, "sizes": []}
        settings["width_mobile"] = {"unit": "%", "size": 100, "sizes": []}
    if css:
        settings["_css_classes"] = css
    return {"id": _id(), "elType": "container", "settings": settings, "elements": children, "isInner": False}


def section(children: list, *, bg: str | None = None, pad: tuple = (80, 80), gap: int = 40, css: str = "") -> dict:
    """Top-level full-width band with an 80px side gutter."""
    return container(children, bg=bg, pad=pad, side=80, gap=gap, css=css)


def row(children: list, *, gap: int = 80, pad: tuple = (0, 0), css: str = "") -> dict:
    return container(children, row=True, gap=gap, pad=pad, css=css)


def col(children: list, *, width: int | None = None, gap: int = 20, bg: str | None = None,
        pad: tuple = (0, 0), side: int = 0, css: str = "") -> dict:
    return container(children, width=width, gap=gap, bg=bg, pad=pad, side=side, css=css)


def h(title: str, level: str = "h2", color: str = FOREST, css: str = "") -> dict:
    return widget("heading", {"title": title, "header_size": level}, css=css, globals_={"title_color": color})


def p(html: str, color: str = INK, css: str = "") -> dict:
    return widget("text-editor", {"editor": html}, css=css, globals_={"text_color": color})


def kicker(text: str, color: str = ORANGE_DARK) -> dict:
    return p(f"<p>{text}</p>", color=color, css="bungry-kicker")


def btn(text: str, url: str, style: str = "primary", size: str = "default") -> dict:
    return widget("bungry-button", {
        "text": text,
        "link": {"url": url, "is_external": "", "nofollow": ""},
        "style": style,
        "size": size,
        "arrow": "yes",
    })


def tag(label: str, tone: str = "light") -> dict:
    return widget("bungry-placeholder-tag", {"label": label, "tone": tone})


def photo(label: str, tag_label: str = "Photo TBC") -> dict:
    """Photo slot with a styled fallback. Replace with an Image widget when photography is supplied."""
    return container(
        [p(f"<p><strong>PHOTO PLACEHOLDER</strong></p><p>{label}</p>", color=FOREST), tag(tag_label)],
        css="bungry-photo-slot", gap=12, pad=(40, 40), side=40,
    )


def section_heading(eyebrow_text: str, title: str, text: str = "", align: str = "left", title_tag: str = "h2") -> dict:
    return widget("bungry-section-heading", {
        "eyebrow": eyebrow_text,
        "title": title,
        "title_tag": title_tag,
        "text": text,
        "align": align,
        "button_text": "",
        "button_link": {"url": ""},
    })


def notice(kind: str, title: str, text: str) -> dict:
    return widget("bungry-notice", {"type": kind, "title": title, "text": text})


def hero(eyebrow_text: str, title: str, body: str = "") -> dict:
    """Forest page hero: kicker, H1, optional intro. Used at the top of every page."""
    items = [kicker(eyebrow_text, ORANGE), h(title, "h1", CREAM)]
    if body:
        items.append(p(f"<p>{body}</p>", color=CREAM, css="bungry-lead bungry-hero-body"))
    items.append(art("patterns/wave-strip", css="bungry-wave"))
    return section(items, bg=FOREST, pad=(72, 100), gap=20)


def page(title: str, sections: list) -> dict:
    return {"version": "0.4", "title": title, "type": "page", "page_settings": {"hide_title": "yes"}, "content": sections}


def art(name: str, css: str = "") -> dict:
    """Figma artwork as an inline SVG (Elementor HTML widget). Source: assets/svg/<name>.svg."""
    svg = (OUT.parent.parent / "assets" / "svg" / f"{name}.svg").read_text(encoding="utf8").strip()
    return widget("html", {"html": svg}, css=css)


def disc(icon: str) -> dict:
    """Forest circle holding a Figma icon."""
    return container([art(f"icons/{icon}", css="bungry-disc-icon")], bg=FOREST, css="bungry-disc")


def socials() -> dict:
    return row([art(f"icons/{n}", css="bungry-social-art") for n in ("instagram", "facebook", "tiktok", "youtube")], gap=12)


def divider() -> dict:
    return container([], bg=OLIVE, css="bungry-divider")


# ---------------------------------------------------------------- HOME (Figma 7:225)
PILLAR_ICONS = ["burger", "chef", "diamond", "bag"]
VALUE_ICONS = ["diamond", "burger", "leaf", "chef"]


def home() -> dict:
    hero_band = section([
        row([
            col([
                kicker("FRESH. SEALED. DIFFERENT.", ORANGE),
                h("A NEW TAKE ON YOUR BURGER CRAVINGS.", "h1", CREAM),
                p("<p>Meet Bungry Burger — a familiar favourite with a distinctive sealed-burger twist. "
                  "Explore our focused menu and find your new go-to.</p>", color=CREAM, css="bungry-lead"),
                row([btn("Explore the menu", "/menu/", "primary"),
                     btn("Find us", "/find-us/", "outline-light")], gap=16),
            ], width=50, gap=24),
            col([art("illustrations/sealed-burger", css="bungry-art-slot")], width=50),
        ], gap=80),
        art("patterns/wave-strip", css="bungry-wave"),
    ], bg=FOREST, pad=(96, 96))

    signature = section([
        row([
            col([art("illustrations/sealed-cutaway", css="bungry-art-slot")], width=50),
            col([
                kicker("THE SIGNATURE CONCEPT"),
                h("NOT JUST A BURGER. A BUNGRY."),
                p("<p>Our signature sealed-burger concept gives a familiar favourite a look of its own. "
                  "Distinctive in shape, simple to choose and made to be remembered.</p>"),
                row([
                    p("<p><strong>01</strong><br>MADE</p>", css="bungry-step"),
                    p("<p><strong>02</strong><br>SEALED</p>", css="bungry-step"),
                    p("<p><strong>03</strong><br>READY TO ENJOY</p>", css="bungry-step"),
                ], gap=24),
                tag("Preparation steps: confirm with kitchen"),
            ], width=50, gap=20),
        ], gap=80),
    ], pad=(96, 96))

    menu_preview = section([
        section_heading("OUR MENU", "MEET THE BUNGRY LINE-UP.",
                        "Five burger concepts. Find the one that fits your craving."),
        widget("bungry-menu-cards", {
            "category": "", "limit": -1,
            "columns": "5", "columns_tablet": "2", "columns_mobile": "1",
            "show_price": "yes", "show_order": "yes",
            "allergen_link": {"url": "/food-allergens-faqs/"},
        }),
        row([btn("View the full menu", "/menu/", "outline-dark")]),
    ], pad=(96, 96), gap=40)

    pillars = [
        ("A DISTINCTIVE SIGNATURE FORMAT", "An instantly recognisable shape, made to be remembered."),
        ("A FOCUSED MENU", "Five burger concepts, simple to choose."),
        ("A RECOGNISABLE BRAND", "Bold, modern and playful from the first glance."),
        ("A WELCOMING FOOD EXPERIENCE", "Designed around the food and the people enjoying it."),
    ]
    why = section([
        section_heading("WHY BUNGRY", "FRESH. SEALED. SIMPLE. MEMORABLE.", align="center", title_tag="h2"),
        row([col([disc(PILLAR_ICONS[i]),
                  h(t, "h5"), p(f"<p>{b}</p>")], width=25, gap=12)
             for i, (t, b) in enumerate(pillars)], gap=32),
    ], pad=(96, 96), gap=48)

    kitchen = section([
        row([
            col([photo("Kitchen or prep photo: only if the kitchen is visible.", "Confirm open kitchen")], width=50),
            col([
                kicker("THE BUNGRY EXPERIENCE"),
                h("GOOD FOOD, MADE IN VIEW."),
                p("<p>The Bungry experience is designed around the food — from the preparation to the signature sealed finish.</p>"),
            ], width=50, gap=20),
        ], gap=80),
    ], pad=(96, 96))

    cta = section([
        widget("bungry-cta-band", {
            "title": "YOUR NEXT BUNGRY IS CLOSER THAN YOU THINK.",
            "text": "Find your nearest Bungry, check today’s hours and choose the best way to order.",
            "button1_text": "Get directions", "button1_link": {"url": "/find-us/"},
            "button2_text": "Order online", "button2_link": {"url": ""},
        }),
    ], pad=(64, 64))

    social = section([
        section_heading("FOLLOW ALONG", "WHICH BUNGRY ARE YOU?",
                        "Follow along for product close-ups and behind-the-scenes prep.", title_tag="h3"),
        socials(),
        tag("Official links TBC"),
    ], pad=(80, 80), gap=20)

    return page("Home", [hero_band, signature, menu_preview, why, kitchen, cta, social])


# ---------------------------------------------------------------- MENU (Figma 9:647)
def menu() -> dict:
    intro = hero("OUR MENU", "THE BUNGRY LINE-UP",
                 "Five burger concepts. One signature sealed-burger idea. Take a look through the line-up and choose your Bungry.")
    filters = section([
        row([
            row([btn("All", "/menu/", "dark", "small"),
                 btn("Beef", "/menu-category/beef/", "outline-dark", "small"),
                 btn("Chicken", "/menu-category/chicken/", "outline-dark", "small"),
                 btn("Vegetarian", "/menu-category/vegetarian/", "outline-dark", "small")], gap=12),
            tag("Categories optional: confirm final menu"),
        ], gap=24),
    ], pad=(56, 0), gap=0)
    allergen = section([
        notice("info", "CHECK INGREDIENTS AND ALLERGENS BEFORE YOU ORDER.",
               "Menu availability and ingredients may change. If you have a food allergy, contact the team to discuss the specific item."),
        row([btn("Allergen information", "/food-allergens-faqs/", "dark", "small")]),
    ], pad=(0, 0), gap=20)
    cards = section([
        widget("bungry-menu-cards", {
            "category": "", "limit": -1,
            "columns": "3", "columns_tablet": "2", "columns_mobile": "1",
            "show_price": "yes", "show_order": "yes",
            "allergen_link": {"url": "/food-allergens-faqs/"},
        }),
        container([
            h("SIDES, DRINKS & ADD-ONS", "h4", DEEP_OLIVE),
            p("<p>To be added once the client confirms the complete menu and prices.</p>", color=DEEP_OLIVE),
        ], css="bungry-more-coming", gap=12, pad=(32, 32), side=32),
    ], pad=(40, 96), gap=40)
    return page("Menu", [intro, filters, allergen, cards])


# ---------------------------------------------------------------- OUR STORY (Figma 9:1123)
def our_story() -> dict:
    intro = hero("OUR STORY", "A FAMILIAR FAVOURITE, WITH A BUNGRY TWIST.")
    story = section([
        row([
            col([
                h("At Bungry, we believe a burger can be familiar and still feel different.", "h4"),
                p("<p>Our idea centres on a distinctive sealed-burger format — a recognisable shape designed to give "
                  "the Bungry experience its own identity. From the product presentation to the packaging and the way "
                  "the brand shows up, every detail is intended to be simple, bold and memorable.</p>",
                  css="bungry-lead"),
            ], width=50, gap=24),
            col([photo("Packaging illustration: add the Figma export or a real packaging photo.",
                       "Replace with real packaging photo")], width=50),
        ], gap=80),
    ], pad=(96, 96))
    values = [
        ("SIMPLE", "Every detail is intended to be easy to choose and easy to enjoy."),
        ("BOLD", "A recognisable shape and a confident, modern look."),
        ("MEMORABLE", "From the product to the packaging, made to be remembered."),
        ("WELCOMING", "A friendly, conversational voice for everyone."),
    ]
    value_band = section([
        section_heading("OUR VALUES", "WHAT BUNGRY STANDS FOR", align="center", title_tag="h3"),
        row([col([disc(VALUE_ICONS[i]),
                  h(t, "h5", css="bungry-center"), p(f"<p>{b}</p>")],
                 bg=WHITE, css="bungry-card", width=25, gap=12, pad=(28, 28), side=28)
             for i, (t, b) in enumerate(values)], gap=24),
    ], bg=CREAM_DARK, pad=(96, 96), gap=56)
    photos = section([photo("Store, packaging and team photography. Add only when verified.", "Photos TBC")],
                     pad=(96, 96))
    closing = section([
        h("ONE DISTINCTIVE BURGER. ONE RECOGNISABLE BRAND.", "h2", CREAM),
        row([btn("Explore the menu", "/menu/", "primary")]),
    ], bg=FOREST, pad=(96, 96), gap=32)
    return page("Our Story", [intro, story, value_band, photos, closing])


# ---------------------------------------------------------------- FIND US (Figma 9:1396)
def find_us() -> dict:
    intro = hero("FIND US / CONTACT", "COME FIND YOUR BUNGRY.",
                 "Have a question about the menu, your order or where to find us? Get in touch using the details below.")
    details = section([
        row([
            col([
                widget("bungry-info-rows", {"use_settings": "yes"}),
            ], width=45, bg=WHITE, css="bungry-card", pad=(40, 40), side=40, gap=28),
            col([photo("Map placeholder: add a verified pin once the address is confirmed.", "Map TBC")], width=55),
        ], gap=56),
    ], pad=(72, 72))
    contact = section([
        row([
            col([
                h("GET IN TOUCH", "h2"),
                p("<p>Questions about the menu or your order? Send us a message. We only ask for what we need to reply.</p>"),
            ], width=40, gap=16),
            col([
                widget("shortcode", {"shortcode": "[bungry_contact_form]"}, css="bungry-cf7"),
            ], width=60, bg=WHITE, css="bungry-card", pad=(40, 40), side=40, gap=16),
        ], gap=80),
    ], bg=CREAM_DARK, pad=(96, 96))
    stores = section([
        h("OUR STORES", "h2"),
        p("<p>Find your nearest Bungry and check today’s hours.</p>"),
        row([
            col([
                h(f"STORE {n} — SUBURB", "h5"),
                tag("Client to confirm"),
                p("<p>Street address, suburb</p>"), tag("TBC"),
                p("<p>Opening hours</p>"), tag("TBC"),
                p("<p>Phone number</p>"), tag("TBC"),
            ], width=33, bg=WHITE, css="bungry-card", pad=(28, 28), side=28, gap=12)
            for n in (1, 2, 3)
        ], gap=24),
        notice("warning", "Placeholder stores",
               "Replace with confirmed locations. Hide this list if there is only one site."),
    ], pad=(72, 72), gap=32)
    return page("Find Us", [intro, details, stores, contact])


# ---------------------------------------------------------------- ALLERGENS & FAQS (Figma 10:1409)
FAQS = [
    ("What makes Bungry different?",
     "<p>Our signature concept is the distinctive sealed-burger format. Explore the menu to see the current line-up.</p>"),
    ("Where can I find Bungry?",
     "<p>We are based in New Zealand. See our address and opening hours on the Find Us page. "
     "[CLIENT TO CONFIRM: suburb or city]</p>"),
    ("Can I order online?",
     "<p>Online ordering is [CLIENT TO CONFIRM: available / not currently available]. When it is available, "
     "use the official Order Online link.</p>"),
    ("Do you have vegetarian options?",
     "<p>The proposed menu includes Green Bungry. Confirm the final ingredients and preparation practices before "
     "publishing dietary assurances.</p>"),
    ("Where can I check allergens?",
     "<p>Please check the current ingredient and allergen information before ordering. If you have a food allergy, "
     "contact the team to discuss the specific item and preparation process.</p>"),
    ("Can ingredients or menu items change?",
     "<p>Menu availability and ingredients may change. We keep the live menu and allergen information aligned with the "
     "current kitchen specification.</p>"),
]

ALLERGEN_TABLE = (
    "<table class=\"bungry-allergen-table\"><thead><tr>"
    "<th>BURGER</th><th>ALLERGENS (CONTAINS)</th><th>CROSS-CONTACT NOTES</th>"
    "</tr></thead><tbody>"
    + "".join(
        f"<tr><td>{name}</td><td>To be verified by kitchen</td><td>To be verified</td></tr>"
        for name in ["Classic Bungry", "Double Bungry", "Smoky Bungry", "Free-Range Bungry", "Green Bungry"]
    )
    + "</tbody></table>"
)


def faqs() -> dict:
    intro = hero("FOOD INFORMATION", "ALLERGENS & FAQS",
                 "Please check the current ingredient and allergen information before ordering.")
    allergens = section([
        h("ALLERGEN INFORMATION", "h3"),
        notice("warning", "Have a food allergy?",
               "Please contact the team to discuss the specific item and preparation process. Item-level information is "
               "published only after the kitchen has verified it. Menu availability and ingredients may change."),
        p(ALLERGEN_TABLE),
    ], pad=(80, 56), gap=28)
    faq_band = section([
        row([
            col([
                h("FREQUENTLY ASKED QUESTIONS", "h2"),
                p("<p>Quick answers about Bungry, ordering and food information.</p>"),
            ], width=35, gap=16),
            col([widget("bungry-faq", {
                "items": [{"_id": _id(), "question": q, "answer": a} for q, a in FAQS],
                "open_first": "yes", "schema": "",
            })], width=65),
        ], gap=72),
    ], bg=CREAM_DARK, pad=(96, 96))
    cta = section([
        row([h("STILL HAVE A QUESTION?", "h3", CREAM), btn("Contact us", "/find-us/", "primary")], gap=40),
    ], bg=FOREST, pad=(64, 64))
    return page("Food Allergens & FAQs", [intro, allergens, faq_band, cta])


# ---------------------------------------------------------------- LEGAL (Figma 10:1682, 10:1907)
def legal(title: str, kicker_text: str, intro: str, toc: list, sections: list, top_notice: dict) -> dict:
    head = hero(kicker_text, title, intro)
    toc_card = col([
        kicker("ON THIS PAGE", ORANGE_DARK),
        *[p(f"<p>{line}</p>", color=FOREST) for line in toc],
    ], width=30, bg=WHITE, css="bungry-card", pad=(28, 28), side=28, gap=14)
    body_items = [tag("Draft outline: not legal advice. Client / legal review required.")
                  if top_notice is None else top_notice]
    for heading, text, tag_label in sections:
        items = [h(heading, "h4"), p(f"<p>{text}</p>")]
        if tag_label:
            items.append(tag(tag_label))
        body_items.append(divider())
        body_items.append(col(items, gap=12))
    body = col(body_items, width=70, gap=28)
    content = section([row([toc_card, body], gap=64)], pad=(72, 96))
    return page(title, [head, content])


def privacy() -> dict:
    return legal(
        "PRIVACY POLICY", "LEGAL",
        "Last updated: [date to confirm]",
        ["1. Who we are", "2. What we collect", "3. Why we collect it", "4. How we use and share it",
         "5. Cookies and analytics", "6. Accessing and correcting your information", "7. Contact us about privacy"],
        [
            ("1. WHO WE ARE",
             "Bungry Burger is a burger brand operating in New Zealand. This policy explains how we handle personal information collected through this website.",
             "Trading name & contact: client to confirm"),
            ("2. WHAT WE COLLECT",
             "If you use our contact form we collect your name (optional), email address, the topic you choose and your message. Providing this information is optional. We may also collect basic website analytics data.",
             "Analytics tools: client to confirm"),
            ("3. WHY WE COLLECT IT",
             "We use the details you send to reply to your enquiry. We collect only what we need to respond.", None),
            ("4. HOW WE USE AND SHARE IT",
             "We do not publish your details. We share information with service providers only where needed to run the website or respond to you.",
             "Providers (form, hosting, ordering): client to confirm"),
            ("5. COOKIES AND ANALYTICS",
             "We will explain any cookies, analytics or advertising tools used on this site and provide consent controls where required.",
             "Actual tracking setup: client to confirm"),
            ("6. ACCESSING AND CORRECTING YOUR INFORMATION",
             "You can ask us for the personal information we hold about you and ask us to correct it.", None),
            ("7. CONTACT US ABOUT PRIVACY",
             "For privacy questions, contact our privacy contact using the details below.",
             "Privacy contact: client to confirm"),
        ],
        None,
    )


def terms() -> dict:
    closed = notice("info", "Online ordering is not available right now",
                    "Please check back soon, or find us in store once our details are confirmed. "
                    "[Show this state if ordering is unavailable, the menu is out of date or the store is temporarily closed.]")
    return legal(
        "TERMS & ORDERING INFORMATION", "ORDERING INFORMATION",
        "Last updated: [date to confirm]",
        ["1. About online ordering", "2. Pickup and delivery", "3. Prices and payment",
         "4. Menu and availability", "5. Food allergies", "6. Changes to these terms", "7. Contact us"],
        [
            ("1. ABOUT ONLINE ORDERING",
             "Online ordering is [available / not currently available]. When available, orders are placed through our official ordering provider.",
             "Ordering provider & URL: client to confirm"),
            ("2. PICKUP AND DELIVERY",
             "Pickup and delivery options, zones and any fees will be shown clearly before you pay.",
             "Pickup / delivery scope: client to confirm"),
            ("3. PRICES AND PAYMENT",
             "All prices are in New Zealand dollars (NZD). Any material fees, conditions or delivery charges are shown clearly during the ordering journey.",
             "Prices & payment methods: client to confirm"),
            ("4. MENU AND AVAILABILITY",
             "Menu availability and ingredients may change. The live menu is the current source of truth.", None),
            ("5. FOOD ALLERGIES",
             "Please check our allergen information before ordering and contact the team if you have a food allergy.", None),
            ("6. CHANGES TO THESE TERMS",
             "We may update these terms from time to time. The latest version will always be published on this page.", None),
            ("7. CONTACT US",
             "Questions about an order? Get in touch using the details on our Find Us page.", None),
        ],
        closed,
    )


# ---------------------------------------------------------------- HEADER & FOOTER (Theme Builder, Elementor Pro)
def contact_us() -> dict:
    intro = hero("CONTACT US", "GET IN TOUCH.",
                 "Have a question about the menu, your order or allergens? Send us a message and we’ll get back to you.")
    channels = section([
        row([
            col([
                col([disc("mail"), kicker("EMAIL US"), p("<p>hello@bungryburger.co.nz</p>"),
                     tag("Verify before publishing")], bg=WHITE, css="bungry-card", pad=(24, 24), side=24, gap=10),
                col([disc("phone"), kicker("CALL US"), p("<p>Phone number</p>"),
                     tag("Client to confirm")], bg=WHITE, css="bungry-card", pad=(24, 24), side=24, gap=10),
                col([disc("clock"), kicker("REPLY TIME"), p("<p>Typical response time</p>"),
                     tag("Client to confirm")], bg=WHITE, css="bungry-card", pad=(24, 24), side=24, gap=10),
                col([h("LOOKING FOR OUR ADDRESS OR HOURS?", "h5"),
                     p("<p>Store details and the map live on the Find Us page.</p>"),
                     btn("FIND US", "/find-us/", "outline-dark")],
                    bg=CREAM_DARK, css="bungry-card", pad=(24, 24), side=24, gap=10),
            ], width=40, gap=20),
            col([
                h("SEND US A MESSAGE", "h4"),
                widget("shortcode", {"shortcode": "[bungry_contact_form]"}, css="bungry-cf7"),
            ], width=60, bg=WHITE, css="bungry-card", pad=(40, 40), side=40, gap=20),
        ], gap=56),
    ], pad=(72, 72))
    topics = [
        ("burger", "MENU QUESTION", "Ask about the line-up, descriptions or availability."),
        ("bag", "ORDER HELP", "Need a hand with an order? Tell us what happened."),
        ("info", "ALLERGENS & DIET", "Check ingredient and allergen information with the team."),
        ("mail", "GENERAL ENQUIRY", "Anything else — we’re happy to help."),
    ]
    topic_band = section([
        h("WHAT CAN WE HELP WITH?", "h2"),
        row([
            col([disc(icon), h(title, "h5"), p(f"<p>{body}</p>")],
                bg=WHITE, css="bungry-card", width=25, gap=12, pad=(28, 28), side=28)
            for icon, title, body in topics
        ], gap=24),
    ], bg=CREAM_DARK, pad=(96, 96), gap=40)
    states = section([
        kicker("FORM STATES"),
        row([
            notice("info", "Thanks — your message has been sent.", "We’ll reply to the email address you provided."),
            notice("warning", "Something went wrong.", "Please try again, or email us directly at the address above."),
        ], gap=24),
    ], pad=(72, 72), gap=24)
    cta = section([
        row([
            h("PREFER TO VISIT?", "h2", CREAM),
            btn("FIND US", "/find-us/", "primary"),
        ], gap=40),
    ], bg=FOREST, pad=(56, 56))
    return page("Contact Us", [intro, channels, topic_band, states, cta])


def nav(menu_slug: str, layout: str = "horizontal", color: str | None = None) -> dict:
    settings = {"menu": menu_slug, "layout": layout, "mobile_layout": "full-screen", "align_items": "center"}
    globals_ = {"color_menu_item": color} if color else None
    return widget("nav-menu", settings, globals_=globals_)


def header() -> dict:
    bar = container([
        art("logos/logo-full-dark", css="bungry-logo-art"),
        nav("bungry-primary"),
        widget("bungry-button", {
            "text": "Order online", "link": {"url": "", "is_external": "", "nofollow": ""},
            "style": "primary", "size": "small", "arrow": "",
        }, css="bungry-header-order"),
    ], row=True, bg=CREAM, pad=(19, 19), side=80, gap=40)
    return {"version": "0.4", "title": "Bungry Header", "type": "header", "page_settings": {}, "content": [bar]}


def footer() -> dict:
    brand = col([
        art("logos/logo-full-light", css="bungry-logo-art bungry-logo-art--footer"),
        p("<p>FRESH. SEALED. DIFFERENT.</p>", color=ORANGE, css="bungry-footer-tagline"),
        p("<p>Meet Bungry Burger. A fresh take on a familiar favourite, made memorable by our signature sealed-burger concept.</p>",
          color=CREAM),
    ], width=28, gap=18)
    quick = col([
        p("<p>QUICK LINKS</p>", color=ORANGE, css="bungry-footer-heading"),
        nav("bungry-footer-quick-links", layout="vertical", color=CREAM),
    ], width=18, gap=14)
    help_ = col([
        p("<p>NEED HELP?</p>", color=ORANGE, css="bungry-footer-heading"),
        nav("bungry-footer-need-help", layout="vertical", color=CREAM),
    ], width=20, gap=14)
    connect = col([
        p("<p>CONNECT</p>", color=ORANGE, css="bungry-footer-heading"),
        widget("bungry-info-rows", {"use_settings": "yes"}),
        socials(),
        tag("Official links TBC", "dark"),
    ], width=30, gap=14, css="bungry-footer-dark")
    top = row([brand, quick, help_, connect], gap=64)
    bottom = container([
        p("<p>© 2026 Bungry Burger. All rights reserved.</p><p>New Zealand</p>", color=CREAM, css="bungry-footer-bottom"),
    ])
    wrapper = section([top, divider(), bottom], bg=FOREST, pad=(80, 40), gap=56, css="bungry-footer-dark")
    return {"version": "0.4", "title": "Bungry Footer", "type": "footer", "page_settings": {}, "content": [wrapper]}


FILES = {
    "bungry-home.json": home,
    "bungry-menu.json": menu,
    "bungry-our-story.json": our_story,
    "bungry-find-us.json": find_us,
    "bungry-contact-us.json": contact_us,
    "bungry-allergens-faqs.json": faqs,
    "bungry-privacy.json": privacy,
    "bungry-terms.json": terms,
    "bungry-header.json": header,
    "bungry-footer.json": footer,
}


def main() -> None:
    OUT.mkdir(parents=True, exist_ok=True)
    for name, build in FILES.items():
        _seq[0] = 0  # stable ids per file
        data = build()
        (OUT / name).write_text(json.dumps(data, ensure_ascii=False, indent=2) + "\n", encoding="utf8")
        print(f"wrote {name}")


if __name__ == "__main__":
    main()

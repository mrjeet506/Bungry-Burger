#!/usr/bin/env python3
"""Builds Elementor import files (type: page) for the seven Bungry Burger pages.

Output: elementor/templates/*.json. Import the page files via Templates > Saved Templates > Import Templates.
Import bungry-header.json and bungry-footer.json via Templates > Theme Builder (Elementor Pro).

Uses the Bungry widgets (elementor/widgets/) plus core Heading and Text widgets.
Colours reference the Bungry kit globals, so retheming in Site Settings flows through.
Unconfirmed details are written as "[CLIENT TO CONFIRM: ...]" text (remove at launch).
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
INK = g("colors", "bungryink")
CREAM = g("colors", "bungrycream")
CREAM_DARK = g("colors", "bungrycreamdark")


def widget(widget_type: str, settings: dict, css: str = "", globals_: dict | None = None) -> dict:
    settings = dict(settings)
    if css:
        settings["_css_classes"] = css
    if globals_:
        settings["__globals__"] = globals_
    return {
        "id": _id(),
        "elType": "widget",
        "widgetType": widget_type,
        "settings": settings,
        "elements": [],
    }


def container(children: list, *, row: bool = False, full: bool = False, bg: str | None = None,
              css: str = "", gap: int = 40, pad: int = 80, width: int | None = None) -> dict:
    settings = {
        "flex_direction": "row" if row else "column",
        "flex_direction_mobile": "column",
        "content_width": "full",
        "flex_gap": {"column": str(gap), "row": str(gap), "unit": "px", "size": gap},
        "padding": {"unit": "px", "top": str(pad), "right": "0", "bottom": str(pad), "left": "0", "isLinked": False},
        "padding_mobile": {"unit": "px", "top": "56", "right": "24", "bottom": "56", "left": "24", "isLinked": False},
    }
    if pad > 0:  # top-level sections get a 80px side gutter; columns do not
        settings["padding"]["right"] = "80"
        settings["padding"]["left"] = "80"
    if bg:
        settings["background_background"] = "classic"
        settings["background_color"] = {"bungrycream": "#F6F7EE", "bungryforest": "#185B28"}.get(bg.split("id=")[1], "#F6F7EE")
        settings["__globals__"] = {"background_color": bg}
    if width:
        settings["width"] = {"unit": "%", "size": width, "sizes": []}
        settings["width_mobile"] = {"unit": "%", "size": 100, "sizes": []}
    if css:
        settings["_css_classes"] = css
    return {"id": _id(), "elType": "container", "settings": settings, "elements": children, "isInner": False}


def row(*children: dict, gap: int = 80, pad: int = 80, bg: str | None = None) -> dict:
    return container(list(children), row=True, full=True, bg=bg, gap=gap, pad=pad)


def half(children: list, *, bg: str | None = None) -> dict:
    return container(children, full=False, bg=bg, width=50, pad=0, gap=20)


def h(title: str, level: str = "h2", color: str = FOREST) -> dict:
    return widget("heading", {"title": title, "header_size": level},
                  globals_={"title_color": color})


def p(html: str, color: str = INK, css: str = "") -> dict:
    return widget("text-editor", {"editor": html}, css=css, globals_={"text_color": color})


def eyebrow(text: str) -> dict:
    return p(f"<p>{text}</p>", color=INK, css="bungry-eyebrow")


def btn(text: str, url: str, style: str = "primary") -> dict:
    return widget("bungry-button", {
        "text": text,
        "link": {"url": url, "is_external": "", "nofollow": ""},
        "style": style,
        "size": "default",
        "arrow": "yes",
    })


def photo_slot(label: str, tag: str = "Photo TBC") -> dict:
    """Real photo slot with a styled fallback. Replace with an Image widget when photography is supplied."""
    return container(
        [
            p(f"<p><strong>PHOTO PLACEHOLDER</strong></p><p>{label}</p>", color=FOREST),
            widget("bungry-placeholder-tag", {"label": tag, "tone": "light"}),
        ],
        css="bungry-photo-slot",
        gap=12,
        pad=40,
    )


def section_heading(eyebrow_text: str, title: str, text: str = "", button: tuple | None = None) -> dict:
    s = {
        "eyebrow": eyebrow_text,
        "title": title,
        "title_tag": "h2",
        "text": text,
        "align": "left",
        "button_text": button[0] if button else "",
        "button_link": {"url": button[1] if button else ""},
    }
    return widget("bungry-section-heading", s)


def notice(kind: str, title: str, text: str) -> dict:
    return widget("bungry-notice", {"type": kind, "title": title, "text": text})


def page(title: str, sections: list) -> dict:
    return {"version": "0.4", "title": title, "type": "page", "page_settings": {"hide_title": "yes"}, "content": sections}


# ---------------------------------------------------------------- HOME
def home() -> dict:
    hero = row(
        half([
            eyebrow("FRESH. SEALED. DIFFERENT."),
            h("A NEW TAKE ON YOUR BURGER CRAVINGS.", "h1"),
            p("<p>Meet Bungry Burger — a familiar favourite with a distinctive sealed-burger twist. "
              "Explore our focused menu and find your new go-to.</p>", css="bungry-lead"),
            container([btn("Explore the menu", "/menu/", "primary"),
                       btn("Find us", "/find-us/", "outline-dark")], row=True, gap=16, pad=0),
        ]),
        half([photo_slot("Hero: close-up product photography showing the sealed edge and mark.", "Product photo TBC")]),
        bg=None,
    )

    signature = row(
        half([photo_slot("Sealed cutaway: add the Figma export or a real product photo.", "Visual TBC")]),
        half([
            eyebrow("THE SIGNATURE CONCEPT"),
            h("NOT JUST A BURGER. A BUNGRY."),
            p("<p>Our signature sealed-burger concept gives a familiar favourite a look of its own. "
              "Distinctive in shape, simple to choose and made to be remembered.</p>"),
            container([
                p("<p><strong>01</strong><br>MADE</p>"),
                p("<p><strong>02</strong><br>SEALED</p>"),
                p("<p><strong>03</strong><br>READY TO ENJOY</p>"),
            ], row=True, gap=24, pad=0),
            widget("bungry-placeholder-tag", {"label": "Preparation steps: confirm with kitchen", "tone": "light"}),
        ]),
        bg=None,
    )

    menu = container([
        section_heading("OUR MENU", "MEET THE BUNGRY LINE-UP.",
                        "Five burger concepts. Find the one that fits your craving.",
                        ("View the full menu", "/menu/")),
        widget("bungry-menu-cards", {
            "category": "", "limit": -1,
            "columns": "5", "columns_tablet": "2", "columns_mobile": "1",
            "show_price": "yes", "show_order": "yes",
            "allergen_link": {"url": "/food-allergens-faqs/"},
        }),
    ], full=True, pad=80, gap=40)

    pillars = [
        ("A DISTINCTIVE SIGNATURE FORM", "An instantly recognisable shape, designed to be remembered."),
        ("A FOCUSED MENU", "Five burger concepts, simple to choose from."),
        ("A RECOGNISABLE BRAND", "Bold, modern and playful, from the packaging to the product."),
        ("A WELCOMING FOOD EXPERIENCE", "Designed around the food and the people who eat it."),
    ]
    why = container([
        section_heading("WHY BUNGRY", "FRESH. SEALED. SIMPLE. MEMORABLE."),
        container([container([h(t, "h5"), p(f"<p>{b}</p>")], gap=10, pad=0) for t, b in pillars],
                  row=True, gap=24, pad=0),
    ], full=True, pad=80, gap=40, bg=None)

    kitchen = row(
        half([photo_slot("Kitchen or prep photo. Use only if the site has a visible kitchen.", "Confirm open kitchen")]),
        half([
            eyebrow("THE BUNGRY EXPERIENCE"),
            h("GOOD FOOD, MADE IN VIEW."),
            p("<p>The Bungry experience is designed around the food — from the preparation to the signature sealed finish.</p>"),
        ]),
    )

    cta = widget("bungry-cta-band", {
        "title": "YOUR NEXT BUNGRY IS CLOSER THAN YOU THINK.",
        "text": "Find your nearest Bungry, check today's hours and choose the best way to order.",
        "button1_text": "Find us", "button1_link": {"url": "/find-us/"},
        "button2_text": "Order online", "button2_link": {"url": ""},
    })

    social = container([
        section_heading("FOLLOW ALONG", "WHICH BUNGRY ARE YOU?",
                        "Follow along for product close-ups and behind-the-scenes prep."),
        widget("bungry-placeholder-tag", {"label": "Official social links TBC", "tone": "light"}),
    ], full=True, pad=64, gap=20)

    return page("Home", [container([hero]), container([signature]), menu, why, container([kitchen]),
                         container([cta], full=True, pad=64), social])


# ---------------------------------------------------------------- MENU
def menu() -> dict:
    intro = container([section_heading(
        "MENU", "THE BUNGRY LINE-UP",
        "Five burger concepts. One signature sealed-burger idea. Take a look through the line-up and choose your Bungry.")],
        full=True, pad=80)
    cards = container([widget("bungry-menu-cards", {
        "category": "", "limit": -1,
        "columns": "5", "columns_tablet": "2", "columns_mobile": "1",
        "show_price": "yes", "show_order": "yes",
        "allergen_link": {"url": "/food-allergens-faqs/"},
    })], full=True, pad=40)
    info = container([
        notice("info", "Check before you order",
               "Menu availability and ingredients may change. Check allergen information before you order."),
        btn("Allergens & FAQs", "/food-allergens-faqs/", "dark"),
    ], full=False, pad=40, gap=20)
    return page("Menu", [intro, cards, info])


# ---------------------------------------------------------------- OUR STORY
def our_story() -> dict:
    top = row(
        half([
            eyebrow("OUR STORY"),
            h("A FAMILIAR FAVOURITE, WITH A BUNGRY TWIST.", "h1"),
            p("<p>At Bungry, we believe a burger can be familiar and still feel different. Our idea centres on a "
              "distinctive sealed-burger format — a recognisable shape designed to give the Bungry experience its own "
              "identity. From the product presentation to the packaging and the way the brand shows up, every detail is "
              "intended to be simple, bold and memorable.</p>"),
        ]),
        half([photo_slot("Packaging: real production packaging photo.", "Packaging photo TBC")]),
    )
    close = container([
        h("ONE DISTINCTIVE BURGER. ONE RECOGNISABLE BRAND.", "h2"),
        btn("Explore the menu", "/menu/", "dark"),
    ], full=True, pad=80, gap=24, bg=None)
    return page("Our Story", [container([top]), close])


# ---------------------------------------------------------------- FIND US
def find_us() -> dict:
    intro = container([section_heading(
        "FIND US", "COME FIND YOUR BUNGRY.",
        "Have a question about the menu, your order or where to find us? Get in touch using the details below.")],
        full=True, pad=80)
    details = row(
        half([widget("bungry-info-rows", {"use_settings": "yes"})]),
        half([photo_slot("Map embed: add once the address is confirmed.", "Map TBC")]),
    )
    contact = container([
        h("SEND US A MESSAGE", "h3"),
        p("<p>Contact form goes here. Use Elementor Pro Form or Contact Form 7. Suggested fields: name (optional), "
          "email, topic, message, and a link to the privacy policy.</p>"),
        widget("bungry-placeholder-tag", {"label": "Form to be added", "tone": "light"}),
    ], full=True, pad=64, gap=16, bg=None)
    return page("Find Us", [intro, container([details]), contact])


# ---------------------------------------------------------------- ALLERGENS & FAQS
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


def faqs() -> dict:
    intro = container([section_heading(
        "FOOD ALLERGENS & FAQS", "FREQUENTLY ASKED QUESTIONS",
        "Answers about our menu, ordering and food information.")], full=True, pad=80)
    faq = container([widget("bungry-faq", {
        "items": [{"_id": _id(), "question": q, "answer": a} for q, a in FAQS],
        "open_first": "", "schema": "",
    })], full=False, pad=40)
    allergen = container([
        notice("warning", "ITEM-LEVEL ALLERGEN INFORMATION",
               "To be verified by the kitchen before publishing. Contact the team to discuss a specific item and its "
               "preparation before you order."),
        widget("bungry-placeholder-tag", {"label": "Allergen table TBC", "tone": "light"}),
    ], full=False, pad=40, gap=20)
    return page("Food Allergens & FAQs", [intro, faq, allergen])


# ---------------------------------------------------------------- LEGAL DRAFTS
def legal(title: str, intro: str, sections: list[tuple[str, str]]) -> dict:
    head = container([
        notice("warning", "DRAFT OUTLINE — NOT LEGAL ADVICE",
               "This page is a draft outline and must be reviewed before publishing."),
        h(title, "h1"),
        p(f"<p>{intro}</p>"),
    ], full=False, pad=64, gap=20)
    body = "".join(f"<h3>{i}. {t}</h3><p>{b}</p>" for i, (t, b) in enumerate(sections, 1))
    return page(title, [head, container([p(body)], full=False, pad=0)])


def privacy() -> dict:
    return legal(
        "Privacy Policy",
        "This policy explains how Bungry Burger handles personal information and analytics data.",
        [
            ("What we collect", "If you contact us, we collect the details you send. [CLIENT TO CONFIRM: contact form fields]"),
            ("Why we collect it", "To respond to your enquiry and to run the website."),
            ("Analytics and cookies", "[CLIENT TO CONFIRM: analytics, advertising and cookie tools in use, and whether consent is required]"),
            ("Sharing", "[CLIENT TO CONFIRM: any third parties that receive information, such as an ordering provider]"),
            ("Your rights", "You can ask what personal information we hold about you and ask us to correct it. "
                            "Contact: [CLIENT TO CONFIRM: monitored privacy email]"),
        ],
    )


def terms() -> dict:
    return legal(
        "Terms / Ordering Information",
        "This page explains ordering. Publish it only if online ordering is enabled.",
        [
            ("Ordering", "[CLIENT TO CONFIRM: ordering provider and URL]"),
            ("Pickup and delivery", "[CLIENT TO CONFIRM: pickup and delivery zones, and any fees]"),
            ("Payment", "[CLIENT TO CONFIRM: accepted payment methods]"),
            ("Prices", "Prices are shown in NZD. [CLIENT TO CONFIRM: any surcharges or conditions]"),
            ("Menu changes", "Menu availability and ingredients may change."),
            ("Allergens", "Please check allergen information before ordering."),
        ],
    )


# ---------------------------------------------------------------- THEME BUILDER HEADER / FOOTER
CREAM_G = g("colors", "bungrycream")


def nav(menu_slug: str, layout: str = "horizontal", color: str | None = None) -> dict:
    """Elementor Pro Nav Menu, pointed at a menu the demo importer creates."""
    settings = {"menu": menu_slug, "layout": layout, "mobile_layout": "full-screen", "align_items": "center"}
    globals_ = {"color_menu_item": color} if color else None
    return widget("nav-menu", settings, globals_=globals_)


def header() -> dict:
    bar = container([
        widget("theme-site-logo", {"width": {"unit": "px", "size": 182, "sizes": []}, "align": "left"}),
        nav("bungry-primary"),
        widget("bungry-button", {
            "text": "Order online", "link": {"url": "", "is_external": "", "nofollow": ""},
            "style": "primary", "size": "small", "arrow": "",
        }, css="bungry-header-order"),
    ], row=True, full=True, pad=19, gap=40, bg=None)
    return {"version": "0.4", "title": "Bungry Header", "type": "header", "page_settings": {}, "content": [bar]}


def footer() -> dict:
    brand = half([
        h("BUNGRY BURGER", "h3", color=CREAM_G),
        p("<p>FRESH. SEALED. DIFFERENT.</p>", color=CREAM_G, css="bungry-footer-tagline"),
        p("<p>Meet Bungry Burger. A fresh take on a familiar favourite, made memorable by our signature sealed-burger concept.</p>",
          color=CREAM_G),
    ])
    quick = container([
        p("<p>QUICK LINKS</p>", color=CREAM_G, css="bungry-footer-heading"),
        nav("bungry-footer-quick-links", layout="vertical", color=CREAM_G),
    ], gap=14, pad=0, width=20)
    help_ = container([
        p("<p>NEED HELP?</p>", color=CREAM_G, css="bungry-footer-heading"),
        nav("bungry-footer-need-help", layout="vertical", color=CREAM_G),
    ], gap=14, pad=0, width=20)
    connect = container([
        p("<p>CONNECT</p>", color=CREAM_G, css="bungry-footer-heading"),
        widget("bungry-info-rows", {"use_settings": "yes"}),
        widget("social-icons", {"social_icon_list": []}),
        widget("bungry-placeholder-tag", {"label": "Official links TBC", "tone": "dark"}),
    ], gap=14, pad=0, width=30, css="bungry-footer-dark")
    top = container([brand, quick, help_, connect], row=True, full=True, pad=0, gap=64)
    bottom = container([
        p("<p>© 2026 Bungry Burger. All rights reserved.</p><p>New Zealand</p>", color=CREAM_G, css="bungry-footer-bottom"),
    ], full=True, pad=0)
    wrapper = container([top, bottom], full=True, pad=80, gap=56, bg=FOREST, css="bungry-footer-dark")
    return {"version": "0.4", "title": "Bungry Footer", "type": "footer", "page_settings": {}, "content": [wrapper]}


FILES = {
    "bungry-home.json": home,
    "bungry-menu.json": menu,
    "bungry-our-story.json": our_story,
    "bungry-find-us.json": find_us,
    "bungry-allergens-faqs.json": faqs,
    "bungry-privacy.json": privacy,
    "bungry-terms.json": terms,
    "bungry-header.json": header,
    "bungry-footer.json": footer,
}


def main() -> None:
    OUT.mkdir(parents=True, exist_ok=True)
    for name, build in FILES.items():
        data = build()
        (OUT / name).write_text(json.dumps(data, ensure_ascii=False, indent=2) + "\n", encoding="utf8")
        print(f"wrote {name}")


if __name__ == "__main__":
    main()

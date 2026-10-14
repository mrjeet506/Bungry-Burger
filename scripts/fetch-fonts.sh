#!/usr/bin/env bash
# Re-downloads Bakbak One and Montserrat (500/600/700) as WOFF2 and rewrites assets/fonts/fonts.css
# to point at the local files. Nothing is loaded from Google at runtime.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
FONT_DIR="$ROOT/assets/fonts"
UA="Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120 Safari/537.36"

CSS_URL="https://fonts.googleapis.com/css2?family=Bakbak+One&family=Montserrat:wght@500;600;700&display=swap"

python3 -I - "$FONT_DIR" "$CSS_URL" "$UA" <<'PY'
import os, re, sys, urllib.request

font_dir, css_url, ua = sys.argv[1], sys.argv[2], sys.argv[3]
css = urllib.request.urlopen(urllib.request.Request(css_url, headers={"User-Agent": ua})).read().decode()

for url in sorted(set(re.findall(r"url\((https://fonts\.gstatic\.com[^)]+\.woff2)\)", css))):
    name = url.rsplit("/", 1)[1]
    with urllib.request.urlopen(url) as src, open(os.path.join(font_dir, name), "wb") as dst:
        dst.write(src.read())
    css = css.replace(url, name)

header = "/* Self-hosted: Bakbak One (headings) + Montserrat (body). Source: Google Fonts (OFL). Regenerate with scripts/fetch-fonts.sh */\n"
open(os.path.join(font_dir, "fonts.css"), "w", encoding="utf8").write(header + css)
print("fonts.css updated")
PY

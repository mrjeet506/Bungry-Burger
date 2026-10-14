#!/usr/bin/env bash
# Downloads the bundled Figma SVG exports listed in assets/svg/manifest.json.
#
# Usage:  FIGMA_TOKEN=figd_xxx ./scripts/fetch-figma-assets.sh
#
# Uses the Figma REST images endpoint (format=svg), then downloads each signed URL.
# Figma's signed asset URLs expire, so re-run this script rather than storing the URLs.
set -euo pipefail

if [[ -z "${FIGMA_TOKEN:-}" ]]; then
	echo "Set FIGMA_TOKEN (a personal access token with file read scope)." >&2
	exit 1
fi

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
MANIFEST="$ROOT/assets/svg/manifest.json"

python3 -I - "$ROOT" "$MANIFEST" "$FIGMA_TOKEN" <<'PY'
import json, os, sys, urllib.parse, urllib.request

root, manifest_path, token = sys.argv[1], sys.argv[2], sys.argv[3]
manifest = json.load(open(manifest_path, encoding="utf8"))
file_key = manifest["fileKey"]
items = manifest["svg"]
ids = ",".join(items.values())

api = f"https://api.figma.com/v1/images/{file_key}?" + urllib.parse.urlencode(
    {"ids": ids, "format": "svg", "svg_include_id": "false"}
)
req = urllib.request.Request(api, headers={"X-Figma-Token": token})
with urllib.request.urlopen(req) as resp:
    images = json.load(resp).get("images", {})

for rel, node_id in items.items():
    url = images.get(node_id)
    if not url:
        print(f"skip {rel}: no image returned for node {node_id}", file=sys.stderr)
        continue
    target = os.path.join(root, "assets", "svg", rel + ".svg")
    os.makedirs(os.path.dirname(target), exist_ok=True)
    with urllib.request.urlopen(url) as src, open(target, "wb") as dst:
        dst.write(src.read())
    print(f"ok   {rel}.svg")
PY

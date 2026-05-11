# Asset build pipeline

The SVGs under `resources/assets/svg/files/` are produced by
`build_emoji_assets.py`. The script is **idempotent and reproducible** --
re-running with the same source-version arguments produces byte-identical
output.

## Quick start

```bash
cd scripts
python3 -m venv .venv
.venv/bin/pip install -r requirements.txt

# Build the full asset set. Pulls Twemoji + OpenMoji + Unicode CLDR data
# into ./.downloads, then writes the categorised SVGs into
# ../resources/assets/svg/files/<set>/<category>/<slug>.svg
.venv/bin/python build_emoji_assets.py \
    --twemoji-version=17.0.0 \
    --openmoji-version=latest \
    --output ../resources/assets/svg

# Dry-run (no writes): useful in CI to catch source drift without
# committing 50 MB of regenerated SVGs.
.venv/bin/python build_emoji_assets.py --dry-run
```

## What the script does

1. **Fetch sources** into `./.downloads`:
   - Twemoji v17 SVG release ZIP from <https://github.com/jdecked/twemoji>
   - OpenMoji color + black SVG releases from <https://github.com/hfg-gmuend/openmoji>
   - Unicode CLDR `emoji-test.txt` from <https://unicode.org/Public/emoji/>
2. **Parse CLDR**: build the canonical codepoint -> (group, subgroup, name) map.
3. **Slugify names**: `"grinning face"` -> `"grinning-face"`. Sequences with
   multiple codepoints (skin tones, ZWJ family emojis) get descriptive slugs
   derived from the full CLDR short name.
4. **Per set**: copy the source SVG (named by codepoint) into
   `files/<set>/<group>/<slug>.svg`.
5. **Build indexes** at the root of `resources/assets/svg/`:
   - `codepoints.json` -- `{ "1f600": "smileys-emotion/grinning-face", ... }`
   - `names.json`      -- `{ "grinning-face": { codepoints, aliases, group }, ... }`
6. **Update `config.json`** with the per-set totals.
7. **Validate**: every Unicode RGI emoji must exist in every set (or be
   listed in `gaps.json` with a reason).

## CI uses

`tests.yml` runs `build_emoji_assets.py --dry-run --check-only` so a
breaking change in any upstream source (Twemoji renaming a file, Unicode
moving an emoji to a different group) fails CI loudly before it surprises
a downstream user.

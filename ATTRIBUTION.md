# Attribution

The PHP / Python / config code in this package is MIT-licensed (see `LICENSE`).
The **emoji SVG assets** shipped under `resources/assets/svg/files/` come from
upstream projects and retain their original licences. Each set is listed
below with its source, licence, and any attribution requirements.

## Twemoji (Twitter/X)

- **Set directory**: `resources/assets/svg/files/twemoji/`
- **Source**: <https://github.com/jdecked/twemoji>
- **Upstream version**: 17.0.0 (Unicode 17 / Emoji 17 spec)
- **Licence (code)**: MIT
- **Licence (assets)**: CC-BY 4.0
- **Attribution requirement**: when redistributing the SVGs, credit
  "Twitter, Inc and other contributors" and link back to the source.

## OpenMoji Color + OpenMoji Black

- **Set directories**:
  - `resources/assets/svg/files/openmoji-color/`
  - `resources/assets/svg/files/openmoji-black/`
- **Source**: <https://github.com/hfg-gmuend/openmoji>
- **Licence (code)**: LGPL-3.0
- **Licence (assets)**: CC-BY-SA 4.0
- **Attribution requirement**: credit "OpenMoji -- the open-source emoji
  and icon project. License: CC BY-SA 4.0" and link to <https://openmoji.org/>.
- **Share-alike notice**: CC-BY-SA 4.0 is *contagious for derivative works*.
  Modifying these SVGs and redistributing the modifications obliges you to
  release your modifications under CC-BY-SA 4.0. **Shipping the SVGs
  unmodified** (which is what this package does) does **not** trigger the
  share-alike clause for your downstream code.

## Index files

- `resources/assets/svg/codepoints.json` and `resources/assets/svg/names.json`
  are produced by the build script from public Unicode CLDR data and are
  released under the same MIT licence as the package code.

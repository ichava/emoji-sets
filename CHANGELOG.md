# Changelog

All notable changes to `ichava/emoji-sets` follow [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- Initial scaffold (Phase A0): repo layout matches the existing Ichava
  child-pack convention (composer.json, IconsServiceProvider, IconsConstants,
  Set + Category enums, IconComponent, config/, tests/, CI).
- `Set` enum (Twemoji, OpenMoji color, OpenMoji black) implementing
  `IconSetVariantInterface`; default set is Twemoji.
- `Category` enum mirroring the 10 Unicode CLDR emoji groups with
  human-readable labels and a canonical `ordered()` list for picker UIs.
- `resources/assets/svg/config.json` with set + category metadata.
- ATTRIBUTION.md explaining the per-set asset licences (CC-BY 4.0 for
  Twemoji, CC-BY-SA 4.0 for OpenMoji).
- `scripts/build_emoji_assets.py` -- reproducible ETL that pulls Twemoji
  v17 + OpenMoji + Unicode CLDR emoji-test.txt and assembles the
  categorised SVG tree. Idempotent and re-runnable; CI uses
  `--check-only` to fail on upstream drift.

### Not yet shipped (next phase)

- The actual ~12,000 SVG assets. The build script runs end-to-end but
  the output is gitignored until the v0.1.0 commit-the-assets pass.

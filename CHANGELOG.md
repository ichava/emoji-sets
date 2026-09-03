# Changelog

All notable changes to `ichava/emoji-sets` follow [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and [Semantic Versioning](https://semver.org/).

## [0.1.1] - 2026-09-02

### Added

- **The emoji assets.** 10,567 SVGs across three sets -- `twemoji` (3,435), `openmoji-color`
  (3,566) and `openmoji-black` (3,566) -- categorised into the nine CLDR groups.

  This package was tagged and published shipping **zero** SVGs (`V4`). A pack with no assets
  reports clean to every scanner, which is exactly the false-clean the corpus census was
  built to detect.

  The cause was three upstream versions being conflated into one. `current_version` read
  `17.0.0`, which `@twemoji/svg` has never published -- npm's latest is `15.0.0` -- so
  `npm pack` failed with an empty message. The build recipe additionally defaulted the
  Unicode emoji version to `17.0`, which Unicode has not released either, so it 404ed on
  `emoji-test.txt` before reaching npm at all. Neither failure was visible from the shipped
  package; it simply had nothing in it.

### Changed

- `resources/assets/svg/config.json` records what was actually vendored: Twemoji `15.0.0`,
  OpenMoji `15.1.0`, Unicode CLDR `16.0`. The three are independent and are now labelled as
  such, so the next refresh cannot repeat the conflation.

## [Unreleased]

### Not yet shipped

- **The emoji SVG assets.** The package ships the engine wiring and the
  upstream/CDN metadata; it vendors no SVGs (`find . -name '*.svg'` returns 0).
  The ETL that assembles them lives in
  [`ichava/maintainer-toolkit`](https://github.com/ichava/maintainer-toolkit)
  as `recipes/emoji_sets.py` with `config/emoji-sets.json`, and the assets are
  intended to land through an automated pull request. Until they do, use the
  CDN URLs documented in the README.

## [0.1.0] - 2026-08-31

First open-source release. Engine wiring only, by design and as the README
states: the package registers the sets, categories and component so a host can
build against the contract before the assets exist.

### Added

- Repository layout matching the Ichava child-pack convention: `composer.json`,
  `IconsServiceProvider`, `IconsConstants`, `Set` and `Category` enums,
  `IconComponent`, `config/`, `tests/`, CI.
- `Set` enum (Twemoji, OpenMoji color, OpenMoji black) implementing
  `IconSetVariantInterface`. Twemoji is the default set.
- `Category` enum mirroring the 10 Unicode CLDR emoji groups, with
  human-readable labels and a canonical `ordered()` list for picker UIs.
- `resources/assets/svg/config.json` carrying the set, category and upstream
  metadata.
- `ATTRIBUTION.md` recording the per-set asset licences: CC-BY 4.0 for Twemoji,
  CC-BY-SA 4.0 for OpenMoji.

### Requirements

- PHP `^8.4.1 || ^8.5`, `illuminate/support ^13.0`.
- `ichava/core ^0.1` and `laranail/package-tools ^0.1.0`. Neither `ichava/*` nor
  `laranail/*` is published on Packagist, so a consumer needs VCS repository
  entries for both; the package declares its own.

### Notes on the version number

This release is `0.1.0`, not `1.0.0`. A `v1.0.0` tag existed and did not
describe anything: it was cut from a dependency-migration commit while the
README already announced the package as "Phase A0 alpha (v0.1.0)" with no
vendored assets. An icon pack that ships zero icons is not a 1.0, and it reports
clean to every scanner precisely because there is nothing to scan. The tag now
matches what the package actually contains.

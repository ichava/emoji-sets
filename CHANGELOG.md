# Changelog

All notable changes to `ichava/emoji-sets` follow [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and [Semantic Versioning](https://semver.org/).

## [0.2.5] - 2026-09-21

### Changed

- **`ichava/core` widened to `^0.2.5 || ^0.3`.** Core `0.3.0` removes the icon-package
  scaffolder and its stub tree, which moved to `ichava/icon-package-scaffolder`. This package
  never used either, so it works unchanged on both series.

  Widened rather than raised on purpose. A caret on a `0.x` version pins the minor, so plain
  `^0.2.5` cannot resolve `0.3.0` and this package would have held every consumer back on the
  0.2 series for a removal that does not affect it. Raising it to `^0.3` instead would have
  forced a core upgrade on anyone deliberately staying on 0.2.x, for the same non-reason.
  Both series genuinely work, so the constraint says so.

## [0.2.4] - 2026-09-21

### Security

- **Floor raised to `ichava/core: ^0.2.5`.** Core `0.2.5` closes an address-notation gap in the
  pack update-check guard: `isPublicIp()` judged addresses by how they were written, so
  `::7f00:1` and `::a9fe:a9fe` — IPv4-compatible IPv6 spellings of `127.0.0.1` and of the
  `169.254.169.254` cloud-metadata address — were accepted while the same addresses in dotted
  form were refused. `^0.2.4` still permitted resolving to `0.2.4`, which has it.

  Weaker than the containment fixes in `0.2.4`: the notation was deprecated in 2006 and most
  stacks will not route it. The floor moves anyway, because a constraint that can resolve to a
  release with a known gap is the thing this rule exists to prevent.

## [0.2.3] - 2026-09-21

### Security

- **Floor raised to `ichava/core: ^0.2.4`.** Core `0.2.4` carries seven security fixes — post-
  sanitizer attribute gating, icon-path containment, off-document paint URLs, sanitizer policy
  flag enforcement, SVG driver containment, debug path leakage and pack update-check URL
  restriction. `^0.2.3` still permitted resolving to `0.2.3`, which has all seven. Raising a
  floor to exclude a known-broken release is not a pin; the constraint stays a range.

## [0.2.2] - 2026-09-16

### Changed

- **Requires `ichava/core: ^0.2.3`.** `0.2.2` decided readiness against columns the schema has
  never had, so `ichava::ichava-core.info status` reported `UNINITIALIZED` on a fully seeded
  database and both auto-seed listeners, which gate on the same check, never fired. The floor is
  raised rather than the range widened; it still tracks every `0.2.x` from `0.2.3` on.

## [0.2.1] - 2026-09-16

### Changed

- **Requires `ichava/core: ^0.2.2`.** The floor is raised rather than the range widened: `0.2.0`
  invoked six of its own Artisan commands by names it had just retired, and `0.2.1` still passed
  `migrate` a `--path` that resolved nowhere, so `database migrate` reported success and created
  no tables. `^0.2` admitted both. Raising a floor to exclude a known-broken release is not a
  pin — the range still tracks every `0.2.x` from `0.2.2` on.

## [0.2.0] - 2026-09-16

### Breaking

- **Requires `ichava/core: ^0.2`.** Core `0.2.0` moved its config key to
  `ichava.ichava-core.*` and renamed every Artisan command with no bare aliases, so a host
  application upgrading this pack has to upgrade core with it.

### Added

- `release.yml` — a `v*.*.*` tag now publishes a release whose body is that version's CHANGELOG
  section. It fails closed when the tagged version has no section, so a tag pointing at the
  wrong commit stops rather than publishing auto-generated notes.
- A `code-quality` workflow and the six composer scripts (`pint`, `pint-fix`, `lint`, `format`,
  `test`, `analyse`), with `phpstan/phpstan` at level 0. This pack had `laranail-pint` vendored
  but no script and no gate, so nothing ran the formatter and the code had drifted from the
  shared preset.
- Removed a stale `lint` job that invoked raw `vendor/bin/pint --test` instead of
  `laranail-pint`. It used Pint's default rules, so it contradicted the shared preset by
  construction and the repo could not satisfy both at once.

### Changed

- Third-party GitHub Actions pinned to the commit SHA of their latest release; `actions/*` keep
  floating on a major tag. A tag is mutable, so `@v4` is a promise the action's owner can
  rewrite — `tj-actions/changed-files` had every tag retagged to secret-dumping code in March
  2025. Pinning GitHub's own actions inside GitHub's own runner buys nothing, so they are left
  alone.
- The test harness reads `DB_CONNECTION`, so the suite targets SQLite, PostgreSQL, MySQL or
  MariaDB — the same environment variables as `ichava/core` and `ichava/browser`. SQLite runs
  enable `foreign_key_constraints`, which Laravel only applies when the key is present.
- Documentation names the renamed commands. The old bare names are gone rather than aliased, so
  a README telling someone to run `php artisan ichava:database seed` named a command that no
  longer exists.

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

### Added

- **`actionlint` runs on every pull request.** Nothing validated the workflow files at all:
  `release.yml` triggers only on `push: tags`, so a broken workflow was first observed as a
  release that refused to start — after the decision to release had been made.

  A YAML parse is not a substitute, and that is the sharp part. `yaml.safe_load` accepts a
  duplicate key and silently keeps the last one, so a double-applied patch that left
  `continue-on-error:` twice on a single step validated clean and would have failed only at tag
  time. `actionlint` rejects what Actions rejects.

  Checked against the defect rather than assumed: injecting that duplicate key, a typo'd step
  key, and an `if:` referencing a property that does not exist are all caught, while
  `yaml.safe_load` still parses the first of them without complaint.

### Fixed

- **`tests/Unit/` was never run.** `phpunit.xml.dist` declared a `Feature`
  testsuite and nothing else, so any unit test added here would have passed
  locally when invoked by path and been silently skipped by `composer test` and
  by CI. Every other pack in the family declares both suites; this one did not.

  It nearly hid itself. The `Feature` suite is 7 tests / 15 assertions, and the
  unit test added in this change is **also** 7 tests / 15 assertions, so the
  totals matched exactly whether or not the new file ran.

### Added

- **`resources/lang/` and `resources/views/`, bringing this pack to the canonical
  resource shape.** It shipped neither.

  This is the only pack in the family with two taxonomies, so it is the only one
  whose lang file carries both `sets` (3 cases) and `categories` (10 cases),
  each with descriptions. Set names are proper nouns from their upstream
  projects and are not translated; their descriptions are.

  `name` and `description` are **deliberately omitted**: `IconRegistry` reads
  those from `resources/assets/svg/config.json`, which is canonical, and the
  packs that kept a second copy had already drifted from it unnoticed.

  `views/components/` is an empty placeholder kept for family consistency.
  Nothing registers it -- the component path renders SVG directly.

  `tests/Unit/ResourceShapeTest.php` pins the shape, holds both enums in step,
  and asserts every `Set` case has a directory under `files/` -- a lang key
  naming a set that does not ship is as wrong as one that does not exist.

  > Nothing loads these translations yet. No pack calls `hasTranslations()`.

### Fixed

- **A failed SBOM download no longer takes the whole release down.** `release.yml` generates the
  SBOM before it publishes, and the Syft installer fetches its checksums from GitHub's
  release-asset CDN. On 2026-09-21 that answered `504` for about twenty minutes, failing the job
  four times *before* the publish step — so the tag existed with no release behind it, which is
  the drift the release table exists to catch, produced by the release machinery itself.

  Two changes. The step now retries once after 45 seconds, which covers a single transient `504`
  — the common case. And a second failure no longer fails the job: the release publishes without
  the asset and emits a `::warning::` naming the re-run.

  **The two failure states are not equally bad, and that asymmetry is the whole design.** A
  release missing an attachment is repaired by re-running this workflow, which re-attaches it. A
  tag with no release persists silently until a person notices. Preferring the recoverable one
  is worth the loss of "every release always carries an SBOM" as an absolute.

  `fail_on_unmatched_files: false` is now stated on the publish step. It is already the action's
  default, but the point of this change is that a missing SBOM must not fail the publish, so it
  should not rest on a default a future reader has to know.

### Not yet shipped

- **The emoji SVG assets.** The package ships the engine wiring and the
  upstream/CDN metadata; it vendors no SVGs (`find . -name '*.svg'` returns 0).
  The ETL that assembles them lives in
  [`ichava/maintainer-toolkit`](https://github.com/ichava/maintainer-toolkit)
  as `recipes/emoji_sets.py` with `config/emoji-sets.json`, and the assets are
  intended to land through an automated pull request. Until they do, use the
  CDN URLs documented in the README.

### Security

- **Floor raised to `ichava/core: ^0.2.8`.** Core `0.2.8` fixes two issues a pack inherits
  through the engine: `%` and `_` in a search query acted as `LIKE` wildcards, widening results
  and forcing full-table scans; and the icon watcher followed symlinks and read files of
  unbounded size, so a link inside a watched directory pointed the reader anywhere on disk.

  `^0.2.5` still permitted resolving to `0.2.5`, `0.2.6` or `0.2.7`, all of which carry both.
  The `|| ^0.3` arm is unchanged — core `0.3.0` moved the scaffolder out but left the engine,
  registry, seeder and SVG pipeline untouched, so an installed pack is unaffected by it.

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

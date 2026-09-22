# Changelog

All notable changes to `ichava/icon-sets-emoji` follow [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Changed

- **`tests.yml` now carries the trigger and runtime settings the other packs
  already had.** Four differences, every one in the same direction -- the
  reference packs had it, this one did not:

  | | before | after |
  |---|---|---|
  | `paths-ignore` | none | `'**.md'` |
  | `branches` | `[main]` | unchanged |
  | `concurrency` | none | cancel in-progress |
  | `timeout-minutes` | none, so GitHub's default of 360 | 15 |

  Found by a natural experiment rather than by reading: six identical
  CHANGELOG-only pull requests opened on the same day ran different checks.
  `browser`, `icon-sets-flag` and `icon-sets-tabler` ran only `Changelog`; this
  pack ran the full PHP matrix, because the `'*.md'` to `'**.md'` sweep had
  changed only the filters that already existed.

  Every one of these fails in the safe direction -- running more than necessary,
  never less -- so this is CI minutes on a free-plan allowance rather than a gate
  that stopped firing. The missing `timeout-minutes` was the expensive one: a
  hung job ran to six hours.

  The `on:` and `concurrency:` blocks are now byte-identical to
  `icon-sets-flag`'s, and a CHANGELOG-only pull request stays gated here because
  `changelog.yml` has its own trigger on that file.

- **The markdown path filter now matches markdown at any depth.**
  `code-quality.yml` and `tests.yml` carried `paths-ignore: '*.md'`. In GitHub's
  filter syntax a single `*` does not cross a `/`, so that pattern matched a
  root-level `README.md` and nothing else -- every edit under `docs/` ran the
  full PHP suite and the static-analysis job, which is precisely what the filter
  existed to skip. `'**.md'` matches at any depth.

  Worth stating which direction this failed in, because it decides how urgent it
  was: a broken `paths-ignore` runs **more** than it should, never less. The cost
  was CI minutes on a free-plan allowance, not a gate that stopped firing.

### Removed

- **`metadata.homepage` is absent rather than naming this package.** It read
  `https://github.com/ichava/icon-sets-emoji`, duplicating
  `metadata.repository`, and the browser API ships both side by side through
  `publicMetadata()` -- two identical links under different names.

  The field means the **upstream project's** own site, and this pack has no
  single one: it vendors Twemoji and OpenMoji across three sets, both already
  carried in `metadata.authors` with their URLs. Naming either as "the homepage"
  would misdescribe two thirds of the icons here, so the honest value is no
  value. Every reader already handles absence -- `IconRegistry` reads it with
  `?? null`, and `array_intersect_key` simply omits it.

  A test asserts the **absence**, rather than skipping the field, so filling it
  back in with one of our own URLs fails here instead of shipping.

## [0.3.2] - 2026-09-21

### Fixed

- **Every documentation page carried the index breadcrumb twice.**
  `[← Docs index](../README.md#documentation)` sat on line 1 as well as in its correct position
  under the closing `---`, on all 3 pages this pack ships (`attribution.md`, `categories.md`, `sets.md`). The stray copy is deleted
  and the footer is untouched.

  It shipped in `v0.3.1`. The pass that introduced it added a footer to pages that already had a
  conforming one, so the defect is **duplication, and the fix is deletion** -- the first report
  described it as a misplacement needing reversal, which would have produced two footers instead
  of two headers.

  Measured across the estate rather than from the pull requests that caused it: **16 pages over
  the five packs, which is every markdown page in `docs/` in all five.** A count taken from those
  originating diffs said 11, and a second count taken with a shell loop that defaulted a failed
  API read to zero said fewer still -- an empty response and a clean page are not the same thing,
  and only one of them is true.

## [0.3.1] - 2026-09-21

### Changed

- **`ichava/core` `^0.4` is accepted.** The constraint read `^0.2.8 || ^0.3`, and a caret on a
  `0.x` version pins the *minor*, so `0.4.0` did not satisfy it at all. An application that wanted
  core `0.4` could not install this pack beside it, and Composer reported that as a conflict on
  `ichava/core` rather than on the pack that was holding it back.

  The branch is **added, not substituted**. `^0.2.8` and `^0.3` keep resolving, because nothing
  here calls an API that `0.4` introduced, so raising the floor would strand 0.2 and 0.3 consumers
  for no gain.

### Fixed

- **`branch-alias` names the series `main` is on, not the one before it.**
  `dev-main` was aliased to `0.2.x-dev` while this package has been on the 0.3
  series since `v0.3.0`. The alias is what a path or VCS consumer sees when it
  tracks `dev-main`, so `0.2.x-dev` fails a `^0.3` constraint outright and
  Composer reports it as a conflict on this package rather than as a stale
  alias.

  Measured: `0.2.9999999.9999999-dev` does not satisfy `^0.3` or
  `^0.3 || ^0.4`; `0.3.9999999.9999999-dev` satisfies both. `demos/ichava-app`
  does not catch this because it requires every package at `*@dev`, which
  matches either alias -- the integration check is structurally blind to it.

- **`ATTRIBUTION.md` named the wrong version of the assets it attributes.** It read
  *"Upstream version: 17.0.0 (Unicode 17 / Emoji 17 spec)"* under Twemoji. The shipped assets are
  npm `@twemoji/svg` 15.0.0, repository tag `v15.1.0`, and the categorisation used CLDR 16.0 —
  so both halves were wrong. **This is the document a redistributor relies on**, which makes a
  wrong version here different in kind from a wrong version in a README.

  OpenMoji had no version line at all; it now carries `15.1.0`, from `additional_sources`. One
  section versioned and one not is the asymmetry that let the other number go unread.

- **`package.upstream_version` still said `twemoji@17.0.0 + openmoji@latest`.** A *second*
  version field, in the same file as the one corrected earlier and in a different block — so the
  earlier fix to `upstream.current_version` left the file self-contradictory.

  It is now `15.0.0`, matching. The composite form could not survive anyway:
  `maintainer-toolkit`'s `version_keys` defaults to
  `["upstream.current_version", "package.upstream_version"]`, documented as *"All are read
  (first hit wins) and all are written"* — so the next sync writes a bare version string over it.
  A field the tooling treats as holding one version cannot hold two.

  > **Found by sweeping the package after declaring it clean.** The Sets-table fix merged, a
  > grep for `17.0.0` was run, and the conclusion *"only CHANGELOG keeps the number"* was written
  > in the same breath as output listing two live sites. The grep was right; the sentence was
  > not.

- **The `## Sets` table named a Twemoji release that did not produce the shipped assets, and its
  heading said the whole table was unshipped.** The second is what let the first survive: a
  reader who believes a table describes a future state does not check the version in it.

  `## Sets (once Phase A1 lands)` — all three sets ship today, on `main`, and have for a while:

  ```
  resources/assets/svg/files/twemoji         3,435
  resources/assets/svg/files/openmoji-color  3,566
  resources/assets/svg/files/openmoji-black  3,566
  ```

  The version was `jdecked/twemoji v17.0.0`. That tag is real and the link worked — it simply is
  not what the vendored assets came from. `config.json` records npm `15.0.0`, whose repository
  tag is `v15.1.0`. **A working link under a label naming the wrong thing**, the same class as
  the README cross-references fixed earlier and invisible to a link checker for the same reason.

  `openmoji` now carries its version too. One row versioned and two not is the asymmetry that let
  a stale number sit unexamined; the table is self-checking against `config.json` now, and says
  so.

- **`config.json` fed one `{version}` into three templates whose upstreams version
  independently.** `twemoji_github_raw` read `.../jdecked/twemoji/v{version}/...`, so a caller
  substituting `current_version: 15.0.0` — the documented pattern — built `v15.0.0`, **a tag that
  repository has never had.** npm publishes `@twemoji/svg` 15.0.0; the repository's 15-line tag
  is `v15.1.0`. The two numbers are not the same artifact and never were.

  The template now carries **`{twemoji_github_tag}`**, with the value beside `current_version`:

  ```json
  "current_version":    "15.0.0",
  "twemoji_github_tag": "v15.1.0",
  ```

  **The placeholder is deliberately not `{version}`.** A caller who substitutes only `{version}`
  now gets a URL with `{twemoji_github_tag}` still in it — visibly unfinished — rather than a
  well-formed `v15.0.0` that 404s and looks like an upstream outage. Failing legibly beats
  failing plausibly.

  This is the defect `_vendored_note` already warned about, one field lower down: *"They move
  independently and conflating them is what left this pack empty."* The note said it of
  `current_version` and the `cdn` block conflated them anyway.

  All five templates were substituted from the config's own fields and fetched: `200` on each.

  > Safe in-tree: `JsonConfigConstants::getUpstreamCdnUrls()` returns the map untouched and
  > nothing in the estate interpolates it, the maintainer-toolkit reads `version_file` rather
  > than `cdn`, and no test pins the shape. The exposure is external callers following the
  > documented `str_replace` example — which is exactly who the visible placeholder is for.

- **The CDN block advertised a Twemoji version that has never been published.** The README
  hardcoded `@twemoji/svg@17.0.0` while `config.json` records `current_version: 15.0.0`. That is
  not drift — `registry.npmjs.org/@twemoji/svg/17.0.0` answers **404**, and so did both CDN URLs
  built from it. It is the same number `V4` caught in `config.json`, reintroduced in prose.

- **The GitHub raw URL needed `v15.1.0`, not `v15.0.0`.** The three Twemoji templates do not
  share a version space: npm `@twemoji/svg` publishes `15.0.0`, and the `jdecked/twemoji`
  repository has no `v15.0.0` tag at all — its 15-line tag is `v15.1.0`. A blanket
  `17.0.0` → `15.0.0` would have fixed two URLs and **broken a third that worked**.

  > That mismatch is also live in `config.json`, which feeds one `{version}` into all three
  > templates. `twemoji_github_raw` interpolated with `15.0.0` yields a tag that does not exist.
  > Left alone here — it is config, not prose, and the update checker reads it.

- **`{codepoint}` is lower-case for Twemoji and UPPER-case for OpenMoji.** The README documented
  one convention (`1f600`) for five URLs across two upstreams with different filename casing, so
  following it served a Twemoji glyph and 404'd on both OpenMoji templates. A caveat now says so
  where the OpenMoji block starts.

  All five URLs were checked end to end against a real codepoint rather than reasoned about:
  `200` on each, with the casing each block states.

- **The README's link label named the old central docs repo.** The URL was already correct and
  points at the hosted `maintainer-toolkit` page, while the text beside it still read
  `ichava/documentation/icon-pack-upstream-tracking.md`. The label now names the page the link
  opens.

  **No link checker sees this class.** The label is a code span, not a target, so the link
  resolves and the text next to it is wrong — `lychee` and every `](...)` sweep pass it. Found
  by grepping for `` `…documentation/….md` `` rather than for links, after the estate-wide link
  scan came back at zero.

- **Every usage example in this pack was wrong, and the rebrand caused only one of the four
  defects.** `README.md` and `IconComponent`'s docblock disagreed with each other, and neither
  matched what the provider registers.

  | Was | Is | Why |
  |---|---|---|
  | `<x-ichava-emoji-sets:icon>` (README) | `<x-icon-sets-emoji-icon>` | wrong prefix **and** wrong shape |
  | `<x-ichava-emoji-sets::icon>` (docblock) | `<x-icon-sets-emoji-icon>` | the same tag, spelled a third way |
  | `<x-ichava:icon>` | `<x-ichava::icon>` | core registers `ichava::icon` |
  | `ichava/icon-sets-emoji:twemoji/…` | `ichava/icon-sets-emoji::twemoji/…` | the path separator is `::` |

  **The tag is derived, not written down.** `Support\ServiceProvider::loadBladeComponent()`
  registers `Blade::component("{$packageName}-icon", …)` and this pack passes
  `packageName: 'icon-sets-emoji'`, so the tag is `<x-icon-sets-emoji-icon />` and appears
  nowhere in the source to grep for. Read off the registration rather than pattern-matched from
  the old string, and cross-checked against `icon-sets-flag`'s README, which had it right.

  The path separator is `config('ichava.ichava-core.separators.path', '::')`, so the
  single-colon forms resolved to nothing. Anyone copying those four lines got no icon.

## [0.3.0] - 2026-09-21

### Changed

- **Renamed to `ichava/icon-sets-emoji`.** The composer package, the GitHub repository and the local
  directory now all read `icon-sets-emoji`, restoring the one-name rule the ecosystem relies on.

  **Breaking, and that is why the minor moves.** A `0.x` caret pins the minor, so `^0.2` will
  not resolve to `0.3.0` -- consumers move deliberately rather than by accident.

  | Surface | Was | Now |
  |---|---|---|
  | Composer package | `ichava/icon-sets-emoji` | `ichava/icon-sets-emoji` |
  | PHP namespace | `Simtabi\Laranail\Ichava\IconSetsEmoji` | `Simtabi\Laranail\Ichava\IconSetsEmoji` |
  | Config file and key | `config/emoji-sets.php` | `config/icon-sets-emoji.php` |

  The config **filename** must match the package short name or the key silently doubles and
  every `config()` read returns `null` -- the `V39` defect that once left an entire shipped
  config inert.

  **Upstream references are deliberately untouched.** The vendor this pack tracks shares the
  token with our old name; a blanket rename would have aimed the update checker at a package
  that does not exist and broken the CDN templates, failing in a host app rather than in CI.

## [0.2.6] - 2026-09-21

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

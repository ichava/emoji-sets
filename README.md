# ichava/emoji-sets

Multi-source emoji bundle for Laravel. Twemoji v17 (Twitter/X) + OpenMoji
color + OpenMoji black, all in one Composer package, categorised by
Unicode CLDR groups, served through the Ichava icon engine.

[![Tests](https://github.com/ichava/emoji-sets/actions/workflows/tests.yml/badge.svg)](https://github.com/ichava/emoji-sets/actions/workflows/tests.yml)

> **Status: Phase A0 alpha (v0.1.0).** The Composer package, service
> provider, enums, indexes, and CDN configuration are wired and tested.
> **The bundled SVG assets are not yet committed.** The maintainer
> toolkit ([`ichava/maintainer-toolkit`](https://github.com/ichava/maintainer-toolkit))
> runs the Twemoji + OpenMoji + CLDR ETL in CI -- the assets land
> through an automated PR. Until Phase A1 (the first asset drop) ships
> v1.0, `composer require ichava/emoji-sets` gives you the engine
> wiring and the CDN config, **not the vendored SVGs**. Use the CDN
> URLs (below) in the meantime.

## Why

The Ichava ecosystem ships per-vendor icon packs (`tabler-icons`,
`metronic-icons`, `bundled-icons`). This pack does the same for emojis --
one install gets you ~12,000 SVGs covering Unicode 17, organised by both
*source style* (Twemoji / OpenMoji) and *Unicode group* (smileys-emotion,
people-body, animals-nature, ...).

This package replaces the older `laranail/flagmoji` (a.k.a. `simtabi/laflamoji`),
which mixed flags and emojis, used codepoint filenames, and required
`blade-ui-kit/blade-icons`. See the `flagmoji` repo's `DEPRECATED.md` for the
migration table.

## Install

```bash
composer require ichava/emoji-sets
```

That's it -- service-provider auto-discovery wires the pack into the
Ichava engine.

## Usage

```blade
{{-- Full form: pick a set, then Unicode CLDR group, then emoji slug --}}
<x-ichava-emoji-sets::icon name="twemoji/smileys-emotion/grinning-face" />

{{-- Default set ("twemoji"); short form skips the set segment --}}
<x-ichava-emoji-sets::icon name="smileys-emotion/grinning-face" />

{{-- Through the generic Ichava engine --}}
<x-ichava::icon name="ichava/emoji-sets::twemoji/flags/flag-japan" />

{{-- Helper function --}}
{{ ichava('ichava/emoji-sets::openmoji-black/objects/light-bulb', ['class' => 'w-6 h-6']) }}
```

## Configuration

```php
// config/emoji-sets.php (publishable)
return [
    'set' => [
        'name'   => 'emoji-sets',
        'prefix' => 'emoji',
    ],

    // Which set the short form resolves to when no set is specified.
    'default_set' => env('ICHAVA_EMOJI_DEFAULT_SET', 'twemoji'),
];
```

## Sets shipped (v1.0)

| Set | Style | Source | Assets licence |
|---|---|---|---|
| `twemoji` (default) | Detailed colourful (Twitter/X look) | [jdecked/twemoji v17.0.0](https://github.com/jdecked/twemoji) | CC-BY 4.0 |
| `openmoji-color` | Flat colourful, outlined | [hfg-gmuend/openmoji](https://github.com/hfg-gmuend/openmoji) | CC-BY-SA 4.0 |
| `openmoji-black` | Monochrome outline | Same as above | CC-BY-SA 4.0 |

See [`ATTRIBUTION.md`](ATTRIBUTION.md) for full attribution requirements.

## Categories

The 10 Unicode CLDR groups, in canonical order:

1. `smileys-emotion`
2. `people-body`
3. `component` -- skin tones, hair styles
4. `animals-nature`
5. `food-drink`
6. `travel-places`
7. `activities`
8. `objects`
9. `symbols`
10. `flags` -- country / regional indicator emojis

Each group lives at `resources/assets/svg/files/<set>/<group>/`. Filenames
are CLDR canonical short-name slugs (e.g. `grinning-face.svg`,
`flag-united-states.svg`). The repo also ships `codepoints.json` and
`names.json` indexes for codepoint-based lookups.

## Building the assets

Assets are committed to the repo, so a `composer require` install is
all-you-need. Refreshing them from upstream (new Twemoji release, new
OpenMoji drop, new Unicode CLDR cycle) is a **maintainer-side** step
that runs in CI via [`ichava/maintainer-toolkit`](https://github.com/ichava/maintainer-toolkit) -- the
Docker-based toolkit that owns every pack's asset pipeline. Cron opens
a PR with the refreshed SVGs; a human reviews + tags a new release.

## CDN endpoints (skip vendoring entirely)

If you'd rather not ship 22MB of SVGs inside your composer install,
serve them from a CDN. The pack registers its CDN URL templates in
`config.json` so other tooling can read them too; the canonical
templates are:

### Twemoji (recommended; CC-BY 4.0)

```
https://cdn.jsdelivr.net/npm/@twemoji/svg@17.0.0/{codepoint}.svg
https://unpkg.com/@twemoji/svg@17.0.0/{codepoint}.svg
https://raw.githubusercontent.com/jdecked/twemoji/v17.0.0/assets/svg/{codepoint}.svg
```

`{codepoint}` is the dash-joined hex codepoint (e.g. `1f600` for 😀,
`1f1fa-1f1f8` for 🇺🇸).

### OpenMoji color (CC-BY-SA 4.0)

```
https://cdn.jsdelivr.net/gh/hfg-gmuend/openmoji@latest/color/svg/{codepoint}.svg
```

### OpenMoji black (CC-BY-SA 4.0)

```
https://cdn.jsdelivr.net/gh/hfg-gmuend/openmoji@latest/black/svg/{codepoint}.svg
```

### Codepoint lookup

The pack ships `resources/assets/svg/codepoints.json` mapping every
canonical slug to its codepoint, so you can resolve
`smileys-emotion/grinning-face` -> `1f600` at runtime and build a CDN
URL on the fly.

## Upstream tracking

This pack participates in Ichava's upstream-tracking system. Run

```bash
php artisan ichava:icons:check-updates --package=ichava/emoji-sets
```

to see whether a newer Twemoji or OpenMoji release exists. The check
hits `registry.npmjs.org` (and supplementary GitHub releases for
OpenMoji), caches results for 12 hours, and dispatches
`IconPackUpdateAvailable` events the host app can route to Slack /
email / dashboards.

See [`ichava/documentation/icon-pack-upstream-tracking.md`](https://github.com/ichava/documentation/blob/main/icon-pack-upstream-tracking.md)
for the full schema + how to subscribe to update events.

## Status

**Alpha (v0.1.0).** Phase A0 skeleton committed. Phase A1 (full asset
build) lands next.

## Licence

MIT code; CC-BY 4.0 + CC-BY-SA 4.0 for the shipped SVG assets. See
[`LICENSE`](LICENSE) and [`ATTRIBUTION.md`](ATTRIBUTION.md).

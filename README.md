# ichava/emoji-sets

Multi-source emoji bundle for Laravel. Twemoji v17 (Twitter/X) + OpenMoji
color + OpenMoji black, all in one Composer package, categorised by
Unicode CLDR groups, served through the Ichava icon engine.

[![Tests](https://github.com/ichava/emoji-sets/actions/workflows/tests.yml/badge.svg)](https://github.com/ichava/emoji-sets/actions/workflows/tests.yml)

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
all-you-need. To rebuild from upstream sources (Twemoji + OpenMoji
releases + Unicode CLDR), see [`scripts/README.md`](scripts/README.md).

## Status

**Alpha (v0.1.0).** Phase A0 skeleton committed. Phase A1 (full asset
build) lands next.

## Licence

MIT code; CC-BY 4.0 + CC-BY-SA 4.0 for the shipped SVG assets. See
[`LICENSE`](LICENSE) and [`ATTRIBUTION.md`](ATTRIBUTION.md).

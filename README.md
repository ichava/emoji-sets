# ichava/icon-sets-emoji

[![Tests](https://github.com/ichava/icon-sets-emoji/actions/workflows/tests.yml/badge.svg)](https://github.com/ichava/icon-sets-emoji/actions/workflows/tests.yml)
[![Code Quality](https://github.com/ichava/icon-sets-emoji/actions/workflows/code-quality.yml/badge.svg)](https://github.com/ichava/icon-sets-emoji/actions/workflows/code-quality.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

> Emoji for the Ichava Laravel icon ecosystem — 10,567 SVGs across Twemoji, OpenMoji colour and OpenMoji black.

This package is not published to Packagist, so there is no registry-version badge to show. Requires [`ichava/core`](https://opensource.simtabi.com/documentation/ichava/core/); targets PHP `^8.4.1 || ^8.5` on Laravel `^13`.

## Install

```bash
composer require ichava/icon-sets-emoji
php artisan ichava::ichava-core.database seed --package=ichava/icon-sets-emoji
```

The seed is not optional — until it runs the registry holds no rows for this pack and every lookup returns nothing. See core's [installation guide](https://opensource.simtabi.com/documentation/ichava/core/installation).

## <a name="documentation"></a>Documentation

Full documentation is at **[opensource.simtabi.com/documentation/ichava/icon-sets-emoji](https://opensource.simtabi.com/documentation/ichava/icon-sets-emoji/)**.

### This pack

- [Installation](docs/installation.md) — requirements, the repositories block, seeding
- [Getting started](docs/getting-started.md) — your first icon from this pack
- [Configuration](docs/configuration.md) — this pack's config key, and what is core's instead
- [Architecture](docs/architecture.md) — what it ships, what it delegates, and why
- [Release](docs/release.md) — how a version is cut, and when the core floor moves
- [Sets](docs/sets.md) — the three sets, their counts, and how to pick one
- [Categories](docs/categories.md) — the category tree and how to address a codepoint
- [Attribution](docs/attribution.md) — per-set licence terms, which differ, and where versions are recorded

### Shared across every pack

- [Use an icon pack](https://opensource.simtabi.com/documentation/ichava/core/recipes/use-an-icon-pack) — addressing icons, in Blade and in PHP
- [Seed pack icons](https://opensource.simtabi.com/documentation/ichava/core/recipes/seed-pack-icons) — the seeding pipeline and its options
- [Check pack updates](https://opensource.simtabi.com/documentation/ichava/core/recipes/check-pack-updates) — the update checker and what its statuses mean
- [Serve icons from a CDN](https://opensource.simtabi.com/documentation/ichava/core/recipes/serve-icons-from-a-cdn) — reading this pack's CDN templates out of `config.json`

Its upstream is `Twemoji and OpenMoji`; run core's [check pack updates](https://opensource.simtabi.com/documentation/ichava/core/recipes/check-pack-updates) recipe to see whether a newer release exists.

## Contributing & security

See [CONTRIBUTING.md](CONTRIBUTING.md). Report vulnerabilities privately through [SECURITY.md](SECURITY.md) — never in a public issue.

## License

MIT. © Simtabi LLC. See [LICENSE](LICENSE).

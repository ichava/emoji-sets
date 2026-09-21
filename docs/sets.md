[← Package README](../README.md#pack-specific-docs)

# Sets

*Reference.*

Three emoji styles, addressed as separate variants under one package. The `Set` enum in
`src/Enums/Set.php` is the source of truth.

| Set | Path prefix | Style | Upstream |
|---|---|---|---|
| `twemoji` | `twemoji/<name>` | full colour, Twitter/X house style. **The default.** | [jdecked/twemoji](https://github.com/jdecked/twemoji) |
| `openmoji-color` | `openmoji-color/<name>` | full colour, OpenMoji house style | [OpenMoji](https://openmoji.org) |
| `openmoji-black` | `openmoji-black/<name>` | single-colour line art, follows `currentColor` | [OpenMoji](https://openmoji.org) |

`openmoji-black` is the only set that responds to a colour prop; the other two carry their own
fills.

## Addressing an emoji

```blade
<x-ichava:icon name="ichava/icon-sets-emoji:twemoji/grinning-face" class="w-6" />
<x-ichava:icon name="ichava/icon-sets-emoji:openmoji-black/rocket" class="w-6 text-indigo-600" />
```

## Assets are not vendored yet

**This package ships no SVGs.** `find . -name '*.svg'` returns 0. What it ships is the wiring: the
provider, the `Set` and `Category` enums, the Blade component and the upstream metadata, so a host
can build against the contract before the artwork lands.

Until it does, render from a CDN using the URLs in the package README. The
[maintainer toolkit](https://github.com/ichava/maintainer-toolkit) runs the Twemoji + OpenMoji +
CLDR pipeline and the assets arrive through an automated pull request.

---

[← Docs index](../README.md#pack-specific-docs)

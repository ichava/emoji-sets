# Getting started

*How-to guide.* Your first icon from this pack, once [Installation](installation.md) is done.

## Render one

```blade
<x-ichava::icon name="ichava/icon-sets-emoji::<set>/<name>" class="w-6 h-6" />
```

The component is core's and is generic: the pack is named in the path, not in the tag. Which
`set` values this pack accepts is the subject of [Sets](sets.md).

## Or from PHP

```blade
{{ ichava('ichava/icon-sets-emoji::<set>/<name>')->class('w-5 h-5') }}
```

Both spellings of the path resolve to the same icon -- slash and dot are normalised by one
resolver, documented in core's [icon path format](https://opensource.simtabi.com/documentation/ichava/core/tools/icon-path-format).

## If nothing renders

Almost always the seed has not run. Check with:

```bash
php artisan ichava::ichava-core.info icons --package=ichava/icon-sets-emoji
```

Zero rows means [Installation](installation.md) step 4 is outstanding. Core's
[troubleshooting](https://opensource.simtabi.com/documentation/ichava/core/troubleshooting) covers the rest.

## See also

- [Sets](sets.md) -- what this pack ships and how to address it
- [Customisation](customization.md) -- colour, size, and this pack's own knobs
- [Use an icon pack](https://opensource.simtabi.com/documentation/ichava/core/recipes/use-an-icon-pack) -- the generic guide, for any pack

---

[← Docs index](../README.md#documentation)

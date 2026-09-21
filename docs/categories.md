# Categories

*Reference.*

Emoji are grouped by the ten Unicode CLDR emoji groups rather than by anything this package invents.
The `Category` enum in `src/Enums/Category.php` mirrors them exactly, and `Category::ordered()`
returns them in the order a picker should display.

| Slug | Group |
|---|---|
| `smileys-emotion` | Smileys & Emotion |
| `people-body` | People & Body |
| `animals-nature` | Animals & Nature |
| `food-drink` | Food & Drink |
| `travel-places` | Travel & Places |
| `activities` | Activities |
| `objects` | Objects |
| `symbols` | Symbols |
| `flags` | Flags |
| `component` | Component |

## Why CLDR and not our own grouping

The groups come from `emoji-test.txt`, the file Unicode publishes with each release. Following it
means a new emoji lands in the group every other picker on the user's device already puts it in, and
that a category never has to be renamed because our taxonomy disagreed with the standard.

`component` is the odd one: skin-tone and hair-colour modifiers, which are not standalone emoji.
Most pickers hide it. It is present because the standard has it, not because it is useful on its
own.

## Categories and sets are orthogonal

A category says *what* an emoji is; a set says *which artwork*. Both appear in the path, set first:

```
ichava/icon-sets-emoji::twemoji/smileys-emotion/grinning-face
```

---

[← Docs index](../README.md#documentation)

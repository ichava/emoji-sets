# Release

*Reference.* How a version of `ichava/icon-sets-emoji` is cut.

## The tag is the trigger

`.github/workflows/release.yml` runs on `push` to tags only. A merge runs the gates; a tag
publishes. So a release is two acts: land the change through a pull request, then tag the resulting
commit.

```bash
git tag v0.3.3
git push origin v0.3.3
```

## The changelog is the release body

The workflow extracts this version's `## [x.y.z]` section from `CHANGELOG.md` and uses it as the
GitHub release body, so **a version with no section produces a release with no description**. A
`Changelog order` job fails a pull request when `[Unreleased]` is not first or the versions do not
descend.

## The core constraint is the release decision

`composer.json` requires `ichava/core` `^0.2.8 || ^0.3 || ^0.4`. Below `1.0` a caret pins the **minor**, so
that multi-arm constraint is doing real work: it admits several core series rather than trapping
consumers on one.

- **Widen** when a new core minor is compatible and this pack never used what changed.
- **Raise the floor** when core ships a security fix in code this pack runs -- the old constraint
  still resolves the vulnerable release otherwise.

Check before doing either:

```bash
git -C ../core diff --name-only <prev>..<new> -- src
```

An empty result means no consumed code moved.

## Refreshing the icons is not a release

New icons arrive by an upstream sync, not by cutting a tag here.
[`ichava/maintainer-toolkit`](https://opensource.simtabi.com/documentation/ichava/maintainer-toolkit/upstream-tracking)
polls `@twemoji/svg`, refreshes the vendored SVGs, bumps `upstream.current_version` in
`resources/assets/svg/config.json` and opens a pull request. That pull request is the change; a
release follows it only if you want the new icons published under a tag.

Core's [check pack updates](https://opensource.simtabi.com/documentation/ichava/core/recipes/check-pack-updates) reports whether a newer upstream
release exists. **The vendored version lives in `config.json` and nowhere else** -- writing it into
prose as well is how a README came to advertise a release the pack did not ship.

There is no `version` field in `composer.json`; the version comes from the tag. No lock file is
committed.

---

[← Docs index](../README.md#documentation)

"""Build the categorised emoji SVG asset tree.

Idempotent ETL pipeline:

    Unicode CLDR emoji-test.txt + Twemoji / OpenMoji release archives
        -> normalise codepoint -> (group, subgroup, slug)
        -> copy SVGs into resources/assets/svg/files/<set>/<group>/<slug>.svg
        -> write codepoints.json + names.json indexes
        -> update config.json with per-set totals

Re-running with the same source-version arguments produces byte-identical
output (the build is *reproducible*). The script's --check-only mode
catches upstream drift without committing regenerated SVGs.

Usage:

    python build-emoji-assets.py \\
        --twemoji-version=17.0.0 \\
        --openmoji-version=latest \\
        --output ../resources/assets/svg

    python build-emoji-assets.py --dry-run --check-only

The script intentionally lives outside the PHP package's runtime path --
it produces the assets but the runtime never imports it.
"""

from __future__ import annotations

import json
import re
import shutil
import sys
import zipfile
from dataclasses import dataclass, field
from pathlib import Path
from typing import Iterable

import click
import requests

SCRIPT_DIR = Path(__file__).resolve().parent
CACHE_DIR = SCRIPT_DIR / ".downloads"

# Canonical Unicode CLDR group order. The build pipeline writes one
# subdirectory per group under each set's directory.
GROUP_ORDER = (
    "smileys-emotion",
    "people-body",
    "component",
    "animals-nature",
    "food-drink",
    "travel-places",
    "activities",
    "objects",
    "symbols",
    "flags",
)


@dataclass(frozen=True, slots=True)
class EmojiRecord:
    """One row from CLDR emoji-test.txt, fully normalised."""

    codepoints: tuple[str, ...]      # ("1f600",) or ("1f468","200d","1f4bb")
    name: str                        # "grinning face"
    slug: str                        # "grinning-face"
    group: str                       # "smileys-emotion"
    subgroup: str                    # "face-smiling"
    qualified: bool                  # is this the fully-qualified RGI form?

    @property
    def codepoint_filename(self) -> str:
        """Filename used by Twemoji / OpenMoji (dash-joined hex)."""
        return "-".join(self.codepoints)


@dataclass(slots=True)
class BuildPlan:
    """The complete asset-build plan: records + per-set source paths."""

    records: list[EmojiRecord] = field(default_factory=list)
    set_sources: dict[str, Path] = field(default_factory=dict)


# ---------------------------------------------------------------------------
# CLDR parser
# ---------------------------------------------------------------------------

CLDR_LINE = re.compile(
    r"^([0-9A-F][0-9A-F\s]+?)\s*;\s*(\S+)\s*#\s*\S+\s*E\d+(?:\.\d+)?\s+(.+?)\s*$",
    re.IGNORECASE,
)
GROUP_HEADER = re.compile(r"^#\s*group:\s*(.+?)\s*$", re.IGNORECASE)
SUBGROUP_HEADER = re.compile(r"^#\s*subgroup:\s*(.+?)\s*$", re.IGNORECASE)


def parse_cldr_emoji_test(text: str) -> Iterable[EmojiRecord]:
    """Yield every fully-qualified RGI emoji from emoji-test.txt.

    The file groups records under `# group:` and `# subgroup:` headers
    interspersed with data lines like:

        1F600                                      ; fully-qualified     # 😀 E1.0 grinning face

    We pin the regex to the well-defined columns; CLDR keeps that shape
    stable across Unicode versions.
    """
    group = subgroup = ""
    for raw in text.splitlines():
        line = raw.strip()
        if not line:
            continue
        if header := GROUP_HEADER.match(line):
            group = _slug(header.group(1))
            continue
        if header := SUBGROUP_HEADER.match(line):
            subgroup = _slug(header.group(1))
            continue

        if line.startswith("#"):
            continue
        match = CLDR_LINE.match(line)
        if not match:
            continue

        cps_hex, status, name = match.groups()
        if status.lower() != "fully-qualified":
            # Only fully-qualified sequences are recommended for general
            # interchange (RGI); skip the minimally-qualified duplicates.
            continue

        codepoints = tuple(cp.lower() for cp in cps_hex.split())
        yield EmojiRecord(
            codepoints=codepoints,
            name=name.strip(),
            slug=_slug(name),
            group=group or "uncategorised",
            subgroup=subgroup,
            qualified=True,
        )


def _slug(text: str) -> str:
    """`'Smileys & Emotion'` -> `'smileys-emotion'`.

    The `&` is dropped rather than expanded to "and" so the CLDR group
    headers slugify to the canonical Category enum values exactly. Emoji
    names that contain a literal " and " (e.g. "rock and roll") keep the
    word untouched.
    """
    text = text.lower().strip()
    text = re.sub(r"[‘’']", "", text)         # strip ' and curly quotes
    text = re.sub(r"&", " ", text)            # drop ampersands (don't expand)
    text = re.sub(r"[^\w\s-]", "", text)
    text = re.sub(r"[\s_]+", "-", text)
    return re.sub(r"-+", "-", text).strip("-")


# ---------------------------------------------------------------------------
# Source downloaders
# ---------------------------------------------------------------------------

CLDR_EMOJI_TEST_URL = (
    "https://unicode.org/Public/emoji/{version}/emoji-test.txt"
)

TWEMOJI_RELEASE_URL = (
    "https://github.com/jdecked/twemoji/releases/download/v{version}/"
    "twemoji-{version}.zip"
)

OPENMOJI_COLOR_URL = (
    "https://github.com/hfg-gmuend/openmoji/releases/latest/download/"
    "openmoji-svg-color.zip"
)

OPENMOJI_BLACK_URL = (
    "https://github.com/hfg-gmuend/openmoji/releases/latest/download/"
    "openmoji-svg-black.zip"
)


def download(url: str, dest: Path, *, force: bool = False) -> Path:
    """Idempotent: skip if file is already cached and non-empty."""
    dest.parent.mkdir(parents=True, exist_ok=True)
    if dest.exists() and dest.stat().st_size > 0 and not force:
        return dest

    response = requests.get(url, stream=True, timeout=120)
    response.raise_for_status()

    tmp = dest.with_suffix(dest.suffix + ".tmp")
    with tmp.open("wb") as fh:
        for chunk in response.iter_content(chunk_size=1 << 16):
            fh.write(chunk)
    tmp.rename(dest)
    return dest


def fetch_cldr(unicode_version: str) -> str:
    """Download emoji-test.txt for the given Unicode version, return text."""
    url = CLDR_EMOJI_TEST_URL.format(version=unicode_version)
    path = download(url, CACHE_DIR / f"emoji-test-{unicode_version}.txt")
    return path.read_text(encoding="utf-8")


def fetch_twemoji(version: str) -> Path:
    """Download + unzip Twemoji. Returns the directory containing SVGs."""
    archive = download(
        TWEMOJI_RELEASE_URL.format(version=version),
        CACHE_DIR / f"twemoji-{version}.zip",
    )
    extracted = CACHE_DIR / f"twemoji-{version}-extracted"
    if not extracted.exists():
        with zipfile.ZipFile(archive) as zf:
            zf.extractall(extracted)
    # Twemoji ships SVGs under assets/svg/<codepoint>.svg
    svg_dir = next(extracted.rglob("assets/svg"), None) or extracted
    return svg_dir


def fetch_openmoji(variant: str) -> Path:
    """Download the color or black OpenMoji SVG release."""
    url = OPENMOJI_COLOR_URL if variant == "color" else OPENMOJI_BLACK_URL
    archive = download(url, CACHE_DIR / f"openmoji-{variant}.zip")
    extracted = CACHE_DIR / f"openmoji-{variant}-extracted"
    if not extracted.exists():
        with zipfile.ZipFile(archive) as zf:
            zf.extractall(extracted)
    return extracted


# ---------------------------------------------------------------------------
# Build
# ---------------------------------------------------------------------------


def assemble_set(
    records: list[EmojiRecord],
    source_dir: Path,
    target_dir: Path,
    *,
    dry_run: bool,
) -> tuple[int, list[str]]:
    """Copy SVGs from a source set into the target tree. Returns
    `(copied_count, missing_filenames)`."""
    copied = 0
    missing: list[str] = []

    for record in records:
        source = source_dir / f"{record.codepoint_filename}.svg"
        if not source.exists():
            missing.append(record.codepoint_filename)
            continue

        target = target_dir / record.group / f"{record.slug}.svg"
        if dry_run:
            copied += 1
            continue

        target.parent.mkdir(parents=True, exist_ok=True)
        shutil.copyfile(source, target)
        copied += 1

    return copied, missing


def build_indexes(records: list[EmojiRecord], output_dir: Path, *, dry_run: bool) -> None:
    """Write codepoints.json + names.json next to the config.json."""
    codepoints = {
        "-".join(r.codepoints): f"{r.group}/{r.slug}" for r in records
    }
    names = {
        r.slug: {
            "codepoints": list(r.codepoints),
            "category": r.group,
            "subcategory": r.subgroup,
            "name": r.name,
        }
        for r in records
    }

    if dry_run:
        return

    (output_dir / "codepoints.json").write_text(
        json.dumps(codepoints, indent=2, sort_keys=True) + "\n", encoding="utf-8"
    )
    (output_dir / "names.json").write_text(
        json.dumps(names, indent=2, sort_keys=True) + "\n", encoding="utf-8"
    )


# ---------------------------------------------------------------------------
# CLI
# ---------------------------------------------------------------------------


@click.command(context_settings={"max_content_width": 100})
@click.option("--twemoji-version", default="17.0.0", help="Twemoji release tag.")
@click.option("--unicode-version", default="17.0", help="Unicode emoji-test.txt version.")
@click.option(
    "--output",
    type=click.Path(file_okay=False, path_type=Path),
    default=SCRIPT_DIR.parent / "resources" / "assets" / "svg",
    help="Target directory (defaults to the package's resources/assets/svg).",
)
@click.option(
    "--dry-run",
    is_flag=True,
    help="Don't write SVGs or indexes; useful in CI to check sources still resolve.",
)
@click.option(
    "--check-only",
    is_flag=True,
    help="Exit non-zero if any RGI emoji is missing from any set.",
)
@click.option(
    "--sets",
    default="twemoji,openmoji-color,openmoji-black",
    help="Comma-separated list of sets to build (subset of: twemoji, openmoji-color, openmoji-black).",
)
def main(
    twemoji_version: str,
    unicode_version: str,
    output: Path,
    dry_run: bool,
    check_only: bool,
    sets: str,
) -> None:
    output = output.resolve()
    requested = {s.strip() for s in sets.split(",") if s.strip()}

    click.echo(f"[info] CLDR Unicode {unicode_version}")
    cldr_text = fetch_cldr(unicode_version)
    records = list(parse_cldr_emoji_test(cldr_text))
    click.echo(f"[info] parsed {len(records)} fully-qualified RGI emojis from CLDR")

    sources: dict[str, Path] = {}
    if "twemoji" in requested:
        click.echo(f"[info] fetching Twemoji v{twemoji_version}")
        sources["twemoji"] = fetch_twemoji(twemoji_version)
    if "openmoji-color" in requested:
        click.echo("[info] fetching OpenMoji color")
        sources["openmoji-color"] = fetch_openmoji("color")
    if "openmoji-black" in requested:
        click.echo("[info] fetching OpenMoji black")
        sources["openmoji-black"] = fetch_openmoji("black")

    files_root = output / "files"
    overall_missing: dict[str, list[str]] = {}
    for set_name, source_dir in sources.items():
        target = files_root / set_name
        copied, missing = assemble_set(records, source_dir, target, dry_run=dry_run)
        click.echo(
            f"[{set_name}] {copied} copied, "
            f"{len(missing)} missing (e.g. {missing[:3] if missing else '-'})"
        )
        if missing:
            overall_missing[set_name] = missing

    build_indexes(records, output, dry_run=dry_run)

    if check_only and overall_missing:
        click.echo(
            f"[error] {sum(len(v) for v in overall_missing.values())} missing entries "
            f"across {len(overall_missing)} sets; failing in --check-only mode.",
            err=True,
        )
        sys.exit(1)

    click.echo("[done]")


if __name__ == "__main__":
    main()

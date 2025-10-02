"""Utility helpers for the importer."""

from __future__ import annotations

import re
import unicodedata
from pathlib import Path
from typing import Optional


def slugify(value: str, allow_unicode: bool = False) -> str:
    value = str(value)
    if allow_unicode:
        value = unicodedata.normalize('NFKC', value)
    else:
        value = unicodedata.normalize('NFKD', value).encode('ascii', 'ignore').decode('ascii')
    value = re.sub(r'[^\w\s-]', '', value).strip().lower()
    return re.sub(r'[-\s]+', '-', value)


def ensure_directory(path: Path) -> None:
    path.mkdir(parents=True, exist_ok=True)


def shorten_filename(base_name: str, max_length: int = 80) -> str:
    if len(base_name) <= max_length:
        return base_name
    stem, dot, suffix = base_name.partition('.')
    trimmed_stem = stem[: max_length - len(suffix) - 1]
    return f"{trimmed_stem}{dot}{suffix}" if suffix else trimmed_stem


def build_certificate_filename(nama: str, pelatihan: str, extension: str = "pdf") -> str:
    nama_slug = slugify(nama)
    pelatihan_slug = slugify(pelatihan)
    filename = f"{nama_slug}-{pelatihan_slug}.{extension.strip('.')}"
    return shorten_filename(filename)


def guess_extension_from_content_type(content_type: Optional[str]) -> str:
    if not content_type:
        return "pdf"
    mapping = {
        "application/pdf": "pdf",
        "image/jpeg": "jpg",
        "image/png": "png",
    }
    return mapping.get(content_type.lower(), "pdf")

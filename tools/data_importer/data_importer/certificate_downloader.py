"""Download helper for pelatihan certificates."""

from __future__ import annotations

import logging
from pathlib import Path
from typing import Mapping, Optional

import requests

from .utils import build_certificate_filename, ensure_directory, guess_extension_from_content_type


logger = logging.getLogger(__name__)


class CertificateDownloader:
    def __init__(self, root_directory: Path) -> None:
        self.root_directory = root_directory
        ensure_directory(self.root_directory)

    def download(
        self,
        url: str,
        nama: str,
        pelatihan: str,
        session: Optional[requests.Session] = None,
        headers: Optional[Mapping[str, str]] = None,
    ) -> Optional[Path]:
        if not url:
            return None

        client = session or requests.Session()

        response = client.get(url, headers=headers or {}, timeout=60, stream=True)
        response.raise_for_status()

        extension = guess_extension_from_content_type(response.headers.get("Content-Type"))
        filename = build_certificate_filename(nama, pelatihan, extension)
        destination = self.root_directory / filename

        ensure_directory(destination.parent)
        with destination.open("wb") as handle:
            for chunk in response.iter_content(chunk_size=8192):
                if chunk:
                    handle.write(chunk)

        logger.info("Downloaded certificate %s", destination)
        return destination

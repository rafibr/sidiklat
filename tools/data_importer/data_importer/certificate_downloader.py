"""Download helper for pelatihan certificates."""

from __future__ import annotations

import logging
from pathlib import Path
from typing import Mapping, Optional
from urllib.parse import urlparse

import requests

from .utils import build_certificate_filename, ensure_directory, guess_extension_from_content_type


logger = logging.getLogger(__name__)


class CertificateDownloader:
    def __init__(self, root_directory: Path) -> None:
        self.root_directory = root_directory
        ensure_directory(self.root_directory)
        logger.info("Certificate downloader initialized. Root directory: %s", self.root_directory.absolute())

    def download(
        self,
        url: str,
        nama: str,
        pelatihan: str,
        session: Optional[requests.Session] = None,
        headers: Optional[Mapping[str, str]] = None,
    ) -> Optional[Path]:
        if not url:
            logger.debug("No URL provided, skipping download")
            return None

        # Validate URL
        try:
            parsed = urlparse(url)
            if not parsed.scheme or not parsed.netloc:
                logger.error("Invalid URL format: %s", url)
                raise ValueError(f"Invalid URL format: {url}")
        except Exception as e:
            logger.error("Failed to parse URL %s: %s", url, e)
            raise

        logger.info("Starting download from URL: %s", url)
        logger.debug("Download for pegawai: %s, pelatihan: %s", nama, pelatihan)

        client = session or requests.Session()

        try:
            response = client.get(url, headers=headers or {}, timeout=60, stream=True)
            logger.debug("Response status code: %s", response.status_code)
            logger.debug("Response headers: %s", dict(response.headers))

            response.raise_for_status()
        except requests.exceptions.HTTPError as e:
            logger.error("HTTP error downloading from %s: %s (Status: %s)", url, e, response.status_code)
            raise
        except requests.exceptions.Timeout:
            logger.error("Timeout downloading from %s after 60 seconds", url)
            raise
        except requests.exceptions.ConnectionError as e:
            logger.error("Connection error downloading from %s: %s", url, e)
            raise
        except Exception as e:
            logger.error("Unexpected error downloading from %s: %s", url, e)
            raise

        extension = guess_extension_from_content_type(response.headers.get("Content-Type"))
        filename = build_certificate_filename(nama, pelatihan, extension)
        destination = self.root_directory / filename

        logger.info("Saving certificate to: %s", destination.absolute())

        try:
            ensure_directory(destination.parent)

            bytes_written = 0
            with destination.open("wb") as handle:
                for chunk in response.iter_content(chunk_size=8192):
                    if chunk:
                        handle.write(chunk)
                        bytes_written += len(chunk)

            logger.info("Successfully downloaded certificate: %s (%d bytes)", destination, bytes_written)
            return destination
        except IOError as e:
            logger.error("Failed to write file to %s: %s", destination, e)
            raise
        except Exception as e:
            logger.error("Unexpected error saving file to %s: %s", destination, e)
            raise

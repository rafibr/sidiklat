"""Scraper for SIMPEG diklat data using Playwright."""

from __future__ import annotations

import logging
import re
from datetime import datetime
from pathlib import Path
from typing import Dict, Iterable, Iterator, List, Optional

import requests
from playwright.sync_api import Browser, BrowserContext, Error as PlaywrightError, Page, sync_playwright

from .models import SimpegTrainingRecord
from .utils import ensure_directory


logger = logging.getLogger(__name__)


TRAINING_TABS = {
    "kepemimpinan": {
        "tab_selector": "ul#demo-pill-nav a:has-text('Diklat Kepemimpinan')",
        "container": "#tabelkepemimpinan",
        "column_map": {
            "induk_diklat": 1,
            "nama_pelatihan": 2,
            "tempat": 3,
            "penyelenggara": 4,
            "tanggal_mulai": 5,
            "tanggal_selesai": 6,
            "jp": 7,
            "nomor_sttp": 8,
            "tanggal_sttp": 9,
        },
    },
    "fungsional": {
        "tab_selector": "ul#demo-pill-nav a:has-text('Diklat Fungsional')",
        "container": "#tabelfungsional",
        "column_map": {
            "nama_pelatihan": 1,
            "tempat": 2,
            "penyelenggara": 3,
            "tanggal_mulai": 4,
            "tanggal_selesai": 5,
            "jp": 6,
            "nomor_sttp": 7,
            "tanggal_sttp": 8,
        },
    },
    "teknis": {
        "tab_selector": "ul#demo-pill-nav a:has-text('Diklat Teknis')",
        "container": "#tabelteknis",
        "column_map": {
            "nama_pelatihan": 1,
            "tempat": 2,
            "penyelenggara": 3,
            "tanggal_mulai": 4,
            "tanggal_selesai": 5,
            "jp": 6,
            "nomor_sttp": 7,
            "tanggal_sttp": 8,
        },
    },
}


class SimpegScraper:
    def __init__(self, base_url: str, headless: bool = True, download_dir: Optional[Path] = None) -> None:
        self.base_url = base_url.rstrip("/")
        self.headless = headless
        self.download_dir = download_dir or Path("downloads")
        ensure_directory(self.download_dir)
        self._play = None
        self.browser: Optional[Browser] = None
        self.context: Optional[BrowserContext] = None
        self.page: Optional[Page] = None

    def __enter__(self) -> "SimpegScraper":
        self._play = sync_playwright().start()
        self.browser = self._play.chromium.launch(headless=self.headless)
        self.context = self.browser.new_context(accept_downloads=True)
        self.page = self.context.new_page()
        return self

    def __exit__(self, exc_type, exc, tb) -> None:
        if self.page:
            self.page.close()
        if self.context:
            self.context.close()
        if self.browser:
            self.browser.close()
        if self._play:
            self._play.stop()

    # Authentication --------------------------------------------------

    def login(self, username: str, password: str) -> None:
        if not self.page:
            raise RuntimeError("Scraper not initialised")

        login_url = f"{self.base_url}/simpeg/site/login"
        self.page.goto(login_url)
        self.page.fill("#LoginForm_username", username)
        self.page.fill("#LoginForm_password", password)
        self.page.click("button[type='submit'], input[type='submit']")
        self.page.wait_for_load_state("networkidle")

        if "login" in self.page.url.lower():
            raise PlaywrightError("Gagal login ke SIMPEG, periksa kredensial.")

    def build_requests_session(self) -> requests.Session:
        if not self.context:
            raise RuntimeError("Browser context belum tersedia")
        session = requests.Session()
        for cookie in self.context.cookies():
            domain = cookie.get("domain", "")
            session.cookies.set(
                cookie["name"],
                cookie["value"],
                domain=domain.lstrip('.'),
                path=cookie.get("path", "/"),
            )
        return session

    # Pegawai listing -------------------------------------------------

    def iter_pegawai_rows(self) -> Iterator[Dict[str, str]]:
        if not self.page:
            raise RuntimeError("Scraper not initialised")

        listing_url = f"{self.base_url}/simpeg/#perekaman/data_pnscpns_v2"
        self.page.goto(listing_url)
        self.page.wait_for_selector("button.btn.btn-primary:has-text('Cari')")
        self.page.click("button.btn.btn-primary:has-text('Cari')")
        self.page.wait_for_selector("#dt_basiccari tbody tr")

        rows = self.page.locator("#dt_basiccari tbody tr")
        count = rows.count()
        logger.info("Menemukan %s baris pegawai", count)
        for index in range(count):
            row = rows.nth(index)
            anchor = row.locator("td:nth-child(2) a").first
            onclick = anchor.get_attribute("onclick") or ""
            match = re.search(r"cari\('(\d+)'\)", onclick)
            if not match:
                continue
            pegawai_id = match.group(1)
            nama = anchor.inner_text().strip()
            nip = (row.locator("td").nth(2).inner_text().strip() or None)
            yield {"id": pegawai_id, "nama": nama, "nip": nip}

    # Training scraping -----------------------------------------------

    def iter_training_records(
        self,
        pegawai_rows: Iterable[Dict[str, str]],
        categories: Optional[List[str]] = None,
    ) -> Iterator[SimpegTrainingRecord]:
        if not self.page:
            raise RuntimeError("Scraper not initialised")

        categories = categories or list(TRAINING_TABS.keys())

        for pegawai in pegawai_rows:
            pegawai_id = pegawai["id"]
            detail_url = f"{self.base_url}/simpeg/#perekaman/data_pnscpns_v2/{pegawai_id}"
            logger.info("Memuat detail pegawai %s (%s)", pegawai_id, pegawai["nama"])
            self.page.goto(detail_url)
            try:
                self.page.wait_for_selector("a[href='#hr3']")
            except PlaywrightError:
                logger.warning("Tidak dapat menemukan tab riwayat untuk %s", pegawai_id)
                continue

            self.page.click("a[href='#hr3']")
            self.page.wait_for_timeout(500)

            for category in categories:
                tab = TRAINING_TABS.get(category)
                if not tab:
                    continue
                try:
                    self.page.click(tab["tab_selector"])
                    self.page.wait_for_timeout(500)
                except PlaywrightError:
                    logger.warning("Tab %s tidak tersedia untuk pegawai %s", category, pegawai_id)
                    continue

                for record in self._scrape_training_table(tab, category, pegawai):
                    yield record

    def _scrape_training_table(
        self,
        tab_config: Dict[str, object],
        category: str,
        pegawai: Dict[str, str],
    ) -> Iterator[SimpegTrainingRecord]:
        if not self.page:
            return

        container_selector = tab_config.get("container")  # type: ignore[assignment]
        if not isinstance(container_selector, str):
            return

        table_locator = self.page.locator(f"{container_selector} table tbody tr")
        if table_locator.count() == 0:
            return

        column_map: Dict[str, Optional[int]] = tab_config.get("column_map", {})  # type: ignore[assignment]

        for index in range(table_locator.count()):
            row = table_locator.nth(index)
            cells = [row.locator("td").nth(i).inner_text().strip() for i in range(row.locator("td").count())]

            def get_cell(column: str) -> Optional[str]:
                idx = column_map.get(column)
                if idx is None or idx >= len(cells):
                    return None
                value = cells[idx].strip()
                return value or None

            sertifikat_link = row.locator("td a[target='_blank']").first
            href = sertifikat_link.get_attribute("href") if sertifikat_link else None
            if href and href.startswith("/"):
                href = f"{self.base_url}{href}"

            yield SimpegTrainingRecord(
                pegawai_id=int(pegawai["id"]),
                pegawai_nama=pegawai["nama"],
                pegawai_nip=pegawai.get("nip"),
                kategori=category,
                induk_diklat=get_cell("induk_diklat"),
                nama_pelatihan=get_cell("nama_pelatihan") or "",
                tempat=get_cell("tempat"),
                penyelenggara=get_cell("penyelenggara"),
                tanggal_mulai=self._parse_date(get_cell("tanggal_mulai")),
                tanggal_selesai=self._parse_date(get_cell("tanggal_selesai")),
                jp=self._parse_int(get_cell("jp")),
                nomor_sttp=get_cell("nomor_sttp"),
                tanggal_sttp=self._parse_date(get_cell("tanggal_sttp")),
                sertifikat_url=href,
            )

    @staticmethod
    def _parse_date(value: Optional[str]) -> Optional[datetime.date]:
        if not value or value in {"-", ""}:
            return None
        value = value.strip()
        for fmt in ("%d-%m-%Y", "%Y-%m-%d", "%d/%m/%Y", "%d %B %Y"):
            try:
                return datetime.strptime(value, fmt).date()
            except ValueError:
                continue
        return None

    @staticmethod
    def _parse_int(value: Optional[str]) -> Optional[int]:
        if not value or value.strip() == "-":
            return None
        try:
            return int(value.replace('.', '').strip())
        except ValueError:
            return None

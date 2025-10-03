"""Command line interface for SIDIKLAT data importer."""

from __future__ import annotations

import logging
from pathlib import Path
from typing import List, Optional

import typer

from .certificate_downloader import CertificateDownloader
from .config import AppConfig
from .db import DatabaseClient, database_client
from .excel_loader import ExcelLoader
from .json_loader import JsonLoader
from .models import ExcelTrainingRow, SimpegTrainingRecord
from .simpeg_scraper import SimpegScraper
from .sql_writer import SqlScriptBuilder, build_training_sql_block


logging.basicConfig(level=logging.INFO, format="[%(levelname)s] %(message)s")
logger = logging.getLogger(__name__)

app = typer.Typer(help="Import data pegawai dan pelatihan ke SIDIKLAT.")


def _store_excel_row(
    row: ExcelTrainingRow,
    downloader: CertificateDownloader,
    certificate_root: Path,
    client: DatabaseClient | None = None,
    sql_builder: SqlScriptBuilder | None = None,
):
    jenis_nama = row.jenis_pelatihan or "Lainnya"

    sertifikat_path = None
    sertifikat_sql_path = None
    if row.sertifikat_url:
        try:
            destination = downloader.download(row.sertifikat_url, row.nama, row.nama_pelatihan)
            sertifikat_path = destination.relative_to(certificate_root.parent)
            sertifikat_sql_path = sertifikat_path.as_posix()
        except Exception as exc:  # noqa: BLE001
            logger.warning("Gagal mengunduh sertifikat %s: %s", row.sertifikat_url, exc)

    if sql_builder is not None:
        sql_builder.add_block(
            build_training_sql_block(
                pegawai_nama=row.nama,
                pegawai_nip=row.nip,
                nama_pelatihan=row.nama_pelatihan,
                jenis_pelatihan=jenis_nama,
                penyelenggara=row.penyelenggara,
                tempat=row.tempat,
                tanggal_mulai=row.tanggal_mulai,
                tanggal_selesai=row.tanggal_selesai,
                jp=row.jp,
                status="selesai",
                deskripsi=row.keterangan,
                sertifikat_path=sertifikat_sql_path,
                jabatan=row.jabatan,
                unit_kerja=row.unit_kerja,
                keterangan=row.keterangan,
            )
        )
        return

    if client is None:
        raise ValueError("Database client atau SQL builder harus disediakan")

    pegawai = client.upsert_pegawai(
        nama=row.nama,
        nip=row.nip,
        jabatan=row.jabatan,
        unit_kerja=row.unit_kerja,
        keterangan=row.keterangan,
    )

    client.upsert_pelatihan(
        pegawai_id=pegawai.id,
        nama_pelatihan=row.nama_pelatihan,
        jenis_nama=jenis_nama,
        penyelenggara=row.penyelenggara,
        tempat=row.tempat,
        tanggal_mulai=row.tanggal_mulai,
        tanggal_selesai=row.tanggal_selesai,
        jp=row.jp or 0,
        status="selesai",
        deskripsi=row.keterangan,
        sertifikat_path=sertifikat_path,
    )


def import_excel_data(
    excel_path: Path,
    sheet_name: Optional[str] = None,
    sql_output: Optional[Path] = None,
) -> None:
    """Core routine for importing Excel data."""

    config = AppConfig.load()
    loader = ExcelLoader()
    downloader = CertificateDownloader(config.storage.certificate_root)
    certificate_root = config.storage.certificate_root

    rows = loader.load(excel_path, sheet_name=sheet_name)
    logger.info("Memproses %s baris dari Excel", len(rows))

    if sql_output:
        builder = SqlScriptBuilder(sql_output)
        for row in rows:
            _store_excel_row(row, downloader, certificate_root, sql_builder=builder)
        output_path = builder.save()
        logger.info("Skrip SQL tersimpan di %s", output_path)
    else:
        with database_client(config.database) as client:
            for row in rows:
                _store_excel_row(row, downloader, certificate_root, client=client)

    logger.info("Import Excel selesai.")


def import_json_data(
    json_path: Path,
    sql_output: Optional[Path] = None,
    jenis_pelatihan: Optional[str] = None,
) -> None:
    """Import training data from a structured JSON file."""

    config = AppConfig.load()
    loader = JsonLoader(fallback_jenis=jenis_pelatihan)
    downloader = CertificateDownloader(config.storage.certificate_root)
    certificate_root = config.storage.certificate_root

    rows = loader.load(json_path)
    if not rows:
        logger.warning("Tidak ada data pelatihan ditemukan di %s", json_path)
        return

    logger.info("Memproses %s entri pelatihan dari JSON", len(rows))

    if sql_output:
        builder = SqlScriptBuilder(sql_output)
        for row in rows:
            _store_excel_row(row, downloader, certificate_root, sql_builder=builder)
        output_path = builder.save()
        logger.info("Skrip SQL tersimpan di %s", output_path)
    else:
        with database_client(config.database) as client:
            for row in rows:
                _store_excel_row(row, downloader, certificate_root, client=client)

    logger.info("Import JSON selesai.")


@app.command("import-excel")
def import_excel(
    excel_path: Path = typer.Argument(..., exists=True, dir_okay=False, help="Path file Excel sumber."),
    sheet_name: Optional[str] = typer.Option(None, help="Nama sheet yang akan dibaca."),
    sql_output: Optional[Path] = typer.Option(
        None,
        help="Jika diset, data tidak langsung dimasukkan ke database melainkan ditulis ke file SQL ini.",
    ),
) -> None:
    """Impor data pelatihan dari berkas Excel."""

    import_excel_data(excel_path, sheet_name=sheet_name, sql_output=sql_output)


@app.command("import-json")
def import_json(
    json_path: Path = typer.Argument(..., exists=True, dir_okay=False, help="Path file JSON sumber."),
    jenis_pelatihan: Optional[str] = typer.Option(
        None,
        help="Nama jenis pelatihan default yang akan disematkan ke setiap entri (opsional).",
    ),
    sql_output: Optional[Path] = typer.Option(
        None,
        help="Jika diset, data tidak langsung dimasukkan ke database melainkan ditulis ke file SQL ini.",
    ),
) -> None:
    """Impor data pelatihan dari berkas JSON terstruktur."""

    import_json_data(json_path, sql_output=sql_output, jenis_pelatihan=jenis_pelatihan)


CATEGORY_LABELS = {
    "kepemimpinan": "Diklat Kepemimpinan",
    "fungsional": "Diklat Fungsional",
    "teknis": "Diklat Teknis",
}


def _store_simpeg_record(
    record: SimpegTrainingRecord,
    downloader: CertificateDownloader,
    certificate_root: Path,
    session,
    client: DatabaseClient | None = None,
    sql_builder: SqlScriptBuilder | None = None,
):
    jenis_nama = CATEGORY_LABELS.get(record.kategori, record.kategori.title())

    sertifikat_path = None
    sertifikat_sql_path = None
    if record.sertifikat_url:
        try:
            destination = downloader.download(
                record.sertifikat_url,
                record.pegawai_nama,
                record.nama_pelatihan or record.induk_diklat or record.kategori,
                session=session,
            )
            sertifikat_path = destination.relative_to(certificate_root.parent)
            sertifikat_sql_path = sertifikat_path.as_posix()
        except Exception as exc:  # noqa: BLE001
            logger.warning("Gagal mengunduh sertifikat untuk %s: %s", record.nama_pelatihan, exc)

    if sql_builder is not None:
        sql_builder.add_block(
            build_training_sql_block(
                pegawai_nama=record.pegawai_nama,
                pegawai_nip=record.pegawai_nip,
                nama_pelatihan=record.nama_pelatihan or record.induk_diklat or "Diklat",
                jenis_pelatihan=jenis_nama,
                penyelenggara=record.penyelenggara,
                tempat=record.tempat,
                tanggal_mulai=record.tanggal_mulai,
                tanggal_selesai=record.tanggal_selesai,
                jp=record.jp,
                status=record.status,
                deskripsi=record.nomor_sttp,
                sertifikat_path=sertifikat_sql_path,
            )
        )
        return

    if client is None:
        raise ValueError("Database client atau SQL builder harus disediakan")

    pegawai = client.upsert_pegawai(
        nama=record.pegawai_nama,
        nip=record.pegawai_nip,
    )

    client.upsert_pelatihan(
        pegawai_id=pegawai.id,
        nama_pelatihan=record.nama_pelatihan or record.induk_diklat or "Diklat",
        jenis_nama=jenis_nama,
        penyelenggara=record.penyelenggara,
        tempat=record.tempat,
        tanggal_mulai=record.tanggal_mulai,
        tanggal_selesai=record.tanggal_selesai,
        jp=record.jp or 0,
        status=record.status,
        deskripsi=record.nomor_sttp,
        sertifikat_path=sertifikat_path,
    )


@app.command("import-simpeg")
def import_simpeg(
    username: Optional[str] = typer.Option(None, help="Username SIMPEG. Default dari env SIMPEG_USERNAME."),
    password: Optional[str] = typer.Option(None, help="Password SIMPEG. Default dari env SIMPEG_PASSWORD."),
    categories: Optional[List[str]] = typer.Option(
        None,
        "--categories",
        "-c",
        help="Daftar kategori yang diambil (kepemimpinan, fungsional, teknis). Opsi ini dapat diulang.",
    ),
    sql_output: Optional[Path] = typer.Option(
        None,
        help="Jika diset, hasil scraping ditulis ke file SQL alih-alih langsung ke database.",
    ),
    extra_categories: List[str] = typer.Argument(
        None,
        metavar="[KATEGORI]...",
        help="Kategori tambahan yang boleh ditulis tanpa opsi --categories.",
    ),
) -> None:
    """Scrape data SIMPEG dan simpan ke database atau file SQL."""

    config = AppConfig.load()
    username = username or config.simpeg.username
    password = password or config.simpeg.password

    if not username or not password:
        raise typer.BadParameter("Username dan password SIMPEG wajib diisi.")

    selected_categories: List[str] = []
    if categories:
        selected_categories.extend(categories)
    if extra_categories:
        selected_categories.extend(extra_categories)

    if not selected_categories:
        selected_categories = ["kepemimpinan"]

    categories = selected_categories

    with SimpegScraper(
        config.simpeg.base_url,
        headless=config.simpeg.headless,
        download_dir=config.storage.certificate_root,
    ) as scraper:
        scraper.login(username, password)
        session = scraper.build_requests_session()
        downloader = CertificateDownloader(config.storage.certificate_root)

        pegawai_rows = list(scraper.iter_pegawai_rows())
        logger.info("Mengambil diklat untuk %s pegawai", len(pegawai_rows))

        if sql_output:
            builder = SqlScriptBuilder(sql_output)
            for record in scraper.iter_training_records(pegawai_rows, categories=categories):
                _store_simpeg_record(
                    record,
                    downloader,
                    config.storage.certificate_root,
                    session,
                    sql_builder=builder,
                )
            output_path = builder.save()
            logger.info("Skrip SQL tersimpan di %s", output_path)
        else:
            with database_client(config.database) as client:
                for record in scraper.iter_training_records(pegawai_rows, categories=categories):
                    _store_simpeg_record(
                        record,
                        downloader,
                        config.storage.certificate_root,
                        session,
                        client=client,
                    )

    logger.info("Import SIMPEG selesai.")


@app.command("gui")
def open_gui() -> None:
    """Buka antarmuka grafis untuk impor data."""

    from .gui import launch_gui

    launch_gui()


def run():  # pragma: no cover - CLI entry point
    app()


if __name__ == "__main__":  # pragma: no cover
    run()

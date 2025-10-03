"""Database helper utilities."""

from __future__ import annotations

import logging
from contextlib import contextmanager
from datetime import date, datetime
from pathlib import Path
from typing import Iterator, Optional

import mysql.connector

from .config import DatabaseConfig
from .models import StoredPegawai, StoredPelatihan


logger = logging.getLogger(__name__)


class DatabaseClient:
    def __init__(self, config: DatabaseConfig) -> None:
        self.config = config
        self.connection: Optional[mysql.connector.MySQLConnection] = None

    def connect(self) -> None:
        if self.connection and self.connection.is_connected():
            return
        self.connection = mysql.connector.connect(
            host=self.config.host,
            port=self.config.port,
            user=self.config.user,
            password=self.config.password,
            database=self.config.name,
            autocommit=False,
        )

    def close(self) -> None:
        if self.connection is not None and self.connection.is_connected():
            self.connection.close()
            self.connection = None

    def __enter__(self) -> "DatabaseClient":
        self.connect()
        return self

    def __exit__(self, exc_type, exc, tb) -> None:
        if exc:
            if self.connection:
                self.connection.rollback()
        else:
            if self.connection:
                self.connection.commit()
        self.close()

    def _get_cursor(self):
        if not self.connection:
            raise RuntimeError("Database connection has not been established")
        return self.connection.cursor(dictionary=True)

    def _current_timestamp(self) -> datetime:
        return datetime.utcnow()

    # Pegawai helpers -------------------------------------------------

    def _find_pegawai(self, *, nip: Optional[str], nama: str) -> Optional[StoredPegawai]:
        cursor = self._get_cursor()
        if nip:
            # Clean NIP by removing spaces
            nip_clean = nip.replace(" ", "").strip()
            cursor.execute(
                "SELECT id, nama_lengkap as nama, nip FROM pegawais WHERE REPLACE(nip, ' ', '') = %s LIMIT 1",
                (nip_clean,),
            )
            row = cursor.fetchone()
            if row:
                return StoredPegawai(id=row["id"], nama=row["nama"], nip=row["nip"])

        cursor.execute(
            "SELECT id, nama_lengkap as nama, nip FROM pegawais WHERE nama_lengkap = %s LIMIT 1",
            (nama,),
        )
        row = cursor.fetchone()
        if row:
            return StoredPegawai(id=row["id"], nama=row["nama"], nip=row["nip"])
        return None

    def upsert_pegawai(
        self,
        nama: str,
        nip: Optional[str] = None,
        jabatan: Optional[str] = None,
        unit_kerja: Optional[str] = None,
        pangkat: Optional[str] = None,
        status: str = "aktif",
        email: Optional[str] = None,
        telepon: Optional[str] = None,
        tanggal_pengangkatan: Optional[date] = None,
        keterangan: Optional[str] = None,
    ) -> StoredPegawai:
        # Provide default value for unit_kerja if None
        if unit_kerja is None:
            unit_kerja = "-"

        # Clean NIP by removing spaces before searching
        nip_clean = nip.replace(" ", "").strip() if nip else None

        existing = self._find_pegawai(nip=nip_clean, nama=nama)
        cursor = self._get_cursor()
        timestamp = self._current_timestamp()

        if existing:
            # Just return existing pegawai without updating
            logger.info("Found existing pegawai %s (ID: %s, NIP: %s)", existing.nama, existing.id, existing.nip)
            return existing

        # Create new pegawai if not found
        cursor.execute(
            """
            INSERT INTO pegawais
                (nama_lengkap, nip, jabatan, unit_kerja, pangkat_golongan, status, email, telepon, tanggal_pengangkatan, keterangan, jp_target, jp_tercapai, created_at, updated_at)
            VALUES
                (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """,
            (
                nama,
                nip_clean,
                jabatan,
                unit_kerja,
                pangkat,
                status,
                email,
                telepon,
                tanggal_pengangkatan,
                keterangan,
                0,
                0,
                timestamp,
                timestamp,
            ),
        )
        pegawai_id = cursor.lastrowid
        logger.info("Created pegawai %s (ID: %s, NIP: %s)", nama, pegawai_id, nip_clean)
        return StoredPegawai(id=pegawai_id, nama=nama, nip=nip_clean)

    # Jenis Pelatihan -------------------------------------------------

    def ensure_jenis_pelatihan(self, nama: str) -> int:
        if not nama:
            raise ValueError("Nama jenis pelatihan tidak boleh kosong")
        cursor = self._get_cursor()
        cursor.execute(
            "SELECT id FROM jenis_pelatihans WHERE nama = %s LIMIT 1",
            (nama,),
        )
        row = cursor.fetchone()
        if row:
            return row["id"]

        cursor.execute(
            """
            INSERT INTO jenis_pelatihans (nama, created_at, updated_at)
            VALUES (%s, %s, %s)
            """,
            (nama, self._current_timestamp(), self._current_timestamp()),
        )
        jenis_id = cursor.lastrowid
        logger.info("Created jenis pelatihan %s (%s)", nama, jenis_id)
        return jenis_id

    # Pelatihan -------------------------------------------------------

    def _find_pelatihan(self, pegawai_id: int, nama_pelatihan: str, tanggal_mulai: Optional[date], tanggal_selesai: Optional[date]) -> Optional[StoredPelatihan]:
        cursor = self._get_cursor()
        cursor.execute(
            """
            SELECT id, pegawai_id, nama_pelatihan, tanggal_mulai, tanggal_selesai, jp, sertifikat_path
              FROM pelatihans
             WHERE pegawai_id = %s
               AND nama_pelatihan = %s
               AND (tanggal_mulai = %s OR (tanggal_mulai IS NULL AND %s IS NULL))
               AND (tanggal_selesai = %s OR (tanggal_selesai IS NULL AND %s IS NULL))
             LIMIT 1
            """,
            (pegawai_id, nama_pelatihan, tanggal_mulai, tanggal_mulai, tanggal_selesai, tanggal_selesai),
        )
        row = cursor.fetchone()
        if row:
            return StoredPelatihan(
                id=row["id"],
                pegawai_id=row["pegawai_id"],
                nama_pelatihan=row["nama_pelatihan"],
                tanggal_mulai=row["tanggal_mulai"],
                tanggal_selesai=row["tanggal_selesai"],
                jp=row["jp"],
                sertifikat_path=Path(row["sertifikat_path"]) if row["sertifikat_path"] else None,
            )
        return None

    def upsert_pelatihan(
        self,
        pegawai_id: int,
        nama_pelatihan: str,
        jenis_nama: Optional[str],
        penyelenggara: Optional[str],
        tempat: Optional[str],
        tanggal_mulai: Optional[date],
        tanggal_selesai: Optional[date],
        jp: Optional[int],
        status: str,
        deskripsi: Optional[str] = None,
        sertifikat_path: Optional[Path] = None,
    ) -> StoredPelatihan:
        # Provide default values for required fields if None
        if penyelenggara is None:
            penyelenggara = "-"
        if tempat is None:
            tempat = "-"

        cursor = self._get_cursor()
        jenis_id = self.ensure_jenis_pelatihan(jenis_nama) if jenis_nama else None
        existing = self._find_pelatihan(pegawai_id, nama_pelatihan, tanggal_mulai, tanggal_selesai)
        timestamp = self._current_timestamp()

        sertifikat_value = str(sertifikat_path) if sertifikat_path else None

        if existing:
            previous_jp = existing.jp or 0
            cursor.execute(
                """
                SELECT status, jp FROM pelatihans WHERE id = %s
                """,
                (existing.id,),
            )
            status_row = cursor.fetchone()
            old_status = status_row["status"] if status_row else None
            old_jp = status_row["jp"] if status_row else previous_jp

            cursor.execute(
                """
                UPDATE pelatihans
                   SET jenis_pelatihan_id = %s,
                       penyelenggara = %s,
                       tempat = %s,
                       tanggal_mulai = %s,
                       tanggal_selesai = %s,
                       jp = %s,
                       status = %s,
                       deskripsi = %s,
                       sertifikat_path = %s,
                       updated_at = %s
                 WHERE id = %s
                """,
                (
                    jenis_id,
                    penyelenggara,
                    tempat,
                    tanggal_mulai,
                    tanggal_selesai,
                    jp,
                    status,
                    deskripsi,
                    sertifikat_value,
                    timestamp,
                    existing.id,
                ),
            )

            self._update_jp_progress(pegawai_id, old_status, old_jp, status, jp)
            return StoredPelatihan(
                id=existing.id,
                pegawai_id=pegawai_id,
                nama_pelatihan=nama_pelatihan,
                tanggal_mulai=tanggal_mulai,
                tanggal_selesai=tanggal_selesai,
                jp=jp,
                sertifikat_path=sertifikat_path,
            )

        cursor.execute(
            """
            INSERT INTO pelatihans
                (pegawai_id, nama_pelatihan, jenis_pelatihan_id, penyelenggara, tempat, tanggal_mulai, tanggal_selesai, jp, status, sertifikat_path, deskripsi, created_at, updated_at)
            VALUES
                (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """,
            (
                pegawai_id,
                nama_pelatihan,
                jenis_id,
                penyelenggara,
                tempat,
                tanggal_mulai,
                tanggal_selesai,
                jp,
                status,
                sertifikat_value,
                deskripsi,
                timestamp,
                timestamp,
            ),
        )
        pelatihan_id = cursor.lastrowid
        self._update_jp_progress(pegawai_id, None, 0, status, jp)
        logger.info("Stored pelatihan %s untuk pegawai %s", pelatihan_id, pegawai_id)
        return StoredPelatihan(
            id=pelatihan_id,
            pegawai_id=pegawai_id,
            nama_pelatihan=nama_pelatihan,
            tanggal_mulai=tanggal_mulai,
            tanggal_selesai=tanggal_selesai,
            jp=jp,
            sertifikat_path=sertifikat_path,
        )

    def _update_jp_progress(
        self,
        pegawai_id: int,
        old_status: Optional[str],
        old_jp: Optional[int],
        new_status: Optional[str],
        new_jp: Optional[int],
    ) -> None:
        adjust = 0
        if old_status == "selesai" and old_jp:
            adjust -= old_jp
        if new_status == "selesai" and new_jp:
            adjust += new_jp
        if adjust == 0:
            return
        cursor = self._get_cursor()
        cursor.execute(
            """
            UPDATE pegawais
               SET jp_tercapai = GREATEST(0, COALESCE(jp_tercapai, 0) + %s),
                   updated_at = %s
             WHERE id = %s
            """,
            (adjust, self._current_timestamp(), pegawai_id),
        )


@contextmanager
def database_client(config: DatabaseConfig) -> Iterator[DatabaseClient]:
    client = DatabaseClient(config)
    try:
        client.connect()
        yield client
        if client.connection:
            client.connection.commit()
    except Exception:
        if client.connection:
            client.connection.rollback()
        raise
    finally:
        client.close()

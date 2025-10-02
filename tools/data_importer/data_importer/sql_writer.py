"""Utility helpers for producing MySQL-compatible SQL scripts."""

from __future__ import annotations

from dataclasses import dataclass, field
from datetime import date, datetime
from pathlib import Path
from typing import List, Optional, Sequence


def _sql_literal(value) -> str:
    """Return a SQL literal string for the provided Python value."""
    if value is None:
        return "NULL"

    if isinstance(value, (datetime, date)):
        return f"'{value.isoformat()}'"

    if isinstance(value, Path):
        value = value.as_posix()

    text = str(value)
    # Escape backslashes and single quotes for MySQL.
    text = text.replace("\\", "\\\\").replace("'", "''")
    return f"'{text}'"


def _pegawai_condition(nama: str, nip: Optional[str]) -> str:
    if nip:
        return f"nip = {_sql_literal(nip)}"
    return f"nama_lengkap = {_sql_literal(nama)}"


def _pelatihan_exists_condition(
    nama_pelatihan: str,
    tanggal_mulai: Optional[date],
    tanggal_selesai: Optional[date],
) -> str:
    conditions: List[str] = [
        "pegawai_id = @pegawai_id",
        f"nama_pelatihan = {_sql_literal(nama_pelatihan)}",
    ]
    if tanggal_mulai is None:
        conditions.append("(tanggal_mulai IS NULL)")
    else:
        conditions.append(f"(tanggal_mulai = {_sql_literal(tanggal_mulai)})")
    if tanggal_selesai is None:
        conditions.append("(tanggal_selesai IS NULL)")
    else:
        conditions.append(f"(tanggal_selesai = {_sql_literal(tanggal_selesai)})")
    return "\n      AND ".join(conditions)


def build_training_sql_block(
    *,
    pegawai_nama: str,
    pegawai_nip: Optional[str],
    nama_pelatihan: str,
    jenis_pelatihan: Optional[str],
    penyelenggara: Optional[str],
    tempat: Optional[str],
    tanggal_mulai: Optional[date],
    tanggal_selesai: Optional[date],
    jp: Optional[int],
    status: str,
    deskripsi: Optional[str],
    sertifikat_path: Optional[str],
    jabatan: Optional[str] = None,
    unit_kerja: Optional[str] = None,
    keterangan: Optional[str] = None,
) -> List[str]:
    """Produce SQL statements required to insert or update one training record."""

    condition = _pegawai_condition(pegawai_nama, pegawai_nip)
    jp_value = str(jp or 0)

    block: List[str] = []
    block.append(
        f"-- Data pelatihan untuk {pegawai_nama}{f' ({pegawai_nip})' if pegawai_nip else ''}"
    )
    block.append("SET @pegawai_id := NULL;")
    block.append("SET @jenis_pelatihan_id := NULL;")

    block.append(
        """
INSERT INTO pegawais
    (nama_lengkap, nip, jabatan, unit_kerja, status, keterangan, jp_target, jp_tercapai, created_at, updated_at)
SELECT
    {nama}, {nip}, {jabatan}, {unit_kerja}, 'aktif', {keterangan}, 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM pegawais WHERE {condition}
);
""".format(
            nama=_sql_literal(pegawai_nama),
            nip=_sql_literal(pegawai_nip) if pegawai_nip else "NULL",
            jabatan=_sql_literal(jabatan) if jabatan else "NULL",
            unit_kerja=_sql_literal(unit_kerja) if unit_kerja else "NULL",
            keterangan=_sql_literal(keterangan) if keterangan else "NULL",
            condition=condition,
        ).strip()
    )

    block.append(
        "SET @pegawai_id := (\n    SELECT id FROM pegawais\n     WHERE {condition}\n     ORDER BY id DESC\n     LIMIT 1\n);".format(
            condition=condition,
        )
    )

    if jenis_pelatihan:
        block.append(
            """
INSERT INTO jenis_pelatihans (nama, created_at, updated_at)
SELECT {nama}, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM jenis_pelatihans WHERE nama = {nama}
);
""".format(
                nama=_sql_literal(jenis_pelatihan)
            ).strip()
        )
        block.append(
            "SET @jenis_pelatihan_id := (\n    SELECT id FROM jenis_pelatihans\n     WHERE nama = {nama}\n     LIMIT 1\n);".format(
                nama=_sql_literal(jenis_pelatihan)
            )
        )

    block.append(
        """
INSERT INTO pelatihans
    (pegawai_id, nama_pelatihan, jenis_pelatihan_id, penyelenggara, tempat, tanggal_mulai, tanggal_selesai, jp, status, sertifikat_path, deskripsi, created_at, updated_at)
SELECT
    @pegawai_id,
    {nama_pelatihan},
    {jenis_id},
    {penyelenggara},
    {tempat},
    {tanggal_mulai},
    {tanggal_selesai},
    {jp},
    {status},
    {sertifikat},
    {deskripsi},
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
FROM DUAL
WHERE @pegawai_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM pelatihans
       WHERE {exists_condition}
  );
""".format(
            nama_pelatihan=_sql_literal(nama_pelatihan),
            jenis_id="@jenis_pelatihan_id" if jenis_pelatihan else "NULL",
            penyelenggara=_sql_literal(penyelenggara) if penyelenggara else "NULL",
            tempat=_sql_literal(tempat) if tempat else "NULL",
            tanggal_mulai=_sql_literal(tanggal_mulai) if tanggal_mulai else "NULL",
            tanggal_selesai=_sql_literal(tanggal_selesai) if tanggal_selesai else "NULL",
            jp=jp_value,
            status=_sql_literal(status),
            sertifikat=_sql_literal(sertifikat_path) if sertifikat_path else "NULL",
            deskripsi=_sql_literal(deskripsi) if deskripsi else "NULL",
            exists_condition=_pelatihan_exists_condition(
                nama_pelatihan,
                tanggal_mulai,
                tanggal_selesai,
            ),
        ).strip()
    )

    block.append(
        """
UPDATE pegawais
   SET jp_tercapai = (
           SELECT COALESCE(SUM(jp), 0)
             FROM pelatihans
            WHERE pelatihans.pegawai_id = pegawais.id
              AND status = 'selesai'
       ),
       updated_at = CURRENT_TIMESTAMP
 WHERE id = @pegawai_id
   AND @pegawai_id IS NOT NULL;
""".strip()
    )

    block.append("")
    return block


@dataclass
class SqlScriptBuilder:
    output_path: Path
    lines: List[str] = field(default_factory=list)
    _finalized: bool = False

    def __post_init__(self) -> None:
        timestamp = datetime.utcnow().strftime("%Y-%m-%d %H:%M:%S")
        self.lines.extend(
            [
                f"-- Generated by SIDIKLAT importer on {timestamp} UTC",
                "SET NAMES utf8mb4;",
                "SET FOREIGN_KEY_CHECKS = 0;",
                "START TRANSACTION;",
                "",
            ]
        )

    def add_block(self, statements: Sequence[str]) -> None:
        if self._finalized:
            raise RuntimeError("Cannot add statements after the script has been finalized")
        for stmt in statements:
            if stmt:
                self.lines.append(stmt)
        if statements and statements[-1] != "":
            self.lines.append("")

    def finalize(self) -> None:
        if self._finalized:
            return
        self.lines.extend([
            "COMMIT;",
            "SET FOREIGN_KEY_CHECKS = 1;",
            "",
        ])
        self._finalized = True

    def save(self) -> Path:
        self.finalize()
        self.output_path.parent.mkdir(parents=True, exist_ok=True)
        content = "\n".join(self.lines).rstrip() + "\n"
        self.output_path.write_text(content, encoding="utf-8")
        return self.output_path

    def __str__(self) -> str:
        return "\n".join(self.lines)

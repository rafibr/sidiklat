"""Data structures shared by importer components."""

from __future__ import annotations

from dataclasses import dataclass
from datetime import date
from pathlib import Path
from typing import Optional


@dataclass
class ExcelTrainingRow:
    nama: str
    nip: Optional[str]
    jabatan: Optional[str]
    unit_kerja: Optional[str]
    nama_pelatihan: str
    jenis_pelatihan: Optional[str]
    penyelenggara: Optional[str]
    tempat: Optional[str]
    tanggal_mulai: Optional[date]
    tanggal_selesai: Optional[date]
    jp: Optional[int]
    sertifikat_url: Optional[str]
    keterangan: Optional[str] = None


@dataclass
class SimpegTrainingRecord:
    pegawai_id: int
    pegawai_nama: str
    pegawai_nip: Optional[str]
    kategori: str
    nama_pelatihan: str
    induk_diklat: Optional[str]
    tempat: Optional[str]
    penyelenggara: Optional[str]
    tanggal_mulai: Optional[date]
    tanggal_selesai: Optional[date]
    jp: Optional[int]
    nomor_sttp: Optional[str]
    tanggal_sttp: Optional[date]
    sertifikat_url: Optional[str]
    sertifikat_path: Optional[Path] = None
    status: str = "selesai"


@dataclass
class StoredPegawai:
    id: int
    nama: str
    nip: Optional[str]


@dataclass
class StoredPelatihan:
    id: int
    pegawai_id: int
    nama_pelatihan: str
    tanggal_mulai: Optional[date]
    tanggal_selesai: Optional[date]
    jp: Optional[int]
    sertifikat_path: Optional[Path]

"""Load training data from JSON datasets."""

from __future__ import annotations

import json
from dataclasses import dataclass
from pathlib import Path
from typing import Any, Dict, List, Optional, Sequence

from .models import ExcelTrainingRow


@dataclass
class JsonTrainingEntry:
    nama: str
    nip: Optional[str]
    jenis_jabatan: Optional[str]
    jabatan: Optional[str]
    total_jp: Optional[int]
    pelatihan: List[Dict[str, Any]]


class JsonLoader:
    """Parse structured JSON data into :class:`ExcelTrainingRow` objects."""

    def __init__(self, fallback_jenis: Optional[str] = None) -> None:
        self.fallback_jenis = fallback_jenis

    def _parse_entry(self, payload: Dict[str, Any]) -> Optional[JsonTrainingEntry]:
        nama = (payload.get("nama") or "").strip()
        if not nama:
            return None

        nip = (payload.get("nip") or None) or None
        if nip:
            nip = str(nip).strip() or None

        jenis_jabatan = (payload.get("jenis_jabatan") or None)
        if jenis_jabatan:
            jenis_jabatan = str(jenis_jabatan).strip() or None

        jabatan = (payload.get("jabatan") or None)
        if jabatan:
            jabatan = str(jabatan).strip() or None

        total_raw = payload.get("total_jp")
        try:
            total_jp = int(total_raw) if total_raw not in (None, "") else None
        except (TypeError, ValueError):
            total_jp = None

        trainings = payload.get("pelatihan") or []
        if not isinstance(trainings, Sequence) or isinstance(trainings, (str, bytes)):
            trainings = []

        normalized: List[Dict[str, Any]] = []
        for item in trainings:
            if not isinstance(item, dict):
                continue
            nama_pelatihan = str(item.get("nama") or "").strip()
            if not nama_pelatihan:
                continue

            jp_value = item.get("jp")
            if isinstance(jp_value, str):
                jp_value = jp_value.strip() or None
            try:
                jp = int(jp_value) if jp_value not in (None, "") else None
            except (TypeError, ValueError):
                jp = None

            link = item.get("link_sertifikat")
            if link:
                link = str(link).strip() or None

            keterangan = item.get("keterangan")
            if keterangan:
                keterangan = str(keterangan).strip() or None

            normalized.append(
                {
                    "nama": nama_pelatihan,
                    "jp": jp,
                    "link_sertifikat": link,
                    "keterangan": keterangan,
                }
            )

        return JsonTrainingEntry(
            nama=nama,
            nip=nip,
            jenis_jabatan=jenis_jabatan,
            jabatan=jabatan,
            total_jp=total_jp,
            pelatihan=normalized,
        )

    def load(self, path: Path) -> List[ExcelTrainingRow]:
        with path.open("r", encoding="utf-8") as handle:
            payload = json.load(handle)

        rows: List[ExcelTrainingRow] = []
        if isinstance(payload, dict):
            payload = [payload]

        for item in payload:
            if not isinstance(item, dict):
                continue
            entry = self._parse_entry(item)
            if entry is None:
                continue

            trainings = entry.pelatihan or []
            for training in trainings:
                jp_value = training["jp"]
                if jp_value is None and entry.total_jp and len(trainings) == 1:
                    jp_value = entry.total_jp

                rows.append(
                    ExcelTrainingRow(
                        nama=entry.nama,
                        nip=entry.nip,
                        jabatan=entry.jabatan,
                        unit_kerja=None,
                        nama_pelatihan=training["nama"],
                        jenis_pelatihan=self.fallback_jenis,
                        penyelenggara=None,
                        tempat=None,
                        tanggal_mulai=None,
                        tanggal_selesai=None,
                        jp=jp_value,
                        sertifikat_url=training["link_sertifikat"],
                        keterangan=training["keterangan"],
                    )
                )

        return rows

    def load_iter(self, path: Path) -> Iterable[ExcelTrainingRow]:
        for row in self.load(path):
            yield row

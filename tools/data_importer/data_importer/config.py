"""Configuration helpers for the data importer."""

from __future__ import annotations

import os
from dataclasses import dataclass
from pathlib import Path
from typing import Optional

from dotenv import load_dotenv


load_dotenv()


@dataclass(frozen=True)
class DatabaseConfig:
    host: str
    port: int
    name: str
    user: str
    password: str

    @classmethod
    def from_env(cls) -> "DatabaseConfig":
        return cls(
            host=os.getenv("SIDIKLAT_DB_HOST", "127.0.0.1"),
            port=int(os.getenv("SIDIKLAT_DB_PORT", "3306")),
            name=os.getenv("SIDIKLAT_DB_NAME", "sidiklat"),
            user=os.getenv("SIDIKLAT_DB_USER", "root"),
            password=os.getenv("SIDIKLAT_DB_PASSWORD", ""),
        )


@dataclass(frozen=True)
class StorageConfig:
    certificate_root: Path

    @classmethod
    def from_env(cls) -> "StorageConfig":
        root = Path(os.getenv("SIDIKLAT_CERTIFICATE_ROOT", "storage/app/public/sertifikat"))
        return cls(certificate_root=root)


@dataclass(frozen=True)
class SimpegConfig:
    base_url: str
    username: Optional[str]
    password: Optional[str]
    headless: bool = False

    @classmethod
    def from_env(cls) -> "SimpegConfig":
        return cls(
            base_url=os.getenv("SIMPEG_BASE_URL", "https://simpeg.bkpp.banjarbarukota.go.id"),
            username=os.getenv("SIMPEG_USERNAME"),
            password=os.getenv("SIMPEG_PASSWORD"),
            headless=os.getenv("SIMPEG_HEADLESS", "false").lower() != "false",
        )


@dataclass(frozen=True)
class AppConfig:
    database: DatabaseConfig
    storage: StorageConfig
    simpeg: SimpegConfig

    @classmethod
    def load(cls) -> "AppConfig":
        return cls(
            database=DatabaseConfig.from_env(),
            storage=StorageConfig.from_env(),
            simpeg=SimpegConfig.from_env(),
        )

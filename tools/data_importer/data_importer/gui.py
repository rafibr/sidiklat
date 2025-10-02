"""Tkinter-based GUI wrapper for the Excel importer workflow."""

from __future__ import annotations

import logging
import threading
from pathlib import Path
import tkinter as tk
from tkinter import filedialog, messagebox, ttk
from tkinter.scrolledtext import ScrolledText

from .certificate_downloader import CertificateDownloader
from .cli import _store_excel_row
from .config import AppConfig
from .db import database_client
from .excel_loader import ExcelLoader
from .sql_writer import SqlScriptBuilder


logger = logging.getLogger(__name__)


class ExcelImportApp:
    """Simple desktop UI to drive the Excel import pipeline."""

    def __init__(self) -> None:
        self.root = tk.Tk()
        self.root.title("SIDIKLAT Excel Importer")
        self.root.resizable(False, False)

        self.file_path = tk.StringVar()
        self.sheet_name = tk.StringVar()
        self.status_var = tk.StringVar(value="Silakan pilih berkas Excel untuk diproses.")

        self._build_layout()

    def _build_layout(self) -> None:
        main_frame = ttk.Frame(self.root, padding=15)
        main_frame.grid(row=0, column=0, sticky="nsew")

        ttk.Label(main_frame, text="Berkas Excel:").grid(row=0, column=0, sticky="w")
        file_entry = ttk.Entry(main_frame, width=50, textvariable=self.file_path)
        file_entry.grid(row=1, column=0, padx=(0, 8), pady=(0, 10), sticky="we")
        browse_btn = ttk.Button(main_frame, text="Browse…", command=self._choose_excel_file)
        browse_btn.grid(row=1, column=1, pady=(0, 10))

        ttk.Label(main_frame, text="Nama Sheet (opsional):").grid(row=2, column=0, sticky="w")
        sheet_entry = ttk.Entry(main_frame, width=50, textvariable=self.sheet_name)
        sheet_entry.grid(row=3, column=0, columnspan=2, pady=(0, 10), sticky="we")

        button_frame = ttk.Frame(main_frame)
        button_frame.grid(row=4, column=0, columnspan=2, pady=(0, 10), sticky="e")

        self.import_btn = ttk.Button(button_frame, text="Import ke Database", command=self._import_to_database)
        self.import_btn.grid(row=0, column=0, padx=(0, 8))

        self.sql_btn = ttk.Button(button_frame, text="Generate SQL…", command=self._export_sql)
        self.sql_btn.grid(row=0, column=1)

        self.progress = ttk.Progressbar(main_frame, mode="indeterminate")
        self.progress.grid(row=5, column=0, columnspan=2, sticky="we")

        status_label = ttk.Label(main_frame, textvariable=self.status_var)
        status_label.grid(row=6, column=0, columnspan=2, pady=(8, 0), sticky="w")

        self.log_widget = ScrolledText(main_frame, width=60, height=12, state="disabled")
        self.log_widget.grid(row=7, column=0, columnspan=2, pady=(10, 0))

    def _choose_excel_file(self) -> None:
        path = filedialog.askopenfilename(
            title="Pilih berkas Excel",
            filetypes=(
                ("Excel Files", "*.xlsx *.xlsm *.xls"),
                ("Semua berkas", "*.*"),
            ),
        )
        if path:
            self.file_path.set(path)

    def _import_to_database(self) -> None:
        self._start_worker(sql_output=None)

    def _export_sql(self) -> None:
        sql_path = filedialog.asksaveasfilename(
            title="Simpan skrip SQL",
            defaultextension=".sql",
            filetypes=(("SQL", "*.sql"), ("Semua berkas", "*.*")),
        )
        if sql_path:
            self._start_worker(sql_output=Path(sql_path))

    def _start_worker(self, sql_output: Path | None) -> None:
        excel_path = self.file_path.get().strip()
        if not excel_path:
            messagebox.showerror("Berkas belum dipilih", "Silakan pilih berkas Excel terlebih dahulu.")
            return

        excel_file = Path(excel_path)
        if not excel_file.exists():
            messagebox.showerror("Berkas tidak ditemukan", f"{excel_file} tidak ada.")
            return

        sheet = self.sheet_name.get().strip() or None

        self._set_running(True)
        self.append_log(f"Memulai proses untuk {excel_file}…")

        thread = threading.Thread(
            target=self._run_import,
            args=(excel_file, sheet, sql_output),
            daemon=True,
        )
        thread.start()

    def _run_import(self, excel_file: Path, sheet: str | None, sql_output: Path | None) -> None:
        try:
            config = AppConfig.load()
            loader = ExcelLoader()
            downloader = CertificateDownloader(config.storage.certificate_root)
            certificate_root = config.storage.certificate_root

            rows = loader.load(excel_file, sheet_name=sheet)
            self.append_log(f"Memproses {len(rows)} baris dari Excel…")

            if sql_output is not None:
                builder = SqlScriptBuilder(sql_output)
                for row in rows:
                    _store_excel_row(row, downloader, certificate_root, sql_builder=builder)
                output_path = builder.save()
                self.append_log(f"Skrip SQL tersimpan di {output_path}")
                self._notify_success("Proses selesai", f"Skrip SQL berhasil dibuat di {output_path}")
            else:
                with database_client(config.database) as client:
                    for row in rows:
                        _store_excel_row(row, downloader, certificate_root, client=client)
                self.append_log("Import ke database selesai.")
                self._notify_success("Proses selesai", "Seluruh data berhasil dimasukkan ke database.")
        except Exception as exc:  # noqa: BLE001
            logger.exception("Gagal memproses Excel", exc_info=exc)
            self.append_log(f"Terjadi kesalahan: {exc}")
            self._notify_error("Gagal", str(exc))
        finally:
            self.root.after(0, lambda: self._set_running(False))

    def append_log(self, message: str) -> None:
        def _write() -> None:
            self.log_widget.configure(state="normal")
            self.log_widget.insert(tk.END, message + "\n")
            self.log_widget.see(tk.END)
            self.log_widget.configure(state="disabled")

        self.root.after(0, _write)

    def _set_running(self, running: bool) -> None:
        if running:
            self.status_var.set("Sedang memproses…")
            self.import_btn.configure(state="disabled")
            self.sql_btn.configure(state="disabled")
            self.progress.start(10)
        else:
            self.status_var.set("Selesai." if self.log_widget.index("end-1c") != "1.0" else self.status_var.get())
            self.import_btn.configure(state="normal")
            self.sql_btn.configure(state="normal")
            self.progress.stop()

    def _notify_success(self, title: str, message: str) -> None:
        self.root.after(0, lambda: messagebox.showinfo(title, message))

    def _notify_error(self, title: str, message: str) -> None:
        self.root.after(0, lambda: messagebox.showerror(title, message))

    def run(self) -> None:  # pragma: no cover - UI loop
        self.root.mainloop()


def run() -> None:  # pragma: no cover - entry point helper
    ExcelImportApp().run()


if __name__ == "__main__":  # pragma: no cover
    run()

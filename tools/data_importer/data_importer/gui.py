"""Simple GUI wrapper for the SIDIKLAT data importer."""

from __future__ import annotations

import queue
import threading
from pathlib import Path
from typing import Optional

import tkinter as tk
from tkinter import filedialog, messagebox, ttk

from .cli import import_excel_data


class ImporterGUI:
    """Tkinter based GUI to import Excel files."""

    def __init__(self) -> None:
        self.root = tk.Tk()
        self.root.title("SIDIKLAT Data Importer")
        self.root.geometry("520x240")
        self.root.resizable(False, False)

        self.excel_path_var = tk.StringVar()
        self.sheet_name_var = tk.StringVar()
        self.use_sql_var = tk.BooleanVar(value=False)
        self.sql_path_var = tk.StringVar()
        self.status_var = tk.StringVar(value="Pilih file Excel untuk mulai.")

        self._queue: queue.Queue[tuple[str, str]] = queue.Queue()
        self._thread: Optional[threading.Thread] = None

        self._build_widgets()

    def _build_widgets(self) -> None:
        padding = {"padx": 12, "pady": 6}

        excel_frame = ttk.LabelFrame(self.root, text="Sumber Excel")
        excel_frame.pack(fill="x", **padding)

        excel_row = ttk.Frame(excel_frame)
        excel_row.pack(fill="x", padx=8, pady=6)

        ttk.Label(excel_row, text="File Excel:").pack(side="left")
        entry = ttk.Entry(excel_row, textvariable=self.excel_path_var, width=45)
        entry.pack(side="left", padx=(6, 6))
        ttk.Button(excel_row, text="Browse...", command=self._browse_excel).pack(side="left")

        sheet_row = ttk.Frame(excel_frame)
        sheet_row.pack(fill="x", padx=8, pady=(0, 6))
        ttk.Label(sheet_row, text="Sheet (opsional):").pack(side="left")
        ttk.Entry(sheet_row, textvariable=self.sheet_name_var, width=20).pack(side="left", padx=(6, 0))

        sql_frame = ttk.LabelFrame(self.root, text="Output SQL (opsional)")
        sql_frame.pack(fill="x", **padding)

        sql_toggle = ttk.Checkbutton(
            sql_frame,
            text="Simpan ke file SQL",
            variable=self.use_sql_var,
            command=self._toggle_sql_entry,
        )
        sql_toggle.pack(anchor="w", padx=8, pady=(6, 0))

        sql_row = ttk.Frame(sql_frame)
        sql_row.pack(fill="x", padx=8, pady=(0, 6))

        self.sql_entry = ttk.Entry(sql_row, textvariable=self.sql_path_var, width=45, state="disabled")
        self.sql_entry.pack(side="left")
        self.sql_button = ttk.Button(sql_row, text="Simpan Sebagai...", command=self._browse_sql, state="disabled")
        self.sql_button.pack(side="left", padx=(6, 0))

        action_frame = ttk.Frame(self.root)
        action_frame.pack(fill="x", **padding)

        self.import_button = ttk.Button(action_frame, text="Mulai Import", command=self._start_import)
        self.import_button.pack(side="right")

        status_label = ttk.Label(self.root, textvariable=self.status_var, foreground="#555555")
        status_label.pack(anchor="w", padx=16, pady=(0, 12))

    def _browse_excel(self) -> None:
        filename = filedialog.askopenfilename(
            title="Pilih berkas Excel",
            filetypes=[("Excel Files", "*.xlsx *.xlsm *.xls"), ("All Files", "*.*")],
        )
        if filename:
            self.excel_path_var.set(filename)

    def _browse_sql(self) -> None:
        filename = filedialog.asksaveasfilename(
            title="Pilih lokasi output SQL",
            defaultextension=".sql",
            filetypes=[("SQL Files", "*.sql"), ("All Files", "*.*")],
        )
        if filename:
            self.sql_path_var.set(filename)

    def _toggle_sql_entry(self) -> None:
        state = "normal" if self.use_sql_var.get() else "disabled"
        self.sql_entry.configure(state=state)
        self.sql_button.configure(state=state)

    def _start_import(self) -> None:
        excel_path = Path(self.excel_path_var.get()).expanduser()
        if not excel_path.exists():
            messagebox.showerror("Kesalahan", "File Excel belum dipilih atau tidak ditemukan.")
            return

        sql_output: Optional[Path] = None
        if self.use_sql_var.get():
            sql_value = self.sql_path_var.get().strip()
            if not sql_value:
                messagebox.showerror("Kesalahan", "Lokasi file SQL harus diisi atau nonaktifkan opsi SQL.")
                return
            sql_output = Path(sql_value).expanduser()

        sheet_name = self.sheet_name_var.get().strip() or None

        if self._thread and self._thread.is_alive():
            messagebox.showwarning("Sedang Berjalan", "Proses import masih berjalan.")
            return

        self.status_var.set("Memulai proses import...")
        self.import_button.configure(state="disabled")

        def runner() -> None:
            try:
                import_excel_data(excel_path, sheet_name=sheet_name, sql_output=sql_output)
            except Exception as exc:  # noqa: BLE001
                self._queue.put(("error", str(exc)))
            else:
                if sql_output:
                    message = f"Selesai. SQL tersimpan di {sql_output}."
                else:
                    message = "Import Excel selesai ke database."
                self._queue.put(("success", message))

        self._thread = threading.Thread(target=runner, daemon=True)
        self._thread.start()
        self.root.after(200, self._poll_queue)

    def _poll_queue(self) -> None:
        try:
            status, message = self._queue.get_nowait()
        except queue.Empty:
            if self._thread and self._thread.is_alive():
                self.root.after(200, self._poll_queue)
            else:
                self.import_button.configure(state="normal")
                self.status_var.set("Siap.")
            return

        self.import_button.configure(state="normal")
        self.status_var.set(message)

        if status == "error":
            messagebox.showerror("Gagal", message)
        else:
            messagebox.showinfo("Berhasil", message)

    def run(self) -> None:
        self.root.mainloop()


def launch_gui() -> None:
    """Launch the data importer GUI."""

    gui = ImporterGUI()
    gui.run()


if __name__ == "__main__":  # pragma: no cover
    launch_gui()

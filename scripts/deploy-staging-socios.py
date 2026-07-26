#!/usr/bin/env python3
"""Copia selectiva y reintentable del portal socios al entorno staging."""

from __future__ import annotations

import argparse
import ftplib
import io
import ssl
import sys
from pathlib import Path, PurePosixPath


FTP_HOST = "162.241.194.161"
FTP_USER = "montana.uchile@ramuch.cl"
REMOTE_ROOT = PurePosixPath("/staging.ramuch.cl/socios")

EXCLUDED_DIRECTORIES = {
    PurePosixPath("template/node_modules"),
    PurePosixPath("template/font-awesome/scss"),
    PurePosixPath("template/font-awesome/svgs"),
    PurePosixPath("template/font-awesome/sprites"),
    PurePosixPath("template/font-awesome/metadata"),
    PurePosixPath("template/font-awesome/less"),
    PurePosixPath("includes/PHPExcel"),
    PurePosixPath("includes/fpdf"),
    PurePosixPath("components/socios/archivos"),
    PurePosixPath("components/mercado/images"),
    PurePosixPath("images/img_perfil"),
    PurePosixPath("images/img_equipo"),
    PurePosixPath("images/equipo"),
    PurePosixPath("images/ingresos"),
    PurePosixPath("images/egresos"),
    PurePosixPath("images/pagos"),
    PurePosixPath("images/deudas"),
}

EXCLUDED_NAMES = {
    ".DS_Store",
    "Thumbs.db",
    "error_log",
    "modelserror.log",
    "conexionMysql.php",
}

EXCLUDED_SUFFIXES = {
    ".log",
    ".sql",
    ".dump",
    ".xls",
    ".xlsx",
}


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser()
    parser.add_argument("--source", type=Path, default=Path("socios"))
    parser.add_argument("--password-file", type=Path, required=True)
    parser.add_argument("--execute", action="store_true")
    parser.add_argument("--skip-existing-same-size", action="store_true")
    return parser.parse_args()


def is_excluded(relative_path: PurePosixPath) -> bool:
    if relative_path.name in EXCLUDED_NAMES:
        return True
    if relative_path.suffix.lower() in EXCLUDED_SUFFIXES:
        return True
    return any(
        relative_path == excluded or excluded in relative_path.parents
        for excluded in EXCLUDED_DIRECTORIES
    )


def build_manifest(source: Path) -> list[tuple[Path, PurePosixPath]]:
    manifest: list[tuple[Path, PurePosixPath]] = []
    for local_path in sorted(source.rglob("*")):
        if not local_path.is_file() or local_path.is_symlink():
            continue
        relative_path = PurePosixPath(local_path.relative_to(source).as_posix())
        if not is_excluded(relative_path):
            manifest.append((local_path, relative_path))
    return manifest


class StagingFtp:
    def __init__(self, password: str):
        self.password = password
        self.ftp: ftplib.FTP_TLS | None = None
        self.created_directories: set[str] = set()

    def connect(self) -> None:
        self.close()
        context = ssl._create_unverified_context()
        ftp = ftplib.FTP_TLS(context=context, timeout=30)
        ftp.connect(FTP_HOST, 21)
        ftp.login(FTP_USER, self.password)
        ftp.prot_p()
        ftp.set_pasv(True)
        self.ftp = ftp
        self.created_directories.clear()

    def close(self) -> None:
        if self.ftp is None:
            return
        try:
            self.ftp.quit()
        except Exception:
            try:
                self.ftp.close()
            except Exception:
                pass
        self.ftp = None

    def ensure_directory(self, remote_directory: PurePosixPath) -> None:
        assert self.ftp is not None
        current = PurePosixPath("/")
        for part in remote_directory.parts[1:]:
            current /= part
            current_text = str(current)
            if current_text in self.created_directories:
                continue
            try:
                self.ftp.mkd(current_text)
            except ftplib.error_perm as exc:
                if not str(exc).startswith("550"):
                    raise
            self.created_directories.add(current_text)

    def upload(self, local_path: Path, relative_path: PurePosixPath) -> None:
        assert self.ftp is not None
        remote_path = REMOTE_ROOT / relative_path
        self.ensure_directory(remote_path.parent)
        with local_path.open("rb") as handle:
            self.ftp.storbinary(f"STOR {remote_path}", handle, blocksize=64 * 1024)

    def has_same_size(self, local_path: Path, relative_path: PurePosixPath) -> bool:
        assert self.ftp is not None
        try:
            remote_size = self.ftp.size(str(REMOTE_ROOT / relative_path))
        except ftplib.error_perm:
            return False
        return remote_size == local_path.stat().st_size

    def copy_staging_connection(self) -> None:
        assert self.ftp is not None
        source = io.BytesIO()
        self.ftp.retrbinary(
            "RETR /staging.ramuch.cl/admin/includes/conexionMysql.php",
            source.write,
        )
        source.seek(0)
        target = REMOTE_ROOT / "includes/conexionMysql.php"
        self.ensure_directory(target.parent)
        self.ftp.storbinary(f"STOR {target}", source, blocksize=64 * 1024)


def main() -> int:
    args = parse_args()
    manifest = build_manifest(args.source)
    total_bytes = sum(local.stat().st_size for local, _ in manifest)
    print(f"Archivos seleccionados: {len(manifest)}")
    print(f"Tamaño seleccionado: {total_bytes / 1024 / 1024:.1f} MiB")

    if not args.execute:
        print("Simulación terminada; use --execute para transferir.")
        return 0

    password = args.password_file.read_text(encoding="utf-8").strip()
    client = StagingFtp(password)
    try:
        client.connect()
        for index, (local_path, relative_path) in enumerate(manifest, start=1):
            for attempt in range(1, 4):
                try:
                    if not (
                        args.skip_existing_same_size
                        and client.has_same_size(local_path, relative_path)
                    ):
                        client.upload(local_path, relative_path)
                    break
                except (OSError, EOFError, ftplib.Error) as exc:
                    if attempt == 3:
                        raise RuntimeError(
                            f"No se pudo subir {relative_path}: {exc}"
                        ) from exc
                    client.connect()
            if index % 100 == 0 or index == len(manifest):
                print(f"Transferidos: {index}/{len(manifest)}", flush=True)

        client.copy_staging_connection()
        print("Conexión staging aplicada a /socios/includes/conexionMysql.php")
    finally:
        client.close()

    return 0


if __name__ == "__main__":
    sys.exit(main())

#!/usr/bin/env python3
"""Publica pagar/registro en staging con configuración aislada."""

from __future__ import annotations

import argparse
import ftplib
import io
import ssl
import threading
from concurrent.futures import ThreadPoolExecutor
from pathlib import Path, PurePosixPath


FTP_HOST = "162.241.194.161"
FTP_USER = "montana.uchile@ramuch.cl"
REMOTE_SITE = PurePosixPath("/staging.ramuch.cl")
MODULES = ("pagar", "registro")
EXCLUDED_NAMES = {".DS_Store", "Thumbs.db", "error_log"}
TEXT_SUFFIXES = {".php", ".js", ".css", ".html", ".htaccess"}
NODE_RUNTIME_PREFIXES = (
    "node_modules/@coreui/coreui/dist/",
    "node_modules/@coreui/icons/css/",
    "node_modules/@coreui/icons/fonts/",
    "node_modules/bootstrap/dist/",
    "node_modules/flag-icon-css/css/",
    "node_modules/flag-icon-css/flags/",
    "node_modules/font-awesome/css/",
    "node_modules/font-awesome/fonts/",
    "node_modules/jquery/dist/",
    "node_modules/pace-progress/pace.min.js",
    "node_modules/perfect-scrollbar/dist/",
    "node_modules/popper.js/dist/",
    "node_modules/simple-line-icons/css/",
    "node_modules/simple-line-icons/fonts/",
)

# Credenciales oficiales de prueba que ya acompañan al cliente Flow del proyecto.
FLOW_PRODUCTION_KEY = "7AB28AEF-7900-4D30-A361-2CLC28E84635"
FLOW_PRODUCTION_SECRET = "022c9f928367b6b084f07b1790ab7b3ae3ab8663"
FLOW_SANDBOX_KEY = "320F6E9B-F458-4190-85B3-4714L7297E0D"
FLOW_SANDBOX_SECRET = "49b6132abe79b2aa70d876523c2dfb55328a683e"


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser()
    parser.add_argument("--source", type=Path, default=Path("."))
    parser.add_argument("--password-file", type=Path, required=True)
    parser.add_argument("--execute", action="store_true")
    return parser.parse_args()


def manifest(source: Path) -> list[tuple[str, Path, PurePosixPath]]:
    files: list[tuple[str, Path, PurePosixPath]] = []
    for module in MODULES:
        root = source / module
        for path in sorted(root.rglob("*")):
            if not path.is_file() or path.is_symlink() or path.name in EXCLUDED_NAMES:
                continue
            relative = PurePosixPath(path.relative_to(root).as_posix())
            relative_text = relative.as_posix()
            if relative_text.startswith("node_modules/") and not relative_text.startswith(
                NODE_RUNTIME_PREFIXES
            ):
                continue
            if relative == PurePosixPath("includes/conexionMysql.php"):
                continue
            files.append((module, path, relative))
    return files


def staging_content(module: str, path: Path, relative: PurePosixPath) -> bytes:
    content = path.read_bytes()
    if path.suffix.lower() not in TEXT_SUFFIXES:
        return content
    try:
        text = content.decode("utf-8")
    except UnicodeDecodeError:
        return content

    text = text.replace("https://www.ramuch.cl", "https://staging.ramuch.cl")
    text = text.replace("https://ramuch.cl", "https://staging.ramuch.cl")
    if module == "pagar" and relative == PurePosixPath("flow/lib/Config.class.php"):
        text = text.replace(FLOW_PRODUCTION_KEY, FLOW_SANDBOX_KEY)
        text = text.replace(FLOW_PRODUCTION_SECRET, FLOW_SANDBOX_SECRET)
        text = text.replace("https://www.flow.cl/api", "https://sandbox.flow.cl/api")
    return text.encode("utf-8")


class Client:
    def __init__(self, password: str):
        context = ssl._create_unverified_context()
        self.ftp = ftplib.FTP_TLS(context=context, timeout=45)
        self.ftp.connect(FTP_HOST, 21)
        self.ftp.login(FTP_USER, password)
        self.ftp.prot_p()
        self.ftp.set_pasv(True)
        self.directories: set[str] = set()

    def close(self) -> None:
        try:
            self.ftp.quit()
        except Exception:
            self.ftp.close()

    def ensure(self, directory: PurePosixPath) -> None:
        current = PurePosixPath("/")
        for part in directory.parts[1:]:
            current /= part
            if str(current) in self.directories:
                continue
            try:
                self.ftp.mkd(str(current))
            except ftplib.error_perm as exc:
                if not str(exc).startswith("550"):
                    raise
            self.directories.add(str(current))

    def upload(self, remote: PurePosixPath, content: bytes) -> None:
        self.ensure(remote.parent)
        self.ftp.storbinary(f"STOR {remote}", io.BytesIO(content), blocksize=64 * 1024)

    def install_database_config(self, module: str) -> None:
        content = io.BytesIO()
        self.ftp.retrbinary(
            "RETR /staging.ramuch.cl/admin/includes/conexionMysql.php", content.write
        )
        self.upload(REMOTE_SITE / module / "includes/conexionMysql.php", content.getvalue())


def main() -> int:
    args = parse_args()
    files = manifest(args.source)
    print(f"Archivos seleccionados: {len(files)}")
    if not args.execute:
        print("Simulación terminada; use --execute para transferir.")
        return 0

    password = args.password_file.read_text(encoding="utf-8").strip()
    local = threading.local()
    clients: list[Client] = []
    clients_lock = threading.Lock()

    def upload_file(item: tuple[str, Path, PurePosixPath]) -> None:
        if not hasattr(local, "client"):
            local.client = Client(password)
            with clients_lock:
                clients.append(local.client)
        module, path, relative = item
        local.client.upload(
            REMOTE_SITE / module / relative,
            staging_content(module, path, relative),
        )

    try:
        with ThreadPoolExecutor(max_workers=8) as executor:
            for number, _ in enumerate(executor.map(upload_file, files), 1):
                if number % 200 == 0 or number == len(files):
                    print(f"Transferidos: {number}/{len(files)}", flush=True)

        config_client = Client(password)
        try:
            for module in MODULES:
                config_client.install_database_config(module)
                print(f"Conexión staging aplicada a /{module}/includes/conexionMysql.php")
        finally:
            config_client.close()
    finally:
        for client in clients:
            client.close()
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

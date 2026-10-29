"""Construye un paquete de despliegue sin secretos ni materiales de referencia."""

from pathlib import Path
import hashlib
import zipfile

ROOT = Path(__file__).resolve().parents[1]
OUTPUT = ROOT / 'dist' / 'aidex-sitio.zip'
DIRECTORIES = ['public', 'src', 'config', 'vendor', 'docs']
FILES = ['composer.json', 'composer.lock', '.env.example', '.gitignore', '.htaccess', 'README.md', 'storage/.gitkeep']

if not (ROOT / 'vendor' / 'autoload.php').is_file():
    raise SystemExit('Ejecuta composer install --no-dev antes de empaquetar.')

OUTPUT.parent.mkdir(exist_ok=True)
paths = [ROOT / name for name in FILES]
for directory in DIRECTORIES:
    paths.extend(path for path in (ROOT / directory).rglob('*') if path.is_file())

with zipfile.ZipFile(OUTPUT, 'w', compression=zipfile.ZIP_DEFLATED) as archive:
    for path in sorted(paths):
        if path.name in {'.DS_Store', '.env'} or path.is_symlink():
            continue
        archive.write(path, Path('sitio') / path.relative_to(ROOT))

print(f'Paquete: {OUTPUT}')
print(f'SHA-256: {hashlib.sha256(OUTPUT.read_bytes()).hexdigest()}')
print(f'Tamaño: {OUTPUT.stat().st_size:,} bytes')

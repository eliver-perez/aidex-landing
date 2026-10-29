"""Pruebas HTTP/SMTP aisladas. Nunca contactan a un proveedor de correo real."""

import http.cookiejar
import json
import os
from pathlib import Path
import re
import socket
import socketserver
import subprocess
import tempfile
import threading
import time
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
ARTIFACTS = ROOT / 'tests' / 'artifacts'
ARTIFACTS.mkdir(exist_ok=True)


class SMTPHandler(socketserver.StreamRequestHandler):
    def handle(self):
        self.wfile.write(b'220 localhost AIDEX test receiver\r\n')
        in_data = False
        lines = []

        while True:
            line = self.rfile.readline()
            if not line:
                return

            if in_data:
                if line == b'.\r\n':
                    self.server.messages.append(b''.join(lines))
                    code = b'550 Message rejected\r\n' if self.server.reject else b'250 Message accepted\r\n'
                    self.wfile.write(code)
                    in_data = False
                    lines = []
                else:
                    lines.append(line)
                continue

            command = line.split(b' ', 1)[0].strip().upper()
            if command in (b'EHLO', b'HELO'):
                self.wfile.write(b'250-localhost\r\n250 SIZE 100000\r\n')
            elif command == b'DATA':
                self.wfile.write(b'354 Send content\r\n')
                in_data = True
            elif command == b'QUIT':
                self.wfile.write(b'221 Bye\r\n')
                return
            else:
                self.wfile.write(b'250 OK\r\n')


class SMTPServer(socketserver.ThreadingTCPServer):
    allow_reuse_address = True
    daemon_threads = True
    reject = False


def free_port():
    with socket.socket() as connection:
        connection.bind(('127.0.0.1', 0))
        return connection.getsockname()[1]


def request(opener, url, data=None, json_response=True):
    headers = {'Accept': 'application/json'} if json_response else {}
    body = urllib.parse.urlencode(data).encode() if data is not None else None
    req = urllib.request.Request(url, data=body, headers=headers)
    try:
        response = opener.open(req)
    except urllib.error.HTTPError as error:
        response = error
    text = response.read().decode()
    return response.status, response.headers, text


def run_suite():
    smtp = SMTPServer(('127.0.0.1', 0), SMTPHandler)
    smtp.messages = []
    thread = threading.Thread(target=smtp.serve_forever, daemon=True)
    thread.start()

    with tempfile.TemporaryDirectory(prefix='integration-', dir=ARTIFACTS) as temporary:
        port = free_port()
        env = {
            **os.environ,
            'APP_ENV': 'local',
            'SMTP_ENABLED': 'true',
            'SMTP_HOST': '127.0.0.1',
            'SMTP_PORT': str(smtp.server_address[1]),
            'SMTP_ENCRYPTION': 'none',
            'SMTP_AUTH': 'false',
            'SMTP_FROM_EMAIL': 'website@example.test',
            'SMTP_TO_EMAIL': 'recipient@example.test',
            'SMTP_REPLY_TO': '',
            'STORAGE_PATH': temporary,
            'CONTACT_RATE_LIMIT': '30',
            'SITE_INDEXABLE': 'true',
            'SITE_URL': 'https://example.test',
        }
        log = open(Path(temporary) / 'server.log', 'w')
        server = subprocess.Popen(
            ['php', '-S', f'127.0.0.1:{port}', '-t', 'public', 'tools/router.php'],
            cwd=ROOT, env=env, stdout=log, stderr=log
        )
        base = f'http://127.0.0.1:{port}'
        opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))

        try:
            for _ in range(50):
                try:
                    status, headers, html = request(opener, base, json_response=False)
                    break
                except urllib.error.URLError:
                    time.sleep(0.1)
            else:
                raise AssertionError('No inició el servidor PHP de pruebas.')

            assert status == 200
            assert "frame-ancestors 'none'" in headers['Content-Security-Policy']
            assert 'HttpOnly' in headers['Set-Cookie']
            assert 'SameSite=Lax' in headers['Set-Cookie']
            assert 'no-store' in headers['Cache-Control']
            token = re.search(r'name="csrf"\s+value="([^"]+)"', html)[1]
            data = {
                'csrf': token,
                'nombre': 'Persona de prueba',
                'empresa': 'Ejemplo ficticio',
                'correo': 'visitor@example.test',
                'telefono': '+52 (81) 1234-5678',
                'mensaje': 'Mensaje local de prueba; no entregar fuera del equipo.',
                'website': '',
            }

            assert request(opener, base + '/contact.php', {**data, 'csrf': 'invalid'})[0] == 403
            assert request(opener, base + '/contact.php')[0] == 405
            time.sleep(2.1)
            assert request(opener, base + '/contact.php', {**data, 'website': 'bot'})[0] == 422
            for field, value in [('nombre', ''), ('correo', 'invalid'), ('mensaje', ' '), ('nombre', 'a' * 121), ('correo', 'a@example.test\r\nBcc: b@example.test')]:
                status, _, content = request(opener, base + '/contact.php', {**data, field: value})
                assert status == 422, (field, status)
                assert field in json.loads(content)['errors']
            assert len(smtp.messages) == 0

            status, _, content = request(opener, base + '/contact.php', data)
            result = json.loads(content)
            assert status == 200 and result['ok'] is True
            assert len(smtp.messages) == 1
            assert b'Reply-To: visitor@example.test' in smtp.messages[0]
            assert b'To: recipient@example.test' in smtp.messages[0]
            assert b'From: AIDEX' in smtp.messages[0]
            assert result['csrf'] != token
            assert request(opener, base + '/contact.php', data)[0] == 403
            data['csrf'] = result['csrf']

            time.sleep(2.1)
            smtp.reject = True
            status, _, content = request(opener, base + '/contact.php', data)
            assert status == 502 and json.loads(content)['ok'] is False
            assert '550' not in content and 'SMTP' not in content
            smtp.reject = False

            # Sin JavaScript: POST, redirección y valores escapados tras el error.
            status, _, content = request(opener, base + '/contact.php', {
                **data, 'nombre': '<script>alert(1)</script>', 'correo': 'invalid'
            }, json_response=False)
            assert status == 200
            assert '&lt;script&gt;alert(1)&lt;/script&gt;' in content
            assert '<script>alert(1)</script>' not in content

            for path in ['/.env', '/config/environment.php', '/src/contact.php', '/vendor/autoload.php', '/composer.json', '/storage/contact-rate.json', '/../.env', '/assets/../../.env']:
                assert request(opener, base + path)[0] == 404, path
            assert 'Sitemap: https://example.test/sitemap.xml' in request(opener, base + '/robots.txt')[2]
            assert '<loc>https://example.test/</loc>' in request(opener, base + '/sitemap.xml')[2]

            # El límite persiste aunque se obtenga otra sesión y otro CSRF.
            for _ in range(32):
                status, _, _ = request(opener, base + '/contact.php', {**data, 'correo': 'invalid'})
                if status == 429:
                    break
            assert status == 429
            other = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
            _, _, html = request(other, base, json_response=False)
            other_token = re.search(r'name="csrf"\s+value="([^"]+)"', html)[1]
            time.sleep(2.1)
            assert request(other, base + '/contact.php', {**data, 'csrf': other_token})[0] == 429
            print('OK: SMTP 250/550, CSRF, validación, honeypot, límite entre sesiones, XSS, privacidad de archivos, SEO y POST sin JavaScript.')
        finally:
            server.terminate()
            server.wait(timeout=5)
            log.close()
            smtp.shutdown()
            smtp.server_close()


if __name__ == '__main__':
    run_suite()

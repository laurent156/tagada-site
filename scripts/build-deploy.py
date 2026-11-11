"""Copy only the files the site actually uses into an output folder (default: _mise-en-ligne/tagada.be).

Used by the GitHub Actions deploy to one.com and for manual uploads. Pages, server files and
every local asset referenced from a page are included; README, source photos, tooling are not.
"""
import glob
import os
import re
import shutil
import sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SERVER_FILES = ['.htaccess', 'contact.php', 'robots.txt', 'sitemap.xml', 'llms.txt', 'site.webmanifest',
                'favicon.ico', 'favicon-16.png', 'favicon-32.png', 'favicon-192.png', 'favicon-512.png',
                'apple-touch-icon.png']
ASSET_REF = re.compile(r'(?:src|href|content|poster)="([^"#?]+)"|url\(["\']?([^)"\'?#]+)')
SITE_URL = re.compile(r'https://tagada\.be/([^"]+\.(?:jpg|png))')


def referenced_assets(page):
    text = open(os.path.join(ROOT, page), encoding='utf-8').read()
    base = os.path.dirname(page)
    relative = [os.path.normpath(os.path.join(base, a or b)) for a, b in ASSET_REF.findall(text)
                if not (a or b).startswith(('http', 'data:', 'mailto:', 'tel:'))]
    absolute = SITE_URL.findall(text)  # og:image etc. point at https://tagada.be/<path>
    for path in relative + absolute:
        if not path.endswith('.html') and os.path.isfile(os.path.join(ROOT, path)):
            yield path


def main(out):
    os.chdir(ROOT)
    pages = sorted(glob.glob('*.html') + glob.glob('en/*.html') + glob.glob('nl/*.html'))
    files = set(pages) | {f for f in SERVER_FILES if os.path.isfile(f)}
    for page in pages:
        files.update(referenced_assets(page))
    shutil.rmtree(out, ignore_errors=True)
    for f in sorted(files):
        dst = os.path.join(out, f)
        os.makedirs(os.path.dirname(dst), exist_ok=True)
        shutil.copy2(f, dst)
    size = sum(os.path.getsize(f) for f in files)
    print(f'{len(files)} fichiers, {size / 1e6:.1f} Mo -> {out}')


if __name__ == '__main__':
    main(sys.argv[1] if len(sys.argv) > 1 else '_mise-en-ligne/tagada.be')

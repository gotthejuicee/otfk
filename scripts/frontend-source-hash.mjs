import { createHash } from 'node:crypto';
import { readFileSync, writeFileSync, existsSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const hashFile = resolve(root, 'public/build/.source-hash');

const inputs = [
    'resources/css/app.css',
    'resources/js/app.js',
    'package.json',
    'package-lock.json',
    'vite.config.js',
];

function computeHash() {
    const hash = createHash('sha256');

    for (const relative of inputs) {
        const path = resolve(root, relative);
        hash.update(relative);
        hash.update(readFileSync(path));
    }

    return hash.digest('hex');
}

const mode = process.argv.includes('--check') ? 'check' : 'write';
const current = computeHash();

if (mode === 'check') {
    if (! existsSync(hashFile)) {
        console.error('Missing public/build/.source-hash — run npm run build locally and commit.');
        process.exit(1);
    }

    const stored = readFileSync(hashFile, 'utf8').trim();

    if (stored !== current) {
        console.error('Frontend sources changed but public/build/.source-hash is stale.');
        console.error('Run: npm run build');
        console.error(`Expected: ${current}`);
        console.error(`Stored:   ${stored}`);
        process.exit(1);
    }

    console.log('Frontend build fingerprint matches sources.');
} else {
    writeFileSync(hashFile, `${current}\n`, 'utf8');
    console.log(`Wrote public/build/.source-hash (${current})`);
}
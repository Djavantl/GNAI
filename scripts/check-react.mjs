import { existsSync, readdirSync, readFileSync } from 'node:fs';
import { dirname, extname, relative, resolve } from 'node:path';

const root = resolve('resources/react');
const errors = [];
const files = [];
function collect(directory) {
    for (const entry of readdirSync(directory, { withFileTypes: true })) {
        const path = resolve(directory, entry.name);
        if (entry.isDirectory()) collect(path);
        else if (/\.(tsx?|css)$/.test(entry.name)) files.push(path);
    }
}
collect(root);

for (const path of files) {
    const source = readFileSync(path, 'utf8');
    const name = relative(root, path);
    const layer = name.split('/')[0];
    const report = (message) => errors.push(`${name}: ${message}`);
    if (extname(path) !== '.css') {
        for (const [, specifier] of source.matchAll(/^import\s+(?:[^'";]*?\sfrom\s*)?['"]([^'"]+)['"]/gm)) {
            if (!specifier.startsWith('@/') && !specifier.startsWith('.')) continue;
            const target = specifier.startsWith('@/')
                ? resolve(root, specifier.slice(2)) : resolve(dirname(path), specifier);
            if (!['', '.ts', '.tsx', '.css', '/index.ts', '/index.tsx'].some((suffix) => existsSync(target + suffix))) {
                report(`Import não encontrado: ${specifier}`);
            }
            const targetLayer = relative(root, target).split('/')[0];
            const forbidden = {
                components: ['pages', 'app'],
                hooks: ['components', 'pages', 'app'],
                utils: ['components', 'pages', 'app', 'hooks'],
                types: ['components', 'pages', 'app', 'hooks', 'utils'],
            };
            if (forbidden[layer]?.includes(targetLayer)) report(`Dependência entre camadas: ${specifier}`);
        }
    }
    for (const [, url] of source.matchAll(/["'`]((?:\/assets\/react\/)[^"'`\s)]+)/g)) {
        if (url.includes('${')) continue;
        const asset = url.split(/[?#]/)[0];
        if (!existsSync(resolve('public', asset.slice(1)))) report(`Asset não encontrado: ${asset}`);
    }
    if (/\/assets\/gnai\/|theme-reference|ubold-bootstrap|--bold-/.test(source)) {
        report('Referência ao caminho anterior da migração.');
    }
}

if (errors.length) {
    console.error(errors.join('\n'));
    process.exitCode = 1;
} else {
    console.log(`React: ${files.length} arquivos validados (imports, camadas e assets locais).`);
}

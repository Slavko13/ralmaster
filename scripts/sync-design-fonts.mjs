import { copyFile, mkdir } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const targetRoot = resolve(
  projectRoot,
  'local/templates/aspro-allcorp2/fonts/ralmaster',
);

const families = [
  {
    packageName: 'onest',
    files: [
      'onest-cyrillic-wght-normal.woff2',
      'onest-latin-wght-normal.woff2',
    ],
    license: 'LICENSE-Onest.txt',
  },
  {
    packageName: 'inter-tight',
    files: [
      'inter-tight-cyrillic-wght-normal.woff2',
      'inter-tight-latin-wght-normal.woff2',
    ],
    license: 'LICENSE-Inter-Tight.txt',
  },
  {
    packageName: 'manrope',
    files: [
      'manrope-cyrillic-wght-normal.woff2',
      'manrope-latin-wght-normal.woff2',
    ],
    license: 'LICENSE-Manrope.txt',
  },
];

await mkdir(targetRoot, { recursive: true });

for (const family of families) {
  const packageRoot = resolve(
    projectRoot,
    'node_modules/@fontsource-variable',
    family.packageName,
  );

  for (const file of family.files) {
    await copyFile(resolve(packageRoot, 'files', file), resolve(targetRoot, file));
  }

  await copyFile(
    resolve(packageRoot, 'LICENSE'),
    resolve(targetRoot, family.license),
  );
}

console.log(`Synced RALMASTER design fonts to ${targetRoot}`);

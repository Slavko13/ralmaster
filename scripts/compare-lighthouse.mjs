import { mkdir, readFile, writeFile } from 'node:fs/promises';
import { resolve } from 'node:path';

const beforePath = resolve(process.env.LIGHTHOUSE_BEFORE ?? 'artifacts/lighthouse/before/summary.json');
const afterPath = resolve(process.env.LIGHTHOUSE_AFTER ?? 'artifacts/lighthouse/after/summary.json');
const outputDir = resolve('artifacts/lighthouse/comparison');
const before = JSON.parse(await readFile(beforePath, 'utf8'));
const after = JSON.parse(await readFile(afterPath, 'utf8'));

const beforeIndex = new Map(before.results.map((result) => [`${result.profile}:${result.route}`, result]));
const rows = after.results
  .map((result) => {
    const baseline = beforeIndex.get(`${result.profile}:${result.route}`);
    if (!baseline) return null;
    const delta = (name) => result.median.scores[name] - baseline.median.scores[name];
    const metricDelta = (name) => result.median.metrics[name] - baseline.median.metrics[name];
    return {
      route: result.route,
      profile: result.profile,
      before: baseline.median,
      after: result.median,
      delta: {
        performance: delta('performance'),
        accessibility: delta('accessibility'),
        bestPractices: delta('bestPractices'),
        seo: delta('seo'),
        fcpMs: metricDelta('fcpMs'),
        lcpMs: metricDelta('lcpMs'),
        speedIndexMs: metricDelta('speedIndexMs'),
        tbtMs: metricDelta('tbtMs'),
        cls: metricDelta('cls'),
      },
    };
  })
  .filter(Boolean);

await mkdir(outputDir, { recursive: true });
await writeFile(resolve(outputDir, 'summary.json'), `${JSON.stringify({ before: beforePath, after: afterPath, rows }, null, 2)}\n`);
const markdownRows = rows.map((row) => `| ${row.route} | ${row.profile} | ${row.before.scores.performance} | ${row.after.scores.performance} | ${row.delta.performance >= 0 ? '+' : ''}${row.delta.performance} | ${row.after.scores.accessibility} | ${row.after.scores.bestPractices} | ${row.after.scores.seo} | ${row.delta.lcpMs >= 0 ? '+' : ''}${row.delta.lcpMs} |`);
await writeFile(resolve(outputDir, 'summary.md'), [
  '# Lighthouse comparison',
  '',
  '| Route | Device | Perf before | Perf after | Perf delta | A11y after | BP after | SEO after | LCP delta ms |',
  '| --- | --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: |',
  ...markdownRows,
  '',
].join('\n'));
console.log(`Compared ${rows.length} route/profile pairs. Output: ${outputDir}`);

import { readFile } from 'node:fs/promises';
import { resolve } from 'node:path';

const summaryPath = resolve(process.env.LIGHTHOUSE_SUMMARY ?? 'artifacts/lighthouse/after/summary.json');
const summary = JSON.parse(await readFile(summaryPath, 'utf8'));
const failures = [];

for (const result of summary.results) {
  const minimumPerformance = result.profile === 'mobile' ? 90 : 95;
  const thresholds = {
    performance: minimumPerformance,
    accessibility: 95,
    bestPractices: 95,
    seo: 95,
  };
  for (const [category, threshold] of Object.entries(thresholds)) {
    const score = result.median.scores[category];
    if (!Number.isFinite(score)) {
      failures.push(`${result.profile}/${result.route}: ${category} has no numeric score`);
    } else if (score < threshold) {
      failures.push(`${result.profile}/${result.route}: ${category} ${score} < ${threshold}`);
    }
  }
}

if (failures.length) {
  console.error(`Lighthouse quality gate failed:\n${failures.map((failure) => `- ${failure}`).join('\n')}`);
  process.exit(1);
}
console.log(`Lighthouse quality gate passed for ${summary.results.length} route/profile pairs.`);

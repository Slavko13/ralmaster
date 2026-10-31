import { spawn } from 'node:child_process';
import { access, mkdir, readFile, writeFile } from 'node:fs/promises';
import { resolve } from 'node:path';

const routeCatalog = [
  { name: 'home', path: '/' },
  { name: 'products', path: '/product/' },
  { name: 'product-detail', path: '/product/kapelnik/' },
  { name: 'metalworking', path: '/services/' },
  { name: 'powder', path: '/painting/' },
  { name: 'laser-cutting', path: '/services/laser-cutting/' },
  { name: 'partners', path: '/company/partners/' },
  { name: 'contacts', path: '/contacts/' },
  { name: 'projects', path: '/projects/' },
  { name: 'project-detail', path: '/projects/zhk-simvol/' },
  { name: 'news', path: '/info/news/' },
];

const args = Object.fromEntries(
  process.argv.slice(2).map((argument) => {
    const normalized = argument.replace(/^--/, '');
    const separator = normalized.indexOf('=');
    return separator === -1
      ? [normalized, true]
      : [normalized.slice(0, separator), normalized.slice(separator + 1)];
  }),
);

const baseUrl = String(args['base-url'] ?? process.env.LIGHTHOUSE_BASE_URL ?? 'https://localhost:8443').replace(/\/$/, '');
const phase = String(args.phase ?? process.env.LIGHTHOUSE_PHASE ?? 'before');
const runCount = Number(args.runs ?? process.env.LIGHTHOUSE_RUNS ?? 3);
const profileArg = String(args.profile ?? process.env.LIGHTHOUSE_PROFILE ?? 'all');
const profiles = profileArg === 'all' ? ['desktop', 'mobile'] : [profileArg];
const requestedRoutes = String(args.routes ?? process.env.LIGHTHOUSE_ROUTES ?? '')
  .split(',')
  .map((value) => value.trim())
  .filter(Boolean);
const routes = requestedRoutes.length
  ? routeCatalog.filter((route) => requestedRoutes.includes(route.name))
  : routeCatalog;

if (!Number.isInteger(runCount) || runCount < 1) {
  throw new Error(`Invalid --runs value: ${runCount}`);
}
if (profiles.some((profile) => !['desktop', 'mobile'].includes(profile))) {
  throw new Error(`Invalid --profile value: ${profileArg}`);
}
if (!routes.length) {
  throw new Error(`No routes matched: ${requestedRoutes.join(', ')}`);
}

const chromePath = process.env.CHROME_PATH ?? 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
await access(chromePath);

const lighthouseCli = resolve('node_modules/lighthouse/cli/index.js');
const outputRoot = resolve('artifacts/lighthouse', phase);
await mkdir(outputRoot, { recursive: true });

function runCommand(command, commandArgs, env) {
  return new Promise((resolvePromise, rejectPromise) => {
    const child = spawn(command, commandArgs, { env, stdio: 'inherit', shell: false });
    child.once('error', rejectPromise);
    child.once('exit', (code) => {
      if (code === 0) resolvePromise();
      else rejectPromise(new Error(`Lighthouse exited with code ${code}`));
    });
  });
}

function median(values) {
  const sorted = values.filter(Number.isFinite).sort((left, right) => left - right);
  if (!sorted.length) return null;
  const middle = Math.floor(sorted.length / 2);
  return sorted.length % 2 ? sorted[middle] : (sorted[middle - 1] + sorted[middle]) / 2;
}

function round(value, digits = 0) {
  if (!Number.isFinite(value)) return null;
  const factor = 10 ** digits;
  return Math.round(value * factor) / factor;
}

function extractRun(lhr, reportBase, run) {
  const score = (category) => {
    const value = lhr.categories[category]?.score;
    return value == null ? null : round(value * 100);
  };
  const metric = (audit) => lhr.audits[audit]?.numericValue ?? null;
  return {
    run,
    finalUrl: lhr.finalDisplayedUrl ?? lhr.finalUrl,
    fetchTime: lhr.fetchTime,
    lighthouseVersion: lhr.lighthouseVersion,
    userAgent: lhr.userAgent,
    scores: {
      performance: score('performance'),
      accessibility: score('accessibility'),
      bestPractices: score('best-practices'),
      seo: score('seo'),
    },
    metrics: {
      fcpMs: round(metric('first-contentful-paint')),
      lcpMs: round(metric('largest-contentful-paint')),
      speedIndexMs: round(metric('speed-index')),
      tbtMs: round(metric('total-blocking-time')),
      cls: round(metric('cumulative-layout-shift'), 4),
    },
    reports: {
      json: `${reportBase}.report.json`,
      html: `${reportBase}.report.html`,
    },
  };
}

function summarizeRuns(runs) {
  const scoreNames = ['performance', 'accessibility', 'bestPractices', 'seo'];
  const metricNames = ['fcpMs', 'lcpMs', 'speedIndexMs', 'tbtMs', 'cls'];
  return {
    scores: Object.fromEntries(scoreNames.map((name) => [name, round(median(runs.map((run) => run.scores[name])))])),
    metrics: Object.fromEntries(metricNames.map((name) => [name, round(median(runs.map((run) => run.metrics[name])), name === 'cls' ? 4 : 0)])),
  };
}

const results = [];
for (const profile of profiles) {
  for (const route of routes) {
    const routeDir = resolve(outputRoot, profile, route.name);
    await mkdir(routeDir, { recursive: true });
    const runs = [];

    for (let run = 1; run <= runCount; run += 1) {
      const runLabel = `run-${String(run).padStart(2, '0')}`;
      const reportBase = resolve(routeDir, runLabel);
      const url = `${baseUrl}${route.path}`;
      const desktopUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36';
      const mobileUserAgent = 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36';
      const profileFlags = profile === 'desktop'
        ? ['--preset=desktop', `--emulatedUserAgent=${desktopUserAgent}`]
        : ['--form-factor=mobile', `--emulatedUserAgent=${mobileUserAgent}`];
      const cliArgs = [
        lighthouseCli,
        url,
        ...profileFlags,
        '--only-categories=performance,accessibility,best-practices,seo',
        // Chrome 156 currently loses the main-document body for this one audit.
        // The response still carries `Content-Type: text/html; charset=UTF-8`;
        // excluding only the errored audit keeps the remaining category measurable.
        '--skip-audits=charset',
        '--throttling-method=simulate',
        '--output=json',
        '--output=html',
        `--output-path=${reportBase}`,
        '--locale=ru',
        '--max-wait-for-load=60000',
        '--disable-full-page-screenshot',
        '--quiet',
        '--chrome-flags=--headless=new --disable-gpu --no-first-run --no-default-browser-check --ignore-certificate-errors',
      ];

      process.stdout.write(`Lighthouse ${phase} ${profile} ${route.name} ${runLabel}/${runCount}\n`);
      await runCommand(process.execPath, cliArgs, { ...process.env, CHROME_PATH: chromePath });
      const reportPath = `${reportBase}.report.json`;
      const lhr = JSON.parse(await readFile(reportPath, 'utf8'));
      runs.push(extractRun(lhr, reportBase, run));
    }

    const result = {
      route: route.name,
      path: route.path,
      profile,
      runs,
      median: summarizeRuns(runs),
    };
    results.push(result);
    console.table([{ route: route.name, profile, ...result.median.scores, ...result.median.metrics }]);
  }
}

const summary = {
  phase,
  baseUrl,
  generatedAt: new Date().toISOString(),
  runCount,
  profiles,
  routes: routes.map(({ name, path }) => ({ name, path })),
  excludedAudits: ['charset'],
  results,
};
await writeFile(resolve(outputRoot, 'summary.json'), `${JSON.stringify(summary, null, 2)}\n`, 'utf8');

const tableRows = results.map((result) => {
  const { scores, metrics } = result.median;
  return `| ${result.route} | ${result.profile} | ${scores.performance} | ${scores.accessibility} | ${scores.bestPractices} | ${scores.seo} | ${metrics.fcpMs} | ${metrics.lcpMs} | ${metrics.speedIndexMs} | ${metrics.tbtMs} | ${metrics.cls} |`;
});
const markdown = [
  `# Lighthouse ${phase}`,
  '',
  `Base URL: \`${baseUrl}\`. Runs per route/profile: ${runCount}.`,
  '',
  '| Route | Device | Performance | Accessibility | Best Practices | SEO | FCP ms | LCP ms | Speed Index ms | TBT ms | CLS |',
  '| --- | --- | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: | ---: |',
  ...tableRows,
  '',
].join('\n');
await writeFile(resolve(outputRoot, 'summary.md'), markdown, 'utf8');

console.log(`Summary: ${resolve(outputRoot, 'summary.json')}`);

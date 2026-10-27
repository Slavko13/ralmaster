import { spawnSync } from 'node:child_process';
import { createRequire } from 'node:module';

const require = createRequire(import.meta.url);
const playwrightCli = require.resolve('@playwright/test/cli');
const baselineURL = process.env.BASELINE_URL ?? 'https://ral-master.ru';

const result = spawnSync(
  process.execPath,
  [playwrightCli, 'test'],
  {
    cwd: process.cwd(),
    env: {
      ...process.env,
      BASE_URL: baselineURL,
      TEST_TARGET: 'baseline',
    },
    stdio: 'inherit',
  },
);

process.exit(result.status ?? 1);

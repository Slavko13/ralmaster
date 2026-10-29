import { defineConfig, devices } from '@playwright/test';

// Docker Compose exposes this project on 8084 by default. Keeping the local
// fallback aligned with Compose prevents a test run from silently exercising
// an unrelated service that happens to own port 8080.
const baseURL = process.env.BASE_URL ?? 'http://127.0.0.1:8084';

export default defineConfig({
  testDir: './tests/e2e',
  outputDir: './test-results',
  fullyParallel: false,
  forbidOnly: Boolean(process.env.CI),
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 2 : 1,
  timeout: 40_000,
  expect: {
    timeout: 8_000,
  },
  reporter: [
    ['list'],
    ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ['json', { outputFile: 'test-results/results.json' }],
  ],
  use: {
    baseURL,
    locale: 'ru-RU',
    timezoneId: 'Europe/Moscow',
    actionTimeout: 10_000,
    navigationTimeout: 25_000,
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
    video: 'retain-on-failure',
  },
  projects: [
    {
      name: 'desktop-chromium',
      grepInvert: /@mobile/,
      use: {
        ...devices['Desktop Chrome'],
        viewport: { width: 1440, height: 1100 },
      },
    },
    {
      name: 'mobile-chromium',
      grep: /@mobile/,
      use: {
        ...devices['iPhone 13'],
        browserName: 'chromium',
        viewport: { width: 390, height: 844 },
      },
    },
  ],
});

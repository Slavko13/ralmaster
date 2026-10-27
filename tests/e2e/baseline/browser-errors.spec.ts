import { expect, test, type ConsoleMessage } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const auditedRoutes = [
  '/',
  '/product/',
  '/services/metal-bending/',
  '/contacts/',
] as const;

type BrowserDiagnostic = {
  kind: 'console.error' | 'pageerror';
  message: string;
  location?: ReturnType<ConsoleMessage['location']>;
  ignoredReason?: string;
};

const knownNoise = [
  {
    // Browser extensions and privacy blockers can cancel analytics requests. This
    // is external to the application and does not represent an uncaught app error.
    pattern: /(?:chrome|moz|edge)-extension:\/\/|net::ERR_BLOCKED_BY_CLIENT/i,
    reason: 'request cancelled by a browser extension or privacy blocker',
  },
] as const;

function ignoredReason(message: string): string | undefined {
  return knownNoise.find(({ pattern }) => pattern.test(message))?.reason;
}

test.describe('P0 browser error baseline', () => {
  for (const path of auditedRoutes) {
    test(`${path} has no unexplained console or page errors`, async ({ page }, testInfo) => {
      const diagnostics: BrowserDiagnostic[] = [];

      page.on('console', (message) => {
        if (message.type() !== 'error') return;

        diagnostics.push({
          kind: 'console.error',
          message: message.text(),
          location: message.location(),
          ignoredReason: ignoredReason(message.text()),
        });
      });

      page.on('pageerror', (error) => {
        diagnostics.push({
          kind: 'pageerror',
          message: error.stack ?? error.message,
          // Uncaught page errors are never suppressed: they represent application
          // runtime failures even if their wording resembles network noise.
        });
      });

      await page.goto(path, { waitUntil: 'domcontentloaded' });
      await expectHealthyDocument(page);
      await page.waitForTimeout(2_000);

      const ignored = diagnostics.filter(
        (diagnostic) => diagnostic.kind === 'console.error' && diagnostic.ignoredReason,
      );
      const unexpected = diagnostics.filter(
        (diagnostic) => diagnostic.kind === 'pageerror' || !diagnostic.ignoredReason,
      );

      await testInfo.attach('browser-diagnostics', {
        body: JSON.stringify({ path, unexpected, ignored, all: diagnostics }, null, 2),
        contentType: 'application/json',
      });

      expect(unexpected, `Unexpected browser errors on ${path}`).toEqual([]);
    });
  }
});

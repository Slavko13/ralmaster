import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';
import { expectHealthyDocument } from '../helpers/site';

const auditedRoutes = ['/', '/services/metal-bending/', '/contacts/'] as const;

test.describe('P1 automated accessibility baseline', () => {
  for (const path of auditedRoutes) {
    test(`${path} has no critical axe violations`, async ({ page }, testInfo) => {
      await page.goto(path, { waitUntil: 'domcontentloaded' });
      await expectHealthyDocument(page);

      const results = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
        .analyze();

      await testInfo.attach('axe-results', {
        body: JSON.stringify(results.violations, null, 2),
        contentType: 'application/json',
      });

      const criticalViolations = results.violations.filter(
        (violation) => violation.impact === 'critical',
      );
      expect(
        criticalViolations,
        `Critical accessibility violations on ${path}`,
      ).toEqual([]);
    });
  }
});

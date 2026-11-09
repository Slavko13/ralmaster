import { expect, test } from '@playwright/test';

test.describe('security regression checks', () => {
  test('public pages emit baseline browser security headers', async ({ request }) => {
    const response = await request.get('/', { timeout: 30_000 });

    expect(response.status()).toBeLessThan(400);
    expect(response.headers()['x-content-type-options']).toBe('nosniff');
    expect(response.headers()['x-frame-options']).toBe('SAMEORIGIN');
    expect(response.headers()['referrer-policy']).toBe('strict-origin-when-cross-origin');
    expect(response.headers()['content-security-policy']).toContain("frame-ancestors 'self'");
    expect(response.headers()['permissions-policy']).toContain('camera=()');
    expect(response.headers()['x-powered-by']).toBeUndefined();
    expect(response.headers()['x-powered-cms']).toBeUndefined();
  });

  for (const path of [
    '/.env',
    '/.git/config',
    '/.htaccess_back',
    '/Dockerfile',
    '/compose.production.yml',
    '/debug.log',
    '/ralmaster-pre-estimate-form.sql',
  ]) {
    test(`sensitive artifact is not public: ${path}`, async ({ request }) => {
      const response = await request.get(path, { maxRedirects: 0 });
      expect([403, 404]).toContain(response.status());
    });
  }

  test('CAPTCHA answer oracle is disabled', async ({ request }) => {
    const response = await request.post('/ajax/check-captcha.php', {
      form: { captcha_sid: '0'.repeat(32), captcha_word: 'AAAAA' },
    });

    expect(response.status()).toBe(410);
    expect(await response.text()).toBe('false');
  });

  test('CAPTCHA refresh rejects GET and missing CSRF token', async ({ request }) => {
    const getResponse = await request.get('/ajax/captcha.php');
    const postResponse = await request.post('/ajax/captcha.php', {
      form: { captcha_sid: '0'.repeat(32) },
    });

    expect(getResponse.status()).toBe(403);
    expect(postResponse.status()).toBe(403);
  });

  test('admin option save rejects missing CSRF token', async ({ request }) => {
    const response = await request.post('/ajax/options_save.php', {
      form: { SAVE_OPTIONS: 'Y' },
    });

    expect(response.status()).toBe(403);
    expect((await response.json()).STATUS).toBe('ERROR');
  });

  test('catalog AJAX rejects a missing CSRF token', async ({ request }) => {
    const response = await request.post('/include/mainpage/comp_catalog_ajax.php', {
      form: {
        AJAX_POST: 'Y',
        AJAX_PARAMS: 'a:0:{}',
        GLOBAL_FILTER: 'a:0:{}',
      },
    });

    expect(response.status()).toBe(403);
  });

  test('basket mutation rejects missing CSRF token', async ({ request }) => {
    const response = await request.post('/include/footer/site-basket.php', {
      form: {
        itemData: JSON.stringify({ ID: 1 }),
        quantity: '1',
      },
    });

    expect(response.status()).toBe(403);
  });

  test('regional autocomplete cannot reflect an arbitrary external URL', async ({ request }) => {
    const response = await request.get('/ajax/city_select.php', {
      params: { term: 'мо', url: '//example.invalid/security-test' },
    });

    expect(response.status()).toBeLessThan(500);
    expect(await response.text()).not.toContain('example.invalid');
  });
});


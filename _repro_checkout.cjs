const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  page.setDefaultTimeout(20000);
  page.setDefaultNavigationTimeout(20000);

  // Block flaky/slow external CDNs so they can't block domcontentloaded,
  // while letting same-origin (ferrecom.test) requests through untouched.
  await page.route('**/*', (route) => {
    const url = route.request().url();
    if (!url.includes('ferrecom.test')) {
      return route.abort();
    }
    return route.continue();
  });

  const consoleMsgs = [];
  page.on('console', msg => consoleMsgs.push(`[console:${msg.type()}] ${msg.text()}`));
  page.on('pageerror', err => consoleMsgs.push(`[pageerror] ${err.message}`));

  const navigations = [];
  page.on('framenavigated', frame => {
    if (frame === page.mainFrame()) {
      navigations.push({ url: frame.url(), t: Date.now() });
    }
  });

  // 1. Login
  await page.goto('http://ferrecom.test/login', { waitUntil: 'domcontentloaded' });
  await page.fill('input[name="email"]', 'cliente@ferremax.com');
  await page.fill('input[name="password"]', 'password123');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.click('button[type="submit"]'),
  ]);
  console.log('POST LOGIN URL:', page.url());

  // 2. Seed cart in localStorage
  await page.evaluate(() => {
    const cart = [{
      id: 1, nombre: 'Taladro Percutor Bosch 1/2"', precio: 95.00, qty: 1,
      stock: 23, imagen: '', cantidad_por_mayor: null, descuento_por_mayor: null
    }];
    localStorage.setItem('ferremax_cart', JSON.stringify(cart));
  });

  // 3. Navigate to checkout and observe
  navigations.length = 0;
  await page.goto('http://ferrecom.test/checkout', { waitUntil: 'domcontentloaded' });
  console.log('CHECKOUT LOAD URL:', page.url());

  try {
    await page.fill('input[name="ruc_cedula"]', '0999999999', { timeout: 5000 });
    console.log('TYPE TEST: succeeded immediately');
  } catch (e) {
    console.log('TYPE TEST FAILED immediately:', e.message);
  }

  await page.waitForTimeout(6000);

  console.log('--- Navigations after checkout load (' + navigations.length + ') ---');
  navigations.forEach(n => console.log(n.t, n.url));

  try {
    const val = await page.inputValue('input[name="ruc_cedula"]');
    console.log('FIELD VALUE AFTER WAIT:', JSON.stringify(val));
  } catch (e) {
    console.log('FIELD READ FAILED:', e.message);
  }

  console.log('--- Console/page messages ---');
  consoleMsgs.forEach(m => console.log(m));

  await page.screenshot({ path: 'C:/Users/ASUS/AppData/Local/Temp/claude/c--laragon-www-FerrEcom/d19b81b7-b2b6-41a6-b0a8-b70af65a3fb8/scratchpad/checkout_screenshot.png', fullPage: true });

  await browser.close();
})().catch(e => { console.error('SCRIPT ERROR', e); process.exit(1); });

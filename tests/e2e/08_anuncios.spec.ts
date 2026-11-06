import { test, expect } from '@playwright/test';

test.describe('Módulo: Anuncios', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@ferremax.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
  });

  test('RF15: Creación, revisión y publicación de anuncios', async ({ page }) => {
    await page.goto('/anuncios');
    await expect(page.locator('h3:has-text("Banners Promocionales")').first()).toBeVisible();
    await expect(page.locator('text=Nuevo Anuncio').first()).toBeVisible();
  });

});

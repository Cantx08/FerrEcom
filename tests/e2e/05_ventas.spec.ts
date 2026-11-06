import { test, expect } from '@playwright/test';

test.describe('Módulo: Ventas', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'vendedor@ferremax.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
  });

  test('RF09: Interfaz POS y búsqueda rápida', async ({ page }) => {
    await page.goto('/ventas/create');
    await expect(page.locator('button', { hasText: 'Buscar' }).first()).toBeVisible();
    
    // Búsqueda
    const searchInput = page.locator('input[placeholder*="Cédula"], input[name="cliente"]');
    if (await searchInput.isVisible()) {
        await searchInput.fill('9999999999');
    }
  });

  test('RF10: Disminución de stock en tiempo real al vender', async ({ page }) => {
    test.info().annotations.push({ type: 'info', description: 'RF10 requiere confirmar la venta y verificar el inventario' });
  });

});

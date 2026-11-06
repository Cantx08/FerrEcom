import { test, expect } from '@playwright/test';

test.describe('Módulo: Reabastecimiento', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'bodeguero@ferremax.com'); // O admin
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
  });

  test('RF07: Ciclo de vida del pedido de compra', async ({ page }) => {
    await page.goto('/pedidos'); // o /reabastecimiento
    await expect(page.locator('h3:has-text("Pedidos Emitidos")').first()).toBeVisible();
    
    // Debería poder verse el flujo de estados
  });

  test('RF08: Actualización de stock al Completar pedido', async ({ page }) => {
    test.info().annotations.push({ type: 'info', description: 'RF08 requiere flujo completo de creación y marcado a Completado' });
  });

});

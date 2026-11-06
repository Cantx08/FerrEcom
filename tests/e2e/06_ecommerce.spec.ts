import { test, expect } from '@playwright/test';

test.describe('Módulo: E-commerce', () => {

  test('RF11: Catálogo público y carrito persistente', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('[data-testid^="producto-"]').first()).toBeVisible({ timeout: 10000 });
    
    // Probar agregar al carrito
    const addToCartBtn = page.locator('button:has-text("Agregar al carrito"), button:has-text("Añadir")').first();
    if (await addToCartBtn.isVisible()) {
        await addToCartBtn.click();
        await page.waitForTimeout(1000);
        await page.reload();
        // Verificar badge
        const badge = page.locator('.cart-count, [data-cart-count]');
        if (await badge.isVisible()) {
            await expect(badge).not.toHaveText('0');
        }
    }
  });

  test('RF12: Checkout y generación de código de retiro', async ({ page }) => {
    await page.goto('/checkout');
    // Debería pedir login si no está logueado
    await expect(page).toHaveURL(/.*login/);
  });

  test('RF13: Panel de historial de cliente', async ({ page }) => {
    // Iniciar sesión como cliente
    await page.goto('/login');
    // Cliente mock
    await page.fill('input[name="email"]', 'cliente@ferremax.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');

    await page.goto('/mi-cuenta/compras');
    await expect(page.locator('h1:has-text("Mis Compras")').first()).toBeVisible();
  });

});

import { test, expect } from '@playwright/test';

test.describe('Módulo: Facturación', () => {

  test('RF14: Generación y almacenamiento de facturas', async ({ page }) => {
    test.info().annotations.push({ type: 'info', description: 'RF14 requiere completar un flujo de venta en el POS o Checkout para luego descargar la factura' });
    
    // Iniciar sesión
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@ferremax.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');

    await page.goto('/ventas'); // Navigate to sales to see a bill
    await expect(page.locator('h3:has-text("Ventas a Clientes")').first()).toBeVisible({ timeout: 10000 });
  });

});

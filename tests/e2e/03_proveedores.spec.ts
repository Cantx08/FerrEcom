import { test, expect } from '@playwright/test';

test.describe('Módulo: Proveedores', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@ferremax.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
  });

  test('RF06: Registrar proveedores y catálogo externo', async ({ page }) => {
    await page.goto('/proveedores'); // Ruta hipotética
    
    // Verificar que existe la tabla de proveedores
    await expect(page.locator('h3:has-text("Proveedores")').first()).toBeVisible();
    
    // Botón para crear proveedor
    await expect(page.locator('a, button').filter({ hasText: 'Nuevo Proveedor' }).first()).toBeVisible();
  });

});

import { test, expect } from '@playwright/test';

test.describe('Módulo: Panel Administrativo', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@ferremax.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
  });

  test('RF16: Reportes estadísticos y gráficos (Dashboard)', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Verificar que los widgets o gráficos se renderizan
    await page.waitForLoadState('networkidle');
    await expect(page.locator('#cashflowChart')).toBeVisible({ timeout: 10000 });
  });

  test('RF17: Exportar registros a PDF y Excel', async ({ page }) => {
    await page.goto('/dashboard');
    
    // Validar existencia de botones de exportación
    await expect(page.locator('a, button').filter({ hasText: 'Exportar Excel' }).first()).toBeVisible({ timeout: 10000 });
  });

});

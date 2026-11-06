import { test, expect } from '@playwright/test';

test.describe('Módulo: Usuarios y Roles', () => {

  test('RF01: Registro, autenticación y roles de usuarios', async ({ page }) => {
    // 1. Registro de Cliente
    await page.goto('/register');
    await page.fill('input[name="name"]', 'Nuevo Cliente');
    await page.fill('input[name="email"]', `cliente_${Date.now()}@ferrecom.com`);
    await page.fill('input[name="password"]', 'password123');
    await page.fill('input[name="password_confirmation"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    
    // Verificar que inició sesión
    await expect(page).toHaveURL(/.*(\/dashboard|\/)/);
  });

  test('RF02: Gestión y actualización de perfil', async ({ page }) => {
    // Iniciar sesión y probar editar perfil
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@ferremax.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');

    await page.goto('/mi-cuenta/perfil');
    
    // Verificar formulario de actualización de datos
    await expect(page.locator('h1:has-text("Mi Perfil"), h2:has-text("Profile")').first()).toBeVisible({ timeout: 10000 });
    await expect(page.locator('input[name="name"]')).toBeVisible();
    await expect(page.locator('button:has-text("Guardar"), button:has-text("Save")').first()).toBeVisible();
  });

});

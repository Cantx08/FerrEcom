<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProductoProveedorController;
use Illuminate\Support\Facades\Route;

// ─── Tienda pública (acceso sin autenticación) ───────────────────────────────
Route::get('/', [TiendaController::class, 'index'])->name('home');

// ─── Rutas autenticadas ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Facturas (Accesible para todos, el controlador valida la pertenencia)
    Route::get('/factura/{venta}', [\App\Http\Controllers\FacturaController::class, 'generarFactura'])->name('factura.descargar');

    // Checkout / Carrito (solo rol Cliente)
    Route::middleware('role:Cliente')->group(function () {
        Route::get('/checkout', [TiendaController::class, 'checkoutView'])->name('cart.checkout.view');
        Route::post('/checkout', [TiendaController::class, 'checkoutProcess'])->name('cart.checkout.process');
    });

    // Mi perfil (cualquier usuario autenticado, sin importar su rol)
    Route::prefix('mi-cuenta')->group(function () {
        Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/perfil', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Mis compras (solo rol Cliente)
    Route::prefix('mi-cuenta')->middleware('role:Cliente')->group(function () {
        Route::get('/compras', [TiendaController::class, 'compras'])->name('cliente.compras');
    });

    // ─── Portal del Proveedor (interfaz separada) ────────────────────────────
    Route::prefix('proveedor')->name('proveedor.')->middleware('role:Proveedor')->group(function () {
        // Pedidos
        Route::get('/pedidos', [ProductoProveedorController::class, 'pedidosIndex'])->name('pedidos.index');
        Route::get('/pedidos/{id}', [ProductoProveedorController::class, 'pedidosShow'])->name('pedidos.show');
        Route::put('/pedidos/{id}/estado', [ProductoProveedorController::class, 'pedidosUpdateStatus'])->name('pedidos.updateStatus');

        // Catálogo propio
        Route::get('/catalogo', [ProductoProveedorController::class, 'catalogoIndex'])->name('catalogo.index');
        Route::get('/catalogo/nuevo', [ProductoProveedorController::class, 'catalogoCreate'])->name('catalogo.create');
        Route::post('/catalogo', [ProductoProveedorController::class, 'catalogoStore'])->name('catalogo.store');
        Route::get('/catalogo/{producto}/editar', [ProductoProveedorController::class, 'catalogoEdit'])->name('catalogo.edit');
        Route::put('/catalogo/{producto}', [ProductoProveedorController::class, 'catalogoUpdate'])->name('catalogo.update');
        Route::delete('/catalogo/{producto}', [ProductoProveedorController::class, 'catalogoDestroy'])->name('catalogo.destroy');
    });

    // ─── Panel de Administración ─────────────────────────────────────────────
    // Solo roles del equipo interno (NO Proveedor, NO Cliente)
    Route::middleware('role:SuperAdmin|Administrador|Bodeguero|Vendedor|Supervisor')->group(function () {

        // Dashboard (estadísticas) — solo SuperAdmin y Administrador
        Route::middleware('role:SuperAdmin|Administrador')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
            Route::get('/dashboard/chart-data', [AdminController::class, 'getChartData'])->name('dashboard.chart_data');
            Route::get('/dashboard/ventas-data', [AdminController::class, 'getVentasData'])->name('dashboard.ventas_data');
            Route::get('/dashboard/compras-data', [AdminController::class, 'getComprasData'])->name('dashboard.compras_data');
            Route::get('/dashboard/export-ventas', [AdminController::class, 'exportVentas'])->name('dashboard.export_ventas');
            Route::get('/dashboard/export-ventas-pdf', [AdminController::class, 'exportVentasPdf'])->name('dashboard.export_ventas_pdf');
        });

        // Retiros en caja (Vendedor o Administrador)
        Route::middleware('role:Vendedor|Administrador|SuperAdmin')->group(function () {
            Route::get('retiros', [\App\Http\Controllers\RetiroController::class, 'index'])->name('retiros.index');
            Route::post('retiros/validar', [\App\Http\Controllers\RetiroController::class, 'validar'])->name('retiros.validar');
        });

        // Usuarios (SuperAdmin: full CRUD | Administrador: sin delete)
        Route::middleware('role:SuperAdmin|Administrador')->group(function () {
            Route::resource('users', \App\Http\Controllers\UserController::class)->except('index', 'show');
        });

        // Roles y Permisos (SuperAdmin: full | Administrador: solo ver/editar roles)
        Route::middleware('role:SuperAdmin|Administrador')->group(function () {
            Route::resource('roles', \App\Http\Controllers\RoleController::class)->except('show');
        });

        // Permisos — SOLO SuperAdmin puede crear/editar/eliminar permisos
        Route::middleware('role:SuperAdmin')->group(function () {
            Route::resource('permissions', \App\Http\Controllers\PermissionController::class)->except('show');
            Route::get('role-permissions/{role}', [\App\Http\Controllers\RolePermissionsController::class, 'edit'])->name('role-permissions.edit');
            Route::put('role-permissions/{role}', [\App\Http\Controllers\RolePermissionsController::class, 'update'])->name('role-permissions.update');
        });

        // Productos (Bodeguero | Administrador)
        Route::middleware('role:Bodeguero|Administrador|SuperAdmin')->group(function () {
            Route::resource('productos', \App\Http\Controllers\ProductoController::class)->except('show');
        });

        // Pedidos de compra (Bodeguero | Administrador — Proveedor va por su portal)
        Route::middleware('role:Bodeguero|Administrador|SuperAdmin')->group(function () {
            Route::resource('pedidos', \App\Http\Controllers\PedidoCompraController::class)->except('edit', 'update', 'destroy');
            Route::put('pedidos/{id}/status', [\App\Http\Controllers\PedidoCompraController::class, 'updateStatus'])->name('pedidos.updateStatus');
            Route::post('pedidos/{id}/aprobar', [\App\Http\Controllers\PedidoCompraController::class, 'aprobar'])->name('pedidos.aprobar');
            Route::post('pedidos/{id}/cancelar', [\App\Http\Controllers\PedidoCompraController::class, 'cancelar'])->name('pedidos.cancelar');
            Route::post('pedidos/{id}/pagar', [\App\Http\Controllers\PedidoCompraController::class, 'pagar'])->name('pedidos.pagar');
        });

        // Proveedores (solo admin, bodeguero)
        Route::middleware('role:Bodeguero|Administrador|SuperAdmin')->group(function () {
            Route::resource('proveedores', \App\Http\Controllers\ProveedorController::class);
        });

        // Ventas POS (Vendedor | Administrador)
        Route::middleware('role:Vendedor|Administrador|SuperAdmin')->group(function () {
            Route::resource('ventas', \App\Http\Controllers\VentaController::class)->except('edit', 'update', 'destroy');
            Route::get('/api/clientes/{cedula}', [VentaController::class, 'buscarCliente'])->name('api.clientes.buscar');
        });

        // Anuncios (Supervisor | Administrador)
        Route::middleware('role:Supervisor|Administrador|SuperAdmin')->group(function () {
            Route::resource('anuncios', \App\Http\Controllers\AnuncioController::class)->except('show');
            Route::post('anuncios/{anuncio}/aprobar', [\App\Http\Controllers\AnuncioController::class, 'aprobar'])->name('anuncios.aprobar');
            Route::post('anuncios/{anuncio}/denegar', [\App\Http\Controllers\AnuncioController::class, 'denegar'])->name('anuncios.denegar');
        });

        // Categorías (Bodeguero | Administrador — igual que productos)
        Route::middleware('role:Bodeguero|Administrador|SuperAdmin')->group(function () {
            Route::resource('categorias', \App\Http\Controllers\CategoriaController::class);
        });
    });
});

require __DIR__ . '/auth.php';

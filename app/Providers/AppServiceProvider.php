<?php

namespace App\Providers;

use App\Events\PedidoEstadoCambiado;
use App\Events\VentaEstadoCambiada;
use App\Listeners\NotificarCambioPedido;
use App\Listeners\NotificarCambioVenta;
use App\Models\PedidoCompra;
use App\Models\User;
use App\Models\Venta;
use App\Policies\PedidoCompraPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\VentaPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar servicios con inyección de dependencias
        $this->app->bind(
            \App\Services\PedidoCompraService::class,
            \App\Services\PedidoCompraService::class
        );

        $this->app->bind(
            \App\Services\VentaService::class,
            \App\Services\VentaService::class
        );
    }

    public function boot(): void
    {
        // ─── Eventos y Listeners ──────────────────────────────────────────────────
        Event::listen(PedidoEstadoCambiado::class, NotificarCambioPedido::class);
        Event::listen(VentaEstadoCambiada::class, NotificarCambioVenta::class);

        // ─── Policies de Autorización ─────────────────────────────────────────────
        Gate::policy(PedidoCompra::class, PedidoCompraPolicy::class);
        Gate::policy(Venta::class, VentaPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);

        // ─── Gate para asignación de rol (usado en UserController) ────────────────
        Gate::define('asignarRol', function (User $actor, string $rolNombre) {
            $policy = new UserPolicy();
            return $policy->asignarRol($actor, $rolNombre);
        });

        // ─── Gate para creación de rol (usado en RoleController) ──────────────────
        Gate::define('crearRol', function (User $actor, string $rolNombre) {
            $policy = new RolePolicy();
            return $policy->crear($actor, $rolNombre);
        });
    }
}

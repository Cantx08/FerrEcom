# FerrEcom - Sistema de Gestión y Comercio Electrónico Ferretero

FerrEcom es una plataforma integral diseñada para la gestión completa de una ferretería. Combina un robusto sistema de punto de venta (POS) y administración de inventarios con una moderna tienda en línea (E-commerce). Permite controlar desde las compras a proveedores y niveles de stock, hasta la facturación física y las ventas por internet con retiros en tienda.

---

## 🛠️ Stack Tecnológico

El sistema está construido utilizando tecnologías modernas para asegurar escalabilidad, seguridad y una experiencia de usuario fluida:

- **Backend:** Laravel (PHP)
- **Base de Datos:** MySQL / MariaDB
- **Frontend / UI:** Blade Templates, Tailwind CSS (para diseño premium y responsive)
- **Interactividad Frontend:** Alpine.js (para carritos de compra y modales reactivos) y JavaScript Vanilla
- **Manejo de Roles:** Spatie Laravel Permission
- **Iconografía:** FontAwesome 6

---

## 🧩 Módulos del Sistema

La arquitectura de FerrEcom está dividida en los siguientes módulos principales:

### 1. Módulo de Usuarios y Roles (RBAC)
Gestiona el acceso al sistema utilizando `spatie/laravel-permission`. 
- **Roles principales:** Dueño, Administrador, Bodeguero, Vendedor, Cliente y Proveedor.
- **Perfiles (`profiles`):** Información detallada del usuario (RUC/Cédula, dirección, teléfono).

### 2. Módulo de Inventario (Catálogo Interno)
- Gestión de `productos` con control de `stock_actual` y `stock_minimo`.
- Precios de venta, costos de compra, e imágenes.
- Configuración de **ventas al por mayor** (cantidad mínima y porcentaje de descuento).
- **Alertas de Bajo Stock:** Notificaciones automáticas enviadas a los administradores/dueños/bodegueros cuando un producto baja de su stock mínimo durante una venta.

### 3. Módulo de Proveedores y Catálogos Externos
- Registro de `proveedors` con sus datos comerciales.
- **Catálogo de Proveedor (`producto_proveedors`):** Productos específicos que oferta cada proveedor con sus respectivos costos y stock disponible.

### 4. Módulo de Reabastecimiento (Pedidos de Compra)
Flujo de 5 pasos para reponer el inventario:
1. **Pendiente:** El bodeguero o dueño genera el pedido hacia un proveedor (incluso pre-llenado automáticamente desde la alerta de bajo stock).
2. **Revisado:** El proveedor revisa la solicitud y valida su inventario (puede marcar "stock incompleto" dejando observaciones).
3. **Confirmado:** El ferretero ajusta o acepta las condiciones del proveedor.
4. **Enviado:** El proveedor despacha la mercadería.
5. **Completado / No Recibido:** El bodeguero marca la recepción del pedido, lo cual alimenta el `stock_actual` automáticamente.
*Nota:* Puede ser Cancelado en las etapas iniciales.

### 5. Módulo de Ventas (Punto de Venta / POS)
- Pantalla para ventas físicas directas (`ventas` de tipo `fisico`).
- Búsqueda de clientes por Cédula/RUC y creación dinámica de facturas.
- Disminución de stock instantánea.

### 6. Módulo de E-commerce (Tienda en Línea)
- Vitrina virtual para clientes visitantes.
- **Carrito de Compras:** Persistente en `localStorage` (gestionado con Alpine.js), requiere inicio de sesión para concretar pagos.
- **Checkout:** Permite realizar pedidos en línea (`ventas` de tipo `online`), genera un `codigo_retiro` (ej. `ONL-X8A9F2`) y notifica al cliente por correo/sistema.
- Panel de "Mis Compras" exclusivo para clientes.

### 7. Módulo de Facturación
Generación automática de comprobantes (`facturas`) asociadas a cada venta física o en línea, almacenando razón social, dirección y detalles impositivos.

### 8. Módulo de Anuncios y Promociones
- Gestión de contenido publicitario y anuncios (`anuncios`).
- Tienen un flujo de aprobación basado en `estado` (pendiente, aprobado, rechazado).
- Cada anuncio está vinculado al administrador o supervisor (`supervisor_id`) que lo gestiona y es visualizado por los clientes en la tienda en línea.

### 9. Módulo de Reportes y Estadísticas (Dashboard)
- Consolida la información operativa de la ferretería en un panel administrativo.
- Presenta KPIs en tiempo real (Total de ventas, productos con bajo stock, pedidos pendientes).
- Cruza datos de los módulos de Ventas, Inventario y Reabastecimiento para ofrecer visualizaciones y métricas útiles para la toma de decisiones gerenciales.

---

## 🏛️ Arquitectura del Sistema

El proyecto sigue el patrón **MVC (Model-View-Controller)** propio de Laravel.

- **Routing:** Definición clara en `web.php` separada por grupos de middleware (`auth`, `role:Bodeguero|Administrador`, etc.).
- **Controllers:** Lógica de negocio encapsulada (ej. `PedidoCompraController`, `TiendaController`, `VentaController`).
- **Models:** Modelos Eloquent con relaciones definidas.
- **Views:** Componentes de Blade estructurados en `layouts` (`admin`, `app`) y organizados por carpetas funcionales (`admin/productos`, `tienda/`, `admin/pedidos/`).
- **Notifications:** Uso de los canales de notificación de Laravel (Base de datos y Mail) para eventos críticos (Bajo Stock, Ventas Realizadas).

```mermaid
graph TD
    Client([Cliente / Navegador Web]) -->|Petición HTTP| Routes[Routing<br>web.php]
    Routes -->|Middleware<br>auth, roles| Controller[Controllers<br>Lógica de Negocio]
    
    subgraph Arquitectura MVC Laravel
        Controller -->|Obtiene/Modifica datos| Model[Models<br>Eloquent ORM]
        Model -->|Consultas SQL| DB[(Base de Datos<br>MySQL)]
        DB -->|Resultados| Model
        Model -->|Retorna Objetos/Colecciones| Controller
        
        Controller -->|Envía datos para renderizar| View[Views<br>Blade Templates]
        View -->|Estilos e Interactividad| UI[Tailwind CSS & Alpine.js]
    end
    
    UI -->|HTML Compilado| HTML[Respuesta HTTP]
    HTML --> Client
    
    Controller -.->|Dispara eventos| Notifications[[Notificaciones<br>Sistema / Mail]]
```

---

## 📊 Diagrama de Base de Datos (ERD)

A continuación se muestra el diagrama Entidad-Relación de las tablas principales que rigen la lógica de FerrEcom:

```mermaid
erDiagram
    USERS ||--o| PROFILES : "has_one"
    USERS ||--o{ ROLES : "has_many via Spatie"
    USERS ||--o{ VENTAS : "is_vendedor_or_cliente"
    USERS ||--o{ PEDIDO_COMPRAS : "is_bodeguero"
    USERS ||--o{ ANUNCIOS : "manages (supervisor_id)"
    
    CATEGORIAS ||--o{ PRODUCTOS : "has_many"
    CATEGORIAS ||--o{ PROVEEDORS : "has_many"

    PROVEEDORS ||--o{ PRODUCTO_PROVEEDORS : "offers"
    PROVEEDORS ||--o{ PEDIDO_COMPRAS : "receives"
    USERS ||--o| PROVEEDORS : "manages (user_id)"

    PRODUCTOS {
        bigint id PK
        string sku
        string nombre
        decimal precio_venta
        decimal precio_compra
        decimal stock_actual
        decimal stock_minimo
        integer cantidad_por_mayor
        decimal descuento_por_mayor
        bigint categoria_id FK
    }

    PROVEEDORS {
        bigint id PK
        string nombre_compania
        string ruc
        bigint user_id FK
        bigint categoria_id FK
    }

    PRODUCTO_PROVEEDORS {
        bigint id PK
        string sku
        string nombre
        decimal costo_unitario
        decimal stock_disponible
        bigint proveedor_id FK
    }

    PEDIDO_COMPRAS ||--o{ DETALLE_PEDIDO_COMPRAS : "contains"
    PRODUCTO_PROVEEDORS ||--o{ DETALLE_PEDIDO_COMPRAS : "is_ordered_in"

    PEDIDO_COMPRAS {
        bigint id PK
        enum estado
        decimal total
        text observaciones_proveedor
        bigint bodeguero_id FK
        bigint proveedor_id FK
    }

    DETALLE_PEDIDO_COMPRAS {
        bigint id PK
        integer cantidad_solicitada
        decimal precio
        bigint pedido_compra_id FK
        bigint producto_catalogo_id FK
    }

    VENTAS ||--o{ DETALLE_VENTAS : "contains"
    VENTAS ||--o| FACTURAS : "generates"
    PRODUCTOS ||--o{ DETALLE_VENTAS : "sold_in"

    VENTAS {
        bigint id PK
        enum tipo "fisico / online"
        enum estado
        enum metodo_pago
        decimal total
        decimal descuento
        string codigo_retiro
        bigint vendedor_id FK
        bigint cliente_id FK
    }

    DETALLE_VENTAS {
        bigint id PK
        integer cantidad
        decimal precio
        bigint venta_id FK
        bigint producto_id FK
    }

    FACTURAS {
        bigint id PK
        string ruc_cedula
        string razon_social
        bigint venta_id FK
    }

    ANUNCIOS {
        bigint id PK
        string titulo
        text contenido
        enum estado "pendiente/aprobado/rechazado"
        bigint supervisor_id FK
    }
```

> **Nota sobre el Módulo de Reportes:** Como se observa en el diagrama, el Módulo de Reportes y Estadísticas no posee tablas físicas propias. Este funciona de manera *lógica*, realizando consultas y agregaciones dinámicas sobre las tablas transaccionales existentes (`VENTAS`, `PEDIDO_COMPRAS`, `PRODUCTOS`) para generar los tableros de control (dashboards) en tiempo real.

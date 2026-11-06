# Análisis de Requisitos - FerrEcom

A continuación se detalla el análisis completo del sistema **FerrEcom**, estructurado en requisitos funcionales, no funcionales y los requisitos técnicos mínimos requeridos, de acuerdo a la documentación y el código fuente del proyecto.

## Requisitos Funcionales

Los requisitos funcionales describen lo que el sistema debe hacer. 

| Código | Requerimiento | Prioridad | Módulo |
| :--- | :--- | :--- | :--- |
| **RF01** | El sistema debe permitir el registro, autenticación y gestión de usuarios con roles específicos (Dueño, Administrador, Bodeguero, Vendedor, Cliente, Proveedor). | Alta | Usuarios y Roles |
| **RF02** | El sistema debe permitir la gestión y actualización de perfiles de usuario (RUC/Cédula, dirección, teléfono). | Alta | Usuarios y Roles |
| **RF03** | El sistema debe permitir la administración completa de productos (creación, edición, eliminación), controlando stock actual, stock mínimo, precio de venta y costo. | Alta | Inventario |
| **RF04** | El sistema debe calcular y aplicar automáticamente descuentos preconfigurados para ventas al por mayor de acuerdo a la cantidad de productos. | Media | Inventario |
| **RF05** | El sistema debe enviar notificaciones de alerta automáticas cuando el stock de un producto alcance su nivel mínimo configurado. | Alta | Inventario |
| **RF06** | El sistema debe permitir registrar proveedores y vincularlos con un catálogo de productos externos específicos con sus respectivos costos. | Media | Proveedores |
| **RF07** | El sistema debe gestionar el ciclo de vida completo de un pedido de compra a través de sus 5 estados (Pendiente, Revisado, Confirmado, Enviado, Completado/No Recibido). | Alta | Reabastecimiento |
| **RF08** | El sistema debe actualizar e incrementar automáticamente el stock de los productos al marcar un pedido de compra como "Completado". | Alta | Reabastecimiento |
| **RF09** | El sistema debe proporcionar una interfaz de Punto de Venta (POS) para procesar ventas físicas, permitiendo la búsqueda rápida de clientes por Cédula o RUC. | Alta | Ventas |
| **RF10** | El sistema debe disminuir el stock del inventario en tiempo real al concretarse una venta (física u online). | Alta | Ventas |
| **RF11** | El sistema debe contar con un catálogo público o E-commerce con un carrito de compras reactivo y persistente. | Alta | E-commerce |
| **RF12** | El sistema debe permitir realizar procesos de checkout para ventas online, requiriendo inicio de sesión, y generar un código de retiro único para cada pedido. | Alta | E-commerce |
| **RF13** | El sistema debe ofrecer a los clientes registrados un panel para consultar el historial y estado de sus compras. | Media | E-commerce |
| **RF14** | El sistema debe generar y almacenar facturas asociadas a cada venta, incluyendo detalles impositivos y datos del cliente. | Alta | Facturación |
| **RF15** | El sistema debe permitir la creación, revisión y publicación de anuncios promocionales mediante un flujo de estados (pendiente, aprobado, rechazado). | Baja | Anuncios |
| **RF16** | El sistema debe generar reportes estadísticos y gráficos de ventas y compras para el panel de control (Dashboard). | Media | Panel Administrativo |
| **RF17** | El sistema debe permitir exportar los registros de ventas a formatos como PDF y Excel. | Media | Panel Administrativo |

---

## Requisitos No Funcionales

Los requisitos no funcionales definen los atributos de calidad, rendimiento y restricciones del sistema.

### Rendimiento

| Código | Requerimiento | Prioridad |
| :--- | :--- | :--- |
| **RNF01** | El sistema debe ser capaz de procesar transacciones en el Punto de Venta (POS) en un tiempo no mayor a 2 segundos para no interrumpir el flujo físico de la ferretería. | Alta |
| **RNF02** | El tiempo de respuesta para la visualización del catálogo de productos en el E-commerce debe ser inferior a 3 segundos bajo carga normal. | Media |
| **RNF03** | Las consultas de reportes gráficos y métricas en el dashboard administrativo deben estar optimizadas para no penalizar el rendimiento del servidor. | Alta |

### Seguridad

| Código | Requerimiento | Prioridad |
| :--- | :--- | :--- |
| **RNF04** | Las contraseñas de todos los usuarios deben almacenarse utilizando algoritmos de encriptación seguros y modernos (ej. Bcrypt, Argon2). | Alta |
| **RNF05** | Todo el acceso a los módulos administrativos y de inventario debe estar protegido y restringido mediante validación de roles (RBAC) en el backend (middleware). | Alta |
| **RNF06** | El sistema debe proteger las rutas de manipulación de datos (POST, PUT, DELETE) contra ataques CSRF e inyecciones SQL (uso de Eloquent). | Alta |

### Usabilidad

| Código | Requerimiento | Prioridad |
| :--- | :--- | :--- |
| **RNF07** | La interfaz de usuario (UI) debe ser intuitiva, utilizando iconografía clara (FontAwesome) y proporcionando retroalimentación visual inmediata en acciones críticas. | Media |
| **RNF08** | El carrito de compras debe mantener el estado para el usuario sin recargar la página completa, logrando una experiencia reactiva fluida (Alpine.js). | Alta |

### Compatibilidad

| Código | Requerimiento | Prioridad |
| :--- | :--- | :--- |
| **RNF09** | La plataforma web (especialmente el E-commerce) debe ser completamente *responsive*, adaptándose correctamente a dispositivos móviles, tablets y computadoras de escritorio. | Alta |
| **RNF10** | El sistema debe ser compatible para su ejecución y correcto funcionamiento en navegadores web modernos (Chrome, Firefox, Safari, Edge). | Alta |

### Mantenibilidad

| Código | Requerimiento | Prioridad |
| :--- | :--- | :--- |
| **RNF11** | El código fuente debe estar estructurado estrictamente bajo el patrón arquitectónico Modelo-Vista-Controlador (MVC) utilizando el framework Laravel. | Alta |
| **RNF12** | La estructura de la base de datos debe ser gestionada a través de migraciones de Laravel para asegurar el control de versiones y la facilidad de despliegue en nuevos entornos. | Alta |

---

## Requisitos Técnicos Mínimos

Estos son los requerimientos técnicos y tecnológicos base sobre los que se sustenta la arquitectura de la aplicación:

*   **Base de datos:** Motor de base de datos relacional MySQL o MariaDB, gestionado a través de Eloquent ORM para asegurar la abstracción e integridad referencial de los datos.
*   **Gestión de usuarios:** Sistema de autenticación de Laravel complementado obligatoriamente con el paquete `spatie/laravel-permission` para el Control de Acceso Basado en Roles (RBAC).
*   **Panel administrativo:** Vistas construidas mediante el motor de plantillas Blade (`.blade.php`), organizadas mediante *layouts* modulares y componentes reutilizables.
*   **Seguridad:** Uso nativo de los middlewares de Laravel (`auth`) combinados con middlewares de roles específicos (`role:Bodeguero|Administrador`) para proteger las rutas expuestas en `web.php`.
*   **Interfaz:** Utilización del framework CSS de utilidad "Tailwind CSS" para asegurar un diseño moderno, ágil y *responsive*. Integración de interactividad en el lado del cliente (modales, carritos reactivos) mediante Alpine.js y JavaScript Vanilla, soportado por iconografía vectorial de FontAwesome 6. El sistema requiere PHP 8.3 o superior.

---

## Tecnologías Utilizadas

| Categoría | Tecnología | Versión | Propósito |
| :--- | :--- | :--- | :--- |
| **Backend** | PHP | 8.3+ | Lenguaje de programación principal del servidor. |
| **Backend** | Laravel | 13.x | Framework MVC para desarrollo rápido y estructurado. |
| **Base de Datos** | MySQL / MariaDB | N/A | Almacenamiento relacional de datos. |
| **Frontend** | Tailwind CSS | N/A | Framework CSS basado en utilidades para diseño responsivo. |
| **Frontend** | Alpine.js | N/A | Framework JavaScript ligero para interactividad en el cliente. |
| **Frontend** | FontAwesome | 6 | Iconografía vectorial. |
| **Seguridad** | spatie/laravel-permission | 8.3 | Gestión de roles y permisos (RBAC). |

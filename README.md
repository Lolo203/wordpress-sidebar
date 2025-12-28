\# WP Price Sidebar Plugin



Plugin de WordPress para mostrar una barra lateral fija con listas de precios personalizables.



\## Características



\- ✅ Barra lateral fija y desplegable

\- ✅ Múltiples divisiones/categorías de precios

\- ✅ Gestión completa desde el panel de administración

\- ✅ 100% responsive

\- ✅ Diseño moderno y atractivo

\- ✅ Activación por página/post específico



\## Instalación



1\. Descarga o clona este repositorio

2\. Sube la carpeta `wp-price-sidebar` a `/wp-content/plugins/`

3\. Activa el plugin desde el menú "Plugins" en WordPress

4\. ¡Listo! Verás un nuevo menú "Listas de Precios" en el admin



\## Estructura de Carpetas



```

wp-price-sidebar/

├── wp-price-sidebar.php          # Archivo principal

├── includes/                     # Clases PHP

│   ├── class-price-sidebar.php

│   ├── class-price-sidebar-admin.php

│   ├── class-price-sidebar-frontend.php

│   └── class-price-sidebar-database.php

├── admin/                        # Panel de administración

│   ├── css/

│   │   └── admin-styles.css

│   ├── js/

│   │   └── admin-scripts.js

│   └── views/

│       ├── admin-page.php

│       ├── division-form.php

│       └── item-form.php

├── public/                       # Frontend

│   ├── css/

│   │   └── frontend-styles.css

│   └── js/

│       └── frontend-scripts.js

├── assets/                       # Recursos estáticos

│   └── icons/

└── uninstall.php                 # Limpieza

```



\## Uso



\### 1. Crear Divisiones



1\. Ve a "Listas de Precios" en el menú de WordPress

2\. Haz clic en "Añadir Nueva División"

3\. Ingresa el nombre (ej: "Productos", "Servicios", "Membresías")

4\. Asigna un orden de visualización (opcional)

5\. Guarda



\### 2. Añadir Items



1\. En la lista de divisiones, haz clic en "Gestionar Items"

2\. Completa el formulario:

&nbsp;  - Nombre del producto/servicio

&nbsp;  - Precio (puede ser cualquier formato: $99, €50, 100 USD, etc.)

&nbsp;  - Orden (opcional)

3\. Guarda



\### 3. Activar en Páginas



1\. Edita la página o post donde quieres mostrar la barra

2\. En el panel lateral, busca "Mostrar Lista de Precios"

3\. Marca la casilla

4\. Actualiza/publica la página

5\. ¡La barra aparecerá en esa página!



\## Base de Datos



El plugin crea 2 tablas:



\- `wp\_price\_divisions` - Almacena las categorías/divisiones

\- `wp\_price\_items` - Almacena los productos/servicios con sus precios



\## Personalización



\### Cambiar ancho de la barra



Edita `public/css/frontend-styles.css`, línea 5:

```css

width: 320px; /\* Cambia este valor \*/

```



\### Cambiar colores



Busca `#2271b1` en `frontend-styles.css` y reemplázalo por tu color preferido.



\## Compatibilidad



\- WordPress 5.0+

\- PHP 7.0+

\- Todos los navegadores modernos

\- Mobile friendly



\## Desinstalación



Al desinstalar el plugin:

\- Se eliminan las tablas de la base de datos

\- Se eliminan todas las configuraciones

\- Se limpia la metadata de posts/páginas



\## Soporte



Para reportar bugs o solicitar features, contacta al desarrollador.



\## Licencia



GPL v2 or later



\## Créditos



Desarrollado para gestionar listas de precios de forma sencilla y profesional.


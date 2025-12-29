<?php
/**
 * Plugin Name: WP Price Sidebar
 * Plugin URI: https://example.com
 * Description: Plugin de barra lateral fija con listas de precios personalizables
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Lorenzo Sayes
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-price-sidebar
 * Domain Path: /languages
 */

// Si se accede directamente, salir
if (!defined('ABSPATH')) {
    die('Acceso directo no permitido');
}

// Definir constantes del plugin
define('WP_PRICE_SIDEBAR_VERSION', '1.0.0');
define('WP_PRICE_SIDEBAR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_PRICE_SIDEBAR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_PRICE_SIDEBAR_PLUGIN_FILE', __FILE__);

// Verificar versión de PHP
if (version_compare(PHP_VERSION, '7.0', '<')) {
    add_action('admin_notices', 'wp_price_sidebar_php_version_notice');
    return;
}

function wp_price_sidebar_php_version_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php _e('WP Price Sidebar requiere PHP 7.0 o superior. Tu versión actual es: ' . PHP_VERSION, 'wp-price-sidebar'); ?></p>
    </div>
    <?php
}

// Requerir archivos necesarios
function wp_price_sidebar_load_files() {
    $files = array(
        'includes/class-price-sidebar-database.php',
        'includes/class-price-sidebar-admin.php',
        'includes/class-price-sidebar-frontend.php',
        'includes/class-price-sidebar.php'
    );
    
    foreach ($files as $file) {
        $filepath = WP_PRICE_SIDEBAR_PLUGIN_DIR . $file;
        if (file_exists($filepath)) {
            require_once $filepath;
        } else {
            add_action('admin_notices', function() use ($file) {
                ?>
                <div class="notice notice-error">
                    <p><?php echo sprintf(__('Error: No se pudo cargar el archivo %s', 'wp-price-sidebar'), $file); ?></p>
                </div>
                <?php
            });
            return false;
        }
    }
    return true;
}

// Activación del plugin
function wp_price_sidebar_activate() {
    if (class_exists('WP_Price_Sidebar_Database')) {
        WP_Price_Sidebar_Database::create_tables();
        flush_rewrite_rules();
    }
}
register_activation_hook(__FILE__, 'wp_price_sidebar_activate');

// Desactivación del plugin
function wp_price_sidebar_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wp_price_sidebar_deactivate');

// Inicializar el plugin
function wp_price_sidebar_init() {
    // Cargar archivos
    if (!wp_price_sidebar_load_files()) {
        return;
    }
    
    // Verificar que la clase principal existe
    if (class_exists('WP_Price_Sidebar')) {
        $plugin = new WP_Price_Sidebar();
        $plugin->run();
    } else {
        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-error">
                <p><?php _e('Error: No se pudo inicializar WP Price Sidebar. Contacte al desarrollador.', 'wp-price-sidebar'); ?></p>
            </div>
            <?php
        });
    }
}
add_action('plugins_loaded', 'wp_price_sidebar_init');
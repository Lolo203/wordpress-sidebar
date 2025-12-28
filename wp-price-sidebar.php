<?php
/**
 * Plugin Name: WP Price Sidebar
 * Plugin URI: https://example.com
 * Description: Plugin de barra lateral fija con listas de precios personalizables
 * Version: 1.0.0
 * Author: Lorenzo Sayes
 * Author URI: https://example.com
 * License: GPL v2 or later
 * Text Domain: wp-price-sidebar
 * Domain Path: /languages
 */

// Si se accede directamente, salir
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('WP_PRICE_SIDEBAR_VERSION', '1.0.0');
define('WP_PRICE_SIDEBAR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_PRICE_SIDEBAR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_PRICE_SIDEBAR_PLUGIN_FILE', __FILE__);

// Requerir archivos necesarios
require_once WP_PRICE_SIDEBAR_PLUGIN_DIR . 'includes/class-price-sidebar-database.php';
require_once WP_PRICE_SIDEBAR_PLUGIN_DIR . 'includes/class-price-sidebar-admin.php';
require_once WP_PRICE_SIDEBAR_PLUGIN_DIR . 'includes/class-price-sidebar-frontend.php';
require_once WP_PRICE_SIDEBAR_PLUGIN_DIR . 'includes/class-price-sidebar.php';

// Activación del plugin
register_activation_hook(__FILE__, array('WP_Price_Sidebar_Database', 'create_tables'));

// Desinstalación del plugin
register_uninstall_hook(__FILE__, array('WP_Price_Sidebar_Database', 'uninstall'));

// Inicializar el plugin
function wp_price_sidebar_init() {
    $plugin = new WP_Price_Sidebar();
    $plugin->run();
}
add_action('plugins_loaded', 'wp_price_sidebar_init');
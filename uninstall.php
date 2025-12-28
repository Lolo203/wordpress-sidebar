<?php
/**
 * Limpieza al desinstalar el plugin
 */

// Si no se está desinstalando, salir
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Eliminar tablas
$table_divisions = $wpdb->prefix . 'price_divisions';
$table_items = $wpdb->prefix . 'price_items';

$wpdb->query("DROP TABLE IF EXISTS $table_items");
$wpdb->query("DROP TABLE IF EXISTS $table_divisions");

// Eliminar opciones
delete_option('wp_price_sidebar_db_version');

// Eliminar post meta de todas las páginas/posts
$wpdb->delete($wpdb->postmeta, array('meta_key' => '_show_price_sidebar'));

// Limpiar caché
wp_cache_flush();
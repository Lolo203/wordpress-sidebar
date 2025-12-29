<?php
/**
 * Manejo de base de datos
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Price_Sidebar_Database {
    
    /**
     * Crear tablas al activar el plugin
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabla de divisiones
        $table_divisions = $wpdb->prefix . 'price_divisions';
        $sql_divisions = "CREATE TABLE IF NOT EXISTS $table_divisions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            order_position int(11) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Tabla de items
        $table_items = $wpdb->prefix . 'price_items';
        $sql_items = "CREATE TABLE IF NOT EXISTS $table_items (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            division_id bigint(20) UNSIGNED NOT NULL,
            product_name varchar(255) NOT NULL,
            price varchar(50) NOT NULL,
            order_position int(11) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY division_id (division_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_divisions);
        dbDelta($sql_items);
        
        // Guardar versión de la base de datos
        add_option('wp_price_sidebar_db_version', WP_PRICE_SIDEBAR_VERSION);
    }
    
    /**
     * Obtener todas las divisiones
     */
    public static function get_divisions() {
        global $wpdb;
        $table = $wpdb->prefix . 'price_divisions';
        $results = $wpdb->get_results("SELECT * FROM $table ORDER BY order_position ASC, id ASC");
        return $results ? $results : array();
    }
    
    /**
     * Obtener una división por ID
     */
    public static function get_division($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'price_divisions';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
    
    /**
     * Crear o actualizar división
     */
    public static function save_division($data, $id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'price_divisions';
        
        $division_data = array(
            'name' => sanitize_text_field($data['name']),
            'order_position' => isset($data['order_position']) ? intval($data['order_position']) : 0
        );
        
        if ($id) {
            $wpdb->update($table, $division_data, array('id' => $id));
            return $id;
        } else {
            $wpdb->insert($table, $division_data);
            return $wpdb->insert_id;
        }
    }
    
    /**
     * Eliminar división y sus items
     */
    public static function delete_division($id) {
        global $wpdb;
        $table_divisions = $wpdb->prefix . 'price_divisions';
        $table_items = $wpdb->prefix . 'price_items';
        
        // Eliminar items de la división
        $wpdb->delete($table_items, array('division_id' => $id));
        
        // Eliminar división
        return $wpdb->delete($table_divisions, array('id' => $id));
    }
    
    /**
     * Obtener items de una división
     */
    public static function get_items($division_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'price_items';
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE division_id = %d ORDER BY order_position ASC, id ASC",
            $division_id
        ));
        return $results ? $results : array();
    }
    
    /**
     * Obtener un item por ID
     */
    public static function get_item($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'price_items';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
    
    /**
     * Crear o actualizar item
     */
    public static function save_item($data, $id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'price_items';
        
        $item_data = array(
            'division_id' => intval($data['division_id']),
            'product_name' => sanitize_text_field($data['product_name']),
            'price' => sanitize_text_field($data['price']),
            'order_position' => isset($data['order_position']) ? intval($data['order_position']) : 0
        );
        
        if ($id) {
            $wpdb->update($table, $item_data, array('id' => $id));
            return $id;
        } else {
            $wpdb->insert($table, $item_data);
            return $wpdb->insert_id;
        }
    }
    
    /**
     * Eliminar item
     */
    public static function delete_item($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'price_items';
        return $wpdb->delete($table, array('id' => $id));
    }
}
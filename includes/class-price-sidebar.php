<?php
/**
 * Clase principal del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Price_Sidebar {
    
    protected $admin;
    protected $frontend;
    
    /**
     * Constructor
     */
    public function __construct() {
        // No inicializar aquí, esperar a run()
    }
    
    /**
     * Ejecutar el plugin
     */
    public function run() {
        // Cargar traducciones
        add_action('init', array($this, 'load_textdomain'));
        
        // Inicializar admin
        if (is_admin()) {
            $this->admin = new WP_Price_Sidebar_Admin();
            $this->admin->init();
        } else {
            // Inicializar frontend
            $this->frontend = new WP_Price_Sidebar_Frontend();
            $this->frontend->init();
        }
    }
    
    /**
     * Cargar traducciones
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'wp-price-sidebar',
            false,
            dirname(plugin_basename(WP_PRICE_SIDEBAR_PLUGIN_FILE)) . '/languages/'
        );
    }
}
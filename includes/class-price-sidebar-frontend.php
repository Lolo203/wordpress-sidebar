<?php
/**
 * Funcionalidad del frontend
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Price_Sidebar_Frontend {
    
    /**
     * Inicializar
     */
    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('wp_footer', array($this, 'render_sidebar'));
    }
    
    /**
     * Cargar assets del frontend
     */
    public function enqueue_frontend_assets() {
        // Solo cargar si la página actual debe mostrar la barra
        if (!$this->should_display_sidebar()) {
            return;
        }
        
        wp_enqueue_style(
            'wp-price-sidebar-frontend',
            WP_PRICE_SIDEBAR_PLUGIN_URL . 'public/css/frontend-styles.css',
            array(),
            WP_PRICE_SIDEBAR_VERSION
        );
        
        wp_enqueue_script(
            'wp-price-sidebar-frontend',
            WP_PRICE_SIDEBAR_PLUGIN_URL . 'public/js/frontend-scripts.js',
            array('jquery'),
            WP_PRICE_SIDEBAR_VERSION,
            true
        );
    }
    
    /**
     * Verificar si debe mostrarse la barra
     */
    private function should_display_sidebar() {
        if (is_singular()) {
            global $post;
            $show = get_post_meta($post->ID, '_show_price_sidebar', true);
            return $show === '1';
        }
        return false;
    }
    
    /**
     * Renderizar la barra lateral
     */
    public function render_sidebar() {
        if (!$this->should_display_sidebar()) {
            return;
        }
        
        $divisions = WP_Price_Sidebar_Database::get_divisions();
        
        if (empty($divisions)) {
            return;
        }
        
        ?>
        <div id="wp-price-sidebar" class="wp-price-sidebar collapsed">
            <button class="wp-price-sidebar-toggle" aria-label="<?php esc_attr_e('Abrir/Cerrar lista de precios', 'wp-price-sidebar'); ?>">
                <svg class="arrow-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            
            <div class="wp-price-sidebar-content">
                <div class="wp-price-sidebar-header">
                    <h3><?php _e('Lista de Precios', 'wp-price-sidebar'); ?></h3>
                </div>
                
                <div class="wp-price-sidebar-body">
                    <?php foreach ($divisions as $division): ?>
                        <?php 
                        $items = WP_Price_Sidebar_Database::get_items($division->id);
                        if (empty($items)) continue;
                        ?>
                        
                        <div class="price-division">
                            <h4 class="division-title"><?php echo esc_html($division->name); ?></h4>
                            <div class="price-items">
                                <?php foreach ($items as $item): ?>
                                    <div class="price-item">
                                        <span class="item-name"><?php echo esc_html($item->product_name); ?></span>
                                        <span class="item-price"><?php echo esc_html($item->price); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
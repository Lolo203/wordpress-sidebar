<?php
/**
 * Funcionalidad del panel de administración
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Price_Sidebar_Admin {
    
    /**
     * Inicializar
     */
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_meta_box'));
        
        // Procesar acciones AJAX y formularios
        add_action('admin_post_save_division', array($this, 'handle_save_division'));
        add_action('admin_post_delete_division', array($this, 'handle_delete_division'));
        add_action('admin_post_save_item', array($this, 'handle_save_item'));
        add_action('admin_post_delete_item', array($this, 'handle_delete_item'));
    }
    
    /**
     * Añadir menú de administración
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Listas de Precios', 'wp-price-sidebar'),
            __('Listas de Precios', 'wp-price-sidebar'),
            'manage_options',
            'wp-price-sidebar',
            array($this, 'render_admin_page'),
            'dashicons-list-view',
            30
        );
    }
    
    /**
     * Cargar assets del admin
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_wp-price-sidebar' !== $hook && 'post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'wp-price-sidebar-admin',
            WP_PRICE_SIDEBAR_PLUGIN_URL . 'admin/css/admin-styles.css',
            array(),
            WP_PRICE_SIDEBAR_VERSION
        );
        
        wp_enqueue_script(
            'wp-price-sidebar-admin',
            WP_PRICE_SIDEBAR_PLUGIN_URL . 'admin/js/admin-scripts.js',
            array('jquery'),
            WP_PRICE_SIDEBAR_VERSION,
            true
        );
    }
    
    /**
     * Renderizar página de administración
     */
    public function render_admin_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $division_id = isset($_GET['division_id']) ? intval($_GET['division_id']) : 0;
        $item_id = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;
        
        include WP_PRICE_SIDEBAR_PLUGIN_DIR . 'admin/views/admin-page.php';
    }
    
    /**
     * Guardar división
     */
    public function handle_save_division() {
        check_admin_referer('save_division');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos suficientes', 'wp-price-sidebar'));
        }
        
        $division_id = isset($_POST['division_id']) ? intval($_POST['division_id']) : 0;
        
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'order_position' => isset($_POST['order_position']) ? intval($_POST['order_position']) : 0
        );
        
        WP_Price_Sidebar_Database::save_division($data, $division_id);
        
        wp_redirect(admin_url('admin.php?page=wp-price-sidebar&message=division_saved'));
        exit;
    }
    
    /**
     * Eliminar división
     */
    public function handle_delete_division() {
        check_admin_referer('delete_division_' . $_GET['division_id']);
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos suficientes', 'wp-price-sidebar'));
        }
        
        $division_id = intval($_GET['division_id']);
        WP_Price_Sidebar_Database::delete_division($division_id);
        
        wp_redirect(admin_url('admin.php?page=wp-price-sidebar&message=division_deleted'));
        exit;
    }
    
    /**
     * Guardar item
     */
    public function handle_save_item() {
        check_admin_referer('save_item');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos suficientes', 'wp-price-sidebar'));
        }
        
        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
        $division_id = intval($_POST['division_id']);
        
        $data = array(
            'division_id' => $division_id,
            'product_name' => sanitize_text_field($_POST['product_name']),
            'price' => sanitize_text_field($_POST['price']),
            'order_position' => isset($_POST['order_position']) ? intval($_POST['order_position']) : 0
        );
        
        WP_Price_Sidebar_Database::save_item($data, $item_id);
        
        wp_redirect(admin_url('admin.php?page=wp-price-sidebar&action=manage_items&division_id=' . $division_id . '&message=item_saved'));
        exit;
    }
    
    /**
     * Eliminar item
     */
    public function handle_delete_item() {
        check_admin_referer('delete_item_' . $_GET['item_id']);
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos suficientes', 'wp-price-sidebar'));
        }
        
        $item_id = intval($_GET['item_id']);
        $division_id = intval($_GET['division_id']);
        
        WP_Price_Sidebar_Database::delete_item($item_id);
        
        wp_redirect(admin_url('admin.php?page=wp-price-sidebar&action=manage_items&division_id=' . $division_id . '&message=item_deleted'));
        exit;
    }
    
    /**
     * Añadir metabox a páginas y posts
     */
    public function add_meta_box() {
        add_meta_box(
            'wp_price_sidebar_display',
            __('Mostrar Lista de Precios', 'wp-price-sidebar'),
            array($this, 'render_meta_box'),
            array('page', 'post'),
            'side',
            'default'
        );
    }
    
    /**
     * Renderizar metabox
     */
    public function render_meta_box($post) {
        wp_nonce_field('wp_price_sidebar_meta_box', 'wp_price_sidebar_meta_box_nonce');
        
        $show_sidebar = get_post_meta($post->ID, '_show_price_sidebar', true);
        ?>
        <label>
            <input type="checkbox" name="show_price_sidebar" value="1" <?php checked($show_sidebar, '1'); ?>>
            <?php _e('Mostrar barra de precios en esta página', 'wp-price-sidebar'); ?>
        </label>
        <?php
    }
    
    /**
     * Guardar metabox
     */
    public function save_meta_box($post_id) {
        if (!isset($_POST['wp_price_sidebar_meta_box_nonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['wp_price_sidebar_meta_box_nonce'], 'wp_price_sidebar_meta_box')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $show_sidebar = isset($_POST['show_price_sidebar']) ? '1' : '0';
        update_post_meta($post_id, '_show_price_sidebar', $show_sidebar);
    }
}
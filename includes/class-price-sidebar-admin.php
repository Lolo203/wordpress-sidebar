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
        
        // Procesar acciones
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
            'Listas de Precios',
            'Listas de Precios',
            'manage_options',
            'wp-price-sidebar',
            array($this, 'render_admin_page'),
            'dashicons-list-view',
            30
        );
        
        // Submenú de debug
        add_submenu_page(
            'wp-price-sidebar',
            'Debug Base de Datos',
            'Debug DB',
            'manage_options',
            'wp-price-sidebar-debug',
            array($this, 'render_debug_page')
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
        
        $view_file = WP_PRICE_SIDEBAR_PLUGIN_DIR . 'admin/views/admin-page.php';
        if (file_exists($view_file)) {
            include $view_file;
        }
    }
    
    /**
     * Guardar división
     */
    public function handle_save_division() {
        check_admin_referer('save_division');
        
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos suficientes');
        }
        
        global $wpdb;
        
        $division_id = isset($_POST['division_id']) ? intval($_POST['division_id']) : 0;
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $order_position = isset($_POST['order_position']) ? intval($_POST['order_position']) : 0;
        
        if (empty($name)) {
            wp_redirect(admin_url('admin.php?page=wp-price-sidebar&error=empty_name'));
            exit;
        }
        
        $table = $wpdb->prefix . 'price_divisions';
        
        if ($division_id > 0) {
            // Actualizar
            $result = $wpdb->update(
                $table,
                array(
                    'name' => $name,
                    'order_position' => $order_position
                ),
                array('id' => $division_id),
                array('%s', '%d'),
                array('%d')
            );
        } else {
            // Insertar
            $result = $wpdb->insert(
                $table,
                array(
                    'name' => $name,
                    'order_position' => $order_position
                ),
                array('%s', '%d')
            );
        }
        
        // Debug: verificar si hubo error
        if ($result === false) {
            wp_redirect(admin_url('admin.php?page=wp-price-sidebar&error=db_error&detail=' . urlencode($wpdb->last_error)));
            exit;
        }
        
        wp_redirect(admin_url('admin.php?page=wp-price-sidebar&message=division_saved'));
        exit;
    }
    
    /**
     * Eliminar división
     */
    public function handle_delete_division() {
        $division_id = isset($_GET['division_id']) ? intval($_GET['division_id']) : 0;
        
        if (!$division_id) {
            wp_redirect(admin_url('admin.php?page=wp-price-sidebar'));
            exit;
        }
        
        check_admin_referer('delete_division_' . $division_id);
        
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos suficientes');
        }
        
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
            wp_die('No tienes permisos suficientes');
        }
        
        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
        $division_id = isset($_POST['division_id']) ? intval($_POST['division_id']) : 0;
        
        if (!$division_id) {
            wp_redirect(admin_url('admin.php?page=wp-price-sidebar'));
            exit;
        }
        
        $data = array(
            'division_id' => $division_id,
            'product_name' => isset($_POST['product_name']) ? sanitize_text_field($_POST['product_name']) : '',
            'price' => isset($_POST['price']) ? sanitize_text_field($_POST['price']) : '',
            'order_position' => isset($_POST['order_position']) ? intval($_POST['order_position']) : 0
        );
        
        if (empty($data['product_name']) || empty($data['price'])) {
            wp_redirect(admin_url('admin.php?page=wp-price-sidebar&action=manage_items&division_id=' . $division_id . '&error=empty_fields'));
            exit;
        }
        
        WP_Price_Sidebar_Database::save_item($data, $item_id);
        
        wp_redirect(admin_url('admin.php?page=wp-price-sidebar&action=manage_items&division_id=' . $division_id . '&message=item_saved'));
        exit;
    }
    
    /**
     * Eliminar item
     */
    public function handle_delete_item() {
        $item_id = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;
        $division_id = isset($_GET['division_id']) ? intval($_GET['division_id']) : 0;
        
        if (!$item_id || !$division_id) {
            wp_redirect(admin_url('admin.php?page=wp-price-sidebar'));
            exit;
        }
        
        check_admin_referer('delete_item_' . $item_id);
        
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos suficientes');
        }
        
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
            'Mostrar Lista de Precios',
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
            Mostrar barra de precios en esta página
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
    
    /**
     * Página de debug
     */
    public function render_debug_page() {
        global $wpdb;
        
        $table_divisions = $wpdb->prefix . 'price_divisions';
        $table_items = $wpdb->prefix . 'price_items';
        
        // Procesar acciones
        if (isset($_GET['action_debug'])) {
            if ($_GET['action_debug'] === 'recreate' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'recreate_tables')) {
                // Eliminar tablas
                $wpdb->query("DROP TABLE IF EXISTS $table_items");
                $wpdb->query("DROP TABLE IF EXISTS $table_divisions");
                
                // Recrear tablas
                WP_Price_Sidebar_Database::create_tables();
                
                echo '<div class="notice notice-success"><p>✅ Tablas recreadas correctamente</p></div>';
            }
            
            if ($_GET['action_debug'] === 'test_insert' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'test_insert')) {
                $result = $wpdb->insert(
                    $table_divisions,
                    array(
                        'name' => 'División de Prueba ' . time(),
                        'order_position' => 0
                    ),
                    array('%s', '%d')
                );
                
                if ($result === false) {
                    echo '<div class="notice notice-error"><p>❌ Error al insertar: ' . $wpdb->last_error . '</p></div>';
                } else {
                    echo '<div class="notice notice-success"><p>✅ Inserción exitosa. ID: ' . $wpdb->insert_id . '</p></div>';
                }
            }
        }
        
        ?>
        <div class="wrap">
            <h1>Debug Base de Datos - WP Price Sidebar</h1>
            
            <div class="card" style="max-width: 100%;">
                <h2>1. Estado de las Tablas</h2>
                <?php
                $divisions_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_divisions'");
                $items_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_items'");
                
                echo '<p><strong>Tabla de divisiones:</strong> ' . $table_divisions . ' - ' . ($divisions_exists ? '✅ EXISTE' : '❌ NO EXISTE') . '</p>';
                echo '<p><strong>Tabla de items:</strong> ' . $table_items . ' - ' . ($items_exists ? '✅ EXISTE' : '❌ NO EXISTE') . '</p>';
                
                if ($divisions_exists) {
                    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_divisions");
                    echo '<p><strong>Registros en divisiones:</strong> ' . intval($count) . '</p>';
                    
                    echo '<h3>Estructura de la tabla:</h3>';
                    $structure = $wpdb->get_results("DESCRIBE $table_divisions");
                    echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr></thead><tbody>';
                    foreach ($structure as $field) {
                        echo '<tr>';
                        echo '<td>' . esc_html($field->Field) . '</td>';
                        echo '<td>' . esc_html($field->Type) . '</td>';
                        echo '<td>' . esc_html($field->Null) . '</td>';
                        echo '<td>' . esc_html($field->Key) . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                    
                    echo '<h3>Contenido de la tabla:</h3>';
                    $content = $wpdb->get_results("SELECT * FROM $table_divisions");
                    if (!empty($content)) {
                        echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>ID</th><th>Nombre</th><th>Orden</th><th>Fecha</th></tr></thead><tbody>';
                        foreach ($content as $row) {
                            echo '<tr>';
                            echo '<td>' . esc_html($row->id) . '</td>';
                            echo '<td>' . esc_html($row->name) . '</td>';
                            echo '<td>' . esc_html($row->order_position) . '</td>';
                            echo '<td>' . esc_html($row->created_at) . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<p>No hay registros en la tabla</p>';
                    }
                }
                ?>
            </div>
            
            <div class="card" style="max-width: 100%; margin-top: 20px;">
                <h2>2. Acciones de Reparación</h2>
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=wp-price-sidebar-debug&action_debug=recreate'), 'recreate_tables'); ?>" 
                       class="button button-primary"
                       onclick="return confirm('¿Estás seguro? Esto eliminará todas las divisiones e items existentes.');">
                        🔄 Recrear Tablas
                    </a>
                </p>
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=wp-price-sidebar-debug&action_debug=test_insert'), 'test_insert'); ?>" 
                       class="button">
                        🧪 Probar Inserción
                    </a>
                </p>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar'); ?>" class="button">
                        ← Volver a Listas de Precios
                    </a>
                </p>
            </div>
            
            <div class="card" style="max-width: 100%; margin-top: 20px;">
                <h2>3. Información del Sistema</h2>
                <p><strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?></p>
                <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                <p><strong>MySQL Version:</strong> <?php echo $wpdb->db_version(); ?></p>
                <p><strong>Prefijo de tablas:</strong> <?php echo $wpdb->prefix; ?></p>
                <p><strong>Charset:</strong> <?php echo $wpdb->get_charset_collate(); ?></p>
            </div>
        </div>
        <?php
    }
}
<?php
/**
 * Vista principal del panel de administración
 */

if (!defined('ABSPATH')) {
    exit;
}

// Mostrar mensajes
if (isset($_GET['message'])) {
    $message = sanitize_text_field($_GET['message']);
    $messages = array(
        'division_saved' => 'División guardada correctamente',
        'division_deleted' => 'División eliminada correctamente',
        'item_saved' => 'Item guardado correctamente',
        'item_deleted' => 'Item eliminado correctamente'
    );
    
    if (isset($messages[$message])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($messages[$message]) . '</p></div>';
    }
}

// Mostrar errores
if (isset($_GET['error'])) {
    $error = sanitize_text_field($_GET['error']);
    $errors = array(
        'empty_name' => 'El nombre de la división no puede estar vacío',
        'empty_fields' => 'Todos los campos son obligatorios',
        'db_error' => 'Error de base de datos: ' . (isset($_GET['detail']) ? sanitize_text_field($_GET['detail']) : 'desconocido')
    );
    
    if (isset($errors[$error])) {
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($errors[$error]) . '</p></div>';
    }
}
?>

<div class="wrap wp-price-sidebar-admin">
    <h1>Gestión de Listas de Precios</h1>
    
    <?php
    switch ($action) {
        case 'add_division':
        case 'edit_division':
            include WP_PRICE_SIDEBAR_PLUGIN_DIR . 'admin/views/division-form.php';
            break;
            
        case 'manage_items':
            include WP_PRICE_SIDEBAR_PLUGIN_DIR . 'admin/views/item-form.php';
            break;
            
        default:
            // Lista de divisiones
            $divisions = WP_Price_Sidebar_Database::get_divisions();
            
            // Debug: Mostrar información
            global $wpdb;
            $table = $wpdb->prefix . 'price_divisions';
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
            
            ?>
            <div class="division-list-header">
                <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar&action=add_division'); ?>" class="button button-primary">
                    Añadir Nueva División
                </a>
                <p style="margin-left: 10px; color: #666;">
                    Total de divisiones en la base de datos: <?php echo intval($count); ?>
                </p>
            </div>
            
            <?php if (empty($divisions)): ?>
                <div class="notice notice-info" style="margin-top: 20px;">
                    <p><strong>No hay divisiones creadas.</strong> Haz clic en "Añadir Nueva División" para crear tu primera división.</p>
                </div>
                
                <?php
                // Debug adicional
                if ($count > 0) {
                    echo '<div class="notice notice-warning"><p><strong>DEBUG:</strong> Hay ' . intval($count) . ' divisiones en la base de datos pero get_divisions() retorna vacío. Revisa el archivo class-price-sidebar-database.php</p></div>';
                    
                    // Mostrar datos raw
                    $raw_divisions = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
                    echo '<pre>';
                    print_r($raw_divisions);
                    echo '</pre>';
                }
                ?>
                
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Orden</th>
                            <th>Items</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($divisions as $division): ?>
                            <?php $items_count = count(WP_Price_Sidebar_Database::get_items($division->id)); ?>
                            <tr>
                                <td><?php echo esc_html($division->id); ?></td>
                                <td><strong><?php echo esc_html($division->name); ?></strong></td>
                                <td><?php echo esc_html($division->order_position); ?></td>
                                <td><?php echo esc_html($items_count); ?> items</td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar&action=manage_items&division_id=' . $division->id); ?>" class="button button-small">
                                        Gestionar Items
                                    </a>
                                    <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar&action=edit_division&division_id=' . $division->id); ?>" class="button button-small">
                                        Editar
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=delete_division&division_id=' . $division->id), 'delete_division_' . $division->id); ?>" 
                                       class="button button-small button-link-delete" 
                                       onclick="return confirm('¿Estás seguro? Esto eliminará todos los items de esta división.');">
                                        Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <?php
            break;
    }
    ?>
</div>
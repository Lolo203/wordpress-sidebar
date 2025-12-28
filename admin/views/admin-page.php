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
        'division_saved' => __('División guardada correctamente', 'wp-price-sidebar'),
        'division_deleted' => __('División eliminada correctamente', 'wp-price-sidebar'),
        'item_saved' => __('Item guardado correctamente', 'wp-price-sidebar'),
        'item_deleted' => __('Item eliminado correctamente', 'wp-price-sidebar')
    );
    
    if (isset($messages[$message])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($messages[$message]) . '</p></div>';
    }
}
?>

<div class="wrap wp-price-sidebar-admin">
    <h1><?php _e('Gestión de Listas de Precios', 'wp-price-sidebar'); ?></h1>
    
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
            ?>
            <div class="division-list-header">
                <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar&action=add_division'); ?>" class="button button-primary">
                    <?php _e('Añadir Nueva División', 'wp-price-sidebar'); ?>
                </a>
            </div>
            
            <?php if (empty($divisions)): ?>
                <p><?php _e('No hay divisiones creadas. ¡Crea tu primera división!', 'wp-price-sidebar'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Nombre', 'wp-price-sidebar'); ?></th>
                            <th><?php _e('Orden', 'wp-price-sidebar'); ?></th>
                            <th><?php _e('Items', 'wp-price-sidebar'); ?></th>
                            <th><?php _e('Acciones', 'wp-price-sidebar'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($divisions as $division): ?>
                            <?php $items_count = count(WP_Price_Sidebar_Database::get_items($division->id)); ?>
                            <tr>
                                <td><strong><?php echo esc_html($division->name); ?></strong></td>
                                <td><?php echo esc_html($division->order_position); ?></td>
                                <td><?php echo esc_html($items_count); ?> items</td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar&action=manage_items&division_id=' . $division->id); ?>" class="button button-small">
                                        <?php _e('Gestionar Items', 'wp-price-sidebar'); ?>
                                    </a>
                                    <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar&action=edit_division&division_id=' . $division->id); ?>" class="button button-small">
                                        <?php _e('Editar', 'wp-price-sidebar'); ?>
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=delete_division&division_id=' . $division->id), 'delete_division_' . $division->id); ?>" 
                                       class="button button-small button-link-delete" 
                                       onclick="return confirm('<?php esc_attr_e('¿Estás seguro? Esto eliminará todos los items de esta división.', 'wp-price-sidebar'); ?>');">
                                        <?php _e('Eliminar', 'wp-price-sidebar'); ?>
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
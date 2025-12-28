<?php
/**
 * Vista para gestionar items de una división
 */

if (!defined('ABSPATH')) {
    exit;
}

$division = WP_Price_Sidebar_Database::get_division($division_id);

if (!$division) {
    echo '<div class="notice notice-error"><p>' . __('División no encontrada', 'wp-price-sidebar') . '</p></div>';
    return;
}

$items = WP_Price_Sidebar_Database::get_items($division_id);
$is_edit_item = isset($_GET['edit_item']) && $item_id > 0;
$item = $is_edit_item ? WP_Price_Sidebar_Database::get_item($item_id) : null;
?>

<div class="items-manager">
    <div class="items-header">
        <h2><?php echo sprintf(__('Gestionar Items de: %s', 'wp-price-sidebar'), esc_html($division->name)); ?></h2>
        <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar'); ?>" class="button">
            <?php _e('← Volver a Divisiones', 'wp-price-sidebar'); ?>
        </a>
    </div>
    
    <div class="items-content">
        <!-- Formulario para añadir/editar item -->
        <div class="item-form-section">
            <h3><?php echo $is_edit_item ? __('Editar Item', 'wp-price-sidebar') : __('Añadir Nuevo Item', 'wp-price-sidebar'); ?></h3>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="item-form">
                <?php wp_nonce_field('save_item'); ?>
                <input type="hidden" name="action" value="save_item">
                <input type="hidden" name="division_id" value="<?php echo esc_attr($division_id); ?>">
                
                <?php if ($is_edit_item): ?>
                    <input type="hidden" name="item_id" value="<?php echo esc_attr($item_id); ?>">
                <?php endif; ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="product_name"><?php _e('Nombre del Producto/Servicio', 'wp-price-sidebar'); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="product_name" 
                                   name="product_name" 
                                   value="<?php echo $is_edit_item ? esc_attr($item->product_name) : ''; ?>" 
                                   class="regular-text" 
                                   required>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="price"><?php _e('Precio', 'wp-price-sidebar'); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="price" 
                                   name="price" 
                                   value="<?php echo $is_edit_item ? esc_attr($item->price) : ''; ?>" 
                                   class="regular-text" 
                                   required
                                   placeholder="$99.99">
                            <p class="description"><?php _e('Puedes usar cualquier formato: $99, €50, 100 USD, etc.', 'wp-price-sidebar'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="item_order"><?php _e('Orden', 'wp-price-sidebar'); ?></label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="item_order" 
                                   name="order_position" 
                                   value="<?php echo $is_edit_item ? esc_attr($item->order_position) : '0'; ?>" 
                                   class="small-text" 
                                   min="0">
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="<?php echo $is_edit_item ? __('Actualizar Item', 'wp-price-sidebar') : __('Añadir Item', 'wp-price-sidebar'); ?>">
                    <?php if ($is_edit_item): ?>
                        <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar&action=manage_items&division_id=' . $division_id); ?>" class="button">
                            <?php _e('Cancelar', 'wp-price-sidebar'); ?>
                        </a>
                    <?php endif; ?>
                </p>
            </form>
        </div>
        
        <!-- Lista de items existentes -->
        <div class="items-list-section">
            <h3><?php _e('Items Actuales', 'wp-price-sidebar'); ?></h3>
            
            <?php if (empty($items)): ?>
                <p><?php _e('No hay items en esta división. ¡Añade el primero!', 'wp-price-sidebar'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Producto/Servicio', 'wp-price-sidebar'); ?></th>
                            <th><?php _e('Precio', 'wp-price-sidebar'); ?></th>
                            <th><?php _e('Orden', 'wp-price-sidebar'); ?></th>
                            <th><?php _e('Acciones', 'wp-price-sidebar'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $existing_item): ?>
                            <tr>
                                <td><strong><?php echo esc_html($existing_item->product_name); ?></strong></td>
                                <td><?php echo esc_html($existing_item->price); ?></td>
                                <td><?php echo esc_html($existing_item->order_position); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar&action=manage_items&division_id=' . $division_id . '&edit_item=1&item_id=' . $existing_item->id); ?>" 
                                       class="button button-small">
                                        <?php _e('Editar', 'wp-price-sidebar'); ?>
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=delete_item&item_id=' . $existing_item->id . '&division_id=' . $division_id), 'delete_item_' . $existing_item->id); ?>" 
                                       class="button button-small button-link-delete" 
                                       onclick="return confirm('<?php esc_attr_e('¿Estás seguro de eliminar este item?', 'wp-price-sidebar'); ?>');">
                                        <?php _e('Eliminar', 'wp-price-sidebar'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
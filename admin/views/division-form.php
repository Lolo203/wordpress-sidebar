<?php
/**
 * Formulario para crear/editar divisiones
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_edit = ($action === 'edit_division' && $division_id > 0);
$division = $is_edit ? WP_Price_Sidebar_Database::get_division($division_id) : null;

$page_title = $is_edit ? __('Editar División', 'wp-price-sidebar') : __('Nueva División', 'wp-price-sidebar');
$button_text = $is_edit ? __('Actualizar División', 'wp-price-sidebar') : __('Crear División', 'wp-price-sidebar');
?>

<div class="division-form-container">
    <h2><?php echo esc_html($page_title); ?></h2>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="division-form">
        <?php wp_nonce_field('save_division'); ?>
        <input type="hidden" name="action" value="save_division">
        
        <?php if ($is_edit): ?>
            <input type="hidden" name="division_id" value="<?php echo esc_attr($division_id); ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="division_name"><?php _e('Nombre de la División', 'wp-price-sidebar'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="text" 
                           id="division_name" 
                           name="name" 
                           value="<?php echo $is_edit ? esc_attr($division->name) : ''; ?>" 
                           class="regular-text" 
                           required>
                    <p class="description"><?php _e('Ejemplo: Productos, Servicios, Membresías', 'wp-price-sidebar'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="division_order"><?php _e('Orden de Visualización', 'wp-price-sidebar'); ?></label>
                </th>
                <td>
                    <input type="number" 
                           id="division_order" 
                           name="order_position" 
                           value="<?php echo $is_edit ? esc_attr($division->order_position) : '0'; ?>" 
                           class="small-text" 
                           min="0">
                    <p class="description"><?php _e('Número menor aparece primero. 0 = sin orden específico', 'wp-price-sidebar'); ?></p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr($button_text); ?>">
            <a href="<?php echo admin_url('admin.php?page=wp-price-sidebar'); ?>" class="button"><?php _e('Cancelar', 'wp-price-sidebar'); ?></a>
        </p>
    </form>
</div>
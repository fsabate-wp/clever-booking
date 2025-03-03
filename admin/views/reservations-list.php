<?php
/**
 * Vista del listado de reservas
 *
 * @link       
 * @since      1.0.0
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

// Comprobar si hay mensajes de sesión
if (!session_id()) {
    session_start();
}

// Mostrar mensajes de sesión y limpiarlos
if (isset($_SESSION['cb_reservation_updated']) && $_SESSION['cb_reservation_updated']) {
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Reserva actualizada exitosamente.', 'clever-booking') . '</p></div>';
    unset($_SESSION['cb_reservation_updated']);
}

if (isset($_SESSION['cb_reservation_deleted']) && $_SESSION['cb_reservation_deleted']) {
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Reserva eliminada exitosamente.', 'clever-booking') . '</p></div>';
    unset($_SESSION['cb_reservation_deleted']);
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Reservas', 'clever-booking'); ?></h1>
    
    <?php
    // Mostrar mensaje de éxito o error si es necesario
    if (isset($_GET['deleted'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Reserva eliminada.', 'clever-booking') . '</p></div>';
    }
    
    // Mostrar errores de settings si hay
    settings_errors('clever_booking');
    ?>
    
    <div class="tablenav top">
        <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
            <input type="hidden" name="page" value="clever-booking-reservations">
            
            <div class="alignleft actions">
                <label for="filter-by-date-from" class="screen-reader-text"><?php echo esc_html__('Filtrar por fecha desde', 'clever-booking'); ?></label>
                <input type="text" id="filter-by-date-from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : ''; ?>" placeholder="<?php echo esc_attr__('Desde fecha...', 'clever-booking'); ?>" class="date-picker">
                
                <label for="filter-by-date-to" class="screen-reader-text"><?php echo esc_html__('Filtrar por fecha hasta', 'clever-booking'); ?></label>
                <input type="text" id="filter-by-date-to" name="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : ''; ?>" placeholder="<?php echo esc_attr__('Hasta fecha...', 'clever-booking'); ?>" class="date-picker">
                
                <label for="filter-by-status" class="screen-reader-text"><?php echo esc_html__('Filtrar por estado', 'clever-booking'); ?></label>
                <select name="status" id="filter-by-status">
                    <option value=""><?php echo esc_html__('Todos los estados', 'clever-booking'); ?></option>
                    <option value="pending" <?php selected(isset($_GET['status']) && $_GET['status'] === 'pending'); ?>><?php echo esc_html__('Pendiente', 'clever-booking'); ?></option>
                    <option value="confirmed" <?php selected(isset($_GET['status']) && $_GET['status'] === 'confirmed'); ?>><?php echo esc_html__('Confirmada', 'clever-booking'); ?></option>
                    <option value="completed" <?php selected(isset($_GET['status']) && $_GET['status'] === 'completed'); ?>><?php echo esc_html__('Completada', 'clever-booking'); ?></option>
                    <option value="cancelled" <?php selected(isset($_GET['status']) && $_GET['status'] === 'cancelled'); ?>><?php echo esc_html__('Cancelada', 'clever-booking'); ?></option>
                </select>
                
                <input type="submit" class="button" value="<?php echo esc_attr__('Filtrar', 'clever-booking'); ?>">
                <?php if (isset($_GET['date_from']) || isset($_GET['date_to']) || isset($_GET['status'])) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=clever-booking-reservations')); ?>" class="button"><?php echo esc_html__('Limpiar', 'clever-booking'); ?></a>
                <?php endif; ?>
            </div>
            
            <?php 
            // Implementación básica de paginación
            $page_links = paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $total_pages,
                'current' => $current_page
            ));
            
            if ($page_links) : ?>
                <div class="tablenav-pages">
                    <?php echo $page_links; ?>
                </div>
            <?php endif; ?>
        </form>
        <br class="clear">
    </div>
    
    <table class="wp-list-table widefat fixed striped reservations">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-id"><?php echo esc_html__('ID', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-customer"><?php echo esc_html__('Cliente', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-service"><?php echo esc_html__('Servicio', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-date"><?php echo esc_html__('Fecha', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-time"><?php echo esc_html__('Hora', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-status"><?php echo esc_html__('Estado', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-created"><?php echo esc_html__('Creada', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-actions"><?php echo esc_html__('Acciones', 'clever-booking'); ?></th>
            </tr>
        </thead>
        
        <tbody>
            <?php if (!empty($reservations)) : ?>
                <?php foreach ($reservations as $reservation) : 
                    // Obtener información del servicio
                    $service = get_post($reservation->service_id);
                    $service_title = $service ? $service->post_title : __('Servicio Desconocido', 'clever-booking');
                    
                    // Formatear fechas y horas
                    $booking_date = date_i18n(get_option('date_format'), strtotime($reservation->booking_date));
                    $booking_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_time));
                    $booking_end_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_end_time));
                    $created_at = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($reservation->created_at));
                    
                    // Mapear estados a etiquetas legibles
                    $status_labels = array(
                        'pending' => __('Pendiente', 'clever-booking'),
                        'confirmed' => __('Confirmada', 'clever-booking'),
                        'completed' => __('Completada', 'clever-booking'),
                        'cancelled' => __('Cancelada', 'clever-booking'),
                    );
                    $status_label = isset($status_labels[$reservation->status]) ? $status_labels[$reservation->status] : $reservation->status;
                ?>
                <tr>
                    <td class="column-id"><?php echo esc_html($reservation->id); ?></td>
                    <td class="column-customer">
                        <strong><?php echo esc_html($reservation->customer_name); ?></strong><br>
                        <small><?php echo esc_html($reservation->customer_email); ?></small><br>
                        <small><?php echo esc_html($reservation->customer_phone); ?></small>
                    </td>
                    <td class="column-service"><?php echo esc_html($service_title); ?></td>
                    <td class="column-date"><?php echo esc_html($booking_date); ?></td>
                    <td class="column-time"><?php echo esc_html($booking_time) . ' - ' . esc_html($booking_end_time); ?></td>
                    <td class="column-status">
                        <span class="reservation-status status-<?php echo esc_attr($reservation->status); ?>">
                            <?php echo esc_html($status_label); ?>
                        </span>
                    </td>
                    <td class="column-created"><?php echo esc_html($created_at); ?></td>
                    <td class="column-actions">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=clever-booking-reservations&action=edit&id=' . $reservation->id)); ?>" class="button button-small"><?php echo esc_html__('Editar', 'clever-booking'); ?></a>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=clever-booking-reservations&action=delete&id=' . $reservation->id), 'delete_reservation_' . $reservation->id)); ?>" class="button button-small delete" onclick="return confirm('<?php echo esc_js(__('¿Estás seguro de que deseas eliminar esta reserva?', 'clever-booking')); ?>')"><?php echo esc_html__('Eliminar', 'clever-booking'); ?></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8"><?php echo esc_html__('No se encontraron reservas.', 'clever-booking'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
        
        <tfoot>
            <tr>
                <th scope="col" class="manage-column column-id"><?php echo esc_html__('ID', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-customer"><?php echo esc_html__('Cliente', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-service"><?php echo esc_html__('Servicio', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-date"><?php echo esc_html__('Fecha', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-time"><?php echo esc_html__('Hora', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-status"><?php echo esc_html__('Estado', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-created"><?php echo esc_html__('Creada', 'clever-booking'); ?></th>
                <th scope="col" class="manage-column column-actions"><?php echo esc_html__('Acciones', 'clever-booking'); ?></th>
            </tr>
        </tfoot>
    </table>
    
    <div class="tablenav bottom">
        <?php if ($page_links) : ?>
            <div class="tablenav-pages">
                <?php echo $page_links; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Inicializar datepickers
        $('.date-picker').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    });
</script>

<style>
    .reservation-status {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        color: #fff;
        font-size: 12px;
    }
    
    .status-pending {
        background-color: #f39c12;
    }
    
    .status-confirmed {
        background-color: #3498db;
    }
    
    .status-completed {
        background-color: #2ecc71;
    }
    
    .status-cancelled {
        background-color: #e74c3c;
    }
    
    .button.delete {
        color: #a00;
    }
    
    .button.delete:hover {
        color: #dc3232;
        border-color: #dc3232;
    }
    
    .column-id {
        width: 50px;
    }
    
    .column-customer {
        width: 20%;
    }
    
    .column-service {
        width: 15%;
    }
    
    .column-date, .column-time, .column-status, .column-created {
        width: 10%;
    }
    
    .column-actions {
        width: 120px;
    }
</style> 
<?php
/**
 * Vista de edición de reserva
 *
 * @link       
 * @since      1.0.0
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

// Obtener la información del servicio
$service = get_post($reservation->service_id);
$service_title = $service ? $service->post_title : __('Servicio Desconocido', 'clever-booking');

// Formatear fechas y horas
$booking_date = date_i18n(get_option('date_format'), strtotime($reservation->booking_date));
$booking_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_time));
$booking_end_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_end_time));
$created_at = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($reservation->created_at));

// Definir opciones de estado
$status_options = array(
    'pending' => __('Pendiente', 'clever-booking'),
    'confirmed' => __('Confirmada', 'clever-booking'),
    'completed' => __('Completada', 'clever-booking'),
    'cancelled' => __('Cancelada', 'clever-booking'),
);
?>

<div class="wrap">
    <h1><?php echo esc_html__('Editar Reserva', 'clever-booking'); ?></h1>
    
    <?php
    // Mostrar mensaje de éxito si la reserva se actualizó
    if (isset($_GET['updated'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Reserva actualizada.', 'clever-booking') . '</p></div>';
    }
    ?>
    
    <div class="reservation-edit-container">
        <div class="reservation-details-panel">
            <div class="reservation-details">
                <h2><?php echo esc_html__('Detalles de la Reserva', 'clever-booking'); ?></h2>
                
                <div class="reservation-info">
                    <p>
                        <strong><?php echo esc_html__('ID de Reserva:', 'clever-booking'); ?></strong>
                        <?php echo esc_html($reservation->id); ?>
                    </p>
                    
                    <p>
                        <strong><?php echo esc_html__('Servicio:', 'clever-booking'); ?></strong>
                        <?php echo esc_html($service_title); ?>
                    </p>
                    
                    <p>
                        <strong><?php echo esc_html__('Fecha:', 'clever-booking'); ?></strong>
                        <?php echo esc_html($booking_date); ?>
                    </p>
                    
                    <p>
                        <strong><?php echo esc_html__('Hora:', 'clever-booking'); ?></strong>
                        <?php echo esc_html($booking_time) . ' - ' . esc_html($booking_end_time); ?>
                    </p>
                    
                    <p>
                        <strong><?php echo esc_html__('Fecha de Creación:', 'clever-booking'); ?></strong>
                        <?php echo esc_html($created_at); ?>
                    </p>
                </div>
                
                <div class="customer-info">
                    <h3><?php echo esc_html__('Información del Cliente', 'clever-booking'); ?></h3>
                    
                    <p>
                        <strong><?php echo esc_html__('Nombre:', 'clever-booking'); ?></strong>
                        <?php echo esc_html($reservation->customer_name); ?>
                    </p>
                    
                    <p>
                        <strong><?php echo esc_html__('Email:', 'clever-booking'); ?></strong>
                        <a href="mailto:<?php echo esc_attr($reservation->customer_email); ?>"><?php echo esc_html($reservation->customer_email); ?></a>
                    </p>
                    
                    <p>
                        <strong><?php echo esc_html__('Teléfono:', 'clever-booking'); ?></strong>
                        <?php echo esc_html($reservation->customer_phone); ?>
                    </p>
                    
                    <p>
                        <strong><?php echo esc_html__('Dirección:', 'clever-booking'); ?></strong>
                        <?php echo esc_html($reservation->customer_address); ?>
                    </p>
                    
                    <?php if (!empty($reservation->notes)) : ?>
                        <p>
                            <strong><?php echo esc_html__('Notas:', 'clever-booking'); ?></strong>
                            <?php echo esc_html($reservation->notes); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="reservation-actions-panel">
            <div class="reservation-actions">
                <h2><?php echo esc_html__('Acciones', 'clever-booking'); ?></h2>
                
                <form method="post" action="">
                    <?php wp_nonce_field('save_reservation', 'reservation_nonce'); ?>
                    
                    <div class="form-field">
                        <label for="status"><?php echo esc_html__('Estado de la Reserva:', 'clever-booking'); ?></label>
                        <select name="status" id="status" class="status-selector">
                            <?php foreach ($status_options as $value => $label) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($reservation->status, $value); ?> class="status-<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="submit-section">
                        <input type="submit" name="save_reservation" class="button button-primary" value="<?php echo esc_attr__('Actualizar Reserva', 'clever-booking'); ?>">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=clever-booking-reservations')); ?>" class="button"><?php echo esc_html__('Volver al Listado', 'clever-booking'); ?></a>
                    </div>
                </form>
                
                <div class="additional-actions">
                    <h3><?php echo esc_html__('Acciones Adicionales', 'clever-booking'); ?></h3>
                    
                    <p>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=clever-booking-reservations&action=delete&id=' . $reservation->id), 'delete_reservation_' . $reservation->id)); ?>" class="button delete" onclick="return confirm('<?php echo esc_js(__('¿Estás seguro de que deseas eliminar esta reserva?', 'clever-booking')); ?>')">
                            <?php echo esc_html__('Eliminar Reserva', 'clever-booking'); ?>
                        </a>
                    </p>
                    
                    <p>
                        <?php 
                        // Solo mostrar botón para enviar recordatorio si la reserva está confirmada
                        if ($reservation->status === 'confirmed') : 
                        ?>
                            <a href="#" class="button send-reminder" id="send-reminder" data-id="<?php echo esc_attr($reservation->id); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('send_reminder_' . $reservation->id)); ?>">
                                <?php echo esc_html__('Enviar Recordatorio', 'clever-booking'); ?>
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Manejar el envío de recordatorio
        $('#send-reminder').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var id = button.data('id');
            var nonce = button.data('nonce');
            
            button.addClass('disabled').text('<?php echo esc_js(__('Enviando...', 'clever-booking')); ?>');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'clever_booking_send_reminder',
                    id: id,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        button.text('<?php echo esc_js(__('Recordatorio Enviado', 'clever-booking')); ?>');
                        setTimeout(function() {
                            button.text('<?php echo esc_js(__('Enviar Recordatorio', 'clever-booking')); ?>').removeClass('disabled');
                        }, 3000);
                    } else {
                        button.text('<?php echo esc_js(__('Error al Enviar', 'clever-booking')); ?>');
                        setTimeout(function() {
                            button.text('<?php echo esc_js(__('Enviar Recordatorio', 'clever-booking')); ?>').removeClass('disabled');
                        }, 3000);
                    }
                },
                error: function() {
                    button.text('<?php echo esc_js(__('Error al Enviar', 'clever-booking')); ?>');
                    setTimeout(function() {
                        button.text('<?php echo esc_js(__('Enviar Recordatorio', 'clever-booking')); ?>').removeClass('disabled');
                    }, 3000);
                }
            });
        });
    });
</script>

<style>
    .reservation-edit-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 20px;
    }
    
    .reservation-details-panel,
    .reservation-actions-panel {
        background: #fff;
        border: 1px solid #e5e5e5;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        padding: 20px;
        margin-right: 20px;
        margin-bottom: 20px;
    }
    
    .reservation-details-panel {
        flex: 2;
        min-width: 500px;
    }
    
    .reservation-actions-panel {
        flex: 1;
        min-width: 300px;
    }
    
    .form-field {
        margin-bottom: 20px;
    }
    
    .form-field label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .status-selector {
        width: 100%;
        padding: 8px;
    }
    
    .submit-section {
        margin-top: 20px;
        margin-bottom: 30px;
    }
    
    .submit-section .button {
        margin-right: 10px;
    }
    
    .additional-actions {
        border-top: 1px solid #e5e5e5;
        padding-top: 20px;
        margin-top: 20px;
    }
    
    .button.delete {
        color: #a00;
    }
    
    .button.delete:hover {
        color: #dc3232;
        border-color: #dc3232;
    }
    
    .reservation-info,
    .customer-info {
        margin-bottom: 30px;
    }
    
    /* Estilos específicos para los estados */
    .status-pending {
        color: #f39c12;
    }
    
    .status-confirmed {
        color: #3498db;
    }
    
    .status-completed {
        color: #2ecc71;
    }
    
    .status-cancelled {
        color: #e74c3c;
    }
</style> 
<?php
/**
 * Vista del formulario de reserva
 *
 * @link       
 * @since      1.0.0
 * 
 * Shortcode: [clever_booking_form]
 * Shortcode: [clever_booking_form service_id="123"]
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

// Inicializar la lógica de reservas
$booking_logic = new Clever_Booking_Logic();

// Obtener servicios disponibles
$services = $booking_logic->get_available_services();

// Si no hay servicios, mostrar un mensaje
if (empty($services)) {
    echo '<div class="cb-message cb-error">';
    echo esc_html__('No hay servicios disponibles actualmente. Por favor, vuelve más tarde.', 'clever-booking');
    echo '</div>';
    return;
}

// Si se proporciona un ID de servicio específico
$preselected_service_id = absint($atts['service_id']);
$single_service_mode = ($preselected_service_id > 0);

// Si estamos en modo de servicio único, verificar que el servicio exista
if ($single_service_mode) {
    $service_exists = false;
    foreach ($services as $service) {
        if ($service->ID == $preselected_service_id) {
            $service_exists = true;
            break;
        }
    }
    
    if (!$service_exists) {
        $single_service_mode = false;
        $preselected_service_id = 0;
    }
}
?>

<div class="clever-booking-form-container">
    <div class="cb-form-steps">
        <div class="cb-step cb-step-1 cb-step-active" data-step="1">
            <span class="cb-step-number">1</span>
            <span class="cb-step-label"><?php echo esc_html__('Selecciona Fecha', 'clever-booking'); ?></span>
        </div>
        <div class="cb-step cb-step-2" data-step="2">
            <span class="cb-step-number">2</span>
            <span class="cb-step-label"><?php echo esc_html__('Selecciona Hora', 'clever-booking'); ?></span>
        </div>
        <div class="cb-step cb-step-3" data-step="3">
            <span class="cb-step-number">3</span>
            <span class="cb-step-label"><?php echo esc_html__('Tus Datos', 'clever-booking'); ?></span>
        </div>
        <div class="cb-step cb-step-4" data-step="4">
            <span class="cb-step-number">4</span>
            <span class="cb-step-label"><?php echo esc_html__('Confirmación', 'clever-booking'); ?></span>
        </div>
    </div>
    
    <div class="cb-form-wrapper">
        <div class="cb-alerts"></div>
        
        <form id="clever-booking-form" class="cb-booking-form">
            <?php wp_nonce_field('clever_booking_nonce', 'cb_nonce'); ?>
            
            <!-- Paso 1: Selección de servicio (si no es modo servicio único) y fecha -->
            <div class="cb-form-step cb-form-step-1 cb-active">
                <div class="cb-form-header">
                    <h3><?php echo esc_html__('Selecciona Servicio y Fecha', 'clever-booking'); ?></h3>
                </div>
                
                <div class="cb-form-body">
                    <?php if (!$single_service_mode) : ?>
                        <div class="cb-form-field">
                            <label for="cb-service"><?php echo esc_html__('Servicio', 'clever-booking'); ?> <span class="required">*</span></label>
                            <select id="cb-service" name="service_id" class="cb-input cb-select" required>
                                <option value=""><?php echo esc_html__('Selecciona un servicio', 'clever-booking'); ?></option>
                                <?php foreach ($services as $service) : 
                                    $price = get_field('service_price', $service->ID);
                                    $duration = get_field('service_duration', $service->ID);
                                    $price_display = '$' . number_format($price, 2);
                                    $duration_display = $duration . ' ' . __('min', 'clever-booking');
                                ?>
                                    <option value="<?php echo esc_attr($service->ID); ?>" data-price="<?php echo esc_attr($price); ?>" data-duration="<?php echo esc_attr($duration); ?>">
                                        <?php echo esc_html($service->post_title); ?> (<?php echo esc_html($price_display); ?> - <?php echo esc_html($duration_display); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else : ?>
                        <?php 
                            // En modo servicio único, obtener detalles del servicio preseleccionado
                            foreach ($services as $service) {
                                if ($service->ID == $preselected_service_id) {
                                    $selected_service = $service;
                                    $price = get_field('service_price', $service->ID);
                                    $duration = get_field('service_duration', $service->ID);
                                    break;
                                }
                            }
                        ?>
                        <div class="cb-form-field">
                            <label><?php echo esc_html__('Servicio', 'clever-booking'); ?></label>
                            <div class="cb-service-info">
                                <strong><?php echo esc_html($selected_service->post_title); ?></strong>
                                <div class="cb-service-details">
                                    <span class="cb-service-price"><?php echo esc_html('$' . number_format($price, 2)); ?></span>
                                    <span class="cb-service-duration"><?php echo esc_html($duration) . ' ' . esc_html__('minutos', 'clever-booking'); ?></span>
                                </div>
                            </div>
                            <input type="hidden" name="service_id" value="<?php echo esc_attr($preselected_service_id); ?>" data-price="<?php echo esc_attr($price); ?>" data-duration="<?php echo esc_attr($duration); ?>">
                        </div>
                    <?php endif; ?>
                    
                    <div class="cb-form-field">
                        <label for="cb-date"><?php echo esc_html__('Fecha', 'clever-booking'); ?> <span class="required">*</span></label>
                        <input type="text" id="cb-date" name="booking_date" class="cb-input cb-datepicker" readonly required placeholder="<?php echo esc_attr__('Selecciona una fecha', 'clever-booking'); ?>">
                    </div>
                </div>
                
                <div class="cb-form-footer">
                    <button type="button" class="cb-button cb-next-step" data-next="2"><?php echo esc_html__('Continuar', 'clever-booking'); ?></button>
                </div>
            </div>
            
            <!-- Paso 2: Selección de horario -->
            <div class="cb-form-step cb-form-step-2">
                <div class="cb-form-header">
                    <h3><?php echo esc_html__('Selecciona Horario', 'clever-booking'); ?></h3>
                </div>
                
                <div class="cb-form-body">
                    <div class="cb-form-field">
                        <label for="cb-time"><?php echo esc_html__('Horario Disponible', 'clever-booking'); ?> <span class="required">*</span></label>
                        <select id="cb-time" name="booking_time" class="cb-input cb-select" required>
                            <option value=""><?php echo esc_html__('Primero selecciona una fecha', 'clever-booking'); ?></option>
                        </select>
                        <div id="cb-time-loading" class="cb-loading-spinner" style="display: none;">
                            <div class="cb-spinner"></div>
                            <span><?php echo esc_html__('Cargando horarios disponibles...', 'clever-booking'); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="cb-form-footer">
                    <button type="button" class="cb-button cb-prev-step" data-prev="1"><?php echo esc_html__('Volver', 'clever-booking'); ?></button>
                    <button type="button" class="cb-button cb-next-step" data-next="3"><?php echo esc_html__('Continuar', 'clever-booking'); ?></button>
                </div>
            </div>
            
            <!-- Paso 3: Formulario de datos del cliente -->
            <div class="cb-form-step cb-form-step-3">
                <div class="cb-form-header">
                    <h3><?php echo esc_html__('Tus Datos de Contacto', 'clever-booking'); ?></h3>
                </div>
                
                <div class="cb-form-body">
                    <div class="cb-form-field">
                        <label for="cb-name"><?php echo esc_html__('Nombre Completo', 'clever-booking'); ?> <span class="required">*</span></label>
                        <input type="text" id="cb-name" name="customer_name" class="cb-input" required placeholder="<?php echo esc_attr__('Ingresa tu nombre completo', 'clever-booking'); ?>">
                    </div>
                    
                    <div class="cb-form-field">
                        <label for="cb-email"><?php echo esc_html__('Email', 'clever-booking'); ?> <span class="required">*</span></label>
                        <input type="email" id="cb-email" name="customer_email" class="cb-input" required placeholder="<?php echo esc_attr__('Ingresa tu correo electrónico', 'clever-booking'); ?>">
                    </div>
                    
                    <div class="cb-form-field">
                        <label for="cb-phone"><?php echo esc_html__('Teléfono', 'clever-booking'); ?> <span class="required">*</span></label>
                        <input type="tel" id="cb-phone" name="customer_phone" class="cb-input" required placeholder="<?php echo esc_attr__('Ingresa tu número de teléfono', 'clever-booking'); ?>">
                    </div>
                    
                    <div class="cb-form-field">
                        <label for="cb-address"><?php echo esc_html__('Dirección', 'clever-booking'); ?> <span class="required">*</span></label>
                        <textarea id="cb-address" name="customer_address" class="cb-input" required placeholder="<?php echo esc_attr__('Ingresa tu dirección completa', 'clever-booking'); ?>"></textarea>
                    </div>
                    
                    <div class="cb-form-field">
                        <label for="cb-notes"><?php echo esc_html__('Notas', 'clever-booking'); ?></label>
                        <textarea id="cb-notes" name="notes" class="cb-input" placeholder="<?php echo esc_attr__('Información adicional o solicitudes especiales', 'clever-booking'); ?>"></textarea>
                    </div>
                </div>
                
                <div class="cb-form-footer">
                    <button type="button" class="cb-button cb-prev-step" data-prev="2"><?php echo esc_html__('Volver', 'clever-booking'); ?></button>
                    <button type="button" class="cb-button cb-next-step" data-next="4"><?php echo esc_html__('Continuar', 'clever-booking'); ?></button>
                </div>
            </div>
            
            <!-- Paso 4: Confirmación -->
            <div class="cb-form-step cb-form-step-4">
                <div class="cb-form-header">
                    <h3><?php echo esc_html__('Confirma tu Reserva', 'clever-booking'); ?></h3>
                </div>
                
                <div class="cb-form-body">
                    <div class="cb-summary">
                        <h4><?php echo esc_html__('Resumen de la Reserva', 'clever-booking'); ?></h4>
                        
                        <div class="cb-summary-item">
                            <span class="cb-summary-label"><?php echo esc_html__('Servicio:', 'clever-booking'); ?></span>
                            <span class="cb-summary-value" id="summary-service">-</span>
                        </div>
                        
                        <div class="cb-summary-item">
                            <span class="cb-summary-label"><?php echo esc_html__('Fecha:', 'clever-booking'); ?></span>
                            <span class="cb-summary-value" id="summary-date">-</span>
                        </div>
                        
                        <div class="cb-summary-item">
                            <span class="cb-summary-label"><?php echo esc_html__('Hora:', 'clever-booking'); ?></span>
                            <span class="cb-summary-value" id="summary-time">-</span>
                        </div>
                        
                        <div class="cb-summary-item">
                            <span class="cb-summary-label"><?php echo esc_html__('Precio:', 'clever-booking'); ?></span>
                            <span class="cb-summary-value" id="summary-price">-</span>
                        </div>
                        
                        <h4 class="cb-mt-20"><?php echo esc_html__('Datos de Contacto', 'clever-booking'); ?></h4>
                        
                        <div class="cb-summary-item">
                            <span class="cb-summary-label"><?php echo esc_html__('Nombre:', 'clever-booking'); ?></span>
                            <span class="cb-summary-value" id="summary-name">-</span>
                        </div>
                        
                        <div class="cb-summary-item">
                            <span class="cb-summary-label"><?php echo esc_html__('Email:', 'clever-booking'); ?></span>
                            <span class="cb-summary-value" id="summary-email">-</span>
                        </div>
                        
                        <div class="cb-summary-item">
                            <span class="cb-summary-label"><?php echo esc_html__('Teléfono:', 'clever-booking'); ?></span>
                            <span class="cb-summary-value" id="summary-phone">-</span>
                        </div>
                        
                        <div class="cb-summary-item">
                            <span class="cb-summary-label"><?php echo esc_html__('Dirección:', 'clever-booking'); ?></span>
                            <span class="cb-summary-value" id="summary-address">-</span>
                        </div>
                        
                        <div class="cb-summary-item" id="summary-notes-container" style="display: none;">
                            <span class="cb-summary-label"><?php echo esc_html__('Notas:', 'clever-booking'); ?></span>
                            <span class="cb-summary-value" id="summary-notes">-</span>
                        </div>
                    </div>
                </div>
                
                <div class="cb-form-footer">
                    <button type="button" class="cb-button cb-prev-step" data-prev="3"><?php echo esc_html__('Volver', 'clever-booking'); ?></button>
                    <button type="submit" class="cb-button cb-submit-booking"><?php echo esc_html__('Confirmar Reserva', 'clever-booking'); ?></button>
                </div>
            </div>
            
            <!-- Paso de Gracias (mostrado después de enviar) -->
            <div class="cb-form-step cb-form-step-thanks">
                <div class="cb-form-header">
                    <h3><?php echo esc_html__('¡Gracias por tu Reserva!', 'clever-booking'); ?></h3>
                </div>
                
                <div class="cb-form-body">
                    <div class="cb-thanks-message">
                        <p><?php echo esc_html__('Tu reserva ha sido registrada correctamente. Hemos enviado un correo electrónico de confirmación con los detalles de tu reserva.', 'clever-booking'); ?></p>
                        
                        <div class="cb-thanks-details"></div>
                        
                        <p><?php echo esc_html__('Si tienes alguna pregunta, por favor contáctanos.', 'clever-booking'); ?></p>
                    </div>
                </div>
                
                <div class="cb-form-footer">
                    <button type="button" class="cb-button cb-new-booking" onclick="window.location.reload();"><?php echo esc_html__('Crear Nueva Reserva', 'clever-booking'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div> 
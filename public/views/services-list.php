<?php
/**
 * Vista de la lista de servicios
 *
 * @link       
 * @since      1.0.0
 * 
 * Shortcode: [clever_booking_services]
 * Shortcode: [clever_booking_services category_id="123" layout="grid|list" columns="3"]
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

// Parámetros por defecto
$category_id = isset($atts['category_id']) ? absint($atts['category_id']) : 0;
$layout = isset($atts['layout']) && in_array($atts['layout'], array('grid', 'list')) ? $atts['layout'] : 'grid';
$columns = isset($atts['columns']) ? absint($atts['columns']) : 3;

// Sanitizar columnas (entre 1 y 4)
$columns = max(1, min(4, $columns));

// Inicializar la lógica de reservas
$booking_logic = new Clever_Booking_Logic();

// Obtener servicios disponibles
$args = array();

// Si se especificó una categoría, agregamos al argumento
if ($category_id > 0) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'cb_service_category',
            'field'    => 'term_id',
            'terms'    => $category_id,
        ),
    );
}

$services = $booking_logic->get_available_services($args);

// Si no hay servicios, mostrar un mensaje
if (empty($services)) {
    echo '<div class="cb-message cb-info">';
    echo esc_html__('No hay servicios disponibles actualmente. Por favor, vuelve más tarde.', 'clever-booking');
    echo '</div>';
    return;
}

// Clases CSS basadas en el layout y columnas
$container_class = 'clever-booking-services';
$container_class .= ' cb-layout-' . $layout;

if ($layout === 'grid') {
    $container_class .= ' cb-columns-' . $columns;
}

?>

<div class="<?php echo esc_attr($container_class); ?>">
    <?php foreach ($services as $service) : 
        // Obtener datos del servicio
        $service_id = $service->ID;
        $title = get_the_title($service_id);
        $permalink = get_permalink($service_id);
        $price = get_field('service_price', $service_id);
        $duration = get_field('service_duration', $service_id);
        $price_display = '$' . number_format($price, 2);
        $duration_display = $duration . ' ' . __('min', 'clever-booking');
        
        // Obtener categorías
        $categories = wp_get_post_terms($service_id, 'cb_service_category', array('fields' => 'names'));
        $category_text = !empty($categories) ? implode(', ', $categories) : '';
        
        // Obtener imagen destacada
        $thumbnail = '';
        if (has_post_thumbnail($service_id)) {
            $thumbnail_id = get_post_thumbnail_id($service_id);
            $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'medium');
            $thumbnail = $thumbnail[0];
        } else {
            // Imagen por defecto si no hay thumbnail
            $thumbnail = plugin_dir_url(dirname(dirname(__FILE__))) . 'public/img/default-service.png';
        }
        
        // Obtener un extracto corto
        $excerpt = '';
        if (has_excerpt($service_id)) {
            $excerpt = get_the_excerpt($service_id);
        } else {
            $content = get_post_field('post_content', $service_id);
            $excerpt = wp_trim_words($content, 20, '...');
        }
        
        // ID único para el modal
        $modal_id = 'cb-modal-list-' . $service_id . '-' . uniqid();
    ?>
    
    <div class="cb-service-item">
        <div class="cb-service-inner">
            <div class="cb-service-thumbnail">
                <a href="<?php echo esc_url($permalink); ?>">
                    <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($title); ?>">
                </a>
            </div>
            
            <div class="cb-service-content">
                <h3 class="cb-service-title">
                    <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                
                <?php if (!empty($category_text)) : ?>
                <div class="cb-service-category">
                    <span><?php echo esc_html($category_text); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="cb-service-meta">
                    <span class="cb-service-price"><?php echo esc_html($price_display); ?></span>
                    <span class="cb-service-duration"><?php echo esc_html($duration_display); ?></span>
                </div>
                
                <?php if ($layout === 'list' || $columns >= 3) : ?>
                <div class="cb-service-excerpt">
                    <p><?php echo esc_html($excerpt); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="cb-service-actions">
                    <a href="<?php echo esc_url($permalink); ?>" class="cb-button cb-button-small cb-view-service">
                        <?php echo esc_html__('Ver Detalles', 'clever-booking'); ?>
                    </a>
                    
                    <button type="button" class="cb-button cb-button-small cb-book-service" data-toggle="modal" data-target="#<?php echo esc_attr($modal_id); ?>">
                        <?php echo esc_html__('Reservar Ahora', 'clever-booking'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="<?php echo esc_attr($modal_id); ?>" class="cb-booking-modal">
        <div class="cb-booking-modal-content">
            <div class="cb-booking-modal-header">
                <h3><?php echo esc_html__('Reservar Servicio', 'clever-booking'); ?></h3>
                <span class="cb-booking-modal-close">&times;</span>
            </div>
            <div class="cb-booking-modal-body">
                <div class="cb-booking-service-info">
                    <h4><?php echo esc_html($title); ?></h4>
                    <?php if (!empty($price)): ?>
                        <div class="cb-booking-service-price">
                            <span><?php echo esc_html__('Precio:', 'clever-booking'); ?></span>
                            <strong><?php echo esc_html($price_display); ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($duration)): ?>
                        <div class="cb-booking-service-duration">
                            <span><?php echo esc_html__('Duración:', 'clever-booking'); ?></span>
                            <strong><?php echo esc_html($duration_display); ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
                
                <form id="cb-booking-form-<?php echo esc_attr($service_id); ?>-<?php echo uniqid(); ?>" class="cb-booking-form">
                    <?php wp_nonce_field('clever_booking_nonce', 'cb_nonce'); ?>
                    <input type="hidden" name="service_id" value="<?php echo esc_attr($service_id); ?>">
                    
                    <div class="cb-booking-form-row">
                        <div class="cb-booking-form-group">
                            <label for="cb-date-<?php echo esc_attr($service_id); ?>"><?php echo esc_html__('Fecha', 'clever-booking'); ?></label>
                            <input type="text" id="cb-date-<?php echo esc_attr($service_id); ?>" name="booking_date" class="cb-booking-datepicker" readonly required>
                        </div>
                        
                        <div class="cb-booking-form-group">
                            <label for="cb-time-<?php echo esc_attr($service_id); ?>"><?php echo esc_html__('Horario', 'clever-booking'); ?></label>
                            <select id="cb-time-<?php echo esc_attr($service_id); ?>" name="booking_time" required disabled>
                                <option value=""><?php echo esc_html__('Primero selecciona una fecha', 'clever-booking'); ?></option>
                            </select>
                            <div class="cb-booking-loading" style="display: none;">
                                <div class="cb-booking-spinner"></div>
                                <span><?php echo esc_html__('Cargando horarios...', 'clever-booking'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cb-booking-form-row">
                        <div class="cb-booking-form-group">
                            <label for="cb-name-<?php echo esc_attr($service_id); ?>"><?php echo esc_html__('Nombre', 'clever-booking'); ?></label>
                            <input type="text" id="cb-name-<?php echo esc_attr($service_id); ?>" name="customer_name" required>
                        </div>
                        
                        <div class="cb-booking-form-group">
                            <label for="cb-email-<?php echo esc_attr($service_id); ?>"><?php echo esc_html__('Email', 'clever-booking'); ?></label>
                            <input type="email" id="cb-email-<?php echo esc_attr($service_id); ?>" name="customer_email" required>
                        </div>
                    </div>
                    
                    <div class="cb-booking-form-row">
                        <div class="cb-booking-form-group">
                            <label for="cb-phone-<?php echo esc_attr($service_id); ?>"><?php echo esc_html__('Teléfono', 'clever-booking'); ?></label>
                            <input type="tel" id="cb-phone-<?php echo esc_attr($service_id); ?>" name="customer_phone" required>
                        </div>
                        
                        <div class="cb-booking-form-group">
                            <label for="cb-address-<?php echo esc_attr($service_id); ?>"><?php echo esc_html__('Dirección', 'clever-booking'); ?></label>
                            <textarea id="cb-address-<?php echo esc_attr($service_id); ?>" name="customer_address" required></textarea>
                        </div>
                    </div>
                    
                    <div class="cb-booking-form-group">
                        <label for="cb-notes-<?php echo esc_attr($service_id); ?>"><?php echo esc_html__('Notas', 'clever-booking'); ?></label>
                        <textarea id="cb-notes-<?php echo esc_attr($service_id); ?>" name="notes"></textarea>
                    </div>
                    
                    <div class="cb-booking-form-alerts"></div>
                    
                    <div class="cb-booking-form-actions">
                        <button type="button" class="cb-booking-cancel"><?php echo esc_html__('Cancelar', 'clever-booking'); ?></button>
                        <button type="submit" class="cb-booking-submit"><?php echo esc_html__('Confirmar Reserva', 'clever-booking'); ?></button>
                    </div>
                </form>
                
                <div class="cb-booking-success" style="display: none;">
                    <div class="cb-booking-success-message">
                        <h4><?php echo esc_html__('¡Reserva Exitosa!', 'clever-booking'); ?></h4>
                        <p><?php echo esc_html__('Tu reserva ha sido registrada correctamente. Hemos enviado un correo electrónico de confirmación con los detalles.', 'clever-booking'); ?></p>
                        <div class="cb-booking-reservation-details"></div>
                    </div>
                    <button type="button" class="cb-booking-close-modal"><?php echo esc_html__('Cerrar', 'clever-booking'); ?></button>
                </div>
            </div>
        </div>
    </div>
    
    <?php endforeach; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Para cada modal en la lista de servicios
    $('.cb-service-actions .cb-book-service').on('click', function() {
        var modalId = $(this).data('target');
        var modal = document.querySelector(modalId);
        
        if (modal) {
            modal.style.display = 'block';
            $('body').addClass('cb-modal-open');
            
            // Inicializar el datepicker
            var formId = $(modalId).find('form').attr('id');
            var serviceId = $(modalId).find('input[name="service_id"]').val();
            var dateInput = $(modalId).find('input[name="booking_date"]');
            var timeSelect = $(modalId).find('select[name="booking_time"]');
            var loadingIndicator = $(modalId).find('.cb-booking-loading');
            
            if ($.fn.datepicker && !dateInput.hasClass('hasDatepicker')) {
                dateInput.datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: 1,
                    maxDate: '+3M',
                    beforeShowDay: function(date) {
                        // Deshabilitar domingos (0) y sábados (6) si es necesario
                        // Esto podría ser configurable desde la administración
                        const day = date.getDay();
                        return [day !== 0, ''];
                    },
                    onSelect: function(dateText) {
                        loadAvailableTimeSlots(serviceId, dateText, timeSelect, loadingIndicator);
                    }
                });
            }
            
            // Cerrar modal
            $(modalId).find('.cb-booking-modal-close').on('click', function() {
                closeModal(modalId);
            });
            
            $(modalId).find('.cb-booking-cancel').on('click', function() {
                closeModal(modalId);
            });
            
            $(modalId).find('.cb-booking-close-modal').on('click', function() {
                closeModal(modalId);
            });
            
            // Cerrar modal al hacer clic fuera del contenido
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    closeModal(modalId);
                }
            });
            
            // Manejar envío del formulario
            $('#' + formId).on('submit', function(e) {
                e.preventDefault();
                
                // Validar formulario
                if (!validateForm(this)) {
                    return false;
                }
                
                // Desactivar botón de envío
                var submitBtn = $(this).find('.cb-booking-submit');
                submitBtn.prop('disabled', true).text(cb_data.messages.processing);
                
                // Mostrar mensaje de carga
                showAlert(modalId, 'info', cb_data.messages.submitting);
                
                // Preparar datos del formulario
                var formData = $(this).serialize() + '&action=cb_create_reservation';
                
                // Asegurar que el nonce esté incluido correctamente
                formData += '&nonce=' + cb_data.nonce;
                
                // Enviar petición AJAX
                $.ajax({
                    url: cb_data.ajax_url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Mostrar mensaje de éxito
                            showAlert(modalId, 'success', response.data.message || cb_data.messages.reservation_created);
                            
                            // Ocultar formulario y mostrar mensaje de éxito
                            $(modalId).find('.cb-booking-form').hide();
                            
                            // Generar detalles
                            var detailsHtml = '';
                            if (response.data.reservation_id) {
                                detailsHtml += '<div><strong>' + cb_data.messages.reservation_id + ':</strong> #' + response.data.reservation_id + '</div>';
                            }
                            if (response.data.service) {
                                detailsHtml += '<div><strong>' + 'Servicio:' + '</strong> ' + response.data.service + '</div>';
                            }
                            if (response.data.date) {
                                detailsHtml += '<div><strong>' + 'Fecha:' + '</strong> ' + response.data.date + '</div>';
                            }
                            if (response.data.time) {
                                detailsHtml += '<div><strong>' + 'Hora:' + '</strong> ' + response.data.time + '</div>';
                            }
                            
                            $(modalId).find('.cb-booking-reservation-details').html(detailsHtml);
                            $(modalId).find('.cb-booking-success').show();
                        } else {
                            // Mostrar error
                            showAlert(modalId, 'error', response.data.message || cb_data.messages.error_creating_reservation);
                            
                            // Habilitar botón de envío nuevamente
                            submitBtn.prop('disabled', false).text('Confirmar Reserva');
                        }
                    },
                    error: function() {
                        // Mostrar error de conexión
                        showAlert(modalId, 'error', cb_data.messages.error_connection);
                        
                        // Habilitar botón de envío nuevamente
                        submitBtn.prop('disabled', false).text('Confirmar Reserva');
                    }
                });
                
                return false;
            });
        }
    });
    
    // Función para cargar horarios disponibles
    function loadAvailableTimeSlots(serviceId, date, timeSelect, loadingIndicator) {
        if (!date) return;
        
        // Mostrar indicador de carga
        timeSelect.prop('disabled', true).html('<option value="">' + cb_data.messages.loading_times + '</option>');
        loadingIndicator.show();
        
        // Realizar petición AJAX
        $.ajax({
            url: cb_data.ajax_url,
            type: 'POST',
            data: {
                action: 'cb_get_available_slots',
                service_id: serviceId,
                date: date,
                nonce: cb_data.nonce
            },
            success: function(response) {
                if (response.success && response.data.slots && response.data.slots.length > 0) {
                    timeSelect.html('<option value="">' + cb_data.messages.select_time_option + '</option>');
                    
                    $.each(response.data.slots, function(i, slot) {
                        timeSelect.append('<option value="' + slot.value + '">' + slot.label + '</option>');
                    });
                    
                    timeSelect.prop('disabled', false);
                } else {
                    timeSelect.html('<option value="">' + cb_data.messages.no_slots_available + '</option>');
                    showAlert(timeSelect.closest('.cb-booking-modal'), 'warning', cb_data.messages.no_slots_available_alert);
                }
            },
            error: function() {
                timeSelect.html('<option value="">' + cb_data.messages.error_loading_times + '</option>');
                showAlert(timeSelect.closest('.cb-booking-modal'), 'error', cb_data.messages.error_connection);
            },
            complete: function() {
                loadingIndicator.hide();
            }
        });
    }
    
    // Función para cerrar el modal
    function closeModal(modalId) {
        $(modalId).css('display', 'none');
        $('body').removeClass('cb-modal-open');
    }
    
    // Función para mostrar alertas
    function showAlert(modalId, type, message) {
        var alertsContainer = $(modalId).find('.cb-booking-form-alerts');
        var alertClass = 'cb-booking-alert cb-booking-alert-' + type;
        
        alertsContainer.html('<div class="' + alertClass + '">' + message + '</div>');
        
        // Eliminar alerta después de un tiempo si es de éxito
        if (type === 'success') {
            setTimeout(function() {
                alertsContainer.html('');
            }, 5000);
        }
    }
    
    // Función para validar el formulario
    function validateForm(form) {
        var requiredFields = $(form).find('[required]');
        var isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).addClass('cb-invalid');
                isValid = false;
            } else {
                $(this).removeClass('cb-invalid');
            }
        });
        
        if (!isValid) {
            showAlert($(form).closest('.cb-booking-modal'), 'error', 'Por favor, completa todos los campos obligatorios.');
            return false;
        }
        
        // Validar email
        var emailField = $(form).find('input[type="email"]');
        if (emailField.length && emailField.val() && !validateEmail(emailField.val())) {
            emailField.addClass('cb-invalid');
            showAlert($(form).closest('.cb-booking-modal'), 'error', cb_data.messages.invalid_email);
            return false;
        }
        
        return true;
    }
    
    // Función para validar email
    function validateEmail(email) {
        var re = /\S+@\S+\.\S+/;
        return re.test(email);
    }
});
</script> 
<?php
/**
 * Widget de Elementor para mostrar un botón de reserva que abre un modal
 *
 * @link       
 * @since      1.0.0
 *
 * @package    Clever_Booking
 * @subpackage Clever_Booking/public/elementor
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

/**
 * Widget de botón de reserva para Elementor
 */
class Clever_Booking_Button_Widget extends \Elementor\Widget_Base {
    
    /**
     * Obtener el nombre del widget
     *
     * @since  1.0.0
     * @return string Nombre del widget.
     */
    public function get_name() {
        return 'clever_booking_button';
    }
    
    /**
     * Obtener el título del widget
     *
     * @since  1.0.0
     * @return string Título del widget.
     */
    public function get_title() {
        return __('Botón de Reserva', 'clever-booking');
    }
    
    /**
     * Obtener el icono del widget
     *
     * @since  1.0.0
     * @return string Nombre del icono.
     */
    public function get_icon() {
        return 'eicon-button';
    }
    
    /**
     * Obtener las categorías del widget
     *
     * @since  1.0.0
     * @return array Lista de categorías.
     */
    public function get_categories() {
        return ['clever-booking'];
    }
    
    /**
     * Obtener las palabras clave del widget
     *
     * @since  1.0.0
     * @return array Lista de palabras clave.
     */
    public function get_keywords() {
        return ['reserva', 'booking', 'botón', 'modal', 'cta'];
    }
    
    /**
     * Registrar los controles del widget
     *
     * @since  1.0.0
     */
    protected function register_controls() {
        // Sección de contenido
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Contenido', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'button_text',
            [
                'label' => __('Texto del Botón', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Reservar Ahora', 'clever-booking'),
                'placeholder' => __('Texto del botón', 'clever-booking'),
            ]
        );
        
        $this->add_control(
            'modal_title',
            [
                'label' => __('Título del Modal', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Reservar Servicio', 'clever-booking'),
                'placeholder' => __('Título del modal', 'clever-booking'),
            ]
        );
        
        $this->end_controls_section();
        
        // Sección de estilo del botón
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => __('Estilo del Botón', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'button_text_color',
            [
                'label' => __('Color de Texto', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .cb-booking-button' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'button_background_color',
            [
                'label' => __('Color de Fondo', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#4a6bff',
                'selectors' => [
                    '{{WRAPPER}} .cb-booking-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .cb-booking-button',
            ]
        );
        
        $this->add_control(
            'button_border_radius',
            [
                'label' => __('Borde Redondeado', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-booking-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => 4,
                    'right' => 4,
                    'bottom' => 4,
                    'left' => 4,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );
        
        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Espaciado', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-booking-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => 12,
                    'right' => 24,
                    'bottom' => 12,
                    'left' => 24,
                    'unit' => 'px',
                    'isLinked' => false,
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Sección de estilo del modal
        $this->start_controls_section(
            'section_modal_style',
            [
                'label' => __('Estilo del Modal', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'modal_background_color',
            [
                'label' => __('Color de Fondo', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '.cb-booking-modal-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'modal_border_radius',
            [
                'label' => __('Borde Redondeado', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '.cb-booking-modal-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => 8,
                    'right' => 8,
                    'bottom' => 8,
                    'left' => 8,
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Renderizar el widget
     *
     * @since  1.0.0
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Obtener el ID del servicio actual
        $service_id = get_the_ID();
        
        // Solo mostrar si estamos en un post de tipo servicio
        if (get_post_type($service_id) !== 'cb_service') {
            echo '<div class="cb-error">' . __('Este widget solo debe usarse en páginas de servicio.', 'clever-booking') . '</div>';
            return;
        }
        
        // Obtener datos del servicio
        $service_title = get_the_title($service_id);
        $price = get_field('service_price', $service_id);
        $duration = get_field('service_duration', $service_id);
        
        // Comprobar si se pueden obtener los datos del servicio
        if (empty($price) || empty($duration)) {
            echo '<div class="cb-warning">' . __('Advertencia: Este servicio no tiene configurado precio o duración. El formulario de reserva podría no funcionar correctamente.', 'clever-booking') . '</div>';
        }
        
        // ID único para el modal
        $modal_id = 'cb-modal-' . $service_id . '-' . uniqid();
        
        // Estructura del botón y el modal
        ?>
        <div class="cb-booking-button-container">
            <button type="button" class="cb-booking-button" data-toggle="modal" data-target="#<?php echo esc_attr($modal_id); ?>">
                <?php echo esc_html($settings['button_text']); ?>
            </button>
        </div>
        
        <!-- Modal -->
        <div id="<?php echo esc_attr($modal_id); ?>" class="cb-booking-modal">
            <div class="cb-booking-modal-content">
                <div class="cb-booking-modal-header">
                    <h3><?php echo esc_html($settings['modal_title']); ?></h3>
                    <span class="cb-booking-modal-close">&times;</span>
                </div>
                <div class="cb-booking-modal-body">
                    <div class="cb-booking-service-info">
                        <h4><?php echo esc_html($service_title); ?></h4>
                        <?php if (!empty($price)): ?>
                            <div class="cb-booking-service-price">
                                <span><?php echo esc_html__('Precio:', 'clever-booking'); ?></span>
                                <strong><?php echo esc_html('$' . number_format($price, 2)); ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($duration)): ?>
                            <div class="cb-booking-service-duration">
                                <span><?php echo esc_html__('Duración:', 'clever-booking'); ?></span>
                                <strong><?php echo esc_html($duration . ' ' . __('minutos', 'clever-booking')); ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form id="cb-booking-form-<?php echo esc_attr($service_id); ?>" class="cb-booking-form">
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
        
        <style>
            /* Estilos para el botón */
            .cb-booking-button {
                display: inline-block;
                cursor: pointer;
                border: none;
                font-weight: 600;
                transition: all 0.3s ease;
                text-align: center;
            }
            
            /* Estilos para el modal */
            .cb-booking-modal {
                display: none;
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0,0,0,0.5);
            }
            
            .cb-booking-modal-content {
                position: relative;
                margin: 5% auto;
                width: 90%;
                max-width: 600px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                animation: cbModalFadeIn 0.3s;
            }
            
            @keyframes cbModalFadeIn {
                from {opacity: 0; transform: translateY(-20px);}
                to {opacity: 1; transform: translateY(0);}
            }
            
            .cb-booking-modal-header {
                padding: 15px 20px;
                border-bottom: 1px solid #e0e0e0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .cb-booking-modal-header h3 {
                margin: 0;
                font-size: 20px;
            }
            
            .cb-booking-modal-close {
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }
            
            .cb-booking-modal-body {
                padding: 20px;
            }
            
            .cb-booking-service-info {
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e0e0e0;
            }
            
            .cb-booking-service-info h4 {
                margin-top: 0;
                margin-bottom: 10px;
            }
            
            .cb-booking-service-price,
            .cb-booking-service-duration {
                display: inline-block;
                margin-right: 20px;
            }
            
            /* Estilos del formulario */
            .cb-booking-form-row {
                display: flex;
                flex-wrap: wrap;
                margin: 0 -10px 15px;
            }
            
            .cb-booking-form-group {
                flex: 1;
                min-width: 200px;
                padding: 0 10px;
                margin-bottom: 15px;
            }
            
            .cb-booking-form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: 500;
            }
            
            .cb-booking-form-group input[type="text"],
            .cb-booking-form-group input[type="email"],
            .cb-booking-form-group input[type="tel"],
            .cb-booking-form-group select,
            .cb-booking-form-group textarea {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            
            .cb-booking-form-group textarea {
                height: 80px;
                resize: vertical;
            }
            
            .cb-booking-form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                margin-top: 20px;
            }
            
            .cb-booking-cancel {
                background-color: #f2f2f2;
                color: #333;
                border: none;
                padding: 10px 15px;
                border-radius: 4px;
                cursor: pointer;
            }
            
            .cb-booking-submit {
                background-color: #4a6bff;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 4px;
                cursor: pointer;
            }
            
            .cb-booking-loading {
                display: flex;
                align-items: center;
                margin-top: 5px;
                font-size: 12px;
            }
            
            .cb-booking-spinner {
                width: 16px;
                height: 16px;
                border: 2px solid rgba(0,0,0,0.1);
                border-top-color: #4a6bff;
                border-radius: 50%;
                margin-right: 8px;
                animation: cbSpinner 0.8s linear infinite;
            }
            
            @keyframes cbSpinner {
                to {transform: rotate(360deg);}
            }
            
            /* Estilos para mensajes */
            .cb-booking-form-alerts {
                margin: 15px 0;
            }
            
            .cb-booking-alert {
                padding: 10px 15px;
                border-radius: 4px;
                margin-bottom: 10px;
            }
            
            .cb-booking-alert-error {
                background-color: #ffe0e0;
                color: #d32f2f;
                border: 1px solid #ffd0d0;
            }
            
            .cb-booking-alert-success {
                background-color: #e0f2e9;
                color: #2e7d32;
                border: 1px solid #d0e9db;
            }
            
            .cb-booking-alert-warning {
                background-color: #fff3e0;
                color: #ef6c00;
                border: 1px solid #ffe0b2;
            }
            
            .cb-booking-alert-info {
                background-color: #e3f2fd;
                color: #0277bd;
                border: 1px solid #bbdefb;
            }
            
            /* Estilos para el mensaje de éxito */
            .cb-booking-success {
                text-align: center;
                padding: 20px;
            }
            
            .cb-booking-success-message h4 {
                color: #2e7d32;
                margin-top: 0;
            }
            
            .cb-booking-reservation-details {
                background-color: #f5f5f5;
                padding: 15px;
                border-radius: 4px;
                margin: 15px 0;
                text-align: left;
            }
            
            .cb-booking-close-modal {
                background-color: #4a6bff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 4px;
                cursor: pointer;
                margin-top: 15px;
            }
            
            /* Responsive */
            @media (max-width: 600px) {
                .cb-booking-form-row {
                    flex-direction: column;
                }
                
                .cb-booking-modal-content {
                    width: 95%;
                    margin: 10% auto;
                }
            }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Modal
            const modalId = '<?php echo esc_js($modal_id); ?>';
            const modal = document.getElementById(modalId);
            const btn = $('[data-target="#' + modalId + '"]');
            const closeBtn = modal.querySelector('.cb-booking-modal-close');
            const cancelBtn = modal.querySelector('.cb-booking-cancel');
            const closeModalBtn = modal.querySelector('.cb-booking-close-modal');
            
            // Abrir modal al hacer clic en el botón
            btn.on('click', function() {
                modal.style.display = 'block';
                $('body').addClass('cb-modal-open');
            });
            
            // Cerrar modal
            function closeModal() {
                modal.style.display = 'none';
                $('body').removeClass('cb-modal-open');
            }
            
            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            closeModalBtn.addEventListener('click', closeModal);
            
            // Cerrar modal al hacer clic fuera del contenido
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            });
            
            // Inicializar datepicker
            const serviceId = <?php echo esc_js($service_id); ?>;
            const dateInput = $('#cb-date-' + serviceId);
            const timeSelect = $('#cb-time-' + serviceId);
            const loadingIndicator = modal.querySelector('.cb-booking-loading');
            
            if ($.fn.datepicker) {
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
                        loadAvailableTimeSlots(serviceId, dateText);
                    }
                });
            }
            
            // Cargar horarios disponibles
            function loadAvailableTimeSlots(serviceId, date) {
                if (!date) return;
                
                // Mostrar indicador de carga
                timeSelect.prop('disabled', true).html('<option value="">' + cb_data.messages.loading_times + '</option>');
                $(loadingIndicator).show();
                
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
                            showAlert('warning', cb_data.messages.no_slots_available_alert);
                        }
                    },
                    error: function() {
                        timeSelect.html('<option value="">' + cb_data.messages.error_loading_times + '</option>');
                        showAlert('error', cb_data.messages.error_connection);
                    },
                    complete: function() {
                        $(loadingIndicator).hide();
                    }
                });
            }
            
            // Mostrar alertas
            function showAlert(type, message) {
                const alertsContainer = modal.querySelector('.cb-booking-form-alerts');
                const alertClass = 'cb-booking-alert cb-booking-alert-' + type;
                
                $(alertsContainer).html('<div class="' + alertClass + '">' + message + '</div>');
                
                // Eliminar alerta después de un tiempo si es de éxito
                if (type === 'success') {
                    setTimeout(function() {
                        $(alertsContainer).html('');
                    }, 5000);
                }
            }
            
            // Manejar envío del formulario
            $('#cb-booking-form-' + serviceId).on('submit', function(e) {
                e.preventDefault();
                
                // Validar formulario
                if (!validateForm(this)) {
                    return false;
                }
                
                // Desactivar botón de envío
                const submitBtn = $(this).find('.cb-booking-submit');
                submitBtn.prop('disabled', true).text(cb_data.messages.processing);
                
                // Mostrar mensaje de carga
                showAlert('info', cb_data.messages.submitting);
                
                // Preparar datos del formulario
                const formData = $(this).serialize() + '&action=cb_create_reservation';
                
                // Asegurar que el nonce esté incluido correctamente
                const formDataWithNonce = formData + '&nonce=' + cb_data.nonce;
                
                // Enviar petición AJAX
                $.ajax({
                    url: cb_data.ajax_url,
                    type: 'POST',
                    data: formDataWithNonce,
                    success: function(response) {
                        if (response.success) {
                            // Mostrar mensaje de éxito
                            showAlert('success', response.data.message || cb_data.messages.reservation_created);
                            
                            // Ocultar formulario y mostrar mensaje de éxito
                            $('.cb-booking-form', modal).hide();
                            
                            // Generar detalles
                            let detailsHtml = '';
                            if (response.data.reservation_id) {
                                detailsHtml += '<div><strong>' + cb_data.messages.reservation_id + ':</strong> #' + response.data.reservation_id + '</div>';
                            }
                            if (response.data.service) {
                                detailsHtml += '<div><strong>' + <?php echo json_encode(__('Servicio:', 'clever-booking')); ?> + '</strong> ' + response.data.service + '</div>';
                            }
                            if (response.data.date) {
                                detailsHtml += '<div><strong>' + <?php echo json_encode(__('Fecha:', 'clever-booking')); ?> + '</strong> ' + response.data.date + '</div>';
                            }
                            if (response.data.time) {
                                detailsHtml += '<div><strong>' + <?php echo json_encode(__('Hora:', 'clever-booking')); ?> + '</strong> ' + response.data.time + '</div>';
                            }
                            
                            $('.cb-booking-reservation-details', modal).html(detailsHtml);
                            $('.cb-booking-success', modal).show();
                        } else {
                            // Mostrar error
                            showAlert('error', response.data.message || cb_data.messages.error_creating_reservation);
                            
                            // Habilitar botón de envío nuevamente
                            submitBtn.prop('disabled', false).text(<?php echo json_encode(__('Confirmar Reserva', 'clever-booking')); ?>);
                        }
                    },
                    error: function() {
                        // Mostrar error de conexión
                        showAlert('error', cb_data.messages.error_connection);
                        
                        // Habilitar botón de envío nuevamente
                        submitBtn.prop('disabled', false).text(<?php echo json_encode(__('Confirmar Reserva', 'clever-booking')); ?>);
                    }
                });
                
                return false;
            });
            
            // Validar formulario
            function validateForm(form) {
                const requiredFields = $(form).find('[required]');
                let isValid = true;
                
                requiredFields.each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('cb-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('cb-invalid');
                    }
                });
                
                if (!isValid) {
                    showAlert('error', <?php echo json_encode(__('Por favor, completa todos los campos obligatorios.', 'clever-booking')); ?>);
                    return false;
                }
                
                // Validar email
                const emailField = $(form).find('input[type="email"]');
                if (emailField.length && emailField.val() && !validateEmail(emailField.val())) {
                    emailField.addClass('cb-invalid');
                    showAlert('error', cb_data.messages.invalid_email);
                    return false;
                }
                
                return true;
            }
            
            // Validar email
            function validateEmail(email) {
                const re = /\S+@\S+\.\S+/;
                return re.test(email);
            }
        });
        </script>
        <?php
    }
} 
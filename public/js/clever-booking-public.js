/**
 * Javascript para el frontend de Clever Booking
 *
 * @link       
 * @since      1.0.0
 */

(function($) {
    'use strict';

    // Inicializar cuando el DOM esté cargado
    $(document).ready(function() {
        // Inicializar formulario de reserva si existe
        if ($('#clever-booking-form').length > 0) {
            initBookingForm();
        }
    });

    /**
     * Inicializar el formulario de reserva
     */
    function initBookingForm() {
        // Inicializar datepicker para la selección de fecha
        initDatePicker();
        
        // Manejar navegación entre pasos
        handleStepsNavigation();
        
        // Manejar cambio de fecha para cargar horarios disponibles
        handleDateChange();
        
        // Manejar envío del formulario
        handleFormSubmission();
        
        // Actualizar resumen en tiempo real
        handleSummaryUpdate();
    }

    /**
     * Inicializar el datepicker
     */
    function initDatePicker() {
        if ($.fn.datepicker) {
            $('.cb-datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 1, // Desde mañana
                maxDate: '+3M', // Hasta 3 meses en el futuro
                beforeShowDay: function(date) {
                    // Deshabilitar domingos (0) y sábados (6) si es necesario
                    // Esto podría ser configurable desde la administración
                    var day = date.getDay();
                    return [day !== 0, ''];
                },
                onSelect: function(dateText) {
                    // Cuando se selecciona una fecha, cargar horarios disponibles
                    loadAvailableTimeSlots();
                }
            });
        }
    }

    /**
     * Manejar la navegación entre pasos del formulario
     */
    function handleStepsNavigation() {
        // Botones de siguiente paso
        $('.cb-next-step').on('click', function() {
            var currentStep = $(this).closest('.cb-form-step');
            var nextStepNum = $(this).data('next');
            
            // Validar el paso actual antes de continuar
            if (validateStep(currentStep)) {
                goToStep(nextStepNum);
            }
        });
        
        // Botones de paso anterior
        $('.cb-prev-step').on('click', function() {
            var prevStepNum = $(this).data('prev');
            goToStep(prevStepNum);
        });
        
        // Navegación a través de los indicadores de paso
        $('.cb-step').on('click', function() {
            var stepNum = $(this).data('step');
            var currentStepNum = $('.cb-form-step.cb-active').data('step');
            
            // Solo permitir navegar a pasos ya visitados o al siguiente
            if (stepNum < currentStepNum || stepNum === currentStepNum + 1) {
                // Validar el paso actual antes de continuar
                if (stepNum > currentStepNum) {
                    if (validateStep($('.cb-form-step-' + currentStepNum))) {
                        goToStep(stepNum);
                    }
                } else {
                    goToStep(stepNum);
                }
            }
        });
    }

    /**
     * Ir a un paso específico
     */
    function goToStep(stepNum) {
        // Ocultar todos los pasos
        $('.cb-form-step').removeClass('cb-active');
        
        // Mostrar el paso seleccionado
        $('.cb-form-step-' + stepNum).addClass('cb-active');
        
        // Actualizar indicadores de paso
        $('.cb-step').removeClass('cb-step-active');
        $('.cb-step-' + stepNum).addClass('cb-step-active');
        
        // Si es el último paso, actualizar resumen
        if (stepNum === 4) {
            updateSummary();
        }
        
        // Desplazarse al inicio del formulario
        $('html, body').animate({
            scrollTop: $('.clever-booking-form-container').offset().top - 50
        }, 400);
    }

    /**
     * Validar un paso del formulario
     */
    function validateStep(step) {
        var isValid = true;
        
        // Obtener número de paso
        var stepNum = step.data('step');
        
        // Validaciones específicas por paso
        switch(stepNum) {
            case 1:
                // Validar selección de servicio y fecha
                var serviceId = $('select[name="service_id"]').val() || $('input[name="service_id"]').val();
                var date = $('#cb-date').val();
                
                if (!serviceId) {
                    showAlert('error', cb_data.messages.select_service);
                    isValid = false;
                } else if (!date) {
                    showAlert('error', cb_data.messages.select_date);
                    isValid = false;
                }
                break;
                
            case 2:
                // Validar selección de horario
                var time = $('#cb-time').val();
                
                if (!time) {
                    showAlert('error', cb_data.messages.select_time);
                    isValid = false;
                }
                break;
                
            case 3:
                // Validar formulario de datos del cliente
                var name = $('#cb-name').val();
                var email = $('#cb-email').val();
                var phone = $('#cb-phone').val();
                var address = $('#cb-address').val();
                
                if (!name) {
                    showAlert('error', cb_data.messages.enter_name);
                    isValid = false;
                } else if (!email) {
                    showAlert('error', cb_data.messages.enter_email);
                    isValid = false;
                } else if (!validateEmail(email)) {
                    showAlert('error', cb_data.messages.invalid_email);
                    isValid = false;
                } else if (!phone) {
                    showAlert('error', cb_data.messages.enter_phone);
                    isValid = false;
                } else if (!address) {
                    showAlert('error', cb_data.messages.enter_address);
                    isValid = false;
                }
                break;
        }
        
        return isValid;
    }

    /**
     * Manejar cambio de fecha para cargar horarios disponibles
     */
    function handleDateChange() {
        $('#cb-date').on('change', function() {
            loadAvailableTimeSlots();
        });
        
        // Si hay un cambio de servicio, reiniciar selección de hora
        $('select[name="service_id"]').on('change', function() {
            if ($('#cb-date').val() !== '') {
                loadAvailableTimeSlots();
            }
        });
    }

    /**
     * Cargar horarios disponibles
     */
    function loadAvailableTimeSlots() {
        var serviceId = $('select[name="service_id"]').val() || $('input[name="service_id"]').val();
        var date = $('#cb-date').val();
        
        if (serviceId && date) {
            // Mostrar mensaje de carga
            $('#cb-time-loading').show();
            $('#cb-time').prop('disabled', true);
            
            // Restablecer opciones
            $('#cb-time').html('<option value="">' + cb_data.messages.loading_times + '</option>');
            
            // Realizar solicitud AJAX
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
                    if (response.success) {
                        // Actualizar opciones de horario
                        $('#cb-time').html('<option value="">' + cb_data.messages.select_time_option + '</option>');
                        
                        if (response.data.slots && response.data.slots.length > 0) {
                            $.each(response.data.slots, function(i, slot) {
                                $('#cb-time').append('<option value="' + slot.value + '">' + slot.label + '</option>');
                            });
                        } else {
                            $('#cb-time').html('<option value="">' + cb_data.messages.no_slots_available + '</option>');
                            showAlert('info', cb_data.messages.no_slots_available_alert);
                        }
                    } else {
                        // Mostrar error
                        $('#cb-time').html('<option value="">' + cb_data.messages.error_loading_times + '</option>');
                        showAlert('error', response.data.message || cb_data.messages.error_loading_times);
                    }
                },
                error: function() {
                    // Mostrar error de conexión
                    $('#cb-time').html('<option value="">' + cb_data.messages.error_loading_times + '</option>');
                    showAlert('error', cb_data.messages.error_connection);
                },
                complete: function() {
                    // Ocultar mensaje de carga
                    $('#cb-time-loading').hide();
                    $('#cb-time').prop('disabled', false);
                }
            });
        }
    }

    /**
     * Manejar envío del formulario
     */
    function handleFormSubmission() {
        $('#clever-booking-form').on('submit', function(e) {
            e.preventDefault();
            
            // Validar todo el formulario antes de enviar
            if (!validateForm()) {
                return false;
            }
            
            // Recopilar datos del formulario
            var formData = $(this).serialize();
            
            // Agregar acción para el endpoint AJAX
            formData += '&action=cb_create_reservation';
            
            // Asegurar que el nonce esté incluido correctamente
            // Usar el nonce de cb_data en lugar del campo del formulario para garantizar consistencia
            formData += '&nonce=' + cb_data.nonce;
            
            // Deshabilitar botón de envío
            $('.cb-submit-booking').prop('disabled', true).text(cb_data.messages.processing);
            
            // Mostrar mensaje de carga
            showAlert('info', cb_data.messages.submitting);
            
            // Realizar solicitud AJAX
            $.ajax({
                url: cb_data.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Mostrar mensaje de éxito
                        showAlert('success', response.data.message || cb_data.messages.reservation_created);
                        
                        // Mostrar paso de agradecimiento con detalles de la reserva
                        showThanksStep(response.data);
                    } else {
                        // Mostrar error
                        showAlert('error', response.data.message || cb_data.messages.error_creating_reservation);
                        
                        // Habilitar botón de envío nuevamente
                        $('.cb-submit-booking').prop('disabled', false).text(cb_data.messages.confirm_reservation);
                    }
                },
                error: function() {
                    // Mostrar error de conexión
                    showAlert('error', cb_data.messages.error_connection);
                    
                    // Habilitar botón de envío nuevamente
                    $('.cb-submit-booking').prop('disabled', false).text(cb_data.messages.confirm_reservation);
                }
            });
            
            return false;
        });
    }

    /**
     * Validar formulario completo
     */
    function validateForm() {
        // Validar cada paso por separado
        for (var i = 1; i <= 3; i++) {
            if (!validateStep($('.cb-form-step-' + i))) {
                goToStep(i);
                return false;
            }
        }
        
        return true;
    }

    /**
     * Mostrar paso de agradecimiento
     */
    function showThanksStep(data) {
        // Ocultar todos los pasos
        $('.cb-form-step').removeClass('cb-active');
        
        // Mostrar paso de agradecimiento
        $('.cb-form-step-thanks').addClass('cb-active');
        
        // Actualizar detalles de la reserva en el paso de agradecimiento
        if (data.reservation_id) {
            var detailsHtml = '<div class="cb-thanks-reservation-id">';
            detailsHtml += '<strong>' + cb_data.messages.reservation_id + ':</strong> #' + data.reservation_id;
            detailsHtml += '</div>';
            
            $('.cb-thanks-details').html(detailsHtml);
        }
        
        // Desplazarse al inicio del formulario
        $('html, body').animate({
            scrollTop: $('.clever-booking-form-container').offset().top - 50
        }, 400);
    }

    /**
     * Actualizar resumen en tiempo real
     */
    function handleSummaryUpdate() {
        // Actualizar resumen cuando cambian los datos del formulario
        $('#cb-service, input[name="service_id"], #cb-date, #cb-time, #cb-name, #cb-email, #cb-phone, #cb-address, #cb-notes').on('change', function() {
            updateSummary();
        });
    }

    /**
     * Actualizar resumen del paso 4
     */
    function updateSummary() {
        // Servicio
        var serviceId = $('select[name="service_id"]').val() || $('input[name="service_id"]').val();
        var serviceName = '';
        
        if ($('select[name="service_id"]').length > 0) {
            serviceName = $('select[name="service_id"] option:selected').text();
        } else {
            serviceName = $('.cb-service-info strong').text();
        }
        
        $('#summary-service').text(serviceName || '-');
        
        // Precio
        var price = 0;
        if ($('select[name="service_id"]').length > 0) {
            price = $('select[name="service_id"] option:selected').data('price');
        } else {
            price = $('input[name="service_id"]').data('price');
        }
        
        if (price) {
            $('#summary-price').text('$' + parseFloat(price).toFixed(2));
        } else {
            $('#summary-price').text('-');
        }
        
        // Fecha
        var date = $('#cb-date').val();
        if (date) {
            // Formatear fecha para mostrar
            var dateObj = new Date(date);
            var formattedDate = dateObj.toLocaleDateString(cb_data.locale || 'es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            $('#summary-date').text(formattedDate);
        } else {
            $('#summary-date').text('-');
        }
        
        // Hora
        var time = $('#cb-time').val();
        var timeText = $('#cb-time option:selected').text();
        if (time) {
            $('#summary-time').text(timeText);
        } else {
            $('#summary-time').text('-');
        }
        
        // Datos del cliente
        $('#summary-name').text($('#cb-name').val() || '-');
        $('#summary-email').text($('#cb-email').val() || '-');
        $('#summary-phone').text($('#cb-phone').val() || '-');
        $('#summary-address').text($('#cb-address').val() || '-');
        
        // Notas
        var notes = $('#cb-notes').val();
        if (notes) {
            $('#summary-notes').text(notes);
            $('#summary-notes-container').show();
        } else {
            $('#summary-notes').text('-');
            $('#summary-notes-container').hide();
        }
    }

    /**
     * Mostrar una alerta
     */
    function showAlert(type, message) {
        // Eliminar alertas existentes
        $('.cb-alerts').html('');
        
        // Crear nueva alerta
        var alertClass = 'cb-message cb-' + type;
        var alertHtml = '<div class="' + alertClass + '">' + message + '</div>';
        
        // Agregar alerta al contenedor
        $('.cb-alerts').html(alertHtml);
        
        // Desplazarse hasta la alerta
        $('html, body').animate({
            scrollTop: $('.cb-alerts').offset().top - 80
        }, 400);
        
        // Ocultar alerta después de 5 segundos si es de éxito
        if (type === 'success') {
            setTimeout(function() {
                $('.cb-alerts').html('');
            }, 5000);
        }
    }

    /**
     * Validar formato de email
     */
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

})(jQuery); 
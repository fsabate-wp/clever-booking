<?php
/**
 * Vista de configuración del plugin
 *
 * @link       
 * @since      1.0.0
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

// Verificar si se han guardado los ajustes
$settings_saved = isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true';
?>

<div class="wrap">
    <h1><?php echo esc_html__('Configuración de Clever Booking', 'clever-booking'); ?></h1>
    
    <?php if ($settings_saved) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html__('La configuración se ha guardado correctamente.', 'clever-booking'); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="cb-admin-tabs">
        <div class="cb-tab-nav">
            <a href="#general" class="cb-tab-link active" data-tab="general">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php echo esc_html__('General', 'clever-booking'); ?>
            </a>
            <a href="#emails" class="cb-tab-link" data-tab="emails">
                <span class="dashicons dashicons-email"></span>
                <?php echo esc_html__('Emails', 'clever-booking'); ?>
            </a>
            <a href="#booking" class="cb-tab-link" data-tab="booking">
                <span class="dashicons dashicons-calendar-alt"></span>
                <?php echo esc_html__('Reservas', 'clever-booking'); ?>
            </a>
            <a href="#appearance" class="cb-tab-link" data-tab="appearance">
                <span class="dashicons dashicons-admin-appearance"></span>
                <?php echo esc_html__('Apariencia', 'clever-booking'); ?>
            </a>
            <a href="#advanced" class="cb-tab-link" data-tab="advanced">
                <span class="dashicons dashicons-admin-tools"></span>
                <?php echo esc_html__('Avanzado', 'clever-booking'); ?>
            </a>
        </div>
        
        <div class="cb-tab-content">
            <form method="post" action="options.php">
                <?php settings_fields('clever_booking_settings'); ?>
                
                <!-- Pestaña General -->
                <div id="general" class="cb-tab-pane active">
                    <div class="cb-settings-section">
                        <h2><?php echo esc_html__('Ajustes Generales', 'clever-booking'); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="cb_company_name"><?php echo esc_html__('Nombre de la Empresa', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="cb_company_name" id="cb_company_name" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_company_name')); ?>" placeholder="<?php echo esc_attr__('Mi Empresa', 'clever-booking'); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_company_email"><?php echo esc_html__('Email de la Empresa', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="email" name="cb_company_email" id="cb_company_email" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_company_email')); ?>" placeholder="<?php echo esc_attr__('info@miempresa.com', 'clever-booking'); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_company_phone"><?php echo esc_html__('Teléfono de la Empresa', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="cb_company_phone" id="cb_company_phone" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_company_phone')); ?>" placeholder="<?php echo esc_attr__('+34 123 456 789', 'clever-booking'); ?>">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Pestaña Emails -->
                <div id="emails" class="cb-tab-pane">
                    <div class="cb-settings-section">
                        <h2><?php echo esc_html__('Configuración de Emails', 'clever-booking'); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="cb_admin_email"><?php echo esc_html__('Email de Administración', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="email" name="cb_admin_email" id="cb_admin_email" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_admin_email')); ?>" 
                                           placeholder="<?php echo esc_attr(get_option('admin_email')); ?>">
                                    <p class="description">
                                        <?php echo esc_html__('Email donde se enviarán las notificaciones de nuevas reservas. Si está vacío, se usará el email del administrador de WordPress.', 'clever-booking'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_email_from_name"><?php echo esc_html__('Nombre del Remitente', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="cb_email_from_name" id="cb_email_from_name" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_email_from_name')); ?>" 
                                           placeholder="<?php echo esc_attr(get_bloginfo('name')); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_email_from_address"><?php echo esc_html__('Email del Remitente', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="email" name="cb_email_from_address" id="cb_email_from_address" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_email_from_address')); ?>" 
                                           placeholder="<?php echo esc_attr(get_option('admin_email')); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_admin_notification_subject"><?php echo esc_html__('Asunto de Notificación para Admin', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="cb_admin_notification_subject" id="cb_admin_notification_subject" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_admin_notification_subject')); ?>" 
                                           placeholder="<?php echo esc_attr__('Nueva reserva en {site_name}', 'clever-booking'); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_customer_notification_subject"><?php echo esc_html__('Asunto de Notificación para Cliente', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="cb_customer_notification_subject" id="cb_customer_notification_subject" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_customer_notification_subject')); ?>" 
                                           placeholder="<?php echo esc_attr__('Confirmación de tu reserva en {site_name}', 'clever-booking'); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_reminder_notification_subject"><?php echo esc_html__('Asunto de Recordatorio', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="cb_reminder_notification_subject" id="cb_reminder_notification_subject" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_reminder_notification_subject')); ?>" 
                                           placeholder="<?php echo esc_attr__('Recordatorio de tu próxima reserva en {site_name}', 'clever-booking'); ?>">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Pestaña Reservas -->
                <div id="booking" class="cb-tab-pane">
                    <div class="cb-settings-section">
                        <h2><?php echo esc_html__('Configuración de Reservas', 'clever-booking'); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="cb_min_time_before_booking"><?php echo esc_html__('Tiempo mínimo antes de reserva', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="cb_min_time_before_booking" id="cb_min_time_before_booking" class="small-text"
                                           value="<?php echo esc_attr(get_option('cb_min_time_before_booking', 24)); ?>" min="0" step="1">
                                    <span><?php echo esc_html__('horas', 'clever-booking'); ?></span>
                                    <p class="description">
                                        <?php echo esc_html__('Tiempo mínimo necesario antes de permitir una reserva (en horas).', 'clever-booking'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_max_days_advance_booking"><?php echo esc_html__('Días máximos para reserva anticipada', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="cb_max_days_advance_booking" id="cb_max_days_advance_booking" class="small-text"
                                           value="<?php echo esc_attr(get_option('cb_max_days_advance_booking', 90)); ?>" min="1" step="1">
                                    <span><?php echo esc_html__('días', 'clever-booking'); ?></span>
                                    <p class="description">
                                        <?php echo esc_html__('Cuántos días en el futuro pueden hacerse reservas.', 'clever-booking'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php echo esc_html__('Días de la semana disponibles', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <?php
                                    $days_of_week = array(
                                        'monday' => __('Lunes', 'clever-booking'),
                                        'tuesday' => __('Martes', 'clever-booking'),
                                        'wednesday' => __('Miércoles', 'clever-booking'),
                                        'thursday' => __('Jueves', 'clever-booking'),
                                        'friday' => __('Viernes', 'clever-booking'),
                                        'saturday' => __('Sábado', 'clever-booking'),
                                        'sunday' => __('Domingo', 'clever-booking'),
                                    );
                                    
                                    $available_days = get_option('cb_available_days', array('monday', 'tuesday', 'wednesday', 'thursday', 'friday'));
                                    
                                    foreach ($days_of_week as $day_key => $day_label) :
                                        $checked = in_array($day_key, $available_days) ? 'checked' : '';
                                    ?>
                                        <label class="cb-checkbox-label">
                                            <input type="checkbox" name="cb_available_days[]" value="<?php echo esc_attr($day_key); ?>" <?php echo $checked; ?>>
                                            <?php echo esc_html($day_label); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_time_slot_interval"><?php echo esc_html__('Intervalo de franjas horarias', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <select name="cb_time_slot_interval" id="cb_time_slot_interval">
                                        <?php
                                        $intervals = array(
                                            15 => __('15 minutos', 'clever-booking'),
                                            30 => __('30 minutos', 'clever-booking'),
                                            60 => __('1 hora', 'clever-booking'),
                                        );
                                        
                                        $selected_interval = get_option('cb_time_slot_interval', 30);
                                        
                                        foreach ($intervals as $value => $label) :
                                            $selected = selected($selected_interval, $value, false);
                                        ?>
                                            <option value="<?php echo esc_attr($value); ?>" <?php echo $selected; ?>>
                                                <?php echo esc_html($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_business_hours_start"><?php echo esc_html__('Horario de inicio', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <select name="cb_business_hours_start" id="cb_business_hours_start">
                                        <?php
                                        for ($hour = 0; $hour < 24; $hour++) {
                                            for ($min = 0; $min < 60; $min += 30) {
                                                $time = sprintf('%02d:%02d', $hour, $min);
                                                $display_time = date('g:i A', strtotime($time));
                                                $selected = selected(get_option('cb_business_hours_start', '09:00'), $time, false);
                                                echo '<option value="' . esc_attr($time) . '" ' . $selected . '>' . esc_html($display_time) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_business_hours_end"><?php echo esc_html__('Horario de cierre', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <select name="cb_business_hours_end" id="cb_business_hours_end">
                                        <?php
                                        for ($hour = 0; $hour < 24; $hour++) {
                                            for ($min = 0; $min < 60; $min += 30) {
                                                $time = sprintf('%02d:%02d', $hour, $min);
                                                $display_time = date('g:i A', strtotime($time));
                                                $selected = selected(get_option('cb_business_hours_end', '18:00'), $time, false);
                                                echo '<option value="' . esc_attr($time) . '" ' . $selected . '>' . esc_html($display_time) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_default_reservation_status"><?php echo esc_html__('Estado por defecto de las reservas', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <select name="cb_default_reservation_status" id="cb_default_reservation_status">
                                        <?php
                                        $statuses = array(
                                            'pending' => __('Pendiente', 'clever-booking'),
                                            'confirmed' => __('Confirmada', 'clever-booking'),
                                        );
                                        
                                        $selected_status = get_option('cb_default_reservation_status', 'pending');
                                        
                                        foreach ($statuses as $value => $label) :
                                            $selected = selected($selected_status, $value, false);
                                        ?>
                                            <option value="<?php echo esc_attr($value); ?>" <?php echo $selected; ?>>
                                                <?php echo esc_html($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Pestaña Apariencia -->
                <div id="appearance" class="cb-tab-pane">
                    <div class="cb-settings-section">
                        <h2><?php echo esc_html__('Configuración de Apariencia', 'clever-booking'); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="cb_primary_color"><?php echo esc_html__('Color Principal', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="cb_primary_color" id="cb_primary_color" class="cb-color-picker"
                                           value="<?php echo esc_attr(get_option('cb_primary_color', '#4a6bff')); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_services_default_layout"><?php echo esc_html__('Diseño predeterminado de servicios', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <select name="cb_services_default_layout" id="cb_services_default_layout">
                                        <?php
                                        $layouts = array(
                                            'grid' => __('Cuadrícula', 'clever-booking'),
                                            'list' => __('Lista', 'clever-booking'),
                                        );
                                        
                                        $selected_layout = get_option('cb_services_default_layout', 'grid');
                                        
                                        foreach ($layouts as $value => $label) :
                                            $selected = selected($selected_layout, $value, false);
                                        ?>
                                            <option value="<?php echo esc_attr($value); ?>" <?php echo $selected; ?>>
                                                <?php echo esc_html($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_services_default_columns"><?php echo esc_html__('Columnas predeterminadas', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <select name="cb_services_default_columns" id="cb_services_default_columns">
                                        <?php
                                        $columns = array(
                                            1 => __('1 columna', 'clever-booking'),
                                            2 => __('2 columnas', 'clever-booking'),
                                            3 => __('3 columnas', 'clever-booking'),
                                            4 => __('4 columnas', 'clever-booking'),
                                        );
                                        
                                        $selected_columns = get_option('cb_services_default_columns', 3);
                                        
                                        foreach ($columns as $value => $label) :
                                            $selected = selected($selected_columns, $value, false);
                                        ?>
                                            <option value="<?php echo esc_attr($value); ?>" <?php echo $selected; ?>>
                                                <?php echo esc_html($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_custom_css"><?php echo esc_html__('CSS Personalizado', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <textarea name="cb_custom_css" id="cb_custom_css" rows="8" class="large-text code"><?php echo esc_textarea(get_option('cb_custom_css')); ?></textarea>
                                    <p class="description">
                                        <?php echo esc_html__('Añade CSS personalizado para sobrescribir los estilos del plugin.', 'clever-booking'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Pestaña Avanzado -->
                <div id="advanced" class="cb-tab-pane">
                    <div class="cb-settings-section">
                        <h2><?php echo esc_html__('Configuración Avanzada', 'clever-booking'); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="cb_allow_multiple_bookings"><?php echo esc_html__('Permitir múltiples reservas', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <label class="cb-switch">
                                        <input type="checkbox" name="cb_allow_multiple_bookings" id="cb_allow_multiple_bookings" value="1"
                                            <?php checked(1, get_option('cb_allow_multiple_bookings', 0)); ?>>
                                        <span class="cb-slider round"></span>
                                    </label>
                                    <p class="description">
                                        <?php echo esc_html__('Permitir múltiples reservas en la misma franja horaria.', 'clever-booking'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_google_maps_api_key"><?php echo esc_html__('Clave API de Google Maps', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="cb_google_maps_api_key" id="cb_google_maps_api_key" class="regular-text"
                                           value="<?php echo esc_attr(get_option('cb_google_maps_api_key')); ?>">
                                    <p class="description">
                                        <?php echo esc_html__('Si deseas integrar mapas en el plugin.', 'clever-booking'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cb_debug_mode"><?php echo esc_html__('Modo de depuración', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <label class="cb-switch">
                                        <input type="checkbox" name="cb_debug_mode" id="cb_debug_mode" value="1"
                                            <?php checked(1, get_option('cb_debug_mode', 0)); ?>>
                                        <span class="cb-slider round"></span>
                                    </label>
                                    <p class="description">
                                        <?php echo esc_html__('Activar el modo de depuración (solo para desarrolladores).', 'clever-booking'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php echo esc_html__('Herramientas de Mantenimiento', 'clever-booking'); ?></label>
                                </th>
                                <td>
                                    <div class="cb-tools-section">
                                        <button type="button" id="cb-reset-settings" class="button button-secondary">
                                            <?php echo esc_html__('Restablecer Configuración', 'clever-booking'); ?>
                                        </button>
                                        <p class="description">
                                            <?php echo esc_html__('Restablecer todas las opciones a sus valores predeterminados.', 'clever-booking'); ?>
                                        </p>
                                    </div>
                                    
                                    <div class="cb-tools-section">
                                        <button type="button" id="cb-clear-data" class="button button-secondary">
                                            <?php echo esc_html__('Limpiar Datos de Reservas', 'clever-booking'); ?>
                                        </button>
                                        <p class="description">
                                            <?php echo esc_html__('Eliminar todas las reservas antiguas/completadas. Esta acción no puede deshacerse.', 'clever-booking'); ?>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php submit_button(); ?>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Pestañas de navegación
    $('.cb-tab-link').on('click', function(e) {
        e.preventDefault();
        
        // Obtener el ID de la pestaña
        var tabId = $(this).data('tab');
        
        // Cambiar la pestaña activa
        $('.cb-tab-link').removeClass('active');
        $(this).addClass('active');
        
        // Mostrar el contenido de la pestaña
        $('.cb-tab-pane').removeClass('active');
        $('#' + tabId).addClass('active');
        
        // Guardar la pestaña activa en localStorage
        localStorage.setItem('cb_settings_active_tab', tabId);
    });
    
    // Recuperar la pestaña activa del almacenamiento local
    var activeTab = localStorage.getItem('cb_settings_active_tab');
    if (activeTab) {
        $('.cb-tab-link[data-tab="' + activeTab + '"]').trigger('click');
    }
    
    // Inicializar selector de color
    if ($.fn.wpColorPicker) {
        $('.cb-color-picker').wpColorPicker();
    }
    
    // Confirmación para restablecer configuración
    $('#cb-reset-settings').on('click', function() {
        if (confirm('<?php echo esc_js(__('¿Estás seguro de que deseas restablecer toda la configuración a sus valores predeterminados?', 'clever-booking')); ?>')) {
            // Enviar solicitud AJAX para restablecer configuración
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cb_reset_settings',
                    nonce: '<?php echo wp_create_nonce('cb_reset_settings_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        window.location.reload();
                    } else {
                        alert(response.data.message || '<?php echo esc_js(__('Error al restablecer la configuración.', 'clever-booking')); ?>');
                    }
                },
                error: function() {
                    alert('<?php echo esc_js(__('Error de conexión al intentar restablecer la configuración.', 'clever-booking')); ?>');
                }
            });
        }
    });
    
    // Confirmación para limpiar datos
    $('#cb-clear-data').on('click', function() {
        if (confirm('<?php echo esc_js(__('¿Estás seguro de que deseas eliminar todas las reservas antiguas/completadas? Esta acción no puede deshacerse.', 'clever-booking')); ?>')) {
            // Enviar solicitud AJAX para limpiar datos
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cb_clear_booking_data',
                    nonce: '<?php echo wp_create_nonce('cb_clear_booking_data_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                    } else {
                        alert(response.data.message || '<?php echo esc_js(__('Error al limpiar los datos.', 'clever-booking')); ?>');
                    }
                },
                error: function() {
                    alert('<?php echo esc_js(__('Error de conexión al intentar limpiar los datos.', 'clever-booking')); ?>');
                }
            });
        }
    });
});
</script>

<style>
/* Estilos de la página de configuración */
.cb-admin-tabs {
    margin-top: 20px;
    background-color: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
}

/* Pestañas de navegación */
.cb-tab-nav {
    display: flex;
    overflow: hidden;
    background-color: #f1f1f1;
    border-bottom: 1px solid #ccd0d4;
}

.cb-tab-link {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    text-decoration: none;
    color: #444;
    font-weight: 500;
    transition: all 0.3s ease;
}

.cb-tab-link .dashicons {
    margin-right: 5px;
}

.cb-tab-link:hover {
    background-color: #e5e5e5;
    color: #000;
}

.cb-tab-link.active {
    background-color: #fff;
    color: #0073aa;
    border-bottom: 2px solid #0073aa;
    position: relative;
    bottom: -1px;
}

/* Contenido de pestañas */
.cb-tab-content {
    padding: 20px;
}

.cb-tab-pane {
    display: none;
}

.cb-tab-pane.active {
    display: block;
}

.cb-settings-section {
    margin-bottom: 30px;
}

.cb-settings-section h2 {
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

/* Checkbox, slider y labels */
.cb-checkbox-label {
    margin-right: 15px;
}

.cb-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.cb-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.cb-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.cb-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
}

input:checked + .cb-slider {
    background-color: #0073aa;
}

input:checked + .cb-slider:before {
    transform: translateX(26px);
}

.cb-slider.round {
    border-radius: 24px;
}

.cb-slider.round:before {
    border-radius: 50%;
}

/* Herramientas de mantenimiento */
.cb-tools-section {
    margin-bottom: 15px;
}
</style> 
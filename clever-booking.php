<?php
/**
 * Plugin Name: Clever Booking
 * Plugin URI: 
 * Description: Plugin para gestionar reservas de servicios online de cualquier tipo. Integrado con Elementor.
 * Version: 1.1.0
 * Author: 
 * Text Domain: clever-booking
 * Domain Path: /languages
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

// Iniciar buffer de salida para evitar problemas de 'headers already sent'
if (!function_exists('cb_ob_start')) {
    function cb_ob_start() {
        // Solo iniciar si no hay un buffer activo
        if (ob_get_level() == 0) {
            ob_start();
        }
    }
    add_action('init', 'cb_ob_start', 1);
}

define('CLEVER_BOOKING_VERSION', '1.1.0');
define('CLEVER_BOOKING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CLEVER_BOOKING_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Función que se ejecuta durante la activación del plugin
 */
function activate_clever_booking() {
    require_once CLEVER_BOOKING_PLUGIN_DIR . 'includes/class-activator.php';
    Clever_Booking_Activator::activate();
}

/**
 * Función que se ejecuta durante la desactivación del plugin
 */
function deactivate_clever_booking() {
    require_once CLEVER_BOOKING_PLUGIN_DIR . 'includes/class-deactivator.php';
    Clever_Booking_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_clever_booking');
register_deactivation_hook(__FILE__, 'deactivate_clever_booking');

/**
 * Verificar si ACF está activo
 */
function clever_booking_check_dependencies() {
    if (!class_exists('ACF')) {
        add_action('admin_notices', 'clever_booking_acf_missing_notice');
        // No desactivamos el plugin, solo mostramos un aviso
        return true; // Devolvemos true para que el plugin continúe funcionando
    }
    return true;
}

/**
 * Mensaje de error cuando ACF no está instalado
 */
function clever_booking_acf_missing_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php _e('Para aprovechar todas las funcionalidades de Clever Booking, se recomienda instalar y activar el plugin Advanced Custom Fields (ACF).', 'clever-booking'); ?></p>
    </div>
    <?php
}

/**
 * Inicia el plugin
 */
function clever_booking_init() {
    if (clever_booking_check_dependencies()) {
        require_once CLEVER_BOOKING_PLUGIN_DIR . 'includes/class-clever-booking.php';
        $plugin = new Clever_Booking();
        $plugin->run();
    }
}
add_action('plugins_loaded', 'clever_booking_init'); 
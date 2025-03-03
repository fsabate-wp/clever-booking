<?php
/**
 * Clase que define la funcionalidad durante la desactivación del plugin
 *
 * @link       
 * @since      1.0.0
 *
 * @package    Clever_Booking
 * @subpackage Clever_Booking/includes
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

/**
 * Clase de desactivación del plugin
 */
class Clever_Booking_Deactivator {

    /**
     * Método que se ejecuta durante la desactivación del plugin
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Eliminar eventos programados
        wp_clear_scheduled_hook('clever_booking_cleanup');
        
        // Limpiar cualquier transient
        delete_transient('clever_booking_reservations_cache');
        
        // Nota: No eliminamos las tablas o datos para evitar pérdida de información
        // Si se desea eliminar datos, esto debería hacerse con una opción de desinstalación
    }
} 
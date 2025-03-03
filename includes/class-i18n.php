<?php
/**
 * Define la funcionalidad de internacionalización.
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
 * Define la funcionalidad de internacionalización.
 *
 * Carga el dominio de texto del plugin para la traducción.
 *
 * @since      1.0.0
 * @package    Clever_Booking
 * @subpackage Clever_Booking/includes
 */
class Clever_Booking_i18n {

    /**
     * Carga el dominio de texto del plugin para la traducción.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'clever-booking',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
} 
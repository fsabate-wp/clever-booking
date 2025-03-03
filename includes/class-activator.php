<?php
/**
 * Clase que define la funcionalidad durante la activación del plugin
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
 * Clase de activación del plugin
 */
class Clever_Booking_Activator {

    /**
     * Método que se ejecuta durante la activación del plugin
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Crear las tablas personalizadas en la base de datos si es necesario
        self::create_tables();
        
        // Programar un evento cron para limpiar reservas antiguas si es necesario
        // self::schedule_events();
        
        // Limpiar transients si es necesario
        // delete_transient('clever_booking_reservations_cache');
        
        // Crear páginas personalizadas si es necesario
        // self::create_pages();
        
        // Establecer permisos si es necesario
        // self::setup_permissions();
    }
    
    /**
     * Crea las tablas necesarias en la base de datos
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabla de reservas
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            service_id bigint(20) NOT NULL,
            customer_name varchar(100) NOT NULL,
            customer_email varchar(100) NOT NULL,
            customer_phone varchar(20) NOT NULL,
            customer_address text NOT NULL,
            booking_date date NOT NULL,
            booking_time time NOT NULL,
            booking_end_time time NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            notes text,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Tabla de disponibilidad
        $table_availability = $wpdb->prefix . 'clever_booking_availability';
        
        $sql_availability = "CREATE TABLE $table_availability (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            day_of_week tinyint(1) NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Requiere wp-admin/includes/upgrade.php
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Crear o actualizar las tablas
        dbDelta($sql);
        dbDelta($sql_availability);
    }
} 
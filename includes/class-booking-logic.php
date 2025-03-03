<?php
/**
 * Maneja la lógica de las reservas
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
 * Maneja la lógica de las reservas.
 */
class Clever_Booking_Logic {

    /**
     * Inicializa la clase.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Asegurar que la tabla existe
        $this->check_table();
    }

    /**
     * Comprueba si la tabla de reservas existe y la crea si no.
     *
     * @since    1.0.0
     */
    private function check_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // La tabla no existe, vamos a crearla
            error_log("La tabla $table_name no existe. Creándola...");
            
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                service_id bigint(20) NOT NULL,
                customer_name varchar(100) NOT NULL,
                customer_email varchar(100) NOT NULL,
                customer_phone varchar(50) NOT NULL,
                customer_address text NOT NULL,
                booking_date date NOT NULL,
                booking_time time NOT NULL,
                booking_end_time time NOT NULL,
                status varchar(20) NOT NULL DEFAULT 'pending',
                notes text,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            
            error_log("Tabla $table_name creada con éxito.");
        }
    }

    /**
     * Obtiene todas las reservas.
     *
     * @since    1.0.0
     * @param    array    $args    Argumentos de consulta adicionales.
     * @return   array              Array de objetos de reserva.
     */
    public function get_reservations($args = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        
        $defaults = array(
            'limit' => 50,
            'offset' => 0,
            'orderby' => 'booking_date',
            'order' => 'DESC',
            'where' => '',
            'where_values' => array(),
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $sql = "SELECT * FROM {$table_name}";
        
        if (!empty($args['where'])) {
            $sql .= " WHERE " . $args['where'];
        }
        
        $sql .= " ORDER BY {$args['orderby']} {$args['order']}";
        
        if ($args['limit'] > 0) {
            $sql .= " LIMIT %d OFFSET %d";
            $args['where_values'][] = $args['limit'];
            $args['where_values'][] = $args['offset'];
        }
        
        if (!empty($args['where_values'])) {
            $sql = $wpdb->prepare($sql, $args['where_values']);
        }
        
        $reservations = $wpdb->get_results($sql);
        
        return $reservations;
    }

    /**
     * Obtiene una reserva por su ID.
     *
     * @since    1.0.0
     * @param    int       $id    ID de la reserva.
     * @return   object|false     Objeto de reserva o false si no se encuentra.
     */
    public function get_reservation($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        
        $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $id);
        
        $reservation = $wpdb->get_row($sql);
        
        return $reservation;
    }

    /**
     * Crea una nueva reserva.
     *
     * @since    1.0.0
     * @param    array     $data    Datos de la reserva.
     * @return   int|false          ID de la reserva creada o false en caso de error.
     */
    public function create_reservation($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        
        // Logging para depuración
        if (get_option('cb_debug_mode')) {
            error_log('Intentando crear una reserva con los siguientes datos:');
            error_log(print_r($data, true));
        }
        
        // Validar y sanitizar los datos
        $service_id = absint($data['service_id']);
        $customer_name = sanitize_text_field($data['customer_name']);
        $customer_email = sanitize_email($data['customer_email']);
        $customer_phone = sanitize_text_field($data['customer_phone']);
        $customer_address = sanitize_textarea_field($data['customer_address']);
        $booking_date = sanitize_text_field($data['booking_date']);
        $booking_time = sanitize_text_field($data['booking_time']);
        $booking_end_time = sanitize_text_field($data['booking_end_time']);
        $notes = isset($data['notes']) ? sanitize_textarea_field($data['notes']) : '';
        
        // Verificar disponibilidad antes de insertar
        if (!$this->check_availability($service_id, $booking_date, $booking_time, $booking_end_time)) {
            error_log("No hay disponibilidad para Service ID: $service_id, Date: $booking_date, Time: $booking_time - $booking_end_time");
            return false;
        }
        
        // Preparar los datos para insertar
        $insert_data = array(
            'service_id' => $service_id,
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'customer_phone' => $customer_phone,
            'customer_address' => $customer_address,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'booking_end_time' => $booking_end_time,
            'status' => 'pending',
            'notes' => $notes,
            'created_at' => current_time('mysql'),
        );
        
        // Formato de los datos
        $format = array(
            '%d', // service_id
            '%s', // customer_name
            '%s', // customer_email
            '%s', // customer_phone
            '%s', // customer_address
            '%s', // booking_date
            '%s', // booking_time
            '%s', // booking_end_time
            '%s', // status
            '%s', // notes
            '%s', // created_at
        );
        
        // En modo debug, mostrar la consulta SQL
        if (get_option('cb_debug_mode')) {
            error_log("Insertando reserva en la tabla: $table_name");
            error_log("Datos: " . print_r($insert_data, true));
        }
        
        // Insertar la reserva
        $result = $wpdb->insert($table_name, $insert_data, $format);
        
        if ($result === false) {
            error_log("Error al insertar la reserva: " . $wpdb->last_error);
            return false;
        }
        
        $reservation_id = $wpdb->insert_id;
        
        if (get_option('cb_debug_mode')) {
            error_log("Reserva creada con éxito. ID: $reservation_id");
        }
        
        return $reservation_id;
    }

    /**
     * Actualiza una reserva existente.
     *
     * @since    1.0.0
     * @param    int       $id      ID de la reserva.
     * @param    array     $data    Datos de la reserva.
     * @return   bool               True en caso de éxito, false en caso contrario.
     */
    public function update_reservation($id, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        
        // Preparar los datos a actualizar
        $update_data = array();
        $format = array();
        
        // Actualizar solo los campos proporcionados
        if (isset($data['service_id'])) {
            $update_data['service_id'] = absint($data['service_id']);
            $format[] = '%d';
        }
        
        if (isset($data['customer_name'])) {
            $update_data['customer_name'] = sanitize_text_field($data['customer_name']);
            $format[] = '%s';
        }
        
        if (isset($data['customer_email'])) {
            $update_data['customer_email'] = sanitize_email($data['customer_email']);
            $format[] = '%s';
        }
        
        if (isset($data['customer_phone'])) {
            $update_data['customer_phone'] = sanitize_text_field($data['customer_phone']);
            $format[] = '%s';
        }
        
        if (isset($data['customer_address'])) {
            $update_data['customer_address'] = sanitize_textarea_field($data['customer_address']);
            $format[] = '%s';
        }
        
        if (isset($data['booking_date'])) {
            $update_data['booking_date'] = sanitize_text_field($data['booking_date']);
            $format[] = '%s';
        }
        
        if (isset($data['booking_time'])) {
            $update_data['booking_time'] = sanitize_text_field($data['booking_time']);
            $format[] = '%s';
        }
        
        if (isset($data['booking_end_time'])) {
            $update_data['booking_end_time'] = sanitize_text_field($data['booking_end_time']);
            $format[] = '%s';
        }
        
        if (isset($data['status'])) {
            $update_data['status'] = sanitize_text_field($data['status']);
            $format[] = '%s';
        }
        
        if (isset($data['notes'])) {
            $update_data['notes'] = sanitize_textarea_field($data['notes']);
            $format[] = '%s';
        }
        
        // Actualizar la reserva
        $result = $wpdb->update(
            $table_name,
            $update_data,
            array('id' => $id),
            $format,
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Elimina una reserva.
     *
     * @since    1.0.0
     * @param    int       $id    ID de la reserva.
     * @return   bool             True en caso de éxito, false en caso contrario.
     */
    public function delete_reservation($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Verifica la disponibilidad para una reserva.
     *
     * @since    1.0.0
     * @param    int       $service_id        ID del servicio.
     * @param    string    $date              Fecha de la reserva (YYYY-MM-DD).
     * @param    string    $start_time        Hora de inicio (HH:MM:SS).
     * @param    string    $end_time          Hora de fin (HH:MM:SS).
     * @param    int       $exclude_id        ID de reserva a excluir (para actualizaciones).
     * @return   bool                         True si está disponible, false en caso contrario.
     */
    public function check_availability($service_id, $date, $start_time, $end_time, $exclude_id = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        
        // Si estamos en modo debug, simplemente permitir la reserva
        if (get_option('cb_debug_mode')) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Modo debug activado - Permitiendo reserva para Service ID: $service_id, Date: $date, Time: $start_time - $end_time");
            }
            return true;
        }
        
        // Verificar si el día está configurado como disponible
        $day_of_week = date('l', strtotime($date));
        $day_of_week = strtolower($day_of_week);
        
        $available_days = get_option('cb_available_days');
        if (!$available_days) {
            // Valores por defecto si no hay configuración
            $available_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday');
        }
        
        if (!in_array($day_of_week, $available_days)) {
            error_log("Día no disponible: " . $day_of_week);
            return false;
        }
        
        // Verificar si permite múltiples reservas a la misma hora
        $allow_multiple_bookings = get_option('cb_allow_multiple_bookings');
        
        if ($allow_multiple_bookings) {
            return true;
        }
        
        // Verificar si hay otras reservas que se superpongan en CUALQUIER servicio
        // Eliminamos la condición "AND service_id = %d" para verificar todos los servicios
        // Verificamos si hay alguna reserva que:
        // 1. Sea para la misma fecha
        // 2. Tenga una franja horaria que se superponga con la solicitada
        // 3. No esté cancelada
        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} 
            WHERE booking_date = %s 
            AND id != %d
            AND status != 'cancelled'
            AND (
                (booking_time <= %s AND booking_end_time > %s) OR
                (booking_time < %s AND booking_end_time >= %s) OR
                (booking_time >= %s AND booking_time < %s)
            )",
            $date, 
            $exclude_id,
            $start_time, $start_time,  // Caso 1: La reserva existente comienza antes y termina después del inicio solicitado
            $end_time, $end_time,      // Caso 2: La reserva existente comienza antes y termina después del fin solicitado
            $start_time, $end_time     // Caso 3: La reserva existente comienza durante la franja solicitada
        );
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Verificando disponibilidad con SQL: $sql");
        }
        
        $count = $wpdb->get_var($sql);
        
        if ($count > 0) {
            error_log("Ya existe una reserva en este horario: Date: $date, Time: $start_time - $end_time");
            return false;
        }
        
        return true;
    }

    /**
     * Obtiene los servicios disponibles.
     *
     * @since    1.0.0
     * @param    array    $args    Argumentos para la consulta WP_Query.
     * @return   array             Array con los servicios disponibles como objetos WP_Post.
     */
    public function get_available_services($args = array()) {
        $defaults = array(
            'post_type' => 'cb_service',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $services_query = new WP_Query($args);
        
        if ($services_query->have_posts()) {
            return $services_query->posts;
        }
        
        return array();
    }

    /**
     * Obtiene las franjas horarias disponibles para una fecha.
     *
     * @since    1.0.0
     * @param    string    $date         Fecha en formato YYYY-MM-DD.
     * @param    int       $service_id   ID del servicio (para obtener duración).
     * @return   array                  Franjas horarias disponibles.
     */
    public function get_available_time_slots($date, $service_id) {
        // Registrar para depuración
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Obteniendo franjas horarias disponibles para fecha: $date, servicio: $service_id");
        }
        
        // Obtener el día de la semana
        $day_of_week = strtolower(date('l', strtotime($date)));
        
        // Obtener los días disponibles de la configuración
        $available_days = get_option('cb_available_days');
        if (!$available_days) {
            // Valores por defecto si no hay configuración
            $available_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday');
        }
        
        // Verificar si el día seleccionado está disponible
        if (!in_array($day_of_week, $available_days)) {
            return array();
        }
        
        // En lugar de depender de ACF, usaremos las opciones estándar de WordPress
        $business_hours_start = get_option('cb_business_hours_start', '09:00');
        $business_hours_end = get_option('cb_business_hours_end', '18:00');
        $time_slot_interval = get_option('cb_time_slot_interval', 60); // En minutos
        
        // Obtener la duración del servicio (ACF o valor por defecto)
        $service_duration = get_field('service_duration', $service_id);
        if (!$service_duration) {
            $service_duration = 60; // 1 hora por defecto
        }
        
        // Convertir duración a segundos
        $duration_seconds = $service_duration * 60;
        
        // Generar franjas horarias disponibles
        $available_slots = array();
        
        // Calculamos las franjas horarias disponibles
        $start_time = strtotime($business_hours_start);
        $end_time = strtotime($business_hours_end);
        
        // Para modo debug
        if (get_option('cb_debug_mode')) {
            $date_obj = new DateTime($date);
            $current_date = new DateTime(date('Y-m-d'));
            
            // Si estamos en modo debug, permitir reservas para cualquier fecha a partir de hoy
            if ($date_obj < $current_date) {
                return array();
            }
        } else {
            // Verificar que la fecha sea futura
            $date_obj = new DateTime($date);
            $current_date = new DateTime(date('Y-m-d'));
            $min_time_before_booking = get_option('cb_min_time_before_booking', 24);
            
            if ($min_time_before_booking > 0) {
                $current_date->modify('+' . $min_time_before_booking . ' hours');
            }
            
            if ($date_obj < $current_date) {
                return array(); // La fecha es demasiado cercana o pasada
            }
            
            // Verificar que no sea demasiado lejana
            $max_days_advance = get_option('cb_max_days_advance_booking', 90);
            $max_date = new DateTime(date('Y-m-d'));
            $max_date->modify('+' . $max_days_advance . ' days');
            
            if ($date_obj > $max_date) {
                return array(); // La fecha es demasiado lejana
            }
        }
        
        // Generar franjas cada intervalo (normalmente la duración del servicio)
        for ($time = $start_time; $time < $end_time; $time += $duration_seconds) {
            $slot_end = $time + $duration_seconds;
            
            // Si el fin de la franja supera el tiempo final permitido, no incluirla
            if ($slot_end > $end_time) {
                continue;
            }
            
            $slot_start_formatted = date('H:i:s', $time);
            $slot_end_formatted = date('H:i:s', $slot_end);
            
            // Verificar disponibilidad para esta franja (modo debug o verificación real)
            $is_available = get_option('cb_debug_mode') ? 
                true : 
                $this->check_availability($service_id, $date, $slot_start_formatted, $slot_end_formatted);
            
            if ($is_available) {
                $available_slots[] = array(
                    'start' => $slot_start_formatted,
                    'end' => $slot_end_formatted,
                    'formatted_start' => date('g:i a', $time),
                    'formatted_end' => date('g:i a', $slot_end),
                );
                
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("Franja disponible: $slot_start_formatted - $slot_end_formatted");
                }
            } else if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log("Franja no disponible: $slot_start_formatted - $slot_end_formatted");
            }
        }
        
        return $available_slots;
    }
} 
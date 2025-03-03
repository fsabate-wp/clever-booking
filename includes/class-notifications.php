<?php
/**
 * Maneja las notificaciones del plugin
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
 * Maneja las notificaciones del plugin.
 */
class Clever_Booking_Notifications {

    /**
     * Inicializa la clase y establece sus propiedades.
     *
     * @since    1.0.0
     */
    public function __construct() {
    }

    /**
     * Envía una notificación al administrador cuando se crea una nueva reserva.
     *
     * @since    1.0.0
     * @param    int    $reservation_id    ID de la reserva.
     * @return   bool                       True si se envió con éxito, false en caso contrario.
     */
    public function send_admin_notification($reservation_id) {
        // Obtener la información de la reserva
        global $wpdb;
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $reservation_id));
        
        if (!$reservation) {
            return false;
        }
        
        // Obtener información del servicio
        $service = get_post($reservation->service_id);
        if (!$service) {
            return false;
        }
        
        // Formatear la fecha y hora
        $booking_date = date_i18n(get_option('date_format'), strtotime($reservation->booking_date));
        $booking_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_time));
        $booking_end_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_end_time));
        
        // Obtener el correo electrónico del administrador
        $admin_email = get_option('admin_email');
        
        // Preparar el asunto
        $subject = sprintf(__('Nueva reserva: %s - %s', 'clever-booking'), $service->post_title, $booking_date);
        
        // Preparar el cuerpo del mensaje
        $message = sprintf(__('Se ha recibido una nueva reserva con los siguientes detalles:', 'clever-booking')) . "\n\n";
        $message .= sprintf(__('Servicio: %s', 'clever-booking'), $service->post_title) . "\n";
        $message .= sprintf(__('Fecha: %s', 'clever-booking'), $booking_date) . "\n";
        $message .= sprintf(__('Hora: %s - %s', 'clever-booking'), $booking_time, $booking_end_time) . "\n\n";
        
        $message .= sprintf(__('Información del cliente:', 'clever-booking')) . "\n";
        $message .= sprintf(__('Nombre: %s', 'clever-booking'), $reservation->customer_name) . "\n";
        $message .= sprintf(__('Email: %s', 'clever-booking'), $reservation->customer_email) . "\n";
        $message .= sprintf(__('Teléfono: %s', 'clever-booking'), $reservation->customer_phone) . "\n";
        $message .= sprintf(__('Dirección: %s', 'clever-booking'), $reservation->customer_address) . "\n\n";
        
        if (!empty($reservation->notes)) {
            $message .= sprintf(__('Notas: %s', 'clever-booking'), $reservation->notes) . "\n\n";
        }
        
        $message .= sprintf(__('Para ver y administrar esta reserva, visite el panel de administración.', 'clever-booking')) . "\n";
        $message .= admin_url('admin.php?page=clever-booking-reservations') . "\n";
        
        // Enviar el correo
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        return wp_mail($admin_email, $subject, $message, $headers);
    }

    /**
     * Envía una confirmación al cliente cuando se crea una nueva reserva.
     *
     * @since    1.0.0
     * @param    int    $reservation_id    ID de la reserva.
     * @return   bool                       True si se envió con éxito, false en caso contrario.
     */
    public function send_customer_confirmation($reservation_id) {
        // Obtener la información de la reserva
        global $wpdb;
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $reservation_id));
        
        if (!$reservation) {
            return false;
        }
        
        // Obtener información del servicio
        $service = get_post($reservation->service_id);
        if (!$service) {
            return false;
        }
        
        // Formatear la fecha y hora
        $booking_date = date_i18n(get_option('date_format'), strtotime($reservation->booking_date));
        $booking_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_time));
        $booking_end_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_end_time));
        
        // Preparar el asunto
        $subject = sprintf(__('Confirmación de reserva: %s - %s', 'clever-booking'), $service->post_title, $booking_date);
        
        // Preparar el cuerpo del mensaje
        $message = sprintf(__('Estimado/a %s,', 'clever-booking'), $reservation->customer_name) . "\n\n";
        $message .= sprintf(__('Gracias por tu reserva. A continuación, encontrarás los detalles de tu reserva:', 'clever-booking')) . "\n\n";
        
        $message .= sprintf(__('Servicio: %s', 'clever-booking'), $service->post_title) . "\n";
        $message .= sprintf(__('Fecha: %s', 'clever-booking'), $booking_date) . "\n";
        $message .= sprintf(__('Hora: %s - %s', 'clever-booking'), $booking_time, $booking_end_time) . "\n\n";
        
        $message .= sprintf(__('Si necesitas modificar o cancelar tu reserva, por favor contáctanos lo antes posible.', 'clever-booking')) . "\n\n";
        
        $message .= sprintf(__('¡Gracias por elegirnos!', 'clever-booking')) . "\n\n";
        $message .= get_bloginfo('name') . "\n";
        $message .= site_url() . "\n";
        
        // Enviar el correo
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        return wp_mail($reservation->customer_email, $subject, $message, $headers);
    }

    /**
     * Envía una notificación al cliente cuando el estado de su reserva cambia.
     *
     * @since    1.0.0
     * @param    int       $reservation_id    ID de la reserva.
     * @param    string    $new_status        Nuevo estado de la reserva.
     * @return   bool                          True si se envió con éxito, false en caso contrario.
     */
    public function send_status_update_notification($reservation_id, $new_status) {
        // Obtener la información de la reserva
        global $wpdb;
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $reservation_id));
        
        if (!$reservation) {
            return false;
        }
        
        // Obtener información del servicio
        $service = get_post($reservation->service_id);
        if (!$service) {
            return false;
        }
        
        // Formatear la fecha y hora
        $booking_date = date_i18n(get_option('date_format'), strtotime($reservation->booking_date));
        $booking_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_time));
        
        // Preparar el asunto según el nuevo estado
        $subject = '';
        switch ($new_status) {
            case 'confirmed':
                $subject = sprintf(__('Reserva confirmada: %s - %s', 'clever-booking'), $service->post_title, $booking_date);
                break;
            case 'cancelled':
                $subject = sprintf(__('Reserva cancelada: %s - %s', 'clever-booking'), $service->post_title, $booking_date);
                break;
            case 'completed':
                $subject = sprintf(__('Reserva completada: %s - %s', 'clever-booking'), $service->post_title, $booking_date);
                break;
            default:
                $subject = sprintf(__('Actualización de reserva: %s - %s', 'clever-booking'), $service->post_title, $booking_date);
                break;
        }
        
        // Preparar el cuerpo del mensaje según el nuevo estado
        $message = sprintf(__('Estimado/a %s,', 'clever-booking'), $reservation->customer_name) . "\n\n";
        
        switch ($new_status) {
            case 'confirmed':
                $message .= sprintf(__('Tu reserva ha sido confirmada. A continuación, encontrarás los detalles:', 'clever-booking')) . "\n\n";
                break;
            case 'cancelled':
                $message .= sprintf(__('Lamentamos informarte que tu reserva ha sido cancelada.', 'clever-booking')) . "\n\n";
                break;
            case 'completed':
                $message .= sprintf(__('Tu reserva ha sido marcada como completada. Gracias por confiar en nuestros servicios.', 'clever-booking')) . "\n\n";
                break;
            default:
                $message .= sprintf(__('El estado de tu reserva ha sido actualizado a: %s.', 'clever-booking'), $new_status) . "\n\n";
                break;
        }
        
        $message .= sprintf(__('Servicio: %s', 'clever-booking'), $service->post_title) . "\n";
        $message .= sprintf(__('Fecha: %s', 'clever-booking'), $booking_date) . "\n";
        $message .= sprintf(__('Hora: %s', 'clever-booking'), $booking_time) . "\n\n";
        
        $message .= sprintf(__('Si tienes alguna pregunta, por favor contáctanos.', 'clever-booking')) . "\n\n";
        
        $message .= sprintf(__('Saludos,', 'clever-booking')) . "\n";
        $message .= get_bloginfo('name') . "\n";
        $message .= site_url() . "\n";
        
        // Enviar el correo
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        return wp_mail($reservation->customer_email, $subject, $message, $headers);
    }

    /**
     * Envía un recordatorio al cliente sobre una reserva próxima.
     *
     * @since    1.0.0
     * @param    int    $reservation_id    ID de la reserva.
     * @return   bool                       True si se envió con éxito, false en caso contrario.
     */
    public function send_reminder_notification($reservation_id) {
        // Obtener la información de la reserva
        global $wpdb;
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        $reservation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $reservation_id));
        
        if (!$reservation) {
            return false;
        }
        
        // Obtener información del servicio
        $service = get_post($reservation->service_id);
        if (!$service) {
            return false;
        }
        
        // Formatear la fecha y hora
        $booking_date = date_i18n(get_option('date_format'), strtotime($reservation->booking_date));
        $booking_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_time));
        $booking_end_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_end_time));
        
        // Preparar el asunto
        $subject = sprintf(__('Recordatorio de reserva: %s - Mañana a las %s', 'clever-booking'), $service->post_title, $booking_time);
        
        // Preparar el cuerpo del mensaje
        $message = sprintf(__('Estimado/a %s,', 'clever-booking'), $reservation->customer_name) . "\n\n";
        $message .= sprintf(__('Te recordamos que tienes una reserva programada para mañana con los siguientes detalles:', 'clever-booking')) . "\n\n";
        
        $message .= sprintf(__('Servicio: %s', 'clever-booking'), $service->post_title) . "\n";
        $message .= sprintf(__('Fecha: %s', 'clever-booking'), $booking_date) . "\n";
        $message .= sprintf(__('Hora: %s - %s', 'clever-booking'), $booking_time, $booking_end_time) . "\n\n";
        
        $message .= sprintf(__('Si necesitas reprogramar o cancelar, por favor contáctanos lo antes posible.', 'clever-booking')) . "\n\n";
        
        $message .= sprintf(__('¡Esperamos verte pronto!', 'clever-booking')) . "\n\n";
        $message .= get_bloginfo('name') . "\n";
        $message .= site_url() . "\n";
        
        // Enviar el correo
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        return wp_mail($reservation->customer_email, $subject, $message, $headers);
    }
} 
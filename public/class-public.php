<?php
/**
 * La funcionalidad específica de la parte pública del plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    Clever_Booking
 * @subpackage Clever_Booking/public
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

/**
 * La clase de la parte pública del plugin.
 */
class Clever_Booking_Public {

    /**
     * El ID de este plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    El ID del plugin.
     */
    private $plugin_name;

    /**
     * La versión actual del plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    La versión actual del plugin.
     */
    private $version;
    
    /**
     * Instancia de la lógica de reservas.
     *
     * @since    1.0.0
     * @access   private
     * @var      Clever_Booking_Logic    $booking_logic    Instancia de la lógica de reservas.
     */
    private $booking_logic;

    /**
     * Inicializa la clase y define sus propiedades.
     *
     * @since    1.0.0
     * @param    string    $plugin_name    El nombre del plugin.
     * @param    string    $version        La versión del plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->booking_logic = new Clever_Booking_Logic();
    }

    /**
     * Registra los estilos de la parte pública.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, CLEVER_BOOKING_PLUGIN_URL . 'public/css/clever-booking-public.css', array(), $this->version, 'all');
        
        // jQuery UI Datepicker - necesario para el selector de fechas
        wp_enqueue_style('jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        
        // Aplicar colores personalizados desde la configuración
        $primary_color = get_option('cb_primary_color', '#4a6bff');
        
        if ($primary_color) {
            $custom_css = "
                :root {
                    --cb-primary: {$primary_color};
                    --cb-primary-hover: " . $this->adjust_brightness($primary_color, -10) . ";
                }
            ";
            
            // Añadir CSS personalizado desde la configuración
            $custom_css .= get_option('cb_custom_css', '');
            
            wp_add_inline_style($this->plugin_name, $custom_css);
        }
    }

    /**
     * Registra los scripts de la parte pública.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        // jQuery UI Datepicker
        wp_enqueue_script('jquery-ui-datepicker');
        
        // Script principal
        wp_enqueue_script($this->plugin_name, CLEVER_BOOKING_PLUGIN_URL . 'public/js/clever-booking-public.js', array('jquery', 'jquery-ui-datepicker'), $this->version, true);
        
        // Localizar script para AJAX y traducciones
        wp_localize_script($this->plugin_name, 'cb_data', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('clever_booking_nonce'),
            'locale' => get_locale(),
            'messages' => array(
                'select_date' => __('Por favor, selecciona una fecha', 'clever-booking'),
                'select_service' => __('Por favor, selecciona un servicio', 'clever-booking'),
                'select_time' => __('Por favor, selecciona un horario', 'clever-booking'),
                'enter_name' => __('Por favor, ingresa tu nombre', 'clever-booking'),
                'enter_email' => __('Por favor, ingresa un email válido', 'clever-booking'),
                'invalid_email' => __('Por favor, ingresa un email válido', 'clever-booking'),
                'enter_phone' => __('Por favor, ingresa tu teléfono', 'clever-booking'),
                'enter_address' => __('Por favor, ingresa tu dirección', 'clever-booking'),
                'loading_times' => __('Cargando horarios...', 'clever-booking'),
                'select_time_option' => __('Selecciona un horario', 'clever-booking'),
                'no_slots_available' => __('No hay horarios disponibles', 'clever-booking'),
                'no_slots_available_alert' => __('No hay horarios disponibles para esta fecha. Por favor, elige otra fecha.', 'clever-booking'),
                'error_loading_times' => __('Error al cargar horarios', 'clever-booking'),
                'error_connection' => __('Error de conexión. Por favor, intenta nuevamente.', 'clever-booking'),
                'processing' => __('Procesando...', 'clever-booking'),
                'submitting' => __('Enviando reserva...', 'clever-booking'),
                'reservation_created' => __('¡Reserva creada con éxito!', 'clever-booking'),
                'error_creating_reservation' => __('Error al crear la reserva. Por favor, intenta nuevamente.', 'clever-booking'),
                'confirm_reservation' => __('Confirmar Reserva', 'clever-booking'),
                'reservation_id' => __('ID de Reserva', 'clever-booking'),
            ),
        ));
    }

    /**
     * Ajusta el brillo de un color hexadecimal
     *
     * @since    1.0.0
     * @param    string    $hex    Color en formato hexadecimal.
     * @param    int       $steps  Cantidad de ajuste (-255 a 255).
     * @return   string            Color ajustado en formato hexadecimal.
     */
    private function adjust_brightness($hex, $steps) {
        // Eliminar el signo # si existe
        $hex = ltrim($hex, '#');
        
        // Convertir a RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Ajustar brillo
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        // Convertir de vuelta a hex
        return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Registra los shortcodes del plugin.
     *
     * @since    1.0.0
     */
    public function register_shortcodes() {
        add_shortcode('clever_booking_form', array($this, 'booking_form_shortcode'));
        add_shortcode('clever_booking_services', array($this, 'services_list_shortcode'));
    }

    /**
     * Shortcode para mostrar el formulario de reserva.
     * 
     * [clever_booking_form]
     * [clever_booking_form service_id="123"]
     *
     * @since    1.0.0
     * @param    array    $atts    Atributos del shortcode.
     * @return   string            Contenido HTML del shortcode.
     */
    public function booking_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'service_id' => 0,
        ), $atts, 'clever_booking_form');
        
        // Iniciar el buffer de salida
        ob_start();
        
        // Incluir la vista del formulario
        include CLEVER_BOOKING_PLUGIN_DIR . 'public/views/booking-form.php';
        
        // Devolver el contenido del buffer y limpiarlo
        return ob_get_clean();
    }

    /**
     * Shortcode para mostrar la lista de servicios.
     * 
     * [clever_booking_services]
     * [clever_booking_services category="muebles"]
     * [clever_booking_services columns="3"]
     *
     * @since    1.0.0
     * @param    array    $atts    Atributos del shortcode.
     * @return   string            Contenido HTML del shortcode.
     */
    public function services_list_shortcode($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'columns' => 3,
        ), $atts, 'clever_booking_services');
        
        // Convertir columnas a un número
        $columns = absint($atts['columns']);
        if ($columns < 1 || $columns > 4) {
            $columns = 3;
        }
        
        // Preparar argumentos para la consulta
        $args = array(
            'limit' => -1, // Sin límite
        );
        
        // Filtrar por categoría si se especificó
        if (!empty($atts['category'])) {
            $args['category'] = sanitize_title($atts['category']);
        }
        
        // Obtener los servicios
        $services = $this->booking_logic->get_available_services($args);
        
        // Iniciar el buffer de salida
        ob_start();
        
        // Incluir la vista de la lista de servicios
        include CLEVER_BOOKING_PLUGIN_DIR . 'public/views/services-list.php';
        
        // Devolver el contenido del buffer y limpiarlo
        return ob_get_clean();
    }

    /**
     * Registra widgets para Elementor si está activo.
     *
     * @since    1.0.0
     */
    public function register_elementor_widgets() {
        // Comprobar si Elementor está disponible
        if (!did_action('elementor/loaded')) {
            return;
        }
        
        // Registrar categoría de widgets
        add_action('elementor/elements/categories_registered', function($categories_manager) {
            $categories_manager->add_category(
                'clever-booking',
                [
                    'title' => __('Clever Booking', 'clever-booking'),
                    'icon' => 'fa fa-calendar',
                ]
            );
        });
        
        // Comprobar si existen las carpetas y crearlas si no existen
        $elementor_dir = CLEVER_BOOKING_PLUGIN_DIR . 'public/elementor';
        if (!file_exists($elementor_dir)) {
            wp_mkdir_p($elementor_dir);
        }
        
        // Archivos de los widgets
        $service_info_widget_file = $elementor_dir . '/class-service-info-widget.php';
        $booking_button_widget_file = $elementor_dir . '/class-booking-button-widget.php';
        $services_list_widget_file = $elementor_dir . '/class-services-list-widget.php';
        
        // Registrar los widgets solo si existen los archivos
        if (file_exists($service_info_widget_file)) {
            require_once $service_info_widget_file;
        }
        
        if (file_exists($booking_button_widget_file)) {
            require_once $booking_button_widget_file;
        }
        
        if (file_exists($services_list_widget_file)) {
            require_once $services_list_widget_file;
        }
        
        // Registrar los widgets usando el nuevo método de Elementor 3.5+
        add_action('elementor/widgets/register', function($widgets_manager) {
            if (class_exists('Clever_Booking_Service_Info_Widget')) {
                $widgets_manager->register(new \Clever_Booking_Service_Info_Widget());
            }
            
            if (class_exists('Clever_Booking_Button_Widget')) {
                $widgets_manager->register(new \Clever_Booking_Button_Widget());
            }
            
            if (class_exists('Clever_Booking_Services_List_Widget')) {
                $widgets_manager->register(new \Clever_Booking_Services_List_Widget());
            }
        });
    }

    /**
     * Maneja la solicitud AJAX para obtener slots de tiempo disponibles.
     *
     * @since    1.0.0
     */
    public function ajax_get_available_slots() {
        // Verificar nonce para seguridad
        check_ajax_referer('clever_booking_nonce', 'nonce');
        
        // Obtener parámetros
        $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : '';
        $service_id = isset($_POST['service_id']) ? absint($_POST['service_id']) : 0;
        
        // Validar parámetros
        if (empty($date) || empty($service_id)) {
            wp_send_json_error(array('message' => __('Parámetros inválidos', 'clever-booking')));
            return;
        }
        
        // Obtener slots disponibles
        $slots = $this->booking_logic->get_available_time_slots($date, $service_id);
        
        // Si no hay slots disponibles
        if (empty($slots)) {
            wp_send_json_error(array('message' => __('No hay horarios disponibles para esta fecha', 'clever-booking')));
            return;
        }
        
        // Devolver slots en formato para select
        $options = array();
        foreach ($slots as $slot) {
            $start_time = date_i18n(get_option('time_format'), strtotime($slot['start']));
            $end_time = date_i18n(get_option('time_format'), strtotime($slot['end']));
            
            $options[] = array(
                'value' => $slot['start'] . ' - ' . $slot['end'],
                'label' => $start_time . ' - ' . $end_time,
            );
        }
        
        wp_send_json_success(array('slots' => $options));
    }

    /**
     * Maneja la solicitud AJAX para crear una reserva.
     *
     * @since    1.0.0
     */
    public function ajax_create_reservation() {
        // Para depuración
        if (get_option('cb_debug_mode')) {
            error_log('Recibida solicitud AJAX para crear reserva:');
            error_log(print_r($_POST, true));
            
            // Depurar el nonce
            if (isset($_POST['nonce'])) {
                error_log('Nonce recibido: ' . $_POST['nonce']);
            } else {
                error_log('No se recibió nonce en la solicitud');
            }
        }
        
        // Verificar nonce para seguridad (mejorado)
        try {
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'clever_booking_nonce')) {
                if (get_option('cb_debug_mode')) {
                    error_log('Verificación de nonce fallida');
                    error_log('Nonce recibido: ' . (isset($_POST['nonce']) ? $_POST['nonce'] : 'no disponible'));
                }
                wp_send_json_error(array('message' => __('Error de seguridad. Por favor, recarga la página e intenta nuevamente.', 'clever-booking')));
                return;
            }
        } catch (Exception $e) {
            error_log('Error en verificación de nonce: ' . $e->getMessage());
            wp_send_json_error(array('message' => __('Error interno. Por favor, contacta al administrador.', 'clever-booking')));
            return;
        }
        
        // Obtener y validar datos del formulario
        $service_id = isset($_POST['service_id']) ? absint($_POST['service_id']) : 0;
        $booking_date = isset($_POST['booking_date']) ? sanitize_text_field($_POST['booking_date']) : '';
        $booking_time = isset($_POST['booking_time']) ? sanitize_text_field($_POST['booking_time']) : '';
        $customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
        $customer_email = isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '';
        $customer_phone = isset($_POST['customer_phone']) ? sanitize_text_field($_POST['customer_phone']) : '';
        $customer_address = isset($_POST['customer_address']) ? sanitize_textarea_field($_POST['customer_address']) : '';
        $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
        
        // Validar campos requeridos
        if (empty($service_id) || empty($booking_date) || empty($booking_time) || 
            empty($customer_name) || empty($customer_email) || 
            empty($customer_phone) || empty($customer_address)) {
            
            if (get_option('cb_debug_mode')) {
                error_log('Datos de reserva incompletos: ' . print_r($_POST, true));
            }
            
            wp_send_json_error(array('message' => __('Por favor, completa todos los campos requeridos', 'clever-booking')));
            return;
        }
        
        // Extraer hora de inicio y fin del formato "HH:MM:SS - HH:MM:SS"
        $time_parts = explode(' - ', $booking_time);
        if (count($time_parts) !== 2) {
            if (get_option('cb_debug_mode')) {
                error_log('Formato de hora inválido: ' . $booking_time);
            }
            
            wp_send_json_error(array('message' => __('Formato de hora inválido', 'clever-booking')));
            return;
        }
        
        $start_time = trim($time_parts[0]);
        $end_time = trim($time_parts[1]);
        
        // Preparar datos para crear la reserva
        $reservation_data = array(
            'service_id' => $service_id,
            'booking_date' => $booking_date,
            'booking_time' => $start_time,
            'booking_end_time' => $end_time,
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'customer_phone' => $customer_phone,
            'customer_address' => $customer_address,
            'notes' => $notes,
            'status' => 'pending', // Estado inicial
        );
        
        try {
            // Intentar crear la reserva
            $reservation_id = $this->booking_logic->create_reservation($reservation_data);
            
            if (!$reservation_id) {
                if (get_option('cb_debug_mode')) {
                    error_log('Error al crear la reserva con los datos: ' . print_r($reservation_data, true));
                }
                
                wp_send_json_error(array('message' => __('Error al crear la reserva. Verifica la disponibilidad.', 'clever-booking')));
                return;
            }
            
            try {
                // Si se creó correctamente, enviar notificaciones
                if (class_exists('Clever_Booking_Notifications')) {
                    $notifications = new Clever_Booking_Notifications();
                    $notifications->send_admin_notification($reservation_id);
                    $notifications->send_customer_confirmation($reservation_id);
                }
            } catch (Exception $e) {
                // Registrar el error pero continuar
                error_log('Error al enviar notificaciones: ' . $e->getMessage());
            }
            
            // Obtener detalles del servicio para la respuesta
            $service = get_post($service_id);
            $service_title = $service ? $service->post_title : __('Servicio', 'clever-booking');
            
            // Formatear fecha y hora para mostrar
            $formatted_date = date_i18n(get_option('date_format'), strtotime($booking_date));
            $formatted_start = date_i18n(get_option('time_format'), strtotime($start_time));
            $formatted_end = date_i18n(get_option('time_format'), strtotime($end_time));
            
            // Enviar respuesta de éxito
            wp_send_json_success(array(
                'message' => __('¡Reserva creada con éxito! Te hemos enviado un email de confirmación.', 'clever-booking'),
                'reservation_id' => $reservation_id,
                'service' => $service_title,
                'date' => $formatted_date,
                'time' => $formatted_start . ' - ' . $formatted_end,
            ));
        } catch (Exception $e) {
            error_log('Exception al crear reserva: ' . $e->getMessage());
            wp_send_json_error(array('message' => __('Error interno al crear la reserva. Por favor, contacta al administrador.', 'clever-booking')));
        }
    }
} 
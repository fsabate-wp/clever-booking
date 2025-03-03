<?php
/**
 * La funcionalidad específica de administración del plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * @package    Clever_Booking
 * @subpackage Clever_Booking/admin
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

/**
 * La clase de administración del plugin.
 */
class Clever_Booking_Admin {

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
     * Instancia de la integración con ACF.
     *
     * @since    1.0.0
     * @access   private
     * @var      Clever_Booking_ACF_Integration    $acf    Instancia de la integración con ACF.
     */
    private $acf;

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
        
        // Inicializar la instancia ACF si no existe
        if (class_exists('ACF') && !isset($this->acf)) {
            $this->acf = new Clever_Booking_ACF_Integration();
        }
        
        // Asegurar que el buffer de salida esté disponible para redirecciones
        add_action('admin_init', array($this, 'ensure_output_buffer'));
    }

    /**
     * Asegura que el buffer de salida esté inicializado
     *
     * @since    1.0.0
     */
    public function ensure_output_buffer() {
        // Solo inicializar si estamos en una página de nuestro plugin
        if (isset($_GET['page']) && strpos($_GET['page'], 'clever-booking') !== false) {
            if (ob_get_level() == 0) {
                ob_start();
            }
        }
    }

    /**
     * Registra los estilos de la interfaz de administración.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, CLEVER_BOOKING_PLUGIN_URL . 'admin/css/clever-booking-admin.css', array(), $this->version, 'all');
        
        // Solo cargar en las páginas del plugin
        $screen = get_current_screen();
        if (strpos($screen->id, 'clever-booking') !== false) {
            // Añadir estilos adicionales para las páginas específicas del plugin
            wp_enqueue_style('jquery-ui-datepicker', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
            
            // FullCalendar para la vista de calendario
            if (strpos($screen->id, 'clever-booking-calendar') !== false) {
                wp_enqueue_style('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css', array(), '5.11.3');
            }
        }
    }

    /**
     * Registra los scripts de la interfaz de administración.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, CLEVER_BOOKING_PLUGIN_URL . 'admin/js/clever-booking-admin.js', array('jquery'), $this->version, false);
        
        // Solo cargar en las páginas del plugin
        $screen = get_current_screen();
        if (strpos($screen->id, 'clever-booking') !== false) {
            // Añadir scripts adicionales para las páginas específicas del plugin
            wp_enqueue_script('jquery-ui-datepicker');
            
            // Localize script para ajax
            wp_localize_script($this->plugin_name, 'clever_booking_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('clever_booking_admin_nonce'),
            ));
            
            // FullCalendar para la vista de calendario
            if (strpos($screen->id, 'clever-booking-calendar') !== false) {
                wp_enqueue_script('fullcalendar-core', 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js', array('jquery'), '5.11.3', true);
                
                // Añadir datos para el calendario
                $booking_logic = new Clever_Booking_Logic();
                $reservations = $booking_logic->get_reservations();
                
                $calendar_events = array();
                foreach ($reservations as $reservation) {
                    $service = get_post($reservation->service_id);
                    $service_title = $service ? $service->post_title : __('Servicio Desconocido', 'clever-booking');
                    
                    $calendar_events[] = array(
                        'id' => $reservation->id,
                        'title' => $service_title . ' - ' . $reservation->customer_name,
                        'start' => $reservation->booking_date . 'T' . $reservation->booking_time,
                        'end' => $reservation->booking_date . 'T' . $reservation->booking_end_time,
                        'url' => admin_url('admin.php?page=clever-booking-reservations&action=edit&id=' . $reservation->id),
                        'className' => 'status-' . $reservation->status,
                        'service_id' => $reservation->service_id
                    );
                }
                
                wp_localize_script($this->plugin_name, 'clever_booking_calendar', array(
                    'events' => $calendar_events,
                ));
            }
        }
    }

    /**
     * Añade el menú del plugin al panel de administración.
     *
     * @since    1.0.0
     */
    public function add_admin_menu() {
        // Menú principal
        add_menu_page(
            __('Clever Booking', 'clever-booking'),
            __('Clever Booking', 'clever-booking'),
            'manage_options',
            'clever-booking',
            array($this, 'display_dashboard_page'),
            'dashicons-calendar-alt',
            26
        );
        
        // Submenú: Dashboard (usa la misma función que el menú principal)
        add_submenu_page(
            'clever-booking',
            __('Dashboard', 'clever-booking'),
            __('Dashboard', 'clever-booking'),
            'manage_options',
            'clever-booking',
            array($this, 'display_dashboard_page')
        );
        
        // Submenú: Servicios (redirige al CPT)
        add_submenu_page(
            'clever-booking',
            __('Servicios', 'clever-booking'),
            __('Servicios', 'clever-booking'),
            'manage_options',
            'edit.php?post_type=cb_service',
            null
        );
        
        // Submenú: Categorías de Servicios (redirige a la taxonomía)
        add_submenu_page(
            'clever-booking',
            __('Categorías', 'clever-booking'),
            __('Categorías', 'clever-booking'),
            'manage_options',
            'edit-tags.php?taxonomy=cb_service_category&post_type=cb_service',
            null
        );
        
        // Submenú: Reservas
        add_submenu_page(
            'clever-booking',
            __('Reservas', 'clever-booking'),
            __('Reservas', 'clever-booking'),
            'manage_options',
            'clever-booking-reservations',
            array($this, 'display_reservations_page')
        );
        
        // Submenú: Calendario
        add_submenu_page(
            'clever-booking',
            __('Calendario', 'clever-booking'),
            __('Calendario', 'clever-booking'),
            'manage_options',
            'clever-booking-calendar',
            array($this, 'display_calendar_page')
        );
        
        // Submenú: Configuración
        add_submenu_page(
            'clever-booking',
            __('Configuración', 'clever-booking'),
            __('Configuración', 'clever-booking'),
            'manage_options',
            'clever-booking-settings',
            array($this, 'display_settings_page')
        );
        
        // Si ACF está activo, registrar la página de opciones
        if (function_exists('acf_add_options_page')) {
            acf_add_options_sub_page(array(
                'page_title'     => __('Configuración de Clever Booking', 'clever-booking'),
                'menu_title'     => __('Configuración', 'clever-booking'),
                'parent_slug'    => 'clever-booking',
                'menu_slug'      => 'clever-booking-settings',
                'capability'     => 'manage_options',
                'position'       => false,
                'parent_slug'    => 'clever-booking',
                'redirect'       => false,
            ));
        }
    }

    /**
     * Registra la configuración del plugin.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        // Registrar opciones generales
        register_setting('clever_booking_settings', 'cb_company_name');
        register_setting('clever_booking_settings', 'cb_company_email');
        register_setting('clever_booking_settings', 'cb_company_phone');
        
        // Registrar opciones de emails
        register_setting('clever_booking_settings', 'cb_admin_email');
        register_setting('clever_booking_settings', 'cb_email_from_name');
        register_setting('clever_booking_settings', 'cb_email_from_address');
        register_setting('clever_booking_settings', 'cb_admin_notification_subject');
        register_setting('clever_booking_settings', 'cb_customer_notification_subject');
        register_setting('clever_booking_settings', 'cb_reminder_notification_subject');
        
        // Registrar opciones de reservas
        register_setting('clever_booking_settings', 'cb_min_time_before_booking');
        register_setting('clever_booking_settings', 'cb_max_days_advance_booking');
        register_setting('clever_booking_settings', 'cb_available_days', array(
            'sanitize_callback' => function($value) {
                return is_array($value) ? $value : array();
            }
        ));
        register_setting('clever_booking_settings', 'cb_time_slot_interval');
        register_setting('clever_booking_settings', 'cb_business_hours_start');
        register_setting('clever_booking_settings', 'cb_business_hours_end');
        register_setting('clever_booking_settings', 'cb_default_reservation_status');
        
        // Registrar opciones de apariencia
        register_setting('clever_booking_settings', 'cb_primary_color');
        register_setting('clever_booking_settings', 'cb_services_default_layout');
        register_setting('clever_booking_settings', 'cb_services_default_columns');
        register_setting('clever_booking_settings', 'cb_custom_css');
        
        // Registrar opciones avanzadas
        register_setting('clever_booking_settings', 'cb_allow_multiple_bookings');
        register_setting('clever_booking_settings', 'cb_google_maps_api_key');
        register_setting('clever_booking_settings', 'cb_debug_mode');
    }

    /**
     * Muestra la página del dashboard.
     *
     * @since    1.0.0
     */
    public function display_dashboard_page() {
        // Inicializar la lógica de reservas
        $booking_logic = new Clever_Booking_Logic();
        
        // Obtener estadísticas
        $reservations = $booking_logic->get_reservations(array(
            'limit' => 10, // Mostrar solo las 10 últimas
        ));
        
        // Contar reservas por estado
        global $wpdb;
        $table_name = $wpdb->prefix . 'clever_booking_reservations';
        
        $pending_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'pending'");
        $confirmed_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'confirmed'");
        $completed_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'completed'");
        $cancelled_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'cancelled'");
        
        // Obtener servicios disponibles
        $services = $booking_logic->get_available_services();
        
        // Incluir la vista del dashboard
        include CLEVER_BOOKING_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Muestra la página de reservas.
     *
     * @since    1.0.0
     */
    public function display_reservations_page() {
        // Verificar qué acción realizar
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        
        // Inicializar la lógica de reservas
        $booking_logic = new Clever_Booking_Logic();
        
        switch ($action) {
            case 'edit':
                // Obtener ID de la reserva
                $id = isset($_GET['id']) ? absint($_GET['id']) : 0;
                
                // Obtener datos de la reserva
                $reservation = $booking_logic->get_reservation($id);
                
                if (!$reservation) {
                    wp_die(__('Reserva no encontrada.', 'clever-booking'));
                }
                
                // Guardar cambios si se ha enviado el formulario
                if (isset($_POST['save_reservation']) && check_admin_referer('save_reservation', 'reservation_nonce')) {
                    $update_data = array(
                        'status' => sanitize_text_field($_POST['status']),
                    );
                    
                    $result = $booking_logic->update_reservation($id, $update_data);
                    
                    if ($result) {
                        // Notificar al cliente si cambió el estado
                        if ($_POST['status'] !== $reservation->status) {
                            $notifications = new Clever_Booking_Notifications();
                            $notifications->send_status_update_notification($id, $_POST['status']);
                        }
                        
                        // Guardar en variable de sesión si la actualización fue exitosa
                        if (!session_id()) {
                            session_start();
                        }
                        $_SESSION['cb_reservation_updated'] = true;
                        
                        // Evitar cualquier salida antes de la redirección
                        if (ob_get_length()) {
                            ob_clean();
                        }
                        
                        // Redirigir para evitar reenvíos
                        wp_redirect(admin_url('admin.php?page=clever-booking-reservations&action=edit&id=' . $id . '&updated=1'));
                        exit;
                    } else {
                        // Si hay un error, mostrar mensaje
                        add_settings_error(
                            'clever_booking',
                            'update_failed',
                            __('No se pudo actualizar la reserva. Por favor, inténtalo de nuevo.', 'clever-booking'),
                            'error'
                        );
                    }
                }
                
                // Incluir la vista de edición
                include CLEVER_BOOKING_PLUGIN_DIR . 'admin/views/edit-reservation.php';
                break;
                
            case 'delete':
                // Obtener ID de la reserva
                $id = isset($_GET['id']) ? absint($_GET['id']) : 0;
                
                // Verificar nonce para seguridad
                if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_reservation_' . $id)) {
                    $result = $booking_logic->delete_reservation($id);
                    
                    // Guardar en variable de sesión si la eliminación fue exitosa
                    if (!session_id()) {
                        session_start();
                    }
                    $_SESSION['cb_reservation_deleted'] = true;
                    
                    // Evitar cualquier salida antes de la redirección
                    if (ob_get_length()) {
                        ob_clean();
                    }
                    
                    wp_redirect(admin_url('admin.php?page=clever-booking-reservations&deleted=1'));
                    exit;
                } else {
                    wp_die(__('No tienes permiso para realizar esta acción.', 'clever-booking'));
                }
                break;
                
            case 'list':
            default:
                // Lista de reservas
                $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
                $per_page = 20;
                $offset = ($current_page - 1) * $per_page;
                
                // Filtros
                $where = '';
                $where_values = array();
                
                // Filtrar por fecha
                if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                    $date_from = sanitize_text_field($_GET['date_from']);
                    $where .= $where ? ' AND ' : '';
                    $where .= 'booking_date >= %s';
                    $where_values[] = $date_from;
                }
                
                if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                    $date_to = sanitize_text_field($_GET['date_to']);
                    $where .= $where ? ' AND ' : '';
                    $where .= 'booking_date <= %s';
                    $where_values[] = $date_to;
                }
                
                // Filtrar por estado
                if (isset($_GET['status']) && !empty($_GET['status'])) {
                    $status = sanitize_text_field($_GET['status']);
                    $where .= $where ? ' AND ' : '';
                    $where .= 'status = %s';
                    $where_values[] = $status;
                }
                
                // Obtener total de registros para paginación
                global $wpdb;
                $table_name = $wpdb->prefix . 'clever_booking_reservations';
                
                $count_sql = "SELECT COUNT(*) FROM {$table_name}";
                if ($where) {
                    $count_sql .= " WHERE " . $where;
                    $count_sql = $wpdb->prepare($count_sql, $where_values);
                }
                
                $total_items = $wpdb->get_var($count_sql);
                $total_pages = ceil($total_items / $per_page);
                
                // Obtener reservas paginadas
                $reservations = $booking_logic->get_reservations(array(
                    'limit' => $per_page,
                    'offset' => $offset,
                    'where' => $where,
                    'where_values' => $where_values,
                ));
                
                // Incluir la vista de listado
                include CLEVER_BOOKING_PLUGIN_DIR . 'admin/views/reservations-list.php';
                break;
        }
    }

    /**
     * Muestra la página del calendario.
     *
     * @since    1.0.0
     */
    public function display_calendar_page() {
        // Incluir la vista del calendario
        include CLEVER_BOOKING_PLUGIN_DIR . 'admin/views/calendar.php';
    }

    /**
     * Muestra la página de configuración.
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        // Incluir la vista de configuración
        include CLEVER_BOOKING_PLUGIN_DIR . 'admin/views/settings.php';
    }
} 
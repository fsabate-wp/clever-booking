<?php
/**
 * La clase principal del plugin que define toda la funcionalidad
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
 * Clase principal del plugin
 */
class Clever_Booking {

    /**
     * Instancia única de esta clase (Singleton)
     *
     * @since    1.0.0
     * @access   protected
     * @var      Clever_Booking    $instance    Instancia única de esta clase.
     */
    protected static $instance = null;

    /**
     * El loader que mantiene y registra todos los hooks del plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Clever_Booking_Loader    $loader    Mantiene y registra todos los hooks del plugin.
     */
    protected $loader;

    /**
     * Nombre único del plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    El nombre del plugin.
     */
    protected $plugin_name;

    /**
     * Versión actual del plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    La versión actual del plugin.
     */
    protected $version;

    /**
     * Constructor de la clase.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->plugin_name = 'clever-booking';
        $this->version = CLEVER_BOOKING_VERSION;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_post_types();
    }

    /**
     * Carga las dependencias necesarias para el plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // El archivo que contiene la clase Loader
        require_once CLEVER_BOOKING_PLUGIN_DIR . 'includes/class-loader.php';

        // El archivo que maneja la internacionalización
        require_once CLEVER_BOOKING_PLUGIN_DIR . 'includes/class-i18n.php';

        // El archivo que contiene la clase de tipos de post
        require_once CLEVER_BOOKING_PLUGIN_DIR . 'includes/class-post-types.php';

        // El archivo que contiene la clase para la integración con ACF
        require_once CLEVER_BOOKING_PLUGIN_DIR . 'includes/class-acf-integration.php';

        // El archivo que contiene la clase para la lógica de las reservas
        require_once CLEVER_BOOKING_PLUGIN_DIR . 'includes/class-booking-logic.php';

        // El archivo que contiene la clase para las notificaciones
        require_once CLEVER_BOOKING_PLUGIN_DIR . 'includes/class-notifications.php';

        // El archivo que define la clase de la parte de administración
        require_once CLEVER_BOOKING_PLUGIN_DIR . 'admin/class-admin.php';

        // El archivo que define la clase de la parte pública
        require_once CLEVER_BOOKING_PLUGIN_DIR . 'public/class-public.php';

        // Crear instancia del loader
        $this->loader = new Clever_Booking_Loader();
    }

    /**
     * Define la configuración de internacionalización del plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Clever_Booking_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Registra todos los hooks relacionados con la funcionalidad de administración.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Clever_Booking_Admin($this->get_plugin_name(), $this->get_version());

        // Archivos CSS y JS
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Menú de administración
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');

        // Páginas de admin personalizadas
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
    }

    /**
     * Registra todos los hooks relacionados con la funcionalidad pública.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Clever_Booking_Public($this->get_plugin_name(), $this->get_version());

        // Archivos CSS y JS
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Shortcodes
        $this->loader->add_action('init', $plugin_public, 'register_shortcodes');

        // Widget de Elementor
        if (did_action('elementor/loaded')) {
            $this->loader->add_action('elementor/widgets/widgets_registered', $plugin_public, 'register_elementor_widgets');
        }
        
        // Registrar funciones AJAX
        $this->loader->add_action('wp_ajax_cb_get_available_slots', $plugin_public, 'ajax_get_available_slots');
        $this->loader->add_action('wp_ajax_nopriv_cb_get_available_slots', $plugin_public, 'ajax_get_available_slots');
        $this->loader->add_action('wp_ajax_cb_create_reservation', $plugin_public, 'ajax_create_reservation');
        $this->loader->add_action('wp_ajax_nopriv_cb_create_reservation', $plugin_public, 'ajax_create_reservation');
    }

    /**
     * Registra tipos de post y taxonomías
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_post_types() {
        $plugin_post_types = new Clever_Booking_Post_Types();
        $this->loader->add_action('init', $plugin_post_types, 'register_post_types', 0);
        $this->loader->add_action('init', $plugin_post_types, 'register_taxonomies', 0);
    }

    /**
     * Ejecuta el loader para registrar todas las acciones y filtros.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Obtiene el nombre del plugin utilizado para identificarlo en WP.
     *
     * @since     1.0.0
     * @return    string    El nombre del plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Obtiene la referencia al loader que registra los hooks.
     *
     * @since     1.0.0
     * @return    Clever_Booking_Loader    Mantiene todos los hooks del plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Obtiene la versión del plugin.
     *
     * @since     1.0.0
     * @return    string    Número de versión actual del plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Obtiene la instancia del singleton de esta clase
     * 
     * @since     1.0.0
     * @return    Clever_Booking    Instancia única de esta clase.
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
} 
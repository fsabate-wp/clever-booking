<?php
/**
 * Widget de Elementor para el formulario de reserva
 *
 * @link       
 * @since      1.0.0
 *
 * @package    Clever_Booking
 * @subpackage Clever_Booking/public/elementor
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}

/**
 * Widget de formulario de reserva para Elementor
 */
class Clever_Booking_Form_Widget extends \Elementor\Widget_Base {
    
    /**
     * Obtener el nombre del widget
     *
     * @since  1.0.0
     * @return string Nombre del widget.
     */
    public function get_name() {
        return 'clever_booking_form';
    }
    
    /**
     * Obtener el título del widget
     *
     * @since  1.0.0
     * @return string Título del widget.
     */
    public function get_title() {
        return __('Formulario de Reserva', 'clever-booking');
    }
    
    /**
     * Obtener el icono del widget
     *
     * @since  1.0.0
     * @return string Nombre del icono.
     */
    public function get_icon() {
        return 'eicon-form-horizontal';
    }
    
    /**
     * Obtener las categorías del widget
     *
     * @since  1.0.0
     * @return array Lista de categorías.
     */
    public function get_categories() {
        return ['clever-booking'];
    }
    
    /**
     * Obtener las palabras clave del widget
     *
     * @since  1.0.0
     * @return array Lista de palabras clave.
     */
    public function get_keywords() {
        return ['reserva', 'booking', 'formulario', 'reservar'];
    }
    
    /**
     * Registrar los controles del widget
     *
     * @since  1.0.0
     */
    protected function register_controls() {
        // Sección de contenido
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Contenido', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        // Obtener lista de servicios para el selector
        $services = get_posts([
            'post_type' => 'cb_service',
            'post_status' => 'publish',
            'numberposts' => -1,
        ]);
        
        $service_options = [
            '0' => __('Todos los servicios', 'clever-booking'),
        ];
        
        foreach ($services as $service) {
            $service_options[$service->ID] = $service->post_title;
        }
        
        $this->add_control(
            'service_id',
            [
                'label' => __('Servicio Preseleccionado', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '0',
                'options' => $service_options,
                'description' => __('Selecciona un servicio específico o muestra todos.', 'clever-booking'),
            ]
        );
        
        $this->end_controls_section();
        
        // Sección de estilo
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Estilo', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'primary_color',
            [
                'label' => __('Color Principal', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#4a6bff',
                'selectors' => [
                    '{{WRAPPER}} .cb-button' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                    '{{WRAPPER}} .cb-step.cb-step-active' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cb-step.cb-step-active .cb-step-number' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .cb-service-price' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Renderizar el widget
     *
     * @since  1.0.0
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $atts = [
            'service_id' => $settings['service_id'],
        ];
        
        // Inicializar la clase Public que contiene el shortcode
        $plugin_public = new Clever_Booking_Public('clever-booking', CLEVER_BOOKING_VERSION);
        
        // Renderizar el shortcode
        echo $plugin_public->booking_form_shortcode($atts);
    }
} 
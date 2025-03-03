<?php
/**
 * Widget de Elementor para la lista de servicios
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
 * Widget de lista de servicios para Elementor
 */
class Clever_Booking_Services_List_Widget extends \Elementor\Widget_Base {
    
    /**
     * Obtener el nombre del widget
     *
     * @since  1.0.0
     * @return string Nombre del widget.
     */
    public function get_name() {
        return 'clever_booking_services_list';
    }
    
    /**
     * Obtener el título del widget
     *
     * @since  1.0.0
     * @return string Título del widget.
     */
    public function get_title() {
        return __('Lista de Servicios', 'clever-booking');
    }
    
    /**
     * Obtener el icono del widget
     *
     * @since  1.0.0
     * @return string Nombre del icono.
     */
    public function get_icon() {
        return 'eicon-products-grid';
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
        return ['servicios', 'booking', 'listado', 'reservar'];
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
        
        // Obtener las categorías para el selector
        $categories = get_terms([
            'taxonomy' => 'cb_service_category',
            'hide_empty' => false,
        ]);
        
        $category_options = [
            '0' => __('Todas las categorías', 'clever-booking'),
        ];
        
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $category_options[$category->term_id] = $category->name;
            }
        }
        
        $this->add_control(
            'category_id',
            [
                'label' => __('Categoría', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '0',
                'options' => $category_options,
                'description' => __('Selecciona una categoría específica o muestra todas.', 'clever-booking'),
            ]
        );
        
        $this->add_control(
            'layout',
            [
                'label' => __('Diseño', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => __('Cuadrícula', 'clever-booking'),
                    'list' => __('Lista', 'clever-booking'),
                ],
            ]
        );
        
        $this->add_control(
            'columns',
            [
                'label' => __('Columnas', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'condition' => [
                    'layout' => 'grid',
                ],
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
                    '{{WRAPPER}} .cb-service-price' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cb-service-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'card_bg_color',
            [
                'label' => __('Color de Fondo de Tarjeta', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .cb-service-inner' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .cb-service-content' => 'background-color: {{VALUE}};',
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
            'category_id' => $settings['category_id'],
            'layout' => $settings['layout'],
            'columns' => $settings['columns'],
        ];
        
        // Inicializar la clase Public que contiene el shortcode
        $plugin_public = new Clever_Booking_Public('clever-booking', CLEVER_BOOKING_VERSION);
        
        // Renderizar el shortcode
        echo $plugin_public->services_list_shortcode($atts);
    }
} 
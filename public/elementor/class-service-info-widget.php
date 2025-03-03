<?php
/**
 * Widget de Elementor para mostrar información del servicio
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
 * Widget para mostrar información del servicio en Elementor
 */
class Clever_Booking_Service_Info_Widget extends \Elementor\Widget_Base {
    
    /**
     * Obtener el nombre del widget
     *
     * @since  1.0.0
     * @return string Nombre del widget.
     */
    public function get_name() {
        return 'clever_booking_service_info';
    }
    
    /**
     * Obtener el título del widget
     *
     * @since  1.0.0
     * @return string Título del widget.
     */
    public function get_title() {
        return __('Info de Servicio', 'clever-booking');
    }
    
    /**
     * Obtener el icono del widget
     *
     * @since  1.0.0
     * @return string Nombre del icono.
     */
    public function get_icon() {
        return 'eicon-info-box';
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
        return ['servicio', 'booking', 'info', 'detalles'];
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
        
        $this->add_control(
            'show_price',
            [
                'label' => __('Mostrar Precio', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'clever-booking'),
                'label_off' => __('No', 'clever-booking'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_duration',
            [
                'label' => __('Mostrar Duración', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'clever-booking'),
                'label_off' => __('No', 'clever-booking'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_categories',
            [
                'label' => __('Mostrar Categorías', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'clever-booking'),
                'label_off' => __('No', 'clever-booking'),
                'return_value' => 'yes',
                'default' => 'yes',
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
                    '{{WRAPPER}} .cb-service-price' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'secondary_color',
            [
                'label' => __('Color Secundario', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#6c757d',
                'selectors' => [
                    '{{WRAPPER}} .cb-service-duration' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cb-service-category' => 'color: {{VALUE}};',
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
        
        // Obtener el ID del servicio actual
        $service_id = get_the_ID();
        
        // Solo mostrar si estamos en un post de tipo servicio
        if (get_post_type($service_id) !== 'cb_service') {
            echo '<div class="cb-error">' . __('Este widget solo debe usarse en páginas de servicio.', 'clever-booking') . '</div>';
            return;
        }
        
        // Obtener datos del servicio
        $price = get_field('service_price', $service_id);
        $duration = get_field('service_duration', $service_id);
        $price_display = '$' . number_format((float)$price, 2);
        $duration_display = $duration . ' ' . __('min', 'clever-booking');
        
        // Obtener categorías
        $categories = wp_get_post_terms($service_id, 'cb_service_category', array('fields' => 'names'));
        $category_text = !empty($categories) ? implode(', ', $categories) : '';
        
        // Mostrar la información
        ?>
        <div class="cb-service-info-widget">
            <?php if ($settings['show_categories'] === 'yes' && !empty($category_text)) : ?>
            <div class="cb-service-category">
                <span class="cb-label"><?php echo esc_html__('Categoría:', 'clever-booking'); ?></span>
                <span class="cb-value"><?php echo esc_html($category_text); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($settings['show_price'] === 'yes') : ?>
            <div class="cb-service-price">
                <span class="cb-label"><?php echo esc_html__('Precio:', 'clever-booking'); ?></span>
                <span class="cb-value"><?php echo esc_html($price_display); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($settings['show_duration'] === 'yes') : ?>
            <div class="cb-service-duration">
                <span class="cb-label"><?php echo esc_html__('Duración:', 'clever-booking'); ?></span>
                <span class="cb-value"><?php echo esc_html($duration_display); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <style>
            .cb-service-info-widget {
                padding: 20px;
                background-color: #f8f9fa;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .cb-service-info-widget > div {
                margin-bottom: 10px;
            }
            .cb-service-info-widget .cb-label {
                font-weight: bold;
                margin-right: 5px;
            }
        </style>
        <?php
    }
} 
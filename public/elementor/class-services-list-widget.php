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

// Asegurarse de que Elementor está cargado
if (!did_action('elementor/loaded')) {
    return;
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
        return 'eicon-posts-grid';
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
     * Obtener estilos del widget
     *
     * @return string[]
     */
    public function get_style_depends() {
        return ['clever-booking-public'];
    }

    /**
     * Obtener scripts del widget
     *
     * @return string[]
     */
    public function get_script_depends() {
        return ['clever-booking-public'];
    }
    
    /**
     * Registrar los controles del widget
     *
     * @since  1.0.0
     */
    protected function register_controls() {
        // SECCIÓN: CONTENIDO BÁSICO
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

        $this->add_control(
            'items_per_page',
            [
                'label' => __('Servicios por página', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'default' => 9,
            ]
        );
        
        $this->end_controls_section();

        // SECCIÓN: ELEMENTOS VISIBLES
        $this->start_controls_section(
            'section_display_elements',
            [
                'label' => __('Elementos Visibles', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label' => __('Mostrar Imagen', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'clever-booking'),
                'label_off' => __('No', 'clever-booking'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __('Mostrar Título', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'clever-booking'),
                'label_off' => __('No', 'clever-booking'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_meta',
            [
                'label' => __('Mostrar Meta (categorías)', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'clever-booking'),
                'label_off' => __('No', 'clever-booking'),
                'return_value' => 'yes',
                'default' => 'yes',
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
            'show_details_button',
            [
                'label' => __('Mostrar Botón Detalles', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'clever-booking'),
                'label_off' => __('No', 'clever-booking'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'details_button_text',
            [
                'label' => __('Texto Botón Detalles', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Ver Detalles', 'clever-booking'),
                'placeholder' => __('Ver Detalles', 'clever-booking'),
                'condition' => [
                    'show_details_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_booking_button',
            [
                'label' => __('Mostrar Botón Reserva', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'clever-booking'),
                'label_off' => __('No', 'clever-booking'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'booking_button_text',
            [
                'label' => __('Texto Botón Reserva', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Reservar Ahora', 'clever-booking'),
                'placeholder' => __('Reservar Ahora', 'clever-booking'),
                'condition' => [
                    'show_booking_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => __('Mostrar Paginación', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'clever-booking'),
                'label_off' => __('No', 'clever-booking'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // SECCIÓN: ESTILO DE LA TARJETA
        $this->start_controls_section(
            'section_card_style',
            [
                'label' => __('Estilo de Tarjeta', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_bg_color',
            [
                'label' => __('Color de Fondo', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .cb-service-inner' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __('Padding', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_responsive_control(
            'card_margin',
            [
                'label' => __('Margin', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Border Radius', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '8',
                    'right' => '8',
                    'bottom' => '8',
                    'left' => '8',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .cb-service-inner',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .cb-service-inner',
            ]
        );

        $this->end_controls_section();

        // SECCIÓN: ESTILO DE IMAGEN
        $this->start_controls_section(
            'section_image_style',
            [
                'label' => __('Imagen', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_image' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => __('Altura', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 500,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-thumbnail img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_padding',
            [
                'label' => __('Padding', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-thumbnail' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // SECCIÓN: ESTILO DE TÍTULO
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => __('Título', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-service-title a' => 'color: {{VALUE}};',
                ],
                'default' => '#333333',
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => __('Color al pasar el mouse', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-service-title a:hover' => 'color: {{VALUE}};',
                ],
                'default' => '#4a6bff',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .cb-service-title',
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => __('Espaciado', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // SECCIÓN: ESTILO DE META
        $this->start_controls_section(
            'section_meta_style',
            [
                'label' => __('Meta', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => __('Color', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-service-category span' => 'color: {{VALUE}};',
                ],
                'default' => '#666666',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'selector' => '{{WRAPPER}} .cb-service-category',
            ]
        );

        $this->add_responsive_control(
            'meta_spacing',
            [
                'label' => __('Espaciado', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // SECCIÓN: ESTILO DE PRECIO
        $this->start_controls_section(
            'section_price_style',
            [
                'label' => __('Precio', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_price' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Color', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-service-price' => 'color: {{VALUE}};',
                ],
                'default' => '#4a6bff',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .cb-service-price',
            ]
        );

        $this->add_responsive_control(
            'price_spacing',
            [
                'label' => __('Espaciado', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // SECCIÓN: ESTILO DE BOTONES
        $this->start_controls_section(
            'section_buttons_style',
            [
                'label' => __('Botones', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'buttons_alignment',
            [
                'label' => __('Alineación', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Izquierda', 'clever-booking'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Centro', 'clever-booking'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Derecha', 'clever-booking'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'space-between' => [
                        'title' => __('Justificado', 'clever-booking'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => 'space-between',
                'selectors' => [
                    '{{WRAPPER}} .cb-service-actions' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'buttons_direction',
            [
                'label' => __('Dirección', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'row',
                'options' => [
                    'row' => __('Horizontal', 'clever-booking'),
                    'column' => __('Vertical', 'clever-booking'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-actions' => 'flex-direction: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'buttons_gap',
            [
                'label' => __('Espacio entre botones', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cb-service-actions' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // SECCIÓN: ESTILO DEL BOTÓN DETALLES
        $this->start_controls_section(
            'section_details_button_style',
            [
                'label' => __('Botón Detalles', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_details_button' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs(
            'details_button_style_tabs'
        );

        $this->start_controls_tab(
            'details_button_normal_tab',
            [
                'label' => __('Normal', 'clever-booking'),
            ]
        );

        $this->add_control(
            'details_button_bg_color',
            [
                'label' => __('Color de fondo', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-view-service' => 'background-color: {{VALUE}};',
                ],
                'default' => '#f5f5f5',
            ]
        );

        $this->add_control(
            'details_button_text_color',
            [
                'label' => __('Color de texto', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-view-service' => 'color: {{VALUE}};',
                ],
                'default' => '#333333',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'details_button_border',
                'label' => __('Borde', 'clever-booking'),
                'selector' => '{{WRAPPER}} .cb-view-service',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => 1,
                            'right' => 1,
                            'bottom' => 1,
                            'left' => 1,
                            'isLinked' => true,
                        ],
                    ],
                    'color' => [
                        'default' => '#e0e0e0',
                    ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'details_button_hover_tab',
            [
                'label' => __('Hover', 'clever-booking'),
            ]
        );

        $this->add_control(
            'details_button_bg_hover_color',
            [
                'label' => __('Color de fondo', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-view-service:hover' => 'background-color: {{VALUE}};',
                ],
                'default' => '#e5e5e5',
            ]
        );

        $this->add_control(
            'details_button_text_hover_color',
            [
                'label' => __('Color de texto', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-view-service:hover' => 'color: {{VALUE}};',
                ],
                'default' => '#000000',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'details_button_border_hover',
                'label' => __('Borde', 'clever-booking'),
                'selector' => '{{WRAPPER}} .cb-view-service:hover',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => 1,
                            'right' => 1,
                            'bottom' => 1,
                            'left' => 1,
                            'isLinked' => true,
                        ],
                    ],
                    'color' => [
                        'default' => '#d0d0d0',
                    ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'details_button_typography',
                'selector' => '{{WRAPPER}} .cb-view-service',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'details_button_border_radius',
            [
                'label' => __('Border Radius', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-view-service' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '4',
                    'right' => '4',
                    'bottom' => '4',
                    'left' => '4',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_responsive_control(
            'details_button_padding',
            [
                'label' => __('Padding', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-view-service' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // SECCIÓN: ESTILO DEL BOTÓN RESERVA
        $this->start_controls_section(
            'section_booking_button_style',
            [
                'label' => __('Botón Reserva', 'clever-booking'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_booking_button' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs(
            'booking_button_style_tabs'
        );

        $this->start_controls_tab(
            'booking_button_normal_tab',
            [
                'label' => __('Normal', 'clever-booking'),
            ]
        );

        $this->add_control(
            'booking_button_bg_color',
            [
                'label' => __('Color de fondo', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-book-service' => 'background-color: {{VALUE}};',
                ],
                'default' => '#4a6bff',
            ]
        );

        $this->add_control(
            'booking_button_text_color',
            [
                'label' => __('Color de texto', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-book-service' => 'color: {{VALUE}};',
                ],
                'default' => '#ffffff',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'booking_button_border',
                'label' => __('Borde', 'clever-booking'),
                'selector' => '{{WRAPPER}} .cb-book-service',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => 1,
                            'right' => 1,
                            'bottom' => 1,
                            'left' => 1,
                            'isLinked' => true,
                        ],
                    ],
                    'color' => [
                        'default' => '#4a6bff',
                    ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'booking_button_hover_tab',
            [
                'label' => __('Hover', 'clever-booking'),
            ]
        );

        $this->add_control(
            'booking_button_bg_hover_color',
            [
                'label' => __('Color de fondo', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-book-service:hover' => 'background-color: {{VALUE}};',
                ],
                'default' => '#3755e6',
            ]
        );

        $this->add_control(
            'booking_button_text_hover_color',
            [
                'label' => __('Color de texto', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-book-service:hover' => 'color: {{VALUE}};',
                ],
                'default' => '#ffffff',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'booking_button_border_hover',
                'label' => __('Borde', 'clever-booking'),
                'selector' => '{{WRAPPER}} .cb-book-service:hover',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => 1,
                            'right' => 1,
                            'bottom' => 1,
                            'left' => 1,
                            'isLinked' => true,
                        ],
                    ],
                    'color' => [
                        'default' => '#3755e6',
                    ],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'booking_button_typography',
                'selector' => '{{WRAPPER}} .cb-book-service',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'booking_button_border_radius',
            [
                'label' => __('Border Radius', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-book-service' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '4',
                    'right' => '4',
                    'bottom' => '4',
                    'left' => '4',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_responsive_control(
            'booking_button_padding',
            [
                'label' => __('Padding', 'clever-booking'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .cb-book-service' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            'items_per_page' => $settings['items_per_page'],
            'show_image' => $settings['show_image'],
            'show_title' => $settings['show_title'],
            'show_meta' => $settings['show_meta'],
            'show_price' => $settings['show_price'],
            'show_details_button' => $settings['show_details_button'],
            'details_button_text' => $settings['details_button_text'],
            'show_booking_button' => $settings['show_booking_button'],
            'booking_button_text' => $settings['booking_button_text'],
            'show_pagination' => $settings['show_pagination'],
        ];
        
        // Inicializar la clase Public
        if (class_exists('Clever_Booking_Public')) {
            $plugin_public = new Clever_Booking_Public('clever-booking', CLEVER_BOOKING_VERSION);
            
            // Renderizar el shortcode
            echo $plugin_public->services_list_shortcode($atts);
        } else {
            echo '<div class="error">Error: Clever_Booking_Public class not found.</div>';
        }
    }
}

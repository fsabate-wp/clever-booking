<?php
/**
 * Maneja la integración con Advanced Custom Fields (ACF)
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
 * Maneja la integración con ACF.
 */
class Clever_Booking_ACF_Integration {

    /**
     * Inicializa la clase y establece sus propiedades.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Registrar los grupos de campos de ACF cuando se inicializa ACF
        add_action('acf/init', array($this, 'register_acf_fields'));
    }

    /**
     * Registra los grupos de campos de ACF.
     *
     * @since    1.0.0
     */
    public function register_acf_fields() {
        // Solo registramos los campos si la función está disponible
        if (function_exists('acf_add_local_field_group')) {
            // Campos para los Servicios
            $this->register_service_fields();
            
            // Campos para la configuración del plugin
            $this->register_settings_fields();
        }
    }

    /**
     * Registra los campos para el Custom Post Type de Servicios.
     *
     * @since    1.0.0
     */
    private function register_service_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_service_details',
            'title' => 'Detalles del Servicio',
            'fields' => array(
                array(
                    'key' => 'field_service_price',
                    'label' => 'Precio',
                    'name' => 'service_price',
                    'type' => 'number',
                    'instructions' => 'Ingrese el precio del servicio',
                    'required' => 1,
                    'default_value' => 0,
                    'min' => 0,
                    'max' => '',
                    'step' => 'any',
                    'placeholder' => '',
                    'prepend' => '$',
                ),
                array(
                    'key' => 'field_service_duration',
                    'label' => 'Duración del Servicio',
                    'name' => 'service_duration',
                    'type' => 'number',
                    'instructions' => 'Duración en minutos',
                    'required' => 1,
                    'default_value' => 60,
                    'min' => 15,
                    'max' => '',
                    'step' => 15,
                    'placeholder' => '',
                    'append' => 'minutos',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'cb_service',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }

    /**
     * Registra los campos para la configuración del plugin.
     *
     * @since    1.0.0
     */
    private function register_settings_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_booking_settings',
            'title' => 'Configuración de Reservas',
            'fields' => array(
                array(
                    'key' => 'field_available_days',
                    'label' => 'Días Disponibles',
                    'name' => 'available_days',
                    'type' => 'checkbox',
                    'instructions' => 'Seleccione los días de la semana disponibles para reservas',
                    'required' => 1,
                    'choices' => array(
                        'monday' => 'Lunes',
                        'tuesday' => 'Martes',
                        'wednesday' => 'Miércoles',
                        'thursday' => 'Jueves',
                        'friday' => 'Viernes',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo',
                    ),
                    'default_value' => array(
                        'monday', 'tuesday', 'wednesday', 'thursday', 'friday'
                    ),
                    'layout' => 'vertical',
                    'toggle' => 0,
                    'return_format' => 'value',
                    'save_custom' => 0,
                ),
                array(
                    'key' => 'field_tab_time_slots',
                    'label' => 'Horarios',
                    'name' => 'tab_time_slots',
                    'type' => 'tab',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'placement' => 'top',
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_time_slots',
                    'label' => 'Horarios por Día',
                    'name' => 'time_slots',
                    'type' => 'repeater',
                    'instructions' => 'Configure los horarios disponibles para cada día',
                    'required' => 1,
                    'min' => 1,
                    'max' => 7,
                    'layout' => 'block',
                    'button_label' => 'Añadir Horario',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_time_slots_day',
                            'label' => 'Día',
                            'name' => 'day',
                            'type' => 'select',
                            'instructions' => '',
                            'required' => 1,
                            'choices' => array(
                                'monday' => 'Lunes',
                                'tuesday' => 'Martes',
                                'wednesday' => 'Miércoles',
                                'thursday' => 'Jueves',
                                'friday' => 'Viernes',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo',
                            ),
                            'default_value' => 'monday',
                            'return_format' => 'value',
                            'multiple' => 0,
                            'allow_null' => 0,
                        ),
                        array(
                            'key' => 'field_time_slots_start',
                            'label' => 'Hora de Inicio',
                            'name' => 'start_time',
                            'type' => 'time_picker',
                            'instructions' => '',
                            'required' => 1,
                            'display_format' => 'g:i a',
                            'return_format' => 'H:i:s',
                            'default_value' => '09:00:00',
                        ),
                        array(
                            'key' => 'field_time_slots_end',
                            'label' => 'Hora de Fin',
                            'name' => 'end_time',
                            'type' => 'time_picker',
                            'instructions' => '',
                            'required' => 1,
                            'display_format' => 'g:i a',
                            'return_format' => 'H:i:s',
                            'default_value' => '17:00:00',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_tab_booking_options',
                    'label' => 'Opciones de Reserva',
                    'name' => 'tab_booking_options',
                    'type' => 'tab',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'placement' => 'top',
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_allow_multiple_bookings',
                    'label' => 'Permitir Reservas Múltiples',
                    'name' => 'allow_multiple_bookings',
                    'type' => 'true_false',
                    'instructions' => 'Si está activado, se permiten múltiples reservas en el mismo horario. Si está desactivado, solo se permite una reserva por horario.',
                    'required' => 0,
                    'default_value' => 0,
                    'ui' => 1,
                    'ui_on_text' => 'Permitir',
                    'ui_off_text' => 'No Permitir',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'clever-booking-settings',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }
} 
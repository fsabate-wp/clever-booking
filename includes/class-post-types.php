<?php
/**
 * La clase responsable de registrar Custom Post Types y Taxonomías
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
 * Registra Custom Post Types y Taxonomías.
 */
class Clever_Booking_Post_Types {

    /**
     * Inicializa la clase y define sus propiedades.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('init', array($this, 'register_post_types'), 0);
        add_action('init', array($this, 'register_taxonomies'), 0);
        
        // Agregar términos predeterminados después de registrar la taxonomía
        add_action('init', array($this, 'add_default_terms'), 20);
    }

    /**
     * Registra Custom Post Types.
     *
     * @since    1.0.0
     */
    public function register_post_types() {
        // Custom Post Type: Servicios
        $labels = array(
            'name'                  => _x('Servicios', 'Post Type General Name', 'clever-booking'),
            'singular_name'         => _x('Servicio', 'Post Type Singular Name', 'clever-booking'),
            'menu_name'             => __('Servicios', 'clever-booking'),
            'name_admin_bar'        => __('Servicio', 'clever-booking'),
            'archives'              => __('Archivo de Servicios', 'clever-booking'),
            'attributes'            => __('Atributos de Servicio', 'clever-booking'),
            'parent_item_colon'     => __('Servicio Padre:', 'clever-booking'),
            'all_items'             => __('Todos los Servicios', 'clever-booking'),
            'add_new_item'          => __('Añadir Nuevo Servicio', 'clever-booking'),
            'add_new'               => __('Añadir Nuevo', 'clever-booking'),
            'new_item'              => __('Nuevo Servicio', 'clever-booking'),
            'edit_item'             => __('Editar Servicio', 'clever-booking'),
            'update_item'           => __('Actualizar Servicio', 'clever-booking'),
            'view_item'             => __('Ver Servicio', 'clever-booking'),
            'view_items'            => __('Ver Servicios', 'clever-booking'),
            'search_items'          => __('Buscar Servicio', 'clever-booking'),
            'not_found'             => __('No encontrado', 'clever-booking'),
            'not_found_in_trash'    => __('No encontrado en Papelera', 'clever-booking'),
            'featured_image'        => __('Imagen Destacada', 'clever-booking'),
            'set_featured_image'    => __('Establecer Imagen destacada', 'clever-booking'),
            'remove_featured_image' => __('Eliminar Imagen destacada', 'clever-booking'),
            'use_featured_image'    => __('Usar como Imagen destacada', 'clever-booking'),
            'insert_into_item'      => __('Insertar en Servicio', 'clever-booking'),
            'uploaded_to_this_item' => __('Subido a este Servicio', 'clever-booking'),
            'items_list'            => __('Lista de Servicios', 'clever-booking'),
            'items_list_navigation' => __('Navegación de lista de Servicios', 'clever-booking'),
            'filter_items_list'     => __('Filtrar lista de Servicios', 'clever-booking'),
        );
        
        $args = array(
            'label'                 => __('Servicio', 'clever-booking'),
            'description'           => __('Servicios para reservas online', 'clever-booking'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields', 'elementor'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-admin-generic',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => array('slug' => 'servicio'),
        );
        
        register_post_type('cb_service', $args);
    }

    /**
     * Registra Taxonomías.
     *
     * @since    1.0.0
     */
    public function register_taxonomies() {
        // Taxonomía: Categorías de Servicios
        $labels = array(
            'name'                       => _x('Categorías de Servicios', 'Taxonomy General Name', 'clever-booking'),
            'singular_name'              => _x('Categoría de Servicio', 'Taxonomy Singular Name', 'clever-booking'),
            'menu_name'                  => __('Categorías', 'clever-booking'),
            'all_items'                  => __('Todas las Categorías', 'clever-booking'),
            'parent_item'                => __('Categoría Padre', 'clever-booking'),
            'parent_item_colon'          => __('Categoría Padre:', 'clever-booking'),
            'new_item_name'              => __('Nuevo Nombre de Categoría', 'clever-booking'),
            'add_new_item'               => __('Añadir Nueva Categoría', 'clever-booking'),
            'edit_item'                  => __('Editar Categoría', 'clever-booking'),
            'update_item'                => __('Actualizar Categoría', 'clever-booking'),
            'view_item'                  => __('Ver Categoría', 'clever-booking'),
            'separate_items_with_commas' => __('Separar categorías con comas', 'clever-booking'),
            'add_or_remove_items'        => __('Añadir o eliminar categorías', 'clever-booking'),
            'choose_from_most_used'      => __('Elegir de las más usadas', 'clever-booking'),
            'popular_items'              => __('Categorías populares', 'clever-booking'),
            'search_items'               => __('Buscar Categorías', 'clever-booking'),
            'not_found'                  => __('No encontrada', 'clever-booking'),
            'no_terms'                   => __('No hay categorías', 'clever-booking'),
            'items_list'                 => __('Lista de Categorías', 'clever-booking'),
            'items_list_navigation'      => __('Navegación de lista de Categorías', 'clever-booking'),
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array('slug' => 'categoria-servicio'),
        );
        
        register_taxonomy('cb_service_category', array('cb_service'), $args);
    }
    
    /**
     * Agrega términos por defecto a la taxonomía
     *
     * @since    1.0.0
     */
    public function add_default_terms() {
        // Esta función ya no crea categorías por defecto, pero se mantiene
        // por compatibilidad con versiones anteriores.
        // Si deseas agregar categorías por defecto, puedes descomentar el siguiente código
        
        /*
        $default_categories = array(
            'Categoría 1',
            'Categoría 2',
            'Categoría 3'
        );
        
        foreach ($default_categories as $category) {
            if (!term_exists($category, 'cb_service_category')) {
                wp_insert_term($category, 'cb_service_category');
            }
        }
        */
    }
} 
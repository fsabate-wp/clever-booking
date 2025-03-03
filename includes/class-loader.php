<?php
/**
 * Registra y mantiene los hooks que alimentan la funcionalidad del plugin.
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
 * Clase Loader para manejar acciones y filtros de WordPress
 */
class Clever_Booking_Loader {

    /**
     * El array de acciones registradas con WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    Las acciones registradas con WordPress para disparar cuando el plugin se carga.
     */
    protected $actions;

    /**
     * El array de filtros registrados con WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    Los filtros registrados con WordPress para disparar cuando el plugin se carga.
     */
    protected $filters;

    /**
     * Inicializa las colecciones utilizadas para mantener las acciones y filtros.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    /**
     * Añade una nueva acción al array de acciones para registrar con WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             El nombre de la acción de WordPress que está siendo registrada.
     * @param    object               $component        La instancia del objeto en el que la acción está definida.
     * @param    string               $callback         El nombre de la función que define la acción.
     * @param    int                  $priority         Opcional. La prioridad con la cual la función debe ser disparada. Por defecto es 10.
     * @param    int                  $accepted_args    Opcional. El número de argumentos que deben pasarse a la función de callback. Por defecto es 1.
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Añade un nuevo filtro al array de filtros para registrar con WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             El nombre del filtro de WordPress que está siendo registrado.
     * @param    object               $component        La instancia del objeto en el que el filtro está definido.
     * @param    string               $callback         El nombre de la función que define el filtro.
     * @param    int                  $priority         Opcional. La prioridad con la cual la función debe ser disparada. Por defecto es 10.
     * @param    int                  $accepted_args    Opcional. El número de argumentos que deben pasarse a la función de callback. Por defecto es 1
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Utilidad que se usa para registrar las acciones y hooks en una única colección.
     *
     * @since    1.0.0
     * @access   private
     * @param    array                $hooks            La colección de hooks que está siendo registrada (acciones o filtros).
     * @param    string               $hook             El nombre del filtro de WordPress que está siendo registrado.
     * @param    object               $component        La instancia del objeto en el que el filtro está definido.
     * @param    string               $callback         El nombre de la función que define el filtro.
     * @param    int                  $priority         La prioridad con la cual la función debe ser disparada.
     * @param    int                  $accepted_args    El número de argumentos que deben pasarse a la función de callback.
     * @return   array                                  La colección de acciones y filtros registrados con WordPress.
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    /**
     * Registra los filtros y acciones con WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
    }
} 
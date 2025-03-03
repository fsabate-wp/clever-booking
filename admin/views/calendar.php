<?php
/**
 * Vista del calendario de reservas
 *
 * @link       
 * @since      1.0.0
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap clever-booking-admin">
    <h1><?php echo esc_html__('Calendario de Reservas', 'clever-booking'); ?></h1>
    
    <div class="notice notice-info">
        <p><?php echo esc_html__('Vista de calendario de todas las reservas. Haz clic en una reserva para verla en detalle.', 'clever-booking'); ?></p>
    </div>
    
    <div class="calendar-container">
        <div class="calendar-filters">
            <select id="calendar-filter-status" class="calendar-filter">
                <option value=""><?php echo esc_html__('Todos los estados', 'clever-booking'); ?></option>
                <option value="status-pending"><?php echo esc_html__('Pendientes', 'clever-booking'); ?></option>
                <option value="status-confirmed"><?php echo esc_html__('Confirmadas', 'clever-booking'); ?></option>
                <option value="status-completed"><?php echo esc_html__('Completadas', 'clever-booking'); ?></option>
                <option value="status-cancelled"><?php echo esc_html__('Canceladas', 'clever-booking'); ?></option>
            </select>
            
            <?php 
            // Obtener todos los servicios
            $services = get_posts(array(
                'post_type' => 'cb_service',
                'numberposts' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            ));
            
            if (!empty($services)) : 
            ?>
                <select id="calendar-filter-service" class="calendar-filter">
                    <option value=""><?php echo esc_html__('Todos los servicios', 'clever-booking'); ?></option>
                    <?php foreach ($services as $service) : ?>
                        <option value="<?php echo esc_attr($service->ID); ?>"><?php echo esc_html($service->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            
            <button id="calendar-filter-apply" class="button"><?php echo esc_html__('Aplicar filtros', 'clever-booking'); ?></button>
            <button id="calendar-filter-reset" class="button"><?php echo esc_html__('Restablecer', 'clever-booking'); ?></button>
        </div>
        
        <div class="calendar-legend">
            <span class="legend-item">
                <span class="legend-color status-pending"></span>
                <?php echo esc_html__('Pendiente', 'clever-booking'); ?>
            </span>
            
            <span class="legend-item">
                <span class="legend-color status-confirmed"></span>
                <?php echo esc_html__('Confirmada', 'clever-booking'); ?>
            </span>
            
            <span class="legend-item">
                <span class="legend-color status-completed"></span>
                <?php echo esc_html__('Completada', 'clever-booking'); ?>
            </span>
            
            <span class="legend-item">
                <span class="legend-color status-cancelled"></span>
                <?php echo esc_html__('Cancelada', 'clever-booking'); ?>
            </span>
        </div>
        
        <div id="clever-booking-calendar"></div>
    </div>
</div> 
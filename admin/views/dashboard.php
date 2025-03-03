<?php
/**
 * Vista del dashboard de administración
 *
 * @link       
 * @since      1.0.0
 */

// Si este archivo es llamado directamente, abortamos
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php echo esc_html__('Dashboard de Clever Booking', 'clever-booking'); ?></h1>
    
    <div class="notice notice-info">
        <p><?php echo esc_html__('Bienvenido al sistema de reservas Clever Booking. Aquí puedes ver un resumen de las reservas y servicios disponibles.', 'clever-booking'); ?></p>
    </div>
    
    <div class="dashboard-stats">
        <div class="dashboard-row">
            <div class="dashboard-box">
                <h2><?php echo esc_html__('Reservas por Estado', 'clever-booking'); ?></h2>
                <div class="dashboard-stats-grid">
                    <div class="stat-box stat-pending">
                        <span class="stat-number"><?php echo esc_html($pending_count); ?></span>
                        <span class="stat-label"><?php echo esc_html__('Pendientes', 'clever-booking'); ?></span>
                    </div>
                    <div class="stat-box stat-confirmed">
                        <span class="stat-number"><?php echo esc_html($confirmed_count); ?></span>
                        <span class="stat-label"><?php echo esc_html__('Confirmadas', 'clever-booking'); ?></span>
                    </div>
                    <div class="stat-box stat-completed">
                        <span class="stat-number"><?php echo esc_html($completed_count); ?></span>
                        <span class="stat-label"><?php echo esc_html__('Completadas', 'clever-booking'); ?></span>
                    </div>
                    <div class="stat-box stat-cancelled">
                        <span class="stat-number"><?php echo esc_html($cancelled_count); ?></span>
                        <span class="stat-label"><?php echo esc_html__('Canceladas', 'clever-booking'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-box">
                <h2><?php echo esc_html__('Servicios Disponibles', 'clever-booking'); ?></h2>
                <div class="services-list">
                    <?php if (!empty($services)) : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php echo esc_html__('Servicio', 'clever-booking'); ?></th>
                                    <th><?php echo esc_html__('Precio', 'clever-booking'); ?></th>
                                    <th><?php echo esc_html__('Duración', 'clever-booking'); ?></th>
                                    <th><?php echo esc_html__('Acciones', 'clever-booking'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service) : 
                                    $price = get_field('service_price', $service->ID);
                                    $duration = get_field('service_duration', $service->ID);
                                ?>
                                <tr>
                                    <td><?php echo esc_html($service->post_title); ?></td>
                                    <td><?php echo '$' . esc_html($price); ?></td>
                                    <td><?php echo esc_html($duration) . ' ' . esc_html__('minutos', 'clever-booking'); ?></td>
                                    <td>
                                        <a href="<?php echo esc_url(get_edit_post_link($service->ID)); ?>" class="button button-small">
                                            <?php echo esc_html__('Editar', 'clever-booking'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=clever-booking-reservations&service_id=' . $service->ID)); ?>" class="button button-small">
                                            <?php echo esc_html__('Ver Reservas', 'clever-booking'); ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php echo esc_html__('No hay servicios disponibles. Por favor, añade servicios para comenzar a recibir reservas.', 'clever-booking'); ?></p>
                        <a href="<?php echo esc_url(admin_url('post-new.php?post_type=cb_service')); ?>" class="button button-primary">
                            <?php echo esc_html__('Añadir Servicio', 'clever-booking'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="dashboard-row">
            <div class="dashboard-box">
                <h2><?php echo esc_html__('Reservas Recientes', 'clever-booking'); ?></h2>
                <?php if (!empty($reservations)) : ?>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('Cliente', 'clever-booking'); ?></th>
                                <th><?php echo esc_html__('Servicio', 'clever-booking'); ?></th>
                                <th><?php echo esc_html__('Fecha', 'clever-booking'); ?></th>
                                <th><?php echo esc_html__('Hora', 'clever-booking'); ?></th>
                                <th><?php echo esc_html__('Estado', 'clever-booking'); ?></th>
                                <th><?php echo esc_html__('Acciones', 'clever-booking'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation) : 
                                $service = get_post($reservation->service_id);
                                $service_title = $service ? $service->post_title : __('Servicio Desconocido', 'clever-booking');
                                
                                $booking_date = date_i18n(get_option('date_format'), strtotime($reservation->booking_date));
                                $booking_time = date_i18n(get_option('time_format'), strtotime($reservation->booking_time));
                            ?>
                            <tr>
                                <td><?php echo esc_html($reservation->customer_name); ?></td>
                                <td><?php echo esc_html($service_title); ?></td>
                                <td><?php echo esc_html($booking_date); ?></td>
                                <td><?php echo esc_html($booking_time); ?></td>
                                <td>
                                    <span class="reservation-status status-<?php echo esc_attr($reservation->status); ?>">
                                        <?php 
                                        $status_labels = array(
                                            'pending' => __('Pendiente', 'clever-booking'),
                                            'confirmed' => __('Confirmada', 'clever-booking'),
                                            'completed' => __('Completada', 'clever-booking'),
                                            'cancelled' => __('Cancelada', 'clever-booking'),
                                        );
                                        echo isset($status_labels[$reservation->status]) ? esc_html($status_labels[$reservation->status]) : esc_html($reservation->status);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=clever-booking-reservations&action=edit&id=' . $reservation->id)); ?>" class="button button-small">
                                        <?php echo esc_html__('Editar', 'clever-booking'); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <p class="alignright">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=clever-booking-reservations')); ?>" class="button">
                            <?php echo esc_html__('Ver Todas las Reservas', 'clever-booking'); ?>
                        </a>
                    </p>
                    <div class="clear"></div>
                    
                <?php else : ?>
                    <p><?php echo esc_html__('No hay reservas recientes.', 'clever-booking'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="dashboard-row">
            <div class="dashboard-box">
                <h2><?php echo esc_html__('Acciones Rápidas', 'clever-booking'); ?></h2>
                <div class="quick-actions">
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=cb_service')); ?>" class="button button-primary">
                        <?php echo esc_html__('Añadir Nuevo Servicio', 'clever-booking'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=clever-booking-calendar')); ?>" class="button button-primary">
                        <?php echo esc_html__('Ver Calendario', 'clever-booking'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=clever-booking-settings')); ?>" class="button button-primary">
                        <?php echo esc_html__('Configuración', 'clever-booking'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-stats {
        margin-top: 20px;
    }
    
    .dashboard-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    
    .dashboard-box {
        background: #fff;
        border: 1px solid #e5e5e5;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        padding: 20px;
        margin-right: 20px;
        margin-bottom: 20px;
        flex: 1;
        min-width: 300px;
    }
    
    .dashboard-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 15px;
        margin-top: 15px;
    }
    
    .stat-box {
        text-align: center;
        padding: 15px;
        border-radius: 3px;
        color: #fff;
    }
    
    .stat-number {
        font-size: 32px;
        font-weight: bold;
        display: block;
    }
    
    .stat-label {
        font-size: 14px;
        display: block;
        margin-top: 5px;
    }
    
    .stat-pending {
        background-color: #f39c12;
    }
    
    .stat-confirmed {
        background-color: #3498db;
    }
    
    .stat-completed {
        background-color: #2ecc71;
    }
    
    .stat-cancelled {
        background-color: #e74c3c;
    }
    
    .quick-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .reservation-status {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        color: #fff;
        font-size: 12px;
    }
    
    .status-pending {
        background-color: #f39c12;
    }
    
    .status-confirmed {
        background-color: #3498db;
    }
    
    .status-completed {
        background-color: #2ecc71;
    }
    
    .status-cancelled {
        background-color: #e74c3c;
    }
</style> 
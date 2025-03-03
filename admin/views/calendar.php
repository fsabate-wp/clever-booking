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

<div class="wrap">
    <h1><?php echo esc_html__('Calendario de Reservas', 'clever-booking'); ?></h1>
    
    <div class="notice notice-info">
        <p><?php echo esc_html__('Vista de calendario de todas las reservas. Haz clic en una reserva para verla en detalle.', 'clever-booking'); ?></p>
    </div>
    
    <div class="calendar-container">
        <div class="calendar-filters">
            <select id="calendar-filter-status" class="calendar-filter">
                <option value=""><?php echo esc_html__('Todos los estados', 'clever-booking'); ?></option>
                <option value="pending"><?php echo esc_html__('Pendientes', 'clever-booking'); ?></option>
                <option value="confirmed"><?php echo esc_html__('Confirmadas', 'clever-booking'); ?></option>
                <option value="completed"><?php echo esc_html__('Completadas', 'clever-booking'); ?></option>
                <option value="cancelled"><?php echo esc_html__('Canceladas', 'clever-booking'); ?></option>
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

<script type="text/javascript">
    jQuery(document).ready(function($) {
        var calendarEl = document.getElementById('clever-booking-calendar');
        var allEvents = clever_booking_calendar.events;
        var calendar;
        
        // Inicializar el calendario
        function initCalendar() {
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: false,
                selectable: false,
                dayMaxEvents: true,
                events: allEvents,
                locale: '<?php echo get_locale(); ?>',
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        return false;
                    }
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: 'short'
                },
                eventClassNames: function(arg) {
                    // Añadir clases según el estado
                    return [ 'fc-event-' + arg.event.classNames[0] ];
                }
            });
            
            calendar.render();
        }
        
        // Inicializar el calendario
        initCalendar();
        
        // Filtrar eventos
        $('#calendar-filter-apply').on('click', function() {
            var statusFilter = $('#calendar-filter-status').val();
            var serviceFilter = $('#calendar-filter-service').val();
            
            var filteredEvents = allEvents.filter(function(event) {
                var matchStatus = true;
                var matchService = true;
                
                if (statusFilter !== '') {
                    matchStatus = event.className === 'status-' + statusFilter;
                }
                
                if (serviceFilter !== '') {
                    // Extraer el ID del servicio del evento
                    // Asumimos que el evento tiene una propiedad extendedProps con el service_id
                    matchService = String(event.service_id) === serviceFilter;
                }
                
                return matchStatus && matchService;
            });
            
            calendar.removeAllEvents();
            calendar.addEventSource(filteredEvents);
        });
        
        // Restablecer filtros
        $('#calendar-filter-reset').on('click', function() {
            $('#calendar-filter-status').val('');
            $('#calendar-filter-service').val('');
            
            calendar.removeAllEvents();
            calendar.addEventSource(allEvents);
        });
    });
</script>

<style>
    .calendar-container {
        margin-top: 20px;
    }
    
    .calendar-filters {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .calendar-filter {
        min-width: 150px;
    }
    
    .calendar-legend {
        margin-bottom: 20px;
        display: flex;
        gap: 20px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        font-size: 12px;
    }
    
    .legend-color {
        display: inline-block;
        width: 12px;
        height: 12px;
        margin-right: 5px;
        border-radius: 2px;
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
    
    #clever-booking-calendar {
        background-color: #fff;
        padding: 20px;
        border: 1px solid #e5e5e5;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
    }
    
    /* Estilos específicos para FullCalendar */
    .fc-event-status-pending {
        background-color: #f39c12;
        border-color: #f39c12;
    }
    
    .fc-event-status-confirmed {
        background-color: #3498db;
        border-color: #3498db;
    }
    
    .fc-event-status-completed {
        background-color: #2ecc71;
        border-color: #2ecc71;
    }
    
    .fc-event-status-cancelled {
        background-color: #e74c3c;
        border-color: #e74c3c;
    }
</style> 
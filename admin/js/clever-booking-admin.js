/**
 * JavaScript para la interfaz de administración
 *
 * @since      1.0.0
 */

(function($) {
    'use strict';

    // Inicialización al cargar el documento
    $(document).ready(function() {
        // Inicializar datepickers
        if ($.fn.datepicker) {
            $('.date-picker').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            });
        }
        
        // Inicializar el calendario si estamos en la página de calendario
        if ($('#clever-booking-calendar').length > 0) {
            initCalendar();
        }
    });
    
    /**
     * Inicializar el calendario de FullCalendar
     */
    function initCalendar() {
        var calendarEl = document.getElementById('clever-booking-calendar');
        var allEvents = clever_booking_calendar.events;
        
        // Comprobar que el elemento del calendario existe
        if (!calendarEl) return;
        
        // Inicializar el calendario
        var calendar = new FullCalendar.Calendar(calendarEl, {
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
            locale: document.documentElement.lang || 'es',
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
        
        // Filtrar eventos
        $('#calendar-filter-apply').on('click', function() {
            var statusFilter = $('#calendar-filter-status').val();
            var serviceFilter = $('#calendar-filter-service').val();
            
            var filteredEvents = allEvents.filter(function(event) {
                var matchStatus = true;
                var matchService = true;
                
                if (statusFilter !== '') {
                    matchStatus = event.className === statusFilter;
                }
                
                if (serviceFilter !== '') {
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
    }

})(jQuery); 
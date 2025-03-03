# Clever Booking

Plugin para WordPress que permite gestionar reservas de servicios de cualquier tipo.

## Descripción

Clever Booking es un sistema de reservas completo para WordPress diseñado para trabajar con Elementor. Permite a los usuarios mostrar servicios y gestionar reservas online de manera flexible, adaptándose a cualquier tipo de negocio de servicios. El plugin incluye:

- Gestión de servicios y categorías como Custom Post Types
- Sistema de reservas con disponibilidad en tiempo real y calendario visual
- Widgets de Elementor para una integración perfecta con tu sitio
- Botón de reserva que abre un modal con selector de fecha/hora en cada página de servicio
- Panel de administración con dashboard, calendario y listados
- Notificaciones por email para administradores y clientes

## Instalación

1. Descarga el plugin y súbelo a la carpeta `/wp-content/plugins/`
2. Activa el plugin a través del menú 'Plugins' en WordPress
3. Configura los ajustes del plugin en 'Clever Booking > Ajustes'
4. Utiliza los widgets de Elementor para mostrar servicios e información de reservas

## Requisitos

- WordPress 5.0 o superior
- PHP 7.0 o superior
- MySQL 5.6 o superior
- Plugin Advanced Custom Fields (ACF) instalado y activado
- Plugin Elementor instalado y activado

## Uso

### Widgets de Elementor

El plugin proporciona los siguientes widgets de Elementor para mostrar la información:

#### Info de Servicio

Este widget muestra información detallada sobre un servicio, como precio, duración y categorías. Úsalo en plantillas de single post de servicio.

#### Botón de Reserva

Muestra un botón de "Reservar Ahora" que abre un modal con el formulario de reserva para el servicio actual. Debe usarse en plantillas de single post de servicio.

#### Lista de Servicios

Muestra una lista de todos los servicios disponibles. Puedes filtrar por categoría y personalizar la apariencia.

### Crear una Plantilla de Single Post en Elementor

1. Ve a Plantillas > Añadir Nueva
2. Selecciona "Single" como tipo de plantilla
3. Elige "Servicio" como tipo de post
4. Diseña tu plantilla usando los widgets de Elementor, incluyendo:
   - Widget "Info de Servicio" para mostrar detalles del servicio
   - Widget "Botón de Reserva" para permitir a los usuarios hacer reservas
5. Publica y asigna la plantilla a las páginas de servicios

## Administración

El plugin añade un menú de administración en WordPress con las siguientes opciones:

- **Dashboard**: Resumen de datos y estadísticas
- **Servicios**: Gestión de los servicios disponibles
- **Categorías**: Gestión de categorías de servicios
- **Reservas**: Listado y gestión de todas las reservas
- **Calendario**: Vista de calendario con todas las reservas
- **Ajustes**: Configuración del plugin

## Personalización

Los widgets de Elementor permiten una personalización completa de la apariencia de los servicios y formularios de reserva. Además, puedes personalizar los estilos CSS desde el panel de control de Elementor o añadiendo CSS personalizado en la configuración del plugin.

El plugin se puede adaptar para funcionar con diversos tipos de servicios, incluyendo:
- Servicios de salud y belleza
- Consultas profesionales
- Servicios de mantenimiento
- Actividades deportivas y de ocio
- Alquiler de equipos o espacios
- Y muchos otros tipos de servicios con reserva por hora

## Soporte

Para soporte, por favor contacta con nosotros a través de nuestro sitio web o envía un email a support@example.com.

## Licencia

Este plugin está licenciado bajo GPL v2 o posterior.

## Créditos

Desarrollado por Tu Nombre/Empresa. 
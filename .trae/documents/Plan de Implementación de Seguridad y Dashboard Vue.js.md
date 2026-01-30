He analizado tu solicitud y he preparado un plan completo para implementar las mejoras de seguridad y el desarrollo del dashboard moderno con Vue.js.

### 🛡️ Fase 1: Seguridad y Validaciones (Backend)
1.  **Validación Robusta en Registro (`RegisteredUserController`):**
    *   **Email:** Implementar Regex estricto (`/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/`) además de la validación DNS estándar.
    *   **Contraseña:** Configurar `Password::min(8)->mixedCase()->numbers()->symbols()` para exigir complejidad real.
    *   **Sanitización:** Confirmar el uso de Eloquent ORM que ya previene SQL Injection nativamente.
2.  **Rate Limiting:**
    *   Aplicar middleware `throttle:6,1` (6 intentos por minuto) a las rutas de Login y Registro en `routes/auth.php` para prevenir fuerza bruta.
3.  **HTTPS y Seguridad de Transporte:**
    *   Forzar HTTPS en `AppServiceProvider` para entornos de producción.

### 🏗️ Fase 2: Arquitectura del Dashboard (Backend API)
1.  **Base de Datos:**
    *   Crear migración para añadir columna `role` (admin/user) a la tabla `users`.
2.  **API RESTful:**
    *   Crear `DashboardController` en `Api` con endpoints optimizados:
        *   `GET /api/dashboard/stats`: Contadores rápidos (Leads, Mensajes, etc.) usando Cache.
        *   `GET /api/dashboard/chart-data`: Datos agregados para gráficos.
        *   `GET /api/dashboard/activity`: Tabla de actividades recientes.
3.  **Autenticación API:**
    *   Utilizar **Laravel Sanctum** para manejar tokens de API (cumpliendo la función de autenticación stateless/JWT para el frontend).

### 🎨 Fase 3: Frontend Moderno (Vue.js)
1.  **Integración Vue.js:**
    *   Instalar Vue 3 y el plugin de Vite en tu proyecto actual.
    *   Configurar `vite.config.js` para compilar componentes Vue junto con Blade.
2.  **Componentes del Dashboard:**
    *   `DashboardStats.vue`: Widgets con contadores.
    *   `UserChart.vue`: Gráficos interactivos usando `chart.js`.
    *   `ActivityTable.vue`: Tabla de logs/actividad.
    *   `NotificationCenter.vue`: Componente para alertas en tiempo real.
3.  **Montaje:**
    *   Incrustar la aplicación Vue dentro de tu layout existente (`dashboard.blade.php`) para una experiencia fluida.

### ⚡ Fase 4: Tiempo Real (WebSockets)
1.  **Sistema de Notificaciones:**
    *   Implementar eventos de Laravel (`DashboardNotification`).
    *   Configurar **Pusher** (o alternativa compatible) en el cliente Vue para recibir actualizaciones en vivo sin recargar la página.

¿Te parece bien este plan para proceder con la implementación?

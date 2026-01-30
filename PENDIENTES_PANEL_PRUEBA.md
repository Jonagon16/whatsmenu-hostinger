# Pendientes Panel de Prueba (Test Panel)

## Problema Actual (Solucionado)
El panel de pruebas `/test-panel` respondía con `302 Found` y fallaba silenciosamente debido a que no enviaba la firma `X-Hub-Signature-256`, lo que provocaba que el `WhatsAppWebhookController` rechazara la solicitud con "Firma inválida".

## Solución Aplicada (30/01/2026)
1. **Actualización de `TestController.php`**:
   - Ahora busca el `BotConfig` asociado al `phone_id` enviado.
   - Calcula la firma HMAC-SHA256 usando el `whatsapp_app_secret` del bot y el payload JSON.
   - Envía el header `HTTP_X_HUB_SIGNATURE_256` en la solicitud simulada (`Request::create`).
   - Envía el payload como una cadena JSON cruda para asegurar que `getContent()` coincida con lo firmado.

2. **Logs de Depuración en `WhatsAppWebhookController.php`**:
   - Se agregaron logs temporales al inicio del método `receive` para inspeccionar los headers recibidos (`X-Hub-Signature-256`, `Content-Type`).

## Síntomas Anteriores
1. Al hacer POST a `/test-panel/simulate`, el servidor redirigía (302) de vuelta al panel.
2. No se mostraba mensaje de éxito ni de error (flash messages).
3. La tabla de logs aparecía vacía ("No hay mensajes registrados aún").
4. Log de Laravel mostraba: `WhatsApp Webhook: Firma inválida para tenant X`.

## Pasos para Verificar
1. Ir a `/test-panel`.
2. Asegurarse de tener un Bot configurado (usar botón Reset si es necesario).
3. Enviar un mensaje de prueba.
4. Verificar que aparezca el mensaje de éxito y el log en la tabla.
5. Verificar `storage/logs/laravel.log` para ver los headers de depuración.

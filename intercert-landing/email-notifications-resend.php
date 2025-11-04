<?php
// Sistema de notificaciones por email usando Resend API

/**
 * Envía email de confirmación automático al cliente
 */
function sendClientConfirmationEmail($formData) {
    // Cargar configuración de Resend
    $resend_config = include 'config-resend.php';
    
    $nombre = htmlspecialchars($formData['nombre_completo'] ?? 'Cliente');
    $primer_nombre = explode(' ', $nombre)[0];
    $email_cliente = $formData['email'] ?? '';
    
    if (empty($email_cliente)) {
        return [
            'success' => false,
            'error' => 'No se proporcionó email del cliente'
        ];
    }
    
    // Construir el HTML del email de confirmación
    $html_body = buildClientConfirmationHTML($primer_nombre, $nombre);
    
    // Preparar datos para Resend API
    $email_data = [
        'from' => $resend_config['from_name'] . ' <' . $resend_config['from_email'] . '>',
        'to' => [$email_cliente],
        'subject' => '¡Gracias por contactarnos! - INTERCERT LATAM',
        'html' => $html_body
    ];
    
    // Enviar email usando Resend API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($email_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $resend_config['api_key'],
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $resend_config['settings']['timeout']);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($curl_error) {
        error_log('Error cURL Resend (Confirmación Cliente): ' . $curl_error);
        return [
            'success' => false,
            'error' => 'Error enviando email: ' . $curl_error
        ];
    }
    
    if ($http_code !== 200 && $http_code !== 201) {
        error_log('Error Resend API (Confirmación Cliente): ' . $response);
        return [
            'success' => false,
            'error' => 'Error Resend API: ' . $response,
            'http_code' => $http_code
        ];
    }
    
    return [
        'success' => true,
        'response' => json_decode($response, true),
        'http_code' => $http_code
    ];
}

/**
 * Envía email de notificación a los empleados de INTERCERT
 */
function sendClientNotificationEmailResend($formData) {
    // Cargar configuración de Resend
    $resend_config = include 'config-resend.php';
    
    // Preparar datos de certificaciones
    $certificaciones = is_array($formData['certificaciones']) ? 
        implode(', ', $formData['certificaciones']) : 
        $formData['certificaciones'];
    
    // Preparar teléfono completo
    $telefono_completo = ($formData['pais_prefijo'] ?? '') . ' ' . ($formData['telefono'] ?? '');
    
    // Construir el HTML del email
    $html_body = buildEmailHTML($formData, $certificaciones, $telefono_completo);
    
    // Preparar datos para Resend API
    $email_data = [
        'from' => $resend_config['from_name'] . ' <' . $resend_config['from_email'] . '>',
        'to' => $resend_config['recipients'],
        'subject' => 'Nuevo Lead - ' . ($formData['nombre_empresa'] ?? 'Sin empresa') . ' - Landing Cajamarca',
        'html' => $html_body
    ];
    
    // Enviar email usando Resend API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($email_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $resend_config['api_key'],
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $resend_config['settings']['timeout']);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($curl_error) {
        error_log('Error cURL Resend: ' . $curl_error);
        return [
            'success' => false,
            'error' => 'Error enviando email: ' . $curl_error
        ];
    }
    
    if ($http_code !== 200 && $http_code !== 201) {
        error_log('Error Resend API: ' . $response);
        return [
            'success' => false,
            'error' => 'Error Resend API: ' . $response,
            'http_code' => $http_code
        ];
    }
    
    return [
        'success' => true,
        'response' => json_decode($response, true),
        'http_code' => $http_code
    ];
}

function buildEmailHTML($formData, $certificaciones, $telefono_completo) {
    $nombre = htmlspecialchars($formData['nombre_completo'] ?? 'No especificado');
    $email = htmlspecialchars($formData['email'] ?? 'No especificado');
    $cargo = htmlspecialchars($formData['cargo'] ?? 'No especificado');
    $empresa = htmlspecialchars($formData['nombre_empresa'] ?? 'No especificado');
    $ruc = htmlspecialchars($formData['ruc_empresa'] ?? 'No especificado');
    $empleados = htmlspecialchars($formData['numero_empleados'] ?? 'No especificado');
    $sector = htmlspecialchars($formData['sector_empresa'] ?? 'No especificado');
    $tipo_servicio = htmlspecialchars($formData['tipo_servicio'] ?? 'No especificado');
    $certificaciones_html = htmlspecialchars($certificaciones);
    $comentarios = htmlspecialchars($formData['comentarios'] ?? 'Sin comentarios');
    $fecha = date('d/m/Y H:i:s');
    
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Lead - INTERCERT LATAM</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!--[if mso]>
    <style type="text/css">
        table { border-collapse: collapse; }
        .header-bg { background-color: #0f3564 !important; }
        .header-text { color: #ffffff !important; }
        .header-subtitle { color: #B3D4FF !important; }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f0f2f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f2f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(15, 53, 100, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td class="header-bg" style="background-color: #0f3564 !important; padding: 35px; text-align: center; border-bottom: 4px solid #FFC107;">
                            <h1 class="header-text" style="color: #ffffff !important; margin: 0; font-size: 30px; font-weight: bold; letter-spacing: -0.5px;">
                                Nuevo Lead Registrado
                            </h1>
                            <p class="header-subtitle" style="color: #B3D4FF !important; margin: 12px 0 0 0; font-size: 15px; font-weight: 500;">
                                Landing Page Cajamarca - Certificaciones ISO
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Información del Cliente -->
                    <tr>
                        <td style="padding: 35px 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <h2 style="color: #0f3564; font-size: 20px; margin: 0 0 20px 0; border-bottom: 3px solid #0f3564; padding-bottom: 12px;">
                                            <i class="ri-user-line" style="color: #1f61b3; margin-right: 8px; font-size: 22px; vertical-align: middle;"></i>
                                            <span style="vertical-align: middle;">Información del Contacto</span>
                                        </h2>
                                    </td>
                                </tr>
                            </table>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e3e8ef; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="background-color: #f8f9fc; padding: 14px 16px; width: 40%; font-weight: 600; color: #0f3564; border-bottom: 1px solid #e3e8ef;">
                                        Nombre Completo
                                    </td>
                                    <td style="background-color: #ffffff; padding: 14px 16px; color: #2c3e50; border-bottom: 1px solid #e3e8ef;">
                                        {$nombre}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fc; padding: 14px 16px; font-weight: 600; color: #0f3564; border-bottom: 1px solid #e3e8ef;">
                                        Email
                                    </td>
                                    <td style="background-color: #ffffff; padding: 14px 16px; border-bottom: 1px solid #e3e8ef;">
                                        <a href="mailto:{$email}" style="color: #1f61b3; text-decoration: none; font-weight: 500;">
                                            {$email}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fc; padding: 14px 16px; font-weight: 600; color: #0f3564; border-bottom: 1px solid #e3e8ef;">
                                        Teléfono
                                    </td>
                                    <td style="background-color: #ffffff; padding: 14px 16px; border-bottom: 1px solid #e3e8ef;">
                                        <a href="tel:{$telefono_completo}" style="color: #1f61b3; text-decoration: none; font-weight: 500;">
                                            {$telefono_completo}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fc; padding: 14px 16px; font-weight: 600; color: #0f3564;">
                                        Cargo
                                    </td>
                                    <td style="background-color: #ffffff; padding: 14px 16px; color: #2c3e50;">
                                        {$cargo}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Información de la Empresa -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <h2 style="color: #0f3564; font-size: 20px; margin: 0 0 20px 0; border-bottom: 3px solid #0f3564; padding-bottom: 12px;">
                                            <i class="ri-building-line" style="color: #1f61b3; margin-right: 8px; font-size: 22px; vertical-align: middle;"></i>
                                            <span style="vertical-align: middle;">Información de la Empresa</span>
                                        </h2>
                                    </td>
                                </tr>
                            </table>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e3e8ef; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="background-color: #f8f9fc; padding: 14px 16px; width: 40%; font-weight: 600; color: #0f3564; border-bottom: 1px solid #e3e8ef;">
                                        Razón Social
                                    </td>
                                    <td style="background-color: #ffffff; padding: 14px 16px; color: #2c3e50; border-bottom: 1px solid #e3e8ef;">
                                        {$empresa}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fc; padding: 14px 16px; font-weight: 600; color: #0f3564; border-bottom: 1px solid #e3e8ef;">
                                        RUC
                                    </td>
                                    <td style="background-color: #ffffff; padding: 14px 16px; color: #2c3e50; border-bottom: 1px solid #e3e8ef;">
                                        {$ruc}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fc; padding: 14px 16px; font-weight: 600; color: #0f3564; border-bottom: 1px solid #e3e8ef;">
                                        N° de Empleados
                                    </td>
                                    <td style="background-color: #ffffff; padding: 14px 16px; color: #2c3e50; border-bottom: 1px solid #e3e8ef;">
                                        {$empleados}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fc; padding: 14px 16px; font-weight: 600; color: #0f3564;">
                                        Sector
                                    </td>
                                    <td style="background-color: #ffffff; padding: 14px 16px; color: #2c3e50;">
                                        {$sector}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Tipo de Servicio -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <h2 style="color: #0f3564; font-size: 20px; margin: 0 0 20px 0; border-bottom: 3px solid #0f3564; padding-bottom: 12px;">
                                            <i class="ri-service-line" style="color: #1f61b3; margin-right: 8px; font-size: 22px; vertical-align: middle;"></i>
                                            <span style="vertical-align: middle;">Tipo de Servicio</span>
                                        </h2>
                                    </td>
                                </tr>
                            </table>
                            <div style="background: linear-gradient(135deg, #fff9e6 0%, #fff3cc 100%); padding: 18px 20px; border-radius: 8px; border-left: 5px solid #FFC107; box-shadow: 0 2px 4px rgba(255, 193, 7, 0.15);">
                                <p style="margin: 0; color: #0f3564; font-weight: 700; line-height: 1.8; font-size: 16px;">
                                    {$tipo_servicio}
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Certificaciones Solicitadas -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <h2 style="color: #0f3564; font-size: 20px; margin: 0 0 20px 0; border-bottom: 3px solid #0f3564; padding-bottom: 12px;">
                                            <i class="ri-file-list-line" style="color: #1f61b3; margin-right: 8px; font-size: 22px; vertical-align: middle;"></i>
                                            <span style="vertical-align: middle;">Certificaciones Solicitadas</span>
                                        </h2>
                                    </td>
                                </tr>
                            </table>
                            <div style="background: linear-gradient(135deg, #e8f4fd 0%, #d5ebfa 100%); padding: 18px 20px; border-radius: 8px; border-left: 5px solid #0f3564; box-shadow: 0 2px 4px rgba(15, 53, 100, 0.08);">
                                <p style="margin: 0; color: #0f3564; font-weight: 600; line-height: 1.8; font-size: 15px;">
                                    {$certificaciones_html}
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Comentarios -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <h2 style="color: #0f3564; font-size: 20px; margin: 0 0 20px 0; border-bottom: 3px solid #0f3564; padding-bottom: 12px;">
                                            <i class="ri-chat-3-line" style="color: #1f61b3; margin-right: 8px; font-size: 22px; vertical-align: middle;"></i>
                                            <span style="vertical-align: middle;">Comentarios Adicionales</span>
                                        </h2>
                                    </td>
                                </tr>
                            </table>
                            <div style="background-color: #f8f9fc; padding: 18px 20px; border-radius: 8px; border: 1px solid #e3e8ef;">
                                <p style="margin: 0; color: #2c3e50; line-height: 1.7; font-size: 14px;">
                                    {$comentarios}
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Call to Action -->
                    <tr>
                        <td style="padding: 0 30px 35px 30px; text-align: center;">
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fc; padding: 25px 20px; text-align: center; border-top: 3px solid #0f3564;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 12px 0; color: #5a6c7d; font-size: 13px; font-weight: 500;">
                                            <i class="ri-calendar-line" style="color: #1f61b3; margin-right: 6px; font-size: 14px; vertical-align: middle;"></i>
                                            <span style="vertical-align: middle;">Fecha de registro: {$fecha}</span>
                                        </p>
                                        <p style="margin: 0 0 8px 0; color: #7f8c9d; font-size: 12px;">
                                            Este es un email automático generado por el sistema de Landing Page Cajamarca
                                        </p>
                                        <p style="margin: 0; color: #0f3564; font-size: 12px; font-weight: 600;">
                                            © 2025 INTERCERT LATAM - Todos los derechos reservados
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}

/**
 * Construye el HTML del email de confirmación para el cliente
 */
function buildClientConfirmationHTML($primer_nombre, $nombre_completo) {
    $anio_actual = date('Y');
    
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por contactarnos! - INTERCERT LATAM</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!--[if mso]>
    <style type="text/css">
        table { border-collapse: collapse; }
        .header-bg { background-color: #0f3564 !important; }
        .header-text { color: #ffffff !important; }
        .header-subtitle { color: #B3D4FF !important; }
        .main-content { background-color: #ffffff !important; }
        .main-text { color: #2c3e50 !important; }
        .blue-text { color: #0f3564 !important; }
        .light-blue-text { color: #1f61b3 !important; }
        .section-bg { background-color: #e8f4fd !important; }
        .contact-bg { background-color: #f8f9fc !important; }
        .footer-bg { background-color: #f8f9fc !important; }
        .footer-text { color: #64748b !important; }
        .copyright-text { color: #94a3b8 !important; }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f0f2f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f2f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(15, 53, 100, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td class="header-bg" style="background-color: #0f3564 !important; padding: 40px; text-align: center; border-bottom: 4px solid #FFC107 !important;">
                            <div style="text-align: center; margin-bottom: 20px;">
                                <i class="ri-checkbox-circle-line" style="font-size: 64px; color: #4ade80 !important; display: block; margin: 0 auto 15px;"></i>
                            </div>
                            <h1 class="header-text" style="color: #ffffff !important; margin: 0; font-size: 28px; font-weight: bold; letter-spacing: -0.5px;">
                                ¡Gracias por contactarnos, {$primer_nombre}!
                            </h1>
                            <p class="header-subtitle" style="color: #B3D4FF !important; margin: 12px 0 0 0; font-size: 15px; font-weight: 500;">
                                Hemos recibido tu solicitud correctamente
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Contenido Principal -->
                    <tr>
                        <td class="main-content" style="background-color: #ffffff !important; padding: 40px 35px;">
                            <p class="main-text" style="color: #2c3e50 !important; font-size: 16px; line-height: 1.8; margin: 0 0 25px 0;">
                                Estimado/a <strong style="color: #2c3e50 !important;">{$nombre_completo}</strong>,
                            </p>
                            
                            <p class="main-text" style="color: #2c3e50 !important; font-size: 16px; line-height: 1.8; margin: 0 0 25px 0;">
                                Gracias por comunicarte con <strong class="blue-text" style="color: #0f3564 !important;">INTERCERT LATAM</strong>. 
                                Hemos recibido tu solicitud de información sobre nuestras certificaciones ISO.
                            </p>
                            
                            <div class="section-bg" style="background-color: #e8f4fd !important; padding: 25px; border-radius: 10px; border-left: 5px solid #0f3564 !important; margin: 30px 0;">
                                <p style="margin: 0 0 15px 0; color: #0f3564 !important; font-size: 18px; font-weight: 700;">
                                    <i class="ri-time-line light-blue-text" style="color: #1f61b3 !important; margin-right: 8px; vertical-align: middle;"></i>
                                    ¿Qué sigue ahora?
                                </p>
                                <p class="main-text" style="margin: 0; color: #2c3e50 !important; font-size: 15px; line-height: 1.7;">
                                    Uno de nuestros asesores especializados se pondrá en contacto contigo 
                                    <strong class="blue-text" style="color: #0f3564 !important;">en los próximos minutos</strong> para brindarte toda la información 
                                    que necesitas sobre nuestras certificaciones ISO.
                                </p>
                            </div>
                            
                            <!-- Contacto de Emergencia -->
                            <div class="contact-bg" style="background-color: #f8f9fc !important; padding: 25px; border-radius: 10px; border: 1px solid #e3e8ef !important; margin: 30px 0;">
                                <h3 style="color: #0f3564 !important; font-size: 18px; margin: 0 0 15px 0; font-weight: 700;">
                                    <i class="ri-customer-service-line light-blue-text" style="color: #1f61b3 !important; margin-right: 8px; vertical-align: middle;"></i>
                                    ¿Necesitas atención inmediata?
                                </h3>
                                <p class="main-text" style="margin: 0 0 20px 0; color: #2c3e50 !important; font-size: 15px; line-height: 1.6;">
                                    También puedes contactarnos directamente:
                                </p>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 10px 0;">
                                            <p style="margin: 0 0 8px 0; color: #64748b !important; font-size: 14px; font-weight: 600;">
                                                Cajamarca:
                                            </p>
                                            <a href="tel:+51982432009" class="light-blue-text" style="color: #1f61b3 !important; text-decoration: none; font-weight: 600; font-size: 16px; display: block; margin-bottom: 8px;">
                                                <i class="ri-phone-line" style="margin-right: 8px; vertical-align: middle;"></i>
                                                +51 982 432 009
                                            </a>
                                            <a href="https://api.whatsapp.com/send?phone=51982432009" style="color: #25D366 !important; text-decoration: none; font-weight: 600; font-size: 16px; display: block;">
                                                <i class="ri-whatsapp-line" style="margin-right: 8px; vertical-align: middle;"></i>
                                                WhatsApp: +51 982 432 009
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 20px 0 10px 0;">
                                            <p style="margin: 0 0 8px 0; color: #64748b !important; font-size: 14px; font-weight: 600;">
                                                Sede Principal:
                                            </p>
                                            <a href="tel:+51986123418" class="light-blue-text" style="color: #1f61b3 !important; text-decoration: none; font-weight: 600; font-size: 16px; display: block; margin-bottom: 8px;">
                                                <i class="ri-phone-line" style="margin-right: 8px; vertical-align: middle;"></i>
                                                +51 986 123 418
                                            </a>
                                            <a href="https://api.whatsapp.com/send?phone=51986123418" style="color: #25D366 !important; text-decoration: none; font-weight: 600; font-size: 16px; display: block;">
                                                <i class="ri-whatsapp-line" style="margin-right: 8px; vertical-align: middle;"></i>
                                                WhatsApp: +51 986 123 418
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0 0 0;">
                                            <a href="mailto:info@intercertlatam.com" class="light-blue-text" style="color: #1f61b3 !important; text-decoration: none; font-weight: 600; font-size: 16px; display: block;">
                                                <i class="ri-mail-line" style="margin-right: 8px; vertical-align: middle;"></i>
                                                info@intercertlatam.com
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <p class="main-text" style="color: #2c3e50 !important; font-size: 15px; line-height: 1.7; margin: 30px 0 0 0;">
                                Saludos cordiales,<br>
                                <strong class="blue-text" style="color: #0f3564 !important;">El equipo de INTERCERT LATAM</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="footer-bg" style="background-color: #f8f9fc !important; padding: 30px; text-align: center; border-top: 3px solid #0f3564 !important;">
                            <p class="footer-text" style="margin: 0 0 15px 0; color: #64748b !important; font-size: 13px; line-height: 1.6;">
                                <strong class="blue-text" style="color: #0f3564 !important;">INTERCERT LATAM</strong><br>
                                Jr. José Sabogal 1296, Cajamarca 06001, Perú<br>
                                Tel: +51 986 123 418 | Email: <a href="mailto:info@intercertlatam.com" class="light-blue-text" style="color: #1f61b3 !important; text-decoration: underline;">info@intercertlatam.com</a>
                            </p>
                            <p class="footer-text" style="margin: 15px 0 0 0; color: #94a3b8 !important; font-size: 12px;">
                                Cajamarca: +51 982 432 009 | Sede Principal: +51 986 123 418
                            </p>
                            <p class="copyright-text" style="margin: 8px 0 0 0; color: #94a3b8 !important; font-size: 12px;">
                                © {$anio_actual} INTERCERT LATAM. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}
?>


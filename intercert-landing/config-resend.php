<?php
// Configuración de Resend Email API
return [
    // API Key de Resend
    'api_key' => 're_REeDsgea_2EDm59Tv8YTPQrhrMAWzLb8J',
    
    // Email del remitente (debe estar verificado en Resend)
    'from_email' => 'no-reply@intercertlatam.net',
    'from_name' => 'INTERCERT LATAM',
    
    // Lista de destinatarios para notificaciones
    'recipients' => [
        'setter@intercertlatam.net',
        'gerentecomercial@intercertlatam.net',
        'karem.intercert@outlook.com'
    ],
    
    // Configuración adicional
    'settings' => [
        'timeout' => 30,
        'retry_attempts' => 2,
        'enable_logging' => true
    ]
];
?>


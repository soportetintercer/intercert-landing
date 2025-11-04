<?php
// Configuración de HubSpot CRM API
return [
    // Access Token de HubSpot (obtener desde tu Developer App)
    'access_token' => 'pat-na1-2cf8b9f5-6341-4afa-b19d-890d1a5424a9',
    
    // Client ID de HubSpot
    'client_id' => '078b06dc-ac4c-4e5e-8416-839c317037c6',
    
    // Client Secret de HubSpot
    'client_secret' => 'bdd1159a-413e-43d8-81bc-fa47f8a37cd7',
    
    // Portal ID de HubSpot
    'portal_id' => '47637900',
    
    // URL base de la API de HubSpot
    'api_url' => 'https://api.hubapi.com',
    
    // Endpoints específicos
    'endpoints' => [
        'contacts' => '/crm/v3/objects/contacts',
        'properties' => '/crm/v3/properties/contacts',
        'schemas' => '/crm/v3/schemas/contacts'
    ],
    
    // Configuración adicional
    'settings' => [
        'timeout' => 30,
        'retry_attempts' => 3,
        'enable_logging' => true,
        'log_file' => 'hubspot_logs.txt'
    ],
    
    // Mapeo de propiedades personalizadas
    'custom_properties' => [
        'form_type' => 'Tipo de Formulario',
        'certifications_requested' => 'Certificaciones Solicitadas',
        'company_sector' => 'Sector de la Empresa',
        'employee_count' => 'Número de Empleados',
        'company_ruc' => 'RUC de la Empresa',
        'lead_source' => 'Fuente del Lead'
    ]
];
?>


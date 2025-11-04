<?php
// Versión simplificada del procesador de formulario con HubSpot
// Desactivar display_errors en producción para evitar HTML en respuesta JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Función para enviar a HubSpot (Contacto + Negocio)
function sendToHubSpot($data, $hubspot_config) {
    $certificaciones = is_array($data['certificaciones']) ? implode(', ', $data['certificaciones']) : ($data['certificaciones'] ?? '');
    
    // 1. CREAR CONTACTO
    $contact_data = [
        'properties' => [
            'firstname' => explode(' ', $data['nombre_completo'])[0],
            'lastname' => explode(' ', $data['nombre_completo'])[1] ?? '',
            'email' => $data['email'],
            'phone' => ($data['pais_prefijo'] ?? '') . ' ' . ($data['telefono'] ?? ''),
            'jobtitle' => $data['cargo'] ?? '',
            'company' => $data['nombre_empresa'] ?? '',
            'lifecyclestage' => 'lead',
            'hs_lead_status' => 'NEW'
        ]
    ];
    
    $contact_url = $hubspot_config['api_url'] . $hubspot_config['endpoints']['contacts'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $contact_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($contact_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $hubspot_config['access_token'],
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $contact_response = curl_exec($ch);
    $contact_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($curl_error) {
        throw new Exception('Error cURL HubSpot Contacto: ' . $curl_error);
    }
    
    if ($contact_http_code !== 200 && $contact_http_code !== 201) {
        // Verificar si es un error de contacto existente
        $error_data = json_decode($contact_response, true);
        if (isset($error_data['category']) && $error_data['category'] === 'CONFLICT') {
            // El contacto ya existe, obtener su ID del mensaje de error
            preg_match('/Existing ID: (\d+)/', $error_data['message'], $matches);
            $existing_contact_id = $matches[1] ?? null;
            
            if ($existing_contact_id) {
                // Crear un resultado simulado para el contacto existente
                $contact_result = [
                    'id' => $existing_contact_id,
                    'properties' => [
                        'firstname' => explode(' ', $data['nombre_completo'])[0],
                        'lastname' => explode(' ', $data['nombre_completo'])[1] ?? '',
                        'email' => $data['email']
                    ],
                    'createdAt' => date('c'),
                    'updatedAt' => date('c'),
                    'archived' => false
                ];
            } else {
                throw new Exception('Error HubSpot API Contacto: ' . $contact_response);
            }
        } else {
            throw new Exception('Error HubSpot API Contacto: ' . $contact_response);
        }
    } else {
        $contact_result = json_decode($contact_response, true);
    }
    
    // 2. INTENTAR CREAR NEGOCIO (DEAL)
    $deal_result = null;
    $association_success = false;
    
    try {
        $deal_data = [
            'properties' => [
                'dealname' => ($data['nombre_empresa'] ?? 'Sin nombre') . ' - Landing Cajamarca',
                'dealstage' => '1175730750',
                'pipeline' => '799805334',
                'dealtype' => 'newbusiness',
                'amount' => '0'
            ]
        ];
        
        $deal_url = $hubspot_config['api_url'] . '/crm/v3/objects/deals';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $deal_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($deal_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $hubspot_config['access_token'],
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $deal_response = curl_exec($ch);
        $deal_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $deal_curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($deal_curl_error) {
            throw new Exception('Error cURL HubSpot Negocio: ' . $deal_curl_error);
        }
        
        if ($deal_http_code !== 200 && $deal_http_code !== 201) {
            throw new Exception('Error HubSpot API Negocio: ' . $deal_response);
        }
        
        $deal_result = json_decode($deal_response, true);
        
        // 3. ASOCIAR CONTACTO CON NEGOCIO
        if (isset($contact_result['id']) && isset($deal_result['id'])) {
            // Usar API v4 batch con tipo de asociación correcto (4 = Contact to Deal)
            $association_url = $hubspot_config['api_url'] . '/crm/v4/associations/contacts/deals/batch/create';
            $association_data = [
                'inputs' => [
                    [
                        'from' => ['id' => $contact_result['id']],
                        'to' => ['id' => $deal_result['id']],
                        'types' => [
                            ['associationCategory' => 'HUBSPOT_DEFINED', 'associationTypeId' => 4]
                        ]
                    ]
                ]
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $association_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($association_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $hubspot_config['access_token'],
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $association_response = curl_exec($ch);
            $association_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $association_success = ($association_http_code === 200 || $association_http_code === 201);
        }
        
    } catch (Exception $e) {
        // Si falla la creación del deal, continuamos sin él
        error_log('Error creando deal en HubSpot: ' . $e->getMessage());
    }
    
    return [
        'contact' => $contact_result,
        'deal' => $deal_result,
        'association_success' => $association_success
    ];
}

// Headers para JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Obtener datos del formulario
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    if (empty($input)) {
        throw new Exception('No se recibieron datos del formulario');
    }
    
    // Validar datos requeridos
    $required_fields = ['nombre_completo', 'email', 'telefono', 'nombre_empresa'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }
    
    // Incluir archivos necesarios
    require_once 'email-notifications-resend.php';
    
    // Verificar si el archivo de configuración existe
    if (!file_exists('config-hubspot.php')) {
        throw new Exception('Archivo de configuración de HubSpot no encontrado');
    }
    
    $hubspot_config = include 'config-hubspot.php';
    
    if (!is_array($hubspot_config)) {
        throw new Exception('Error al cargar configuración de HubSpot');
    }
    
    // Enviar a HubSpot CRM
    $hubspot_result = sendToHubSpot($input, $hubspot_config);
    
    // Enviar email de confirmación al cliente
    $confirmation_result = sendClientConfirmationEmail($input);
    
    // Enviar email de notificación a empleados
    $notification_result = sendClientNotificationEmailResend($input);
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Formulario procesado exitosamente - Contacto creado en HubSpot',
        'hubspot' => [
            'contact_id' => $hubspot_result['contact']['id'] ?? null,
            'deal_id' => $hubspot_result['deal']['id'] ?? null,
            'association_success' => $hubspot_result['association_success']
        ],
        'emails' => [
            'confirmation_sent' => $confirmation_result['success'] ?? false,
            'notification_sent' => $notification_result['success'] ?? false
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error fatal: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
?>
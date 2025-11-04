<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración para Google Sheets
$config = [
    'google_sheets_id' => '1HvdjqeZZV8RnPWLgIoZLbDEJTjm_l8XtOWu6P9TJ-wE',
    'service_account_email' => 'forumcajamarca@forumcajamarca.iam.gserviceaccount.com',
    'private_key' => '-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC9JYyVnz0Ggrdb
ysWFuxcxupGLdZhWQOim8NtHmIVNEkB1ZDylEVkWDKKNxjT64n7vFVt42awGFyqX
xuCN4o/xbly5FIFdUHgjgBX1cORtq1o+NdMRayXai4xFPxAfMg3nkdtdVjm2FKKT
/anRlOm10uLEGypWHsh33GpSTwMoGXfR9U7Gvz7BWHKoVDhRswi+8Xpssqws/4Su
fI20VSMuSAIwSSWS4mFOvt5RSf76CUzQjXtDsKMZM/vgrR6k9sZn4rqxQCzn4KDc
0j7E9BjL6XQ5JTU9lGxqOAdW9ktKRoe2WcKI1HFswZJcZTRdTER36V5LnQGaXMNt
CvGXCLgtAgMBAAECggEALM0+V7lkX8yqCEbHEwZN9+Q4ccdMqk+NG+/MjNCacHc8
h+HSgInTp2aeQUUbyvpHtTNCm6U5eybVGqbLjSgHTtlEgr1R6Au1rTj7WovfO3/d
LAOdR1CxImsv8j3+AFmflASHKHxSRqfsG3A333HtirkVJ/zEQipf8w4lK/JVrdMz
VveyxmTzqAqmTDsbjACaRu33T1Pbp7bFdW7gKu2SmRfr6wxgxnOtd4TZB/jWNyMa
MGywx0qk9Qhig29vK234Qe+uGer8qbdYlZAJ7N3uQVyCs70o3ifDo1Wfd60ZYUnx
35PlVzdUaiOAVwyRxjs6dw08EXk55pNAWzgvv1g1aQKBgQD7MfM86WrmnPXiKQhN
nCVN1CBv0nhCqXFz2P5sWOUfXZlQikZyl75kTmDFQhyR4FifwR9JUQnJGdRraxiZ
QBaRRlnpRBMYBBptjoev7OdVIt4WWz4q0W1lpOGDzvx0Qkt2w/WuNDmmczS8DV7n
x96kLyaN8d9k37NpfEM2triBBQKBgQDAw8LDea9LIniZfsp+/V8FnEBuxTwl7lRl
ieLy4jBahBNP1Zup8UJGrGm3GnfNqodfP9OQeQIm7EAAzTdeImi5bS52C8nyRmvk
iUQ85DsrR3f8dd1d43HjbqrRInyfv+/VOAFBj3a4TH/Fri+Aboqeba6SpQLCMl2s
Z/7qM66jCQKBgHHl0DjKTeKLQSJvIIiwSGnlyV9qu4Ted8bjmVlfZWS3eBEA4biL
/ZRVxaMruvCaHRUy6BDCYgGBaMPcJ6c1XAY7NAGFEHNPSPxsKDIo5SvBR5ozfRjF
JSl1fZvaerXsaXNMn1WB8LH8gujR1zqZceZYSs8J4RVIKX2nDoL8juRRAoGAcWJ0
rHBmGKbVE0yOJbY5b15iVAW2BIW0pOk/UiiT0po/lcUKxCKCHnoJ0MdD0vjMmc08
T42uI2DxoMcVG0zYFsHPA2aZyeV8TzgiqEKDP0jRnfFiXXiMo/+TgGJttbW7h8pi
isKtTWjJ+2gizsO2y8uuVNm0zq+7g87EFdSCk6kCgYEA5ZL16S26V0fCgJvmT0My
dMf3oATl3IuZHZ8iVn4uKtsOiPMYR6NmL7F+qinEm6+kVjfdtWUmB0TN7g/vlHkz
wyRiNi+GwROozszgU+X3kwl9cvsTseR46ZK7j65iYxmd/8Lt9DiWDk6w9248TYbM
qd2b7CGePjX77coYxKEP5T8=
-----END PRIVATE KEY-----',
    'sheet_name' => 'Sheet1',
    'mode' => 'production'
];

// Configuración de HubSpot
$hubspot_config = include 'config-hubspot.php';

// Incluir funciones de notificación por correo usando Resend
require_once 'email-notifications-resend.php';

// Función para obtener token de Service Account
function getServiceAccountToken($config) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
    
    $payload = json_encode([
        'iss' => $config['service_account_email'],
        'scope' => 'https://www.googleapis.com/auth/spreadsheets',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => time() + 3600,
        'iat' => time()
    ]);
    
    $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    
    $signature = '';
    $private_key = $config['private_key'];
    openssl_sign($base64Header . '.' . $base64Payload, $signature, $private_key, 'SHA256');
    $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    return $data['access_token'] ?? null;
}

// Función para enviar a Google Sheets
function sendToGoogleSheets($data, $config) {
    $token = getServiceAccountToken($config);
    if (!$token) {
        throw new Exception('No se pudo obtener el token de acceso');
    }
    
    $certificaciones = is_array($data['certificaciones']) ? implode(', ', $data['certificaciones']) : $data['certificaciones'];
    
    $values = [
        [
            date('Y-m-d H:i:s'),
            $data['nombre_completo'] ?? '',
            $data['email'] ?? '',
            $data['pais_prefijo'] ?? '',
            $data['telefono'] ?? '',
            $data['cargo'] ?? '',
            $data['nombre_empresa'] ?? '',
            $data['ruc_empresa'] ?? '',
            $data['numero_empleados'] ?? '',
            $data['sector_empresa'] ?? '',
            $data['tipo_servicio'] ?? '',
            $certificaciones,
            $data['comentarios'] ?? ''
        ]
    ];
    
    $url = "https://sheets.googleapis.com/v4/spreadsheets/{$config['google_sheets_id']}/values/A1:append?valueInputOption=RAW";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['values' => $values]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception('Error al enviar a Google Sheets: ' . $response);
    }
    
    return json_decode($response, true);
}

// Función para enviar a HubSpot (Contacto + Negocio)
function sendToHubSpot($data, $hubspot_config) {
    $certificaciones = is_array($data['certificaciones']) ? implode(', ', $data['certificaciones']) : $data['certificaciones'];
    
    // 1. CREAR CONTACTO
    $contact_data = [
        'properties' => [
            'firstname' => explode(' ', $data['nombre_completo'])[0],
            'lastname' => explode(' ', $data['nombre_completo'])[1] ?? '',
            'email' => $data['email'],
            'phone' => ($data['pais_prefijo'] ?? '') . ' ' . ($data['telefono'] ?? ''),
            'jobtitle' => $data['cargo'],
            'company' => $data['nombre_empresa'],
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
                'dealname' => $data['nombre_empresa'] . ' - Landing Cajamarca',
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
            $association_data = [
                'inputs' => [
                    [
                        'from' => [
                            'id' => $contact_result['id']
                        ],
                        'to' => [
                            'id' => $deal_result['id']
                        ],
                        'type' => 'contact_to_deal'
                    ]
                ]
            ];
            
            $association_url = $hubspot_config['api_url'] . '/crm/v3/associations/contacts/deals/batch/create';
            
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
            
            if (!$association_success) {
                error_log('Error asociando contacto con negocio: ' . $association_response);
            }
        }
        
    } catch (Exception $e) {
        // Si falla la creación del negocio, solo log el error pero continúa
        error_log('Error creando negocio en HubSpot: ' . $e->getMessage());
        $deal_result = ['error' => $e->getMessage()];
    }
    
    return [
        'contact' => $contact_result,
        'deal' => $deal_result,
        'association_success' => $association_success
    ];
}

// Procesar formulario
try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('No se recibieron datos del formulario');
    }
    
    // Validar campos requeridos
    $required_fields = ['nombre_completo', 'email', 'telefono', 'cargo', 'nombre_empresa'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            throw new Exception("El campo {$field} es requerido");
        }
    }
    
    // Enviar a Google Sheets (backup)
    $sheets_result = sendToGoogleSheets($input, $config);
    
    // Enviar a HubSpot (CRM)
    $hubspot_result = sendToHubSpot($input, $hubspot_config);
    
    // Enviar notificación por correo a los ejecutivos usando Resend
    $email_result = sendClientNotificationEmailResend($input);
    
    // Enviar email de confirmación al cliente
    $confirmation_result = sendClientConfirmationEmail($input);
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Formulario enviado exitosamente - Contacto procesado en HubSpot',
        'sheets_result' => $sheets_result,
        'hubspot_contact' => $hubspot_result['contact'],
        'hubspot_deal' => $hubspot_result['deal'],
        'hubspot_association' => $hubspot_result['association_success'],
        'email_notification' => $email_result,
        'client_confirmation' => $confirmation_result
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>


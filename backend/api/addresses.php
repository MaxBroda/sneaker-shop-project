<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/cors.php';
require_once __DIR__ . '/../utils/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] 
    ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] 
    ?? '';
if (!$authHeader && function_exists('getallheaders')) {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $authHeader = $headers['authorization'] ?? '';
}

if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Nicht authentifiziert']);
    exit;
}

$token = $matches[1];
$user = getUserFromToken($token, $pdo);

if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Ungültiger Token']);
    exit;
}

$userId = $user['id'];

try {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->prepare("SELECT id, street, house_number, city, postal_code, country, is_default FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id ASC");
            $stmt->execute([$userId]);
            $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $addresses
            ]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['street'], $data['house_number'], $data['city'], $data['postal_code'], $data['country'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Fehlende Pflichtfelder']);
                exit;
            }

            $isDefault = $data['is_default'] ?? 0;

            if ($isDefault) {
                $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?")->execute([$userId]);
            }

            $stmt = $pdo->prepare("INSERT INTO addresses (user_id, street, house_number, city, postal_code, country, is_default) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $data['street'],
                $data['house_number'],
                $data['city'],
                $data['postal_code'],
                $data['country'],
                $isDefault ? 1 : 0
            ]);

            $newId = $pdo->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Adresse erfolgreich hinzugefügt',
                'data' => ['id' => $newId]
            ]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Adress-ID fehlt']);
                exit;
            }

            $addressId = $data['id'];

            $stmt = $pdo->prepare("SELECT id FROM addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addressId, $userId]);
            if (!$stmt->fetch()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Keine Berechtigung']);
                exit;
            }

            if (isset($data['is_default']) && $data['is_default']) {
                $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?")->execute([$userId]);
            }

            $fields = [];
            $values = [];
            
            if (isset($data['street'])) { $fields[] = 'street = ?'; $values[] = $data['street']; }
            if (isset($data['house_number'])) { $fields[] = 'house_number = ?'; $values[] = $data['house_number']; }
            if (isset($data['city'])) { $fields[] = 'city = ?'; $values[] = $data['city']; }
            if (isset($data['postal_code'])) { $fields[] = 'postal_code = ?'; $values[] = $data['postal_code']; }
            if (isset($data['country'])) { $fields[] = 'country = ?'; $values[] = $data['country']; }
            if (isset($data['is_default'])) { $fields[] = 'is_default = ?'; $values[] = $data['is_default'] ? 1 : 0; }

            if (empty($fields)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Keine Felder zum Aktualisieren']);
                exit;
            }

            $values[] = $addressId;
            $values[] = $userId;

            $sql = "UPDATE addresses SET " . implode(', ', $fields) . " WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            echo json_encode([
                'success' => true,
                'message' => 'Adresse erfolgreich aktualisiert'
            ]);
            break;

        case 'DELETE':
            $addressId = $_GET['id'] ?? null;
            
            if (!$addressId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Adress-ID fehlt']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT is_default FROM addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addressId, $userId]);
            $address = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$address) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Adresse nicht gefunden']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addressId, $userId]);

            if ($address['is_default']) {
                $pdo->prepare("UPDATE addresses SET is_default = 1 WHERE user_id = ? ORDER BY id ASC LIMIT 1")->execute([$userId]);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Adresse erfolgreich gelöscht'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

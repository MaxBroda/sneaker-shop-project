<?php
/**
 * Addresses API Endpoint
 * Handles CRUD operations for user addresses
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validation.php';

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    // Require authentication for all address operations
    $user = Auth::require();
    $userId = $user['id'];

    switch ($method) {
        case 'GET':
            $stmt = $pdo->prepare("
                SELECT id, street, house_number, city, postal_code, country, is_default 
                FROM addresses 
                WHERE user_id = ? 
                ORDER BY is_default DESC, id ASC
            ");
            $stmt->execute([$userId]);
            $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ApiResponse::success($addresses);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true) ?? [];

            $validator = Validator::make($data)
                ->required('street', 'Straße ist erforderlich')
                ->required('house_number', 'Hausnummer ist erforderlich')
                ->required('city', 'Stadt ist erforderlich')
                ->required('postal_code', 'PLZ ist erforderlich')
                ->required('country', 'Land ist erforderlich');

            if ($validator->fails()) {
                ApiResponse::validationError($validator->getErrors());
            }

            $isDefault = isset($data['is_default']) && $data['is_default'] ? 1 : 0;

            // If setting as default, unset other defaults
            if ($isDefault) {
                $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?")->execute([$userId]);
            }

            $stmt = $pdo->prepare("
                INSERT INTO addresses (user_id, street, house_number, city, postal_code, country, is_default) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                trim($data['street']),
                trim($data['house_number']),
                trim($data['city']),
                trim($data['postal_code']),
                trim($data['country']),
                $isDefault
            ]);

            $newId = $pdo->lastInsertId();

            // Return the complete address data
            $newAddress = [
                'id' => $newId,
                'street' => trim($data['street']),
                'house_number' => trim($data['house_number']),
                'city' => trim($data['city']),
                'postal_code' => trim($data['postal_code']),
                'country' => trim($data['country']),
                'is_default' => $isDefault
            ];

            ApiResponse::success($newAddress, 'Adresse erfolgreich hinzugefügt', 201);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true) ?? [];

            if (!isset($data['id'])) {
                ApiResponse::error('Adress-ID fehlt', 400);
            }

            $addressId = intval($data['id']);

            // Verify ownership
            $stmt = $pdo->prepare("SELECT id FROM addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addressId, $userId]);
            if (!$stmt->fetch()) {
                ApiResponse::forbidden('Keine Berechtigung für diese Adresse');
            }

            // If setting as default, unset other defaults
            if (isset($data['is_default']) && $data['is_default']) {
                $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?")->execute([$userId]);
            }

            // Build update query dynamically
            $fields = [];
            $values = [];
            $allowedFields = ['street', 'house_number', 'city', 'postal_code', 'country', 'is_default'];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $field === 'is_default' ? ($data[$field] ? 1 : 0) : trim($data[$field]);
                }
            }

            if (empty($fields)) {
                ApiResponse::error('Keine Felder zum Aktualisieren', 400);
            }

            $values[] = $addressId;
            $values[] = $userId;

            $sql = "UPDATE addresses SET " . implode(', ', $fields) . " WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            ApiResponse::success(null, 'Adresse erfolgreich aktualisiert');
            break;

        case 'DELETE':
            $addressId = $_GET['id'] ?? null;

            if (!$addressId) {
                ApiResponse::error('Adress-ID fehlt', 400);
            }

            $addressId = intval($addressId);

            // Check ownership and get address info
            $stmt = $pdo->prepare("SELECT is_default FROM addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addressId, $userId]);
            $address = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$address) {
                ApiResponse::notFound('Adresse nicht gefunden');
            }

            // Delete the address
            $stmt = $pdo->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addressId, $userId]);

            // If deleted address was default, set another as default
            if ($address['is_default']) {
                $pdo->prepare("
                    UPDATE addresses 
                    SET is_default = 1 
                    WHERE user_id = ? 
                    ORDER BY id ASC 
                    LIMIT 1
                ")->execute([$userId]);
            }

            ApiResponse::success(null, 'Adresse erfolgreich gelöscht');
            break;

        default:
            ApiResponse::methodNotAllowed();
    }

} catch (Exception $e) {
    ApiResponse::serverError('Ein Fehler ist aufgetreten');
}

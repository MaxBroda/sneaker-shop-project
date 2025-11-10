<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/cors.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // 🔹 Check basic required fields
    if (!isset($data['email'], $data['password'], $data['role'], $data['firstName'], $data['lastName'], $data['address'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Erforderliche Felder fehlen (inklusive Adresse).']);
        exit;
    }

    $email = trim($data['email']);
    $firstName = trim($data['firstName']);
    $lastName = trim($data['lastName']);
    $password = $data['password'];
    $role = strtolower(trim($data['role']));
    $address = $data['address'];

    // 🔹 Check role
    if (!in_array($role, ['customer', 'seller'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ungültige Rolle']);
        exit;
    }

    // 🔹 Validate address fields
    $requiredAddressFields = ['street', 'house_number', 'city', 'postal_code', 'country'];
    foreach ($requiredAddressFields as $field) {
        if (empty($address[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Adressfeld '$field' darf nicht leer sein."]);
            exit;
        }
    }

    // 🔹 Check if user already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'E-Mail wird bereits verwendet.']);
        exit;
    }

    // 🔹 Create user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertStmt = $pdo->prepare("
        INSERT INTO users (email, first_name, last_name, password_hash, role)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insertStmt->execute([$email, $firstName, $lastName, $hashedPassword, $role]);

    $userId = $pdo->lastInsertId();

    // 🔹 Insert required address (kein optionaler Block mehr!)
    $insertAddr = $pdo->prepare("
        INSERT INTO addresses (user_id, street, house_number, city, postal_code, country)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $insertAddr->execute([
        $userId,
        $address['street'],
        $address['house_number'],
        $address['city'],
        $address['postal_code'],
        $address['country']
    ]);

    // 🔹 Fetch address for response
    $addrStmt = $pdo->prepare("SELECT street, house_number, city, postal_code, country FROM addresses WHERE user_id = ?");
    $addrStmt->execute([$userId]);
    $address = $addrStmt->fetch(PDO::FETCH_ASSOC);

    // 🔹 Generate login token
    $token = base64_encode(random_bytes(32));
    $tokenStmt = $pdo->prepare("INSERT INTO user_tokens (user_id, token) VALUES (?, ?)");
    $tokenStmt->execute([$userId, $token]);

    echo json_encode([
        'success' => true,
        'message' => 'Registrierung erfolgreich. Automatisch eingeloggt.',
        'user' => [
            'id' => $userId,
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'role' => $role,
            'address' => $address
        ],
        'token' => $token
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

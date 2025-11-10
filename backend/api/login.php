<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/cors.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'E-Mail oder Passwort fehlen']);
        exit;
    }

    $email = trim($data['email']);
    $password = $data['password'];

    // User abrufen
    $stmt = $pdo->prepare("SELECT id, email, first_name, last_name, password_hash, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Ungültige E-Mail oder Passwort']);
        exit;
    }

    // Adresse abrufen
    $addrStmt = $pdo->prepare("SELECT street,house_number, city, postal_code, country FROM addresses WHERE user_id = ?");
    $addrStmt->execute([$user['id']]);
    $address = $addrStmt->fetch(PDO::FETCH_ASSOC);

    // Token generieren
    $token = base64_encode(random_bytes(32));

    // In der DB speichern
    $stmt = $pdo->prepare("INSERT INTO user_tokens (user_id, token) VALUES (?, ?)");
    $stmt->execute([$user['id'], $token]);

    echo json_encode([
        'success' => true,
        'message' => 'Login erfolgreich',
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'role' => $user['role'],
            'address' => $address ?: null
        ],
        'token' => $token
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

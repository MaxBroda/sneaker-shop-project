<?php
require_once __DIR__ . '/db_connection.php';

function authenticate()
{
    global $pdo;

    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Fehlender Token']);
        exit;
    }

    $token = trim(str_replace('Bearer ', '', $headers['Authorization']));
    $stmt = $pdo->prepare("
        SELECT users.id, users.email, users.first_name, users.last_name, users.role
        FROM users
        JOIN user_tokens ON users.id = user_tokens.user_id
        WHERE user_tokens.token = ?
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Ungültiger Token']);
        exit;
    }

    return $user;
}

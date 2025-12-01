<?php
require_once __DIR__ . '/db_connection.php';

function authenticate()
{
    global $pdo;

    $authHeader = null;

    error_log("All headers: " . print_r(getallheaders(), true));
    error_log("HTTP_AUTHORIZATION: " . ($_SERVER['HTTP_AUTHORIZATION'] ?? 'not set'));

    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $authHeader = $headers['authorization'];
        }
    }

    if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (!$authHeader && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $authHeader = $headers['authorization'];
        }
    }

    error_log("Final auth header: " . ($authHeader ?? 'NULL'));

    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Fehlender Token']);
        exit;
    }

    $token = trim(str_replace('Bearer ', '', $authHeader));
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

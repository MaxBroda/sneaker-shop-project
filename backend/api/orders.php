<?php
require_once __DIR__ . '/../utils/cors.php';
require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../models/Order.php';

$orderModel = new Order($pdo);
$method = $_SERVER['REQUEST_METHOD'];

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if (!$authHeader && function_exists('getallheaders')) {
    $headers = getallheaders();
    $authHeader = $headers['authorization'] ?? '';
}

$token = '';
if (preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

if ($method === 'GET') {
    error_log("Orders GET - Token received: " . $token);
    error_log("Orders GET - Auth header: " . $authHeader);
    
    $user = getUserFromToken($token, $pdo);
    error_log("Orders GET - User found: " . ($user ? json_encode($user) : 'null'));
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Nicht autorisiert']);
        exit;
    }

    try {
        $orders = $orderModel->getUserOrders($user['id']);
        
        echo json_encode([
            'success' => true,
            'data' => $orders
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $user = null;
    if ($token) {
        $user = getUserFromToken($token, $pdo);
    }

    if (!isset($data['items']) || empty($data['items'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Keine Artikel in der Bestellung']);
        exit;
    }

    if (!isset($data['total']) || !isset($data['billing_address'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Fehlende Bestelldaten']);
        exit;
    }

    try {
        $result = $orderModel->create(
            $user ? $user['id'] : null,
            $data['items'],
            $data['total'],
            $data['billing_address'],
            $data['shipping_address'] ?? null,
            $data['payment_method'] ?? 'paypal'
        );

        echo json_encode([
            'success' => true,
            'message' => 'Bestellung erfolgreich erstellt',
            'data' => [
                'order_id' => $result['order_id'],
                'order_number' => $result['order_number']
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt']);

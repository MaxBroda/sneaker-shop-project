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

$user = getUserFromToken($token, $pdo);
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Nicht autorisiert']);
    exit;
}

if ($user['role'] !== 'seller') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Nur für Verkäufer']);
    exit;
}

if ($method === 'GET') {
    try {
        error_log("[SELLER-ORDERS] Fetching orders for seller ID: " . $user['id']);
        $orders = $orderModel->getSellerOrders($user['id']);
        error_log("[SELLER-ORDERS] Orders fetched: " . count($orders));
        
        echo json_encode([
            'success' => true,
            'data' => $orders
        ]);
    } catch (Exception $e) {
        error_log("[SELLER-ORDERS] Exception: " . $e->getMessage());
        error_log("[SELLER-ORDERS] Stack trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['order_id']) || !isset($data['status'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Fehlende Daten']);
        exit;
    }

    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($data['status'], $validStatuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ungültiger Status']);
        exit;
    }

    try {
        $orderModel->updateStatus($data['order_id'], $data['status'], $user['id']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Status aktualisiert'
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

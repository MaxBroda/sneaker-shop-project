<?php
/**
 * Seller Orders API Endpoint
 * Handles order management for sellers
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validation.php';
require_once __DIR__ . '/../models/Order.php';

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}   

$orderModel = new Order($pdo);
$method = $_SERVER['REQUEST_METHOD'];

try {
    // Require seller authentication
    $user = Auth::requireSeller();

    // GET - Retrieve seller's orders
    if ($method === 'GET') {
        $orders = $orderModel->getSellerOrders($user['id']);
        ApiResponse::success($orders);
    }

    // PUT - Update order status
    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = Validator::make($data)
            ->required('order_id', 'Bestellungs-ID ist erforderlich')
            ->required('status', 'Status ist erforderlich')
            ->integer('order_id')
            ->inArray('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'], 'Ungültiger Status');

        if ($validator->fails()) {
            ApiResponse::validationError($validator->getErrors());
        }

        $orderId = $validator->getInt('order_id');
        $status = $validator->getValue('status');

        $orderModel->updateStatus($orderId, $status, $user['id']);

        ApiResponse::success(null, 'Status aktualisiert');
    }

    ApiResponse::methodNotAllowed();

} catch (Exception $e) {
    ApiResponse::serverError('Ein Fehler ist aufgetreten');
}

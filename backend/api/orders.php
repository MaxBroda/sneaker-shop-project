<?php
/**
 * Orders API Endpoint
 * Handles order creation and retrieval
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
    // GET - Retrieve user's orders
    if ($method === 'GET') {
        $user = Auth::require();

        $orders = $orderModel->getUserOrders($user['id']);
        ApiResponse::success($orders);
    }

    // POST - Create new order
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        // User is optional (guest checkout allowed)
        $user = Auth::user();

        // Check if items exists and is an array first
        if (!isset($data['items']) || empty($data['items']) || !is_array($data['items'])) {
            ApiResponse::error('Keine Artikel in der Bestellung', 400);
        }

        // Check if billing_address exists and is an array
        if (!isset($data['billing_address']) || !is_array($data['billing_address'])) {
            ApiResponse::error('Rechnungsadresse ist erforderlich', 400);
        }

        $validator = Validator::make($data)
            ->required('total', 'Gesamtbetrag ist erforderlich')
            ->numeric('total')
            ->positive('total');

        if ($validator->fails()) {
            ApiResponse::validationError($validator->getErrors());
        }

        $items = $data['items'];

        // Validate each item
        foreach ($items as $index => $item) {
            $itemValidator = Validator::make($item)
                ->required('product_id', "Artikel $index: Produkt-ID fehlt")
                ->required('quantity', "Artikel $index: Menge fehlt")
                ->required('size', "Artikel $index: Größe fehlt")
                ->required('price', "Artikel $index: Preis fehlt");

            if ($itemValidator->fails()) {
                ApiResponse::validationError($itemValidator->getErrors());
            }
        }

        $total = floatval($data['total']);
        $billingAddress = $data['billing_address'];
        $shippingAddress = $data['shipping_address'] ?? null;
        $paymentMethod = $data['payment_method'] ?? 'paypal';

        $result = $orderModel->create(
            $user ? $user['id'] : null,
            $items,
            $total,
            $billingAddress,
            $shippingAddress,
            $paymentMethod
        );

        ApiResponse::success([
            'order_id' => $result['order_id'],
            'order_number' => $result['order_number']
        ], 'Bestellung erfolgreich erstellt', 201);
    }

    ApiResponse::methodNotAllowed();

} catch (Exception $e) {
    ApiResponse::serverError('Ein Fehler ist aufgetreten: ' . $e->getMessage());
}

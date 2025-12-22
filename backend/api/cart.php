<?php
/**
 * Cart API Endpoint
 * Handles shopping cart operations
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validation.php';
require_once __DIR__ . '/../models/Cart.php';

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();

// Generate or get session ID for guest carts
if (!isset($_SESSION['cart_session_id'])) {
    $_SESSION['cart_session_id'] = bin2hex(random_bytes(16));
}
$sessionId = $_SESSION['cart_session_id'];

// Check for authenticated user
$userId = null;
$user = Auth::user();

if ($user) {
    $userId = $user['id'];

    // Merge guest cart into user cart if needed
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart_items WHERE session_id = ? AND user_id IS NULL");
    $stmt->execute([$sessionId]);
    $guestCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($guestCount > 0) {
        $cart = new Cart($pdo);
        $cart->mergeCarts($userId, $sessionId);
    }

    // User carts don't use session_id
    $sessionId = null;
}

$cart = new Cart($pdo);
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $items = $cart->getCartItems($userId, $sessionId);
            ApiResponse::success([
                'items' => $items,
                'count' => array_sum(array_column($items, 'quantity'))
            ]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true) ?? [];

            $validator = Validator::make($data)
                ->required('product_id', 'Produkt-ID ist erforderlich')
                ->required('size', 'Größe ist erforderlich')
                ->integer('product_id');

            if ($validator->fails()) {
                ApiResponse::validationError($validator->getErrors());
            }

            $productId = $validator->getInt('product_id');
            $size = $validator->getValue('size');
            $quantity = $validator->getInt('quantity') ?: 1;

            if ($quantity < 1) {
                $quantity = 1;
            }

            $result = $cart->addItem($userId, $sessionId, $productId, $quantity, $size);

            if ($result['success']) {
                $items = $cart->getCartItems($userId, $sessionId);
                ApiResponse::success([
                    'items' => $items,
                    'count' => array_sum(array_column($items, 'quantity'))
                ], $result['message']);
            } else {
                ApiResponse::error($result['message']);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true) ?? [];

            $validator = Validator::make($data)
                ->required('cart_item_id', 'Warenkorb-Item-ID ist erforderlich')
                ->required('quantity', 'Menge ist erforderlich')
                ->integer('cart_item_id')
                ->integer('quantity');

            if ($validator->fails()) {
                ApiResponse::validationError($validator->getErrors());
            }

            $cartItemId = $validator->getInt('cart_item_id');
            $quantity = $validator->getInt('quantity');

            $result = $cart->updateQuantity($cartItemId, $userId, $sessionId, $quantity);

            if ($result['success']) {
                $items = $cart->getCartItems($userId, $sessionId);
                ApiResponse::success([
                    'items' => $items,
                    'count' => array_sum(array_column($items, 'quantity'))
                ], $result['message']);
            } else {
                ApiResponse::error($result['message']);
            }
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true) ?? [];

            // If no cart_item_id, clear entire cart
            if (!isset($data['cart_item_id'])) {
                $result = $cart->clearCart($userId, $sessionId);

                if ($result['success']) {
                    ApiResponse::success([
                        'items' => [],
                        'count' => 0
                    ], 'Warenkorb geleert');
                } else {
                    ApiResponse::error($result['message']);
                }
                break;
            }

            $cartItemId = intval($data['cart_item_id']);
            $result = $cart->removeItem($cartItemId, $userId, $sessionId);

            if ($result['success']) {
                $items = $cart->getCartItems($userId, $sessionId);
                ApiResponse::success([
                    'items' => $items,
                    'count' => array_sum(array_column($items, 'quantity'))
                ], $result['message']);
            } else {
                ApiResponse::error($result['message']);
            }
            break;

        default:
            ApiResponse::methodNotAllowed();
    }
} catch (Exception $e) {
    ApiResponse::serverError('Ein Fehler ist aufgetreten');
}

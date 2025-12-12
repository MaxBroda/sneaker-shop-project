<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../models/Cart.php';

session_start();

if (!isset($_SESSION['cart_session_id'])) {
    $_SESSION['cart_session_id'] = bin2hex(random_bytes(16));
}
$sessionId = $_SESSION['cart_session_id'];

$userId = null;
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if (!$authHeader && function_exists('getallheaders')) {
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $authHeader = $headers['authorization'] ?? '';
}

if ($authHeader) {
    $token = str_replace('Bearer ', '', $authHeader);
    $user = getUserFromToken($token, $pdo);
    if ($user) {
        $userId = $user['id'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart_items WHERE session_id = ? AND user_id IS NULL");
        $stmt->execute([$sessionId]);
        $guestCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($guestCount > 0) {
            $cart = new Cart($pdo);
            $cart->mergeCarts($userId, $sessionId);
        }

        $sessionId = null;
    }
}

$cart = new Cart($pdo);
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $items = $cart->getCartItems($userId, $sessionId);
            echo json_encode([
                'success' => true,
                'items' => $items,
                'count' => array_sum(array_column($items, 'quantity'))
            ]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['product_id']) || !isset($data['size'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Produkt-ID und Größe erforderlich']);
                break;
            }

            $quantity = $data['quantity'] ?? 1;
            $result = $cart->addItem($userId, $sessionId, $data['product_id'], $quantity, $data['size']);

            if ($result['success']) {
                $items = $cart->getCartItems($userId, $sessionId);
                echo json_encode([
                    'success' => true,
                    'message' => $result['message'],
                    'items' => $items,
                    'count' => array_sum(array_column($items, 'quantity'))
                ]);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['cart_item_id']) || !isset($data['quantity'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Warenkorb-Item-ID und Menge erforderlich']);
                break;
            }

            $result = $cart->updateQuantity($data['cart_item_id'], $userId, $sessionId, $data['quantity']);

            if ($result['success']) {
                $items = $cart->getCartItems($userId, $sessionId);
                echo json_encode([
                    'success' => true,
                    'message' => $result['message'],
                    'items' => $items,
                    'count' => array_sum(array_column($items, 'quantity'))
                ]);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['cart_item_id'])) {
                $result = $cart->clearCart($userId, $sessionId);

                if ($result['success']) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Warenkorb geleert',
                        'items' => [],
                        'count' => 0
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode($result);
                }
                break;
            }

            $result = $cart->removeItem($data['cart_item_id'], $userId, $sessionId);

            if ($result['success']) {
                $items = $cart->getCartItems($userId, $sessionId);
                echo json_encode([
                    'success' => true,
                    'message' => $result['message'],
                    'items' => $items,
                    'count' => array_sum(array_column($items, 'quantity'))
                ]);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Serverfehler: ' . $e->getMessage()]);
}

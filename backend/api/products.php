<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/cors.php';
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product($pdo);

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // GET: Fetch products (all or by seller)
    if ($method === 'GET') {
        // Check if we need to filter by seller_id
        if (isset($_GET['seller_id'])) {
            $sellerId = intval($_GET['seller_id']);
            $products = $productModel->getBySellerId($sellerId);
        } else {
            $products = $productModel->getAll();
        }

        echo json_encode([
            'success' => true,
            'data' => $products
        ]);
        exit;
    }

    // POST: Create a new product (requires authentication and seller role)
    if ($method === 'POST') {
        require_once __DIR__ . '/../utils/auth.php';
        $user = authenticate();

        // Check if user is a seller
        if ($user['role'] !== 'seller') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Nur Verkäufer können Produkte hinzufügen'
            ]);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (!isset($data['name']) || !isset($data['price'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Name und Preis sind erforderlich'
            ]);
            exit;
        }

        $name = trim($data['name']);
        $description = isset($data['description']) ? trim($data['description']) : '';
        $price = floatval($data['price']);
        $image = isset($data['image']) ? trim($data['image']) : null;
        $category = isset($data['category']) ? trim($data['category']) : null;

        // Validate price
        if ($price <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Der Preis muss größer als 0 sein'
            ]);
            exit;
        }

        $product = $productModel->create(
            $name,
            $description,
            $price,
            $user['id'],
            $image,
            $category
        );

        echo json_encode([
            'success' => true,
            'message' => 'Produkt erfolgreich erstellt',
            'data' => $product
        ]);
        exit;
    }

    // DELETE: Delete a product (requires authentication and ownership)
    if ($method === 'DELETE') {
        require_once __DIR__ . '/../utils/auth.php';
        $user = authenticate();

        // Check if user is a seller
        if ($user['role'] !== 'seller') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Nur Verkäufer können Produkte löschen'
            ]);
            exit;
        }

        // Get product ID from query parameter
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Produkt-ID fehlt'
            ]);
            exit;
        }

        $productId = intval($_GET['id']);

        // Check if product exists and belongs to the seller
        $product = $productModel->getById($productId);

        if (!$product) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Produkt nicht gefunden'
            ]);
            exit;
        }

        if ($product['seller_id'] != $user['id']) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Sie können nur Ihre eigenen Produkte löschen'
            ]);
            exit;
        }

        // Delete the product
        $productModel->delete($productId);

        echo json_encode([
            'success' => true,
            'message' => 'Produkt erfolgreich gelöscht'
        ]);
        exit;
    }

    // Method not allowed
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Methode nicht erlaubt'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

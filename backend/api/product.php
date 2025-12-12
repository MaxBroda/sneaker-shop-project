<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../utils/db_connection.php';

require_once __DIR__ . '/../models/Product.php';

$productModel = new Product($pdo);

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
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

    if ($method === 'POST') {
        require_once __DIR__ . '/../utils/auth.php';
        $user = authenticate();

        if ($user['role'] !== 'seller') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Nur Verkäufer können Produkte hinzufügen'
            ]);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

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
        $technicalSpecs = isset($data['technical_specs']) ? trim($data['technical_specs']) : null;
        $tagIcon = isset($data['tag_icon']) ? trim($data['tag_icon']) : null;
        $tagText = isset($data['tag_text']) ? trim($data['tag_text']) : null;

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
            $category,
            $technicalSpecs,
            $tagIcon,
            $tagText
        );

        echo json_encode([
            'success' => true,
            'message' => 'Produkt erfolgreich erstellt',
            'data' => $product
        ]);
        exit;
    }

    if ($method === 'PUT') {
        require_once __DIR__ . '/../utils/auth.php';
        $user = authenticate();

        if ($user['role'] !== 'seller') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Nur Verkäufer können Produkte bearbeiten'
            ]);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id']) || !isset($data['name']) || !isset($data['price'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID, Name und Preis sind erforderlich'
            ]);
            exit;
        }

        $productId = intval($data['id']);

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
                'message' => 'Sie können nur Ihre eigenen Produkte bearbeiten'
            ]);
            exit;
        }

        $name = trim($data['name']);
        $description = isset($data['description']) ? trim($data['description']) : '';
        $price = floatval($data['price']);
        $image = isset($data['image']) ? trim($data['image']) : $product['image'];
        $category = isset($data['category']) ? trim($data['category']) : $product['category'];
        $technicalSpecs = isset($data['technical_specs']) ? trim($data['technical_specs']) : $product['technical_specs'];
        $tagIcon = isset($data['tag_icon']) ? trim($data['tag_icon']) : $product['tag_icon'];
        $tagText = isset($data['tag_text']) ? trim($data['tag_text']) : $product['tag_text'];

        if ($price <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Der Preis muss größer als 0 sein'
            ]);
            exit;
        }

        $updatedProduct = $productModel->update(
            $productId,
            $name,
            $description,
            $price,
            $image,
            $category,
            $technicalSpecs,
            $tagIcon,
            $tagText
        );

        echo json_encode([
            'success' => true,
            'message' => 'Produkt erfolgreich aktualisiert',
            'data' => $updatedProduct
        ]);
        exit;
    }

    if ($method === 'DELETE') {
        require_once __DIR__ . '/../utils/auth.php';
        $user = authenticate();

        if ($user['role'] !== 'seller') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Nur Verkäufer können Produkte löschen'
            ]);
            exit;
        }

        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Produkt-ID fehlt'
            ]);
            exit;
        }

        $productId = intval($_GET['id']);

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

        if (!empty($product['image']) && file_exists(__DIR__ . '/../uploads/' . $product['image'])) {
            unlink(__DIR__ . '/../uploads/' . $product['image']);
        }

        $productModel->delete($productId);

        echo json_encode([
            'success' => true,
            'message' => 'Produkt erfolgreich gelöscht'
        ]);
        exit;
    }

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

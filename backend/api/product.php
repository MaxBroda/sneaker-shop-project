<?php
/**
 * Product API Endpoint
 * Handles CRUD operations for products
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validation.php';
require_once __DIR__ . '/../models/Product.php';

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$productModel = new Product($pdo);
$method = $_SERVER['REQUEST_METHOD'];

try {
    // GET - Retrieve products
    if ($method === 'GET') {
        if (isset($_GET['seller_id'])) {
            $sellerId = intval($_GET['seller_id']);
            $products = $productModel->getBySellerId($sellerId);
        } elseif (isset($_GET['id'])) {
            $productId = intval($_GET['id']);
            $product = $productModel->getById($productId);
            if (!$product) {
                ApiResponse::notFound('Produkt nicht gefunden');
            }
            ApiResponse::success($product);
        } else {
            $products = $productModel->getAll();
        }

        ApiResponse::success($products);
    }

    // POST - Create product
    if ($method === 'POST') {
        $user = Auth::requireSeller();

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = Validator::make($data)
            ->required('name', 'Produktname ist erforderlich')
            ->required('price', 'Preis ist erforderlich')
            ->numeric('price')
            ->positive('price')
            ->maxLength('name', 255);

        if ($validator->fails()) {
            ApiResponse::validationError($validator->getErrors());
        }

        $name = $validator->getValue('name');
        $description = $validator->getValue('description', '');
        $price = $validator->getFloat('price');
        $image = $validator->getValue('image');
        $category = $validator->getValue('category');
        $technicalSpecs = $validator->getValue('technical_specs');
        $tagIcon = $validator->getValue('tag_icon');
        $tagText = $validator->getValue('tag_text');

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

        ApiResponse::success($product, 'Produkt erfolgreich erstellt', 201);
    }

    // PUT - Update product
    if ($method === 'PUT') {
        $user = Auth::requireSeller();

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = Validator::make($data)
            ->required('id', 'Produkt-ID ist erforderlich')
            ->required('name', 'Produktname ist erforderlich')
            ->required('price', 'Preis ist erforderlich')
            ->integer('id')
            ->numeric('price')
            ->positive('price');

        if ($validator->fails()) {
            ApiResponse::validationError($validator->getErrors());
        }

        $productId = $validator->getInt('id');
        $product = $productModel->getById($productId);

        if (!$product) {
            ApiResponse::notFound('Produkt nicht gefunden');
        }

        if ($product['seller_id'] != $user['id']) {
            ApiResponse::forbidden('Sie können nur Ihre eigenen Produkte bearbeiten');
        }

        $name = $validator->getValue('name');
        $description = $validator->getValue('description', '');
        $price = $validator->getFloat('price');
        $image = $validator->getValue('image') ?? $product['image'];
        $category = $validator->getValue('category') ?? $product['category'];
        $technicalSpecs = $validator->getValue('technical_specs') ?? $product['technical_specs'];
        $tagIcon = $validator->getValue('tag_icon') ?? $product['tag_icon'];
        $tagText = $validator->getValue('tag_text') ?? $product['tag_text'];

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

        ApiResponse::success($updatedProduct, 'Produkt erfolgreich aktualisiert');
    }

    // DELETE - Delete product
    if ($method === 'DELETE') {
        $user = Auth::requireSeller();

        if (!isset($_GET['id'])) {
            ApiResponse::error('Produkt-ID fehlt', 400);
        }

        $productId = intval($_GET['id']);
        $product = $productModel->getById($productId);

        if (!$product) {
            ApiResponse::notFound('Produkt nicht gefunden');
        }

        if ($product['seller_id'] != $user['id']) {
            ApiResponse::forbidden('Sie können nur Ihre eigenen Produkte löschen');
        }

        // Delete associated image file
        if (!empty($product['image'])) {
            $imagePath = __DIR__ . '/../uploads/' . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $productModel->delete($productId);

        ApiResponse::success(null, 'Produkt erfolgreich gelöscht');
    }

    ApiResponse::methodNotAllowed();

} catch (Exception $e) {
    ApiResponse::serverError('Ein Fehler ist aufgetreten');
}

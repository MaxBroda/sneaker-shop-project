<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../utils/cors.php';
require_once __DIR__ . '/../utils/auth.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        $user = authenticate();

        if ($user['role'] !== 'seller') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Nur Verkäufer können Bilder hochladen'
            ]);
            exit;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Kein Bild hochgeladen oder Fehler beim Upload'
            ]);
            exit;
        }

        $file = $_FILES['image'];
        
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Datei ist zu groß. Maximum: 5MB'
            ]);
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Ungültiger Dateityp. Nur JPG, PNG und WebP erlaubt'
            ]);
            exit;
        }

        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Ungültige Bilddatei'
            ]);
            exit;
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('product_') . '_' . time() . '.' . $extension;
        $uploadDir = __DIR__ . '/../uploads/';
        $uploadPath = $uploadDir . $filename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Fehler beim Speichern der Datei'
            ]);
            exit;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Bild erfolgreich hochgeladen',
            'data' => [
                'filename' => $filename,
                'url' => '/uploads/' . $filename
            ]
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
        'message' => 'Serverfehler: ' . $e->getMessage()
    ]);
}

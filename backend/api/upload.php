<?php
/**
 * Upload API Endpoint
 * Handles product image uploads
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/config.php';

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method !== 'POST') {
        ApiResponse::methodNotAllowed();
    }

    // Require seller authentication
    $user = Auth::requireSeller();

    // Check for uploaded file
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'Datei überschreitet die maximale Upload-Größe',
            UPLOAD_ERR_FORM_SIZE => 'Datei überschreitet die maximale Formulargröße',
            UPLOAD_ERR_PARTIAL => 'Datei wurde nur teilweise hochgeladen',
            UPLOAD_ERR_NO_FILE => 'Keine Datei hochgeladen',
            UPLOAD_ERR_NO_TMP_DIR => 'Temporärer Ordner fehlt',
            UPLOAD_ERR_CANT_WRITE => 'Datei konnte nicht geschrieben werden',
            UPLOAD_ERR_EXTENSION => 'Upload durch Erweiterung gestoppt'
        ];
        $errorCode = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
        $message = $errorMessages[$errorCode] ?? 'Unbekannter Upload-Fehler';
        ApiResponse::error($message, 400);
    }

    $file = $_FILES['image'];

    // Validate file size (5MB max)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        ApiResponse::error('Datei ist zu groß. Maximum: 5MB', 400);
    }

    // Validate MIME type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        ApiResponse::error('Ungültiger Dateityp. Nur JPG, PNG und WebP erlaubt', 400);
    }

    // Verify it's actually an image
    $imageInfo = getimagesize($file['tmp_name']);
    if (!$imageInfo) {
        ApiResponse::error('Ungültige Bilddatei', 400);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('product_') . '_' . time() . '.' . strtolower($extension);

    // Ensure upload directory exists
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadPath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        ApiResponse::serverError('Fehler beim Speichern der Datei');
    }

    ApiResponse::success([
        'filename' => $filename,
        'url' => '/uploads/' . $filename
    ], 'Bild erfolgreich hochgeladen');

} catch (Exception $e) {
    ApiResponse::serverError('Ein Fehler ist aufgetreten');
}

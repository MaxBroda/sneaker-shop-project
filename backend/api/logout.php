<?php
/**
 * Logout API Endpoint
 * Revokes the current authentication token
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';

try {
    $token = Auth::extractToken();

    if (!$token) {
        ApiResponse::unauthorized('Fehlender oder ungültiger Token');
    }

    $revoked = Auth::revokeToken($token);

    if ($revoked) {
        ApiResponse::success(null, 'Erfolgreich ausgeloggt');
    } else {
        ApiResponse::error('Ungültiger oder abgelaufener Token', 400);
    }

} catch (Exception $e) {
    ApiResponse::serverError('Ein Fehler ist aufgetreten');
}

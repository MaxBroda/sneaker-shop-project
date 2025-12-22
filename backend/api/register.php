<?php
/**
 * Registration API Endpoint
 * Creates new user accounts with address
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validation.php';

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];

    // Check if address exists and is an array
    if (!isset($data['address']) || !is_array($data['address'])) {
        ApiResponse::validationError(['address' => 'Adresse ist erforderlich']);
    }

    // Validate basic fields
    $validator = Validator::make($data)
        ->required('email', 'E-Mail ist erforderlich')
        ->required('password', 'Passwort ist erforderlich')
        ->required('firstName', 'Vorname ist erforderlich')
        ->required('lastName', 'Nachname ist erforderlich')
        ->required('role', 'Rolle ist erforderlich')
        ->email('email')
        ->minLength('password', 6, 'Passwort muss mindestens 6 Zeichen lang sein')
        ->inArray('role', ['customer', 'seller'], 'Ungültige Rolle');

    if ($validator->fails()) {
        ApiResponse::validationError($validator->getErrors());
    }

    // Validate address fields
    $address = $data['address'];
    $addressValidator = Validator::make($address)
        ->required('street', 'Straße ist erforderlich')
        ->required('house_number', 'Hausnummer ist erforderlich')
        ->required('city', 'Stadt ist erforderlich')
        ->required('postal_code', 'PLZ ist erforderlich')
        ->required('country', 'Land ist erforderlich');

    if ($addressValidator->fails()) {
        ApiResponse::validationError($addressValidator->getErrors(), 'Adressfelder unvollständig');
    }

    $email = trim($data['email']);
    $firstName = trim($data['firstName']);
    $lastName = trim($data['lastName']);
    $password = $data['password'];
    $role = strtolower(trim($data['role']));

    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        ApiResponse::error('E-Mail wird bereits verwendet', 409);
    }

    // Create user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertStmt = $pdo->prepare("
        INSERT INTO users (email, first_name, last_name, password_hash, role)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insertStmt->execute([$email, $firstName, $lastName, $hashedPassword, $role]);
    $userId = $pdo->lastInsertId();

    // Create address
    $insertAddr = $pdo->prepare("
        INSERT INTO addresses (user_id, street, house_number, city, postal_code, country, is_default)
        VALUES (?, ?, ?, ?, ?, ?, 1)
    ");
    $insertAddr->execute([
        $userId,
        trim($address['street']),
        trim($address['house_number']),
        trim($address['city']),
        trim($address['postal_code']),
        trim($address['country'])
    ]);

    // Fetch created address
    $addrStmt = $pdo->prepare("
        SELECT street, house_number, city, postal_code, country 
        FROM addresses 
        WHERE user_id = ?
    ");
    $addrStmt->execute([$userId]);
    $savedAddress = $addrStmt->fetch(PDO::FETCH_ASSOC);

    // Create token for auto-login
    $token = Auth::createToken($userId);

    ApiResponse::success([
        'user' => [
            'id' => $userId,
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'role' => $role,
            'address' => $savedAddress
        ],
        'token' => $token
    ], 'Registrierung erfolgreich', 201);

} catch (Exception $e) {
    ApiResponse::serverError('Ein Fehler ist aufgetreten');
}

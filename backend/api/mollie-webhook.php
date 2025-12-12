<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../utils/db_connection.php';

$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey("test_bPwBfWnzJSnBpPbkz87qeywhSuJyS6");

try {
    $paymentId = $_POST['id'] ?? null;
    
    if (!$paymentId) {
        http_response_code(400);
        exit;
    }
    
    $payment = $mollie->payments->get($paymentId);
    
    $file = __DIR__ . '/../storage/payments.json';
    $payments = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    if (isset($payments[$paymentId])) {
        $payments[$paymentId]['status'] = $payment->status;
        $payments[$paymentId]['updated_at'] = date('Y-m-d H:i:s');
        file_put_contents($file, json_encode($payments, JSON_PRETTY_PRINT));
        
        // Update order payment status in database
        $orderNumber = $payments[$paymentId]['order_number'] ?? null;
        if ($orderNumber) {
            $stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE order_number = ?");
            $stmt->execute([$payment->status, $orderNumber]);
        }
    }
    
    http_response_code(200);
    echo "OK";
    
} catch (Exception $e) {
    http_response_code(500);

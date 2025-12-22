<?php
/**
 * Mollie Webhook Endpoint
 * Handles payment status updates from Mollie
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/db_connection.php';

try {
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setApiKey(Config::getMollieApiKey());

    $paymentId = $_POST['id'] ?? null;

    if (!$paymentId) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing payment ID']);
        exit;
    }

    $payment = $mollie->payments->get($paymentId);

    // Load stored payments
    $storageFile = __DIR__ . '/../storage/payments.json';
    $payments = file_exists($storageFile)
        ? json_decode(file_get_contents($storageFile), true) ?? []
        : [];

    if (isset($payments[$paymentId])) {
        // Update stored payment status
        $payments[$paymentId]['status'] = $payment->status;
        $payments[$paymentId]['updated_at'] = date('Y-m-d H:i:s');
        file_put_contents($storageFile, json_encode($payments, JSON_PRETTY_PRINT));

        // Update order payment status in database
        $orderNumber = $payments[$paymentId]['order_number'] ?? null;
        if ($orderNumber) {
            $stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE order_number = ?");
            $stmt->execute([$payment->status, $orderNumber]);
        }
    }

    http_response_code(200);
    echo 'OK';

} catch (\Mollie\Api\Exceptions\ApiException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Mollie API error']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}

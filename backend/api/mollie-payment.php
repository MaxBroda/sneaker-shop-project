<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';

$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey("test_bPwBfWnzJSnBpPbkz87qeywhSuJyS6");
$file = __DIR__ . '/../storage/payments.json';

function getPayments()
{
    global $file;
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function savePayments($payments)
{
    global $file;
    file_put_contents($file, json_encode($payments, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['amount'], $data['description'], $data['order_number'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing fields']);
            exit;
        }

        $paymentData = [
            "amount" => ["currency" => "EUR", "value" => number_format((float)$data['amount'], 2, '.', '')],
            "description" => $data['description'],
            "redirectUrl" => "http://localhost:3000/checkout?payment=return&order=" . urlencode($data['order_number']),
            "locale" => "de_DE",
            "metadata" => ["order_number" => $data['order_number']]
        ];

        // Only add webhook for production (not localhost)
        // For localhost testing, we'll update status when user returns
        // Uncomment the line below if you have a public webhook URL
        // $paymentData["webhookUrl"] = "https://your-domain.com/api/mollie-webhook.php";

        $payment = $mollie->payments->create($paymentData);

        $payments = getPayments();
        $payments[$payment->id] = [
            'payment_id' => $payment->id,
            'order_number' => $data['order_number'],
            'amount' => (float)$data['amount'],
            'status' => $payment->status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        savePayments($payments);

        echo json_encode(['success' => true, 'data' => ['payment_id' => $payment->id, 'checkout_url' => $payment->getCheckoutUrl()]]);
    } catch (\Mollie\Api\Exceptions\ApiException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['order_number'])) {
    try {
        require_once __DIR__ . '/../utils/db_connection.php';

        $payments = getPayments();
        $paymentData = current(array_filter($payments, fn($p) => $p['order_number'] === $_GET['order_number']));

        if (!$paymentData) {
            echo json_encode(['success' => false, 'message' => 'Payment not found']);
            exit;
        }

        $molliePayment = $mollie->payments->get($paymentData['payment_id']);
        $payments[$paymentData['payment_id']]['status'] = $molliePayment->status;
        savePayments($payments);

        // Update payment status in database
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE order_number = ?");
        $stmt->execute([$molliePayment->status, $_GET['order_number']]);

        echo json_encode(['success' => true, 'data' => ['status' => $molliePayment->status, 'is_paid' => $molliePayment->isPaid()]]);
    } catch (\Mollie\Api\Exceptions\ApiException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);

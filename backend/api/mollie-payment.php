<?php
/**
 * Mollie Payment API Endpoint
 * Creates and checks payment status
 */

header('Content-Type: application/json');

// Handle preflight OPTIONS request BEFORE any other logic
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validation.php';

try {
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setApiKey(Config::getMollieApiKey());
} catch (Exception $e) {
    ApiResponse::serverError('Zahlungssystem nicht konfiguriert');
}

$storageFile = __DIR__ . '/../storage/payments.json';

function getPayments(): array
{
    global $storageFile;
    if (!file_exists($storageFile)) {
        return [];
    }
    $content = file_get_contents($storageFile);
    return json_decode($content, true) ?: [];
}

function savePayments(array $payments): void
{
    global $storageFile;
    $dir = dirname($storageFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($storageFile, json_encode($payments, JSON_PRETTY_PRINT));
}

$method = $_SERVER['REQUEST_METHOD'];

// Create new payment
if ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = Validator::make($data)
            ->required('amount', 'Betrag ist erforderlich')
            ->required('description', 'Beschreibung ist erforderlich')
            ->required('order_number', 'Bestellnummer ist erforderlich')
            ->numeric('amount')
            ->positive('amount');

        if ($validator->fails()) {
            ApiResponse::validationError($validator->getErrors());
        }

        $amount = $validator->getFloat('amount');
        $description = $validator->getValue('description');
        $orderNumber = $validator->getValue('order_number');

        $frontendUrl = Config::getFrontendUrl();
        $redirectUrl = $frontendUrl . '/checkout?payment=return&order=' . urlencode($orderNumber);

        $paymentData = [
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format($amount, 2, '.', '')
            ],
            'description' => $description,
            'redirectUrl' => $redirectUrl,
            'method' => 'banktransfer',
            'locale' => 'de_DE',
            'metadata' => ['order_number' => $orderNumber]
        ];

        // Add webhook URL in production (when not localhost)
        $appUrl = Config::getAppUrl();
        if (strpos($appUrl, 'localhost') === false) {
            $paymentData['webhookUrl'] = $appUrl . '/api/mollie-webhook.php';
        }

        $payment = $mollie->payments->create($paymentData);

        // Store payment reference
        $payments = getPayments();
        $payments[$payment->id] = [
            'payment_id' => $payment->id,
            'order_number' => $orderNumber,
            'amount' => $amount,
            'status' => $payment->status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        savePayments($payments);

        ApiResponse::success([
            'payment_id' => $payment->id,
            'checkout_url' => $payment->getCheckoutUrl()
        ]);

    } catch (\Mollie\Api\Exceptions\ApiException $e) {
        error_log('Mollie API error: ' . $e->getMessage());
        ApiResponse::serverError('Zahlung konnte nicht erstellt werden: ' . $e->getMessage());
    } catch (Exception $e) {
        error_log('Payment creation error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        ApiResponse::serverError('Ein Fehler ist aufgetreten: ' . $e->getMessage());
    }
    exit;
}

// Check payment status
if ($method === 'GET' && isset($_GET['order_number'])) {
    try {
        require_once __DIR__ . '/../utils/db_connection.php';

        $orderNumber = $_GET['order_number'];
        $payments = getPayments();

        // Find payment by order number
        $paymentData = null;
        foreach ($payments as $p) {
            if ($p['order_number'] === $orderNumber) {
                $paymentData = $p;
                break;
            }
        }

        if (!$paymentData) {
            ApiResponse::notFound('Zahlung nicht gefunden');
        }

        // Get current status from Mollie
        $molliePayment = $mollie->payments->get($paymentData['payment_id']);

        // Update stored status
        $payments[$paymentData['payment_id']]['status'] = $molliePayment->status;
        $payments[$paymentData['payment_id']]['updated_at'] = date('Y-m-d H:i:s');
        savePayments($payments);

        // Update order payment status in database
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE order_number = ?");
        $stmt->execute([$molliePayment->status, $orderNumber]);

        ApiResponse::success([
            'status' => $molliePayment->status,
            'is_paid' => $molliePayment->isPaid()
        ]);

    } catch (\Mollie\Api\Exceptions\ApiException $e) {
        ApiResponse::serverError('Fehler beim Abrufen des Zahlungsstatus');
    } catch (Exception $e) {
        ApiResponse::serverError('Ein Fehler ist aufgetreten');
    }
    exit;
}

ApiResponse::methodNotAllowed();

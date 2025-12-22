<?php
/**
 * Order Model
 * Handles all order-related database operations
 */

require_once __DIR__ . '/../utils/config.php';

class Order
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all orders for a user with items in a single efficient query
     */
    public function getUserOrders(int $userId): array
    {
        // Get orders with items in one query using JSON aggregation
        $stmt = $this->pdo->prepare("
            SELECT 
                o.id,
                o.order_number,
                o.total,
                o.status,
                o.payment_status,
                o.billing_address,
                o.shipping_address,
                o.payment_method,
                o.created_at
            FROM orders o
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orders)) {
            return [];
        }

        // Get all order IDs
        $orderIds = array_column($orders, 'id');
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

        // Fetch all items for these orders in one query
        $itemsStmt = $this->pdo->prepare("
            SELECT 
                oi.order_id,
                oi.id,
                oi.product_id,
                oi.quantity,
                oi.size,
                oi.price,
                p.name as product_name,
                p.image as product_image
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id IN ($placeholders)
        ");
        $itemsStmt->execute($orderIds);
        $allItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Group items by order_id
        $itemsByOrder = [];
        foreach ($allItems as $item) {
            $orderId = $item['order_id'];
            unset($item['order_id']);
            $item['product_image'] = $this->formatImageUrl($item['product_image']);
            $itemsByOrder[$orderId][] = $item;
        }

        // Attach items to orders and decode addresses
        foreach ($orders as &$order) {
            $order['items'] = $itemsByOrder[$order['id']] ?? [];
            $order['billing_address'] = json_decode($order['billing_address'], true);
            $order['shipping_address'] = $order['shipping_address'] 
                ? json_decode($order['shipping_address'], true) 
                : null;
        }

        return $orders;
    }

    /**
     * Get orders containing a seller's products with items
     */
    public function getSellerOrders(int $sellerId): array
    {
        // Get distinct orders that contain this seller's products
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT
                o.id,
                o.order_number,
                o.total,
                o.status,
                o.payment_status,
                o.billing_address,
                o.shipping_address,
                o.payment_method,
                o.created_at,
                o.user_id,
                u.email as customer_email,
                u.first_name as customer_first_name,
                u.last_name as customer_last_name
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            LEFT JOIN users u ON o.user_id = u.id
            WHERE p.seller_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$sellerId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orders)) {
            return [];
        }

        // Get all order IDs
        $orderIds = array_column($orders, 'id');
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

        // Fetch only items from this seller for these orders
        $itemsStmt = $this->pdo->prepare("
            SELECT 
                oi.order_id,
                oi.id,
                oi.product_id,
                oi.quantity,
                oi.size,
                oi.price,
                p.name as product_name,
                p.image as product_image
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id IN ($placeholders) AND p.seller_id = ?
        ");
        $params = array_merge($orderIds, [$sellerId]);
        $itemsStmt->execute($params);
        $allItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Group items by order_id
        $itemsByOrder = [];
        foreach ($allItems as $item) {
            $orderId = $item['order_id'];
            unset($item['order_id']);
            $item['product_image'] = $this->formatImageUrl($item['product_image']);
            $itemsByOrder[$orderId][] = $item;
        }

        // Attach items and customer info to orders
        foreach ($orders as &$order) {
            $order['items'] = $itemsByOrder[$order['id']] ?? [];
            $order['billing_address'] = json_decode($order['billing_address'], true);
            $order['shipping_address'] = $order['shipping_address'] 
                ? json_decode($order['shipping_address'], true) 
                : null;

            // Format customer data
            if ($order['user_id']) {
                $order['customer'] = [
                    'email' => $order['customer_email'],
                    'first_name' => $order['customer_first_name'],
                    'last_name' => $order['customer_last_name']
                ];
            } else {
                $order['customer'] = null;
            }

            // Clean up temporary fields
            unset($order['customer_email'], $order['customer_first_name'], $order['customer_last_name']);
        }

        return $orders;
    }

    /**
     * Create a new order with items
     */
    public function create(
        ?int $userId,
        array $items,
        float $total,
        array $billingAddress,
        ?array $shippingAddress,
        string $paymentMethod
    ): array {
        try {
            $this->pdo->beginTransaction();

            $orderNumber = 'ORD-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
            $billingAddressJson = json_encode($billingAddress);
            $shippingAddressJson = $shippingAddress ? json_encode($shippingAddress) : null;

            $stmt = $this->pdo->prepare("
                INSERT INTO orders (
                    user_id,
                    order_number,
                    total,
                    status,
                    payment_status,
                    billing_address,
                    shipping_address,
                    payment_method,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");

            $stmt->execute([
                $userId,
                $orderNumber,
                $total,
                'pending',
                'open',
                $billingAddressJson,
                $shippingAddressJson,
                $paymentMethod
            ]);

            $orderId = $this->pdo->lastInsertId();

            // Insert order items
            $itemStmt = $this->pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, size, price)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['size'],
                    $item['price']
                ]);
            }

            // Clear user's cart if logged in
            if ($userId) {
                $clearCartStmt = $this->pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
                $clearCartStmt->execute([$userId]);
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'order_id' => $orderId,
                'order_number' => $orderNumber
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception('Fehler beim Erstellen der Bestellung: ' . $e->getMessage());
        }
    }

    /**
     * Update order status (seller operation)
     */
    public function updateStatus(int $orderId, string $status, int $sellerId): array
    {
        // Verify seller has products in this order
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ? AND p.seller_id = ?
        ");
        $stmt->execute([$orderId, $sellerId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] == 0) {
            throw new Exception('Keine Berechtigung für diese Bestellung');
        }

        $updateStmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $updateStmt->execute([$status, $orderId]);

        return ['success' => true];
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(string $orderNumber, string $paymentStatus): array
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET payment_status = ? WHERE order_number = ?");
        $stmt->execute([$paymentStatus, $orderNumber]);

        return ['success' => true];
    }

    /**
     * Format image URL with configurable base URL
     */
    private function formatImageUrl(?string $imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }

        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }

        $baseUrl = Config::getAppUrl();

        if (strpos($imagePath, 'uploads/') !== 0) {
            $imagePath = 'uploads/' . $imagePath;
        }

        return $baseUrl . '/' . $imagePath;
    }
}

<?php

class Order
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserOrders($userId)
    {
        try {
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

            foreach ($orders as &$order) {
                $order['items'] = $this->getOrderItems($order['id']);

                $order['billing_address'] = json_decode($order['billing_address'], true);
                $order['shipping_address'] = $order['shipping_address'] ? json_decode($order['shipping_address'], true) : null;
            }

            return $orders;
        } catch (Exception $e) {
            throw new Exception('Fehler beim Abrufen der Bestellungen: ' . $e->getMessage());
        }
    }

    public function getOrderItems($orderId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    oi.id,
                    oi.product_id,
                    oi.quantity,
                    oi.size,
                    oi.price,
                    p.name as product_name,
                    p.image as product_image
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as &$item) {
                if ($item['product_image']) {
                    $imagePath = $item['product_image'];
                    if (strpos($imagePath, 'uploads/') !== 0) {
                        $imagePath = 'uploads/' . $imagePath;
                    }
                    $item['product_image'] = 'http://localhost:8080/' . $imagePath;
                }
            }

            return $items;
        } catch (Exception $e) {
            return [];
        }
    }

    public function create($userId, $items, $total, $billingAddress, $shippingAddress, $paymentMethod)
    {
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

    public function getSellerOrders($sellerId)
    {
        try {
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
                    o.user_id
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE p.seller_id = ?
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$sellerId]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($orders as &$order) {
                $order['items'] = $this->getSellerOrderItems($order['id'], $sellerId);

                $order['billing_address'] = json_decode($order['billing_address'], true);
                $order['shipping_address'] = $order['shipping_address'] ? json_decode($order['shipping_address'], true) : null;

                if ($order['user_id']) {
                    $userStmt = $this->pdo->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
                    $userStmt->execute([$order['user_id']]);
                    $order['customer'] = $userStmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $order['customer'] = null;
                }
            }

            return $orders;
        } catch (Exception $e) {
            throw new Exception('Fehler beim Abrufen der Bestellungen: ' . $e->getMessage());
        }
    }

    public function getSellerOrderItems($orderId, $sellerId)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    oi.id,
                    oi.product_id,
                    oi.quantity,
                    oi.size,
                    oi.price,
                    p.name as product_name,
                    p.image as product_image
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ? AND p.seller_id = ?
            ");
            $stmt->execute([$orderId, $sellerId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as &$item) {
                if ($item['product_image']) {
                    $imagePath = $item['product_image'];
                    if (strpos($imagePath, 'uploads/') !== 0) {
                        $imagePath = 'uploads/' . $imagePath;
                    }
                    $item['product_image'] = 'http://localhost:8080/' . $imagePath;
                }
            }

            return $items;
        } catch (Exception $e) {
            return [];
        }
    }

    public function updateStatus($orderId, $status, $sellerId)
    {
        try {
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

            $updateStmt = $this->pdo->prepare("
                UPDATE orders 
                SET status = ? 
                WHERE id = ?
            ");
            $updateStmt->execute([$status, $orderId]);

            return ['success' => true];
        } catch (Exception $e) {
            throw new Exception('Fehler beim Aktualisieren des Status: ' . $e->getMessage());
        }
    }

    public function updatePaymentStatus($orderNumber, $paymentStatus)
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE orders 
                SET payment_status = ? 
                WHERE order_number = ?
            ");
            $stmt->execute([$paymentStatus, $orderNumber]);

            return ['success' => true];
        } catch (Exception $e) {
            throw new Exception('Fehler beim Aktualisieren des Zahlungsstatus: ' . $e->getMessage());
        }
    }
}

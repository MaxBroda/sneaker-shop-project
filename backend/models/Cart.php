<?php

class Cart {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addItem($userId, $sessionId, $productId, $quantity, $size) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, quantity FROM cart_items 
                WHERE product_id = ? AND size = ? AND (
                    (user_id = ? AND user_id IS NOT NULL) OR 
                    (session_id = ? AND session_id IS NOT NULL AND user_id IS NULL)
                )
            ");
            $stmt->execute([$productId, $size, $userId, $sessionId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $newQuantity = $existing['quantity'] + $quantity;
                $updateStmt = $this->pdo->prepare("
                    UPDATE cart_items 
                    SET quantity = ? 
                    WHERE id = ?
                ");
                $updateStmt->execute([$newQuantity, $existing['id']]);
                return ['success' => true, 'message' => 'Menge aktualisiert'];
            } else {
                $insertStmt = $this->pdo->prepare("
                    INSERT INTO cart_items (user_id, session_id, product_id, quantity, size) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $insertStmt->execute([$userId, $sessionId, $productId, $quantity, $size]);
                return ['success' => true, 'message' => 'Zum Warenkorb hinzugefügt'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Hinzufügen: ' . $e->getMessage()];
        }
    }

    public function getCartItems($userId, $sessionId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    ci.id,
                    ci.product_id,
                    ci.quantity,
                    ci.size,
                    p.name,
                    p.price,
                    p.image,
                    p.category
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE (
                    (ci.user_id = ? AND ci.user_id IS NOT NULL) OR 
                    (ci.session_id = ? AND ci.session_id IS NOT NULL AND ci.user_id IS NULL)
                )
                ORDER BY ci.created_at DESC
            ");
            $stmt->execute([$userId, $sessionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function updateQuantity($cartItemId, $userId, $sessionId, $quantity) {
        try {
            if ($quantity <= 0) {
                return $this->removeItem($cartItemId, $userId, $sessionId);
            }

            $stmt = $this->pdo->prepare("
                UPDATE cart_items 
                SET quantity = ? 
                WHERE id = ? AND (
                    (user_id = ? AND user_id IS NOT NULL) OR 
                    (session_id = ? AND session_id IS NOT NULL AND user_id IS NULL)
                )
            ");
            $stmt->execute([$quantity, $cartItemId, $userId, $sessionId]);
            return ['success' => true, 'message' => 'Menge aktualisiert'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Aktualisieren: ' . $e->getMessage()];
        }
    }

    public function removeItem($cartItemId, $userId, $sessionId) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM cart_items 
                WHERE id = ? AND (
                    (user_id = ? AND user_id IS NOT NULL) OR 
                    (session_id = ? AND session_id IS NOT NULL AND user_id IS NULL)
                )
            ");
            $stmt->execute([$cartItemId, $userId, $sessionId]);
            return ['success' => true, 'message' => 'Artikel entfernt'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Entfernen: ' . $e->getMessage()];
        }
    }

    public function mergeCarts($userId, $sessionId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT product_id, quantity, size 
                FROM cart_items 
                WHERE session_id = ? AND user_id IS NULL
            ");
            $stmt->execute([$sessionId]);
            $guestItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($guestItems as $item) {
                $checkStmt = $this->pdo->prepare("
                    SELECT id, quantity FROM cart_items 
                    WHERE user_id = ? AND product_id = ? AND size = ?
                ");
                $checkStmt->execute([$userId, $item['product_id'], $item['size']]);
                $userItem = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($userItem) {
                    $newQuantity = $userItem['quantity'] + $item['quantity'];
                    $updateStmt = $this->pdo->prepare("
                        UPDATE cart_items 
                        SET quantity = ? 
                        WHERE id = ?
                    ");
                    $updateStmt->execute([$newQuantity, $userItem['id']]);
                } else {
                    $insertStmt = $this->pdo->prepare("
                        INSERT INTO cart_items (user_id, product_id, quantity, size) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $insertStmt->execute([$userId, $item['product_id'], $item['quantity'], $item['size']]);
                }
            }

            $deleteStmt = $this->pdo->prepare("
                DELETE FROM cart_items 
                WHERE session_id = ? AND user_id IS NULL
            ");
            $deleteStmt->execute([$sessionId]);

            return ['success' => true, 'message' => 'Warenkorb zusammengeführt'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Zusammenführen: ' . $e->getMessage()];
        }
    }

    public function clearCart($userId, $sessionId) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM cart_items 
                WHERE (user_id = ? AND user_id IS NOT NULL) OR 
                      (session_id = ? AND session_id IS NOT NULL AND user_id IS NULL)
            ");
            $stmt->execute([$userId, $sessionId]);
            return ['success' => true, 'message' => 'Warenkorb geleert'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Leeren: ' . $e->getMessage()];
        }
    }

    public function getCartCount($userId, $sessionId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT SUM(quantity) as count 
                FROM cart_items 
                WHERE (user_id = ? AND user_id IS NOT NULL) OR 
                      (session_id = ? AND session_id IS NOT NULL AND user_id IS NULL)
            ");
            $stmt->execute([$userId, $sessionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }
}

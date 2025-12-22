<?php
/**
 * Cart Model
 * Handles all shopping cart operations
 */

class Cart
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Add item to cart
     */
    public function addItem(?int $userId, ?string $sessionId, int $productId, int $quantity, string $size): array
    {
        try {
            // Check for existing item with same product and size
            $stmt = $this->pdo->prepare("
                SELECT id, quantity FROM cart_items 
                WHERE product_id = ? AND size = ? AND (
                    (user_id = ?) OR 
                    (session_id = ? AND user_id IS NULL)
                )
            ");
            $stmt->execute([$productId, $size, $userId, $sessionId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update existing item quantity
                $newQuantity = $existing['quantity'] + $quantity;
                $updateStmt = $this->pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
                $updateStmt->execute([$newQuantity, $existing['id']]);
                return ['success' => true, 'message' => 'Menge aktualisiert'];
            }

            // Insert new item
            $insertStmt = $this->pdo->prepare("
                INSERT INTO cart_items (user_id, session_id, product_id, quantity, size) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $insertStmt->execute([$userId, $sessionId, $productId, $quantity, $size]);
            return ['success' => true, 'message' => 'Zum Warenkorb hinzugefügt'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Hinzufügen: ' . $e->getMessage()];
        }
    }

    /**
     * Get all items in cart with product details
     */
    public function getCartItems(?int $userId, ?string $sessionId): array
    {
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
                WHERE (ci.user_id = ?) OR (ci.session_id = ? AND ci.user_id IS NULL)
                ORDER BY ci.created_at DESC
            ");
            $stmt->execute([$userId, $sessionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(int $cartItemId, ?int $userId, ?string $sessionId, int $quantity): array
    {
        try {
            if ($quantity <= 0) {
                return $this->removeItem($cartItemId, $userId, $sessionId);
            }

            $stmt = $this->pdo->prepare("
                UPDATE cart_items 
                SET quantity = ? 
                WHERE id = ? AND (
                    (user_id = ?) OR 
                    (session_id = ? AND user_id IS NULL)
                )
            ");
            $stmt->execute([$quantity, $cartItemId, $userId, $sessionId]);
            return ['success' => true, 'message' => 'Menge aktualisiert'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Aktualisieren: ' . $e->getMessage()];
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartItemId, ?int $userId, ?string $sessionId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM cart_items 
                WHERE id = ? AND (
                    (user_id = ?) OR 
                    (session_id = ? AND user_id IS NULL)
                )
            ");
            $stmt->execute([$cartItemId, $userId, $sessionId]);
            return ['success' => true, 'message' => 'Artikel entfernt'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Entfernen: ' . $e->getMessage()];
        }
    }

    /**
     * Merge guest cart into user cart after login
     */
    public function mergeCarts(int $userId, string $sessionId): array
    {
        try {
            // Get all guest cart items
            $stmt = $this->pdo->prepare("
                SELECT product_id, quantity, size 
                FROM cart_items 
                WHERE session_id = ? AND user_id IS NULL
            ");
            $stmt->execute([$sessionId]);
            $guestItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($guestItems as $item) {
                // Check if user already has this item
                $checkStmt = $this->pdo->prepare("
                    SELECT id, quantity FROM cart_items 
                    WHERE user_id = ? AND product_id = ? AND size = ?
                ");
                $checkStmt->execute([$userId, $item['product_id'], $item['size']]);
                $userItem = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($userItem) {
                    // Add quantities
                    $newQuantity = $userItem['quantity'] + $item['quantity'];
                    $updateStmt = $this->pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
                    $updateStmt->execute([$newQuantity, $userItem['id']]);
                } else {
                    // Move guest item to user
                    $insertStmt = $this->pdo->prepare("
                        INSERT INTO cart_items (user_id, product_id, quantity, size) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $insertStmt->execute([$userId, $item['product_id'], $item['quantity'], $item['size']]);
                }
            }

            // Delete guest cart items
            $deleteStmt = $this->pdo->prepare("DELETE FROM cart_items WHERE session_id = ? AND user_id IS NULL");
            $deleteStmt->execute([$sessionId]);

            return ['success' => true, 'message' => 'Warenkorb zusammengeführt'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Zusammenführen: ' . $e->getMessage()];
        }
    }

    /**
     * Clear entire cart
     */
    public function clearCart(?int $userId, ?string $sessionId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM cart_items 
                WHERE (user_id = ?) OR (session_id = ? AND user_id IS NULL)
            ");
            $stmt->execute([$userId, $sessionId]);
            return ['success' => true, 'message' => 'Warenkorb geleert'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fehler beim Leeren: ' . $e->getMessage()];
        }
    }

    /**
     * Get total item count in cart
     */
    public function getCartCount(?int $userId, ?string $sessionId): int
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT SUM(quantity) as count 
                FROM cart_items 
                WHERE (user_id = ?) OR (session_id = ? AND user_id IS NULL)
            ");
            $stmt->execute([$userId, $sessionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }
}

<?php

class Product
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a new product
     */
    public function create($name, $description, $price, $sellerId, $image = null, $category = null)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, description, price, seller_id, image, category)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([$name, $description, $price, $sellerId, $image, $category]);

        return [
            'id' => $this->pdo->lastInsertId(),
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'seller_id' => $sellerId,
            'image' => $image,
            'category' => $category
        ];
    }

    /**
     * Get all products with seller information
     */
    public function getAll()
    {
        $stmt = $this->pdo->query("
            SELECT 
                p.*,
                u.first_name as seller_first_name,
                u.last_name as seller_last_name,
                u.email as seller_email
            FROM products p
            LEFT JOIN users u ON p.seller_id = u.id
            ORDER BY p.id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get products by seller ID
     */
    public function getBySellerId($sellerId)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                u.first_name as seller_first_name,
                u.last_name as seller_last_name,
                u.email as seller_email
            FROM products p
            LEFT JOIN users u ON p.seller_id = u.id
            WHERE p.seller_id = ?
            ORDER BY p.id DESC
        ");

        $stmt->execute([$sellerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single product by ID
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                u.first_name as seller_first_name,
                u.last_name as seller_last_name,
                u.email as seller_email
            FROM products p
            LEFT JOIN users u ON p.seller_id = u.id
            WHERE p.id = ?
        ");

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update a product
     */
    public function update($id, $name, $description, $price, $image = null, $category = null)
    {
        $stmt = $this->pdo->prepare("
            UPDATE products 
            SET name = ?, description = ?, price = ?, image = ?, category = ?
            WHERE id = ?
        ");

        return $stmt->execute([$name, $description, $price, $image, $category, $id]);
    }

    /**
     * Delete a product
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

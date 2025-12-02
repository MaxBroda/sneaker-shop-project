<?php

class Product
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($name, $description, $price, $sellerId, $image = null, $category = null, $technicalSpecs = null, $tagIcon = null, $tagText = null)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, description, price, seller_id, image, category, technical_specs, tag_icon, tag_text)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([$name, $description, $price, $sellerId, $image, $category, $technicalSpecs, $tagIcon, $tagText]);

        return [
            'id' => $this->pdo->lastInsertId(),
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'seller_id' => $sellerId,
            'image' => $image,
            'category' => $category,
            'technical_specs' => $technicalSpecs,
            'tag_icon' => $tagIcon,
            'tag_text' => $tagText
        ];
    }

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

    public function update($id, $name, $description, $price, $image = null, $category = null, $technicalSpecs = null, $tagIcon = null, $tagText = null)
    {
        $stmt = $this->pdo->prepare("
            UPDATE products 
            SET name = ?, description = ?, price = ?, image = ?, category = ?, technical_specs = ?, tag_icon = ?, tag_text = ?
            WHERE id = ?
        ");

        $stmt->execute([$name, $description, $price, $image, $category, $technicalSpecs, $tagIcon, $tagText, $id]);

        return $this->getById($id);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

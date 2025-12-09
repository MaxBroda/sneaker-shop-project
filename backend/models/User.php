<?php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getById($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, first_name, last_name, role, created_at 
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getByIdWithAddresses($userId) {
        try {
            $userData = $this->getById($userId);
            
            if (!$userData) {
                return null;
            }

            $addrStmt = $this->pdo->prepare("
                SELECT id, street, house_number, city, postal_code, country 
                FROM addresses 
                WHERE user_id = ? 
                ORDER BY id ASC
            ");
            $addrStmt->execute([$userId]);
            $addresses = $addrStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'user' => $userData,
                'addresses' => $addresses
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAll() {
        try {
            $stmt = $this->pdo->query("
                SELECT id, email, first_name, last_name, role, created_at 
                FROM users 
                ORDER BY id ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}

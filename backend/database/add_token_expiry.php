<?php
/**
 * Migration script to add expires_at column and indexes to user_tokens table
 */

$dbFile = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if expires_at column exists
    $columns = $pdo->query("PRAGMA table_info(user_tokens)")->fetchAll(PDO::FETCH_ASSOC);
    $hasExpiresAt = false;

    foreach ($columns as $column) {
        if ($column['name'] === 'expires_at') {
            $hasExpiresAt = true;
            break;
        }
    }

    if (!$hasExpiresAt) {
        echo "Adding expires_at column to user_tokens table...\n";
        $pdo->exec("ALTER TABLE user_tokens ADD COLUMN expires_at TEXT");
        echo "✓ expires_at column added successfully\n";
    } else {
        echo "✓ expires_at column already exists\n";
    }

    // Create indexes if they don't exist
    echo "Creating indexes...\n";
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_user_tokens_token ON user_tokens(token)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_user_tokens_expires ON user_tokens(expires_at)");
    echo "✓ Indexes created\n";

    // Set expiry for existing tokens (24 hours from now)
    $expiresAt = date('Y-m-d H:i:s', time() + 86400);
    $stmt = $pdo->prepare("UPDATE user_tokens SET expires_at = ? WHERE expires_at IS NULL");
    $stmt->execute([$expiresAt]);
    $count = $stmt->rowCount();
    if ($count > 0) {
        echo "✓ Updated $count existing tokens with expiry time\n";
    }

    echo "\nMigration completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

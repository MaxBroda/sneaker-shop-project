<?php

// Migration script to add payment_status column to existing orders table

$dbFile = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if payment_status column exists
    $columns = $pdo->query("PRAGMA table_info(orders)")->fetchAll(PDO::FETCH_ASSOC);
    $hasPaymentStatus = false;

    foreach ($columns as $column) {
        if ($column['name'] === 'payment_status') {
            $hasPaymentStatus = true;
            break;
        }
    }

    if (!$hasPaymentStatus) {
        echo "Adding payment_status column to orders table...\n";
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_status TEXT DEFAULT 'open'");
        echo "✓ payment_status column added successfully\n";
    } else {
        echo "✓ payment_status column already exists\n";
    }

    echo "\nMigration completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

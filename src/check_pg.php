<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432', 'postgres', 'master');
    echo "CONNECTED OK\n";
    
    // Check if database exists
    $result = $pdo->query("SELECT 1 FROM pg_database WHERE datname='encomiendabd'");
    if ($result->fetchColumn()) {
        echo "DATABASE encomiendabd ALREADY EXISTS\n";
    } else {
        // Create database
        $pdo->exec("CREATE DATABASE encomiendabd");
        echo "DATABASE encomiendabd CREATED\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

<?php
/**
 * Database Setup Script
 * Run this once to initialize the database
 */

require __DIR__ . '/database.php';


try {
    // Create database if not exists
    $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    echo "✓ Database created/verified<br>";
    
    // Initialize tables
    initializeDatabase();
    echo "✓ Tables created<br>";
    
    // Seed initial data
    seedDatabase();
    echo "✓ Initial data seeded<br>";
    
    echo "<div style='background:#d4edda;border:1px solid #c3e6cb;padding:15px;margin-top:20px;border-radius:5px;'>";
    echo "<strong>✓ Database Setup Complete!</strong><br>";
    echo "The database 'barber_booking_db' has been initialized with all tables and sample data.<br>";
    echo "<a href='index.php' style='color:#155724;'>Return to Home</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;padding:15px;border-radius:5px;'>";
    echo "<strong>✗ Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>

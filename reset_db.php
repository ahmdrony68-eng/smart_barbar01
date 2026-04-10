<?php
/**
 * Database Reset Script
 * WARNING: This will DELETE all data and re-seed the database
 */

require __DIR__ . '/database.php';

try {
    // Drop all tables
    echo "🔄 Resetting database...<br>";
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("DROP TABLE IF EXISTS bookings");
    $pdo->exec("DROP TABLE IF EXISTS available_slots");
    $pdo->exec("DROP TABLE IF EXISTS barber_roster");
    $pdo->exec("DROP TABLE IF EXISTS barber_services");
    $pdo->exec("DROP TABLE IF EXISTS services");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    echo "✓ Tables dropped<br>";
    
    // Recreate tables and seed
    initializeDatabase();
    echo "✓ Tables recreated<br>";
    
    seedDatabase();
    echo "✓ Database seeded with new data<br><br>";
    
    // Display new credentials
    echo "<h2>✅ Database Reset Complete!</h2>";
    echo "<p style='color: green; font-weight: bold;'>New credentials are ready:</p>";
    echo "<ul style='font-family: monospace;'>";
    echo "<li><strong>Customer 1:</strong> customer1@email.com / customer123</li>";
    echo "<li><strong>Customer 2:</strong> customer2@email.com / customer456</li>";
    echo "<li><strong>Customer 3:</strong> customer3@email.com / customer789</li>";
    echo "<li><strong>Barber 1:</strong> barber1@email.com / barber123</li>";
    echo "<li><strong>Barber 2:</strong> barber2@email.com / barber456</li>";
    echo "<li><strong>Barber 3:</strong> barber3@email.com / barber789</li>";
    echo "<li><strong>Admin:</strong> admin@email.com / admin123</li>";
    echo "</ul>";
    echo "<p><a href='index.php'>← Return to home</a></p>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

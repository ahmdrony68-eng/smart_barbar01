<?php
/**
 * Database Connection
 * MySQL Database Connection Handler
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'barber_booking_db');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Database Connection Error: ' . $e->getMessage());
}

/**
 * Initialize Database Schema
 */
function initializeDatabase() {
    global $pdo;
    
    // Users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            role ENUM('customer', 'barber', 'admin') NOT NULL,
            specialization VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Services table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS services (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            duration INT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Barber Specializations table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS barber_services (
            id INT PRIMARY KEY AUTO_INCREMENT,
            barber_id INT NOT NULL,
            service_id INT NOT NULL,
            FOREIGN KEY (barber_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
            UNIQUE KEY unique_barber_service (barber_id, service_id)
        )
    ");
    
    // Barber Availability/Roster
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS barber_roster (
            id INT PRIMARY KEY AUTO_INCREMENT,
            barber_id INT NOT NULL,
            day_of_week VARCHAR(10) NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            FOREIGN KEY (barber_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    // Available Slots
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS available_slots (
            id INT PRIMARY KEY AUTO_INCREMENT,
            barber_id INT NOT NULL,
            slot_date DATE NOT NULL,
            slot_time TIME NOT NULL,
            is_booked BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (barber_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    // Bookings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            customer_id INT NOT NULL,
            barber_id INT NOT NULL,
            service_id INT NOT NULL,
            booking_date DATE NOT NULL,
            booking_time TIME NOT NULL,
            status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (barber_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
        )
    ");
}

/**
 * Seed Initial Data
 */
function seedDatabase() {
    global $pdo;
    
    // Check if users already exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        return; // Already seeded
    }
    
    // Insert users
    $users = [
        ['customer1@email.com', password_hash('customer123', PASSWORD_BCRYPT), 'Ahmed Hassan', 'customer', null],
        ['customer2@email.com', password_hash('customer456', PASSWORD_BCRYPT), 'Fatima Ali', 'customer', null],
        ['barber1@email.com', password_hash('barber123', PASSWORD_BCRYPT), 'Ali Khan', 'barber', 'Fade Specialist'],
        ['barber2@email.com', password_hash('barber456', PASSWORD_BCRYPT), 'Usman Raza', 'barber', 'Beard Grooming'],
        ['barber3@email.com', password_hash('barber789', PASSWORD_BCRYPT), 'Hamza Noor', 'barber', 'Kids Styling'],
        ['admin@email.com', password_hash('admin123', PASSWORD_BCRYPT), 'Admin User', 'admin', null],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (email, password, name, role, specialization) VALUES (?, ?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute($user);
    }
    
    // Insert services
    $services = [
        ['Fade Haircut', 30, 15.00],
        ['Beard Styling', 20, 10.00],
        ['Kids Haircut', 25, 12.00],
        ['Hair Wash', 15, 5.00],
        ['Full Grooming', 60, 30.00],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO services (name, duration, price) VALUES (?, ?, ?)");
    foreach ($services as $service) {
        $stmt->execute($service);
    }
}

?>

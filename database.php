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
    
    // Seed sample rosters
    $rosters = [
        [3, 'Monday', '09:00:00', '18:00:00'],
        [3, 'Tuesday', '09:00:00', '18:00:00'],
        [3, 'Wednesday', '09:00:00', '18:00:00'],
        [3, 'Thursday', '09:00:00', '18:00:00'],
        [3, 'Friday', '10:00:00', '19:00:00'],
        [4, 'Monday', '11:00:00', '19:00:00'],
        [4, 'Tuesday', '11:00:00', '19:00:00'],
        [4, 'Wednesday', '11:00:00', '19:00:00'],
        [4, 'Thursday', '11:00:00', '19:00:00'],
        [4, 'Saturday', '10:00:00', '17:00:00'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO barber_roster (barber_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
    foreach ($rosters as $roster) {
        $stmt->execute($roster);
    }
}

/**
 * Roster Management Functions
 */

function getBarberRoster($barberId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM barber_roster WHERE barber_id = ? ORDER BY day_of_week");
        $stmt->execute([$barberId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching roster: ' . $e->getMessage());
        return [];
    }
}

function saveBarberRoster($barberId, $roster) {
    global $pdo;
    try {
        // Delete existing roster
        $pdo->prepare("DELETE FROM barber_roster WHERE barber_id = ?")->execute([$barberId]);
        
        // Insert new roster
        $stmt = $pdo->prepare("INSERT INTO barber_roster (barber_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        foreach ($roster as $day => $times) {
            if ($times['enabled']) {
                $stmt->execute([$barberId, $day, $times['start'], $times['end']]);
            }
        }
        return true;
    } catch (Exception $e) {
        error_log('Error saving roster: ' . $e->getMessage());
        return false;
    }
}

/**
 * Slot Generation Functions
 */

function generateSlotsForDate($barberId, $date) {
    global $pdo;
    try {
        $dayOfWeek = date('l', strtotime($date)); // Monday, Tuesday, etc.
        
        // Get barber's working hours for this day
        $stmt = $pdo->prepare("SELECT start_time, end_time FROM barber_roster WHERE barber_id = ? AND day_of_week = ? LIMIT 1");
        $stmt->execute([$barberId, $dayOfWeek]);
        $roster = $stmt->fetch();
        
        if (!$roster) {
            return false; // Barber doesn't work on this day
        }
        
        // Get service duration (default 30 mins)
        $slotDuration = 30;
        
        // Generate 30-minute slots
        $startTime = strtotime($roster['start_time']);
        $endTime = strtotime($roster['end_time']);
        $currentTime = $startTime;
        
        while ($currentTime < $endTime) {
            $slotTime = date('H:i:s', $currentTime);
            $nextSlot = $currentTime + ($slotDuration * 60);
            
            // Check if slot fits within working hours
            if ($nextSlot <= $endTime) {
                $stmt = $pdo->prepare("
                    INSERT IGNORE INTO available_slots (barber_id, slot_date, slot_time, is_booked) 
                    VALUES (?, ?, ?, 0)
                ");
                $stmt->execute([$barberId, $date, $slotTime]);
            }
            
            $currentTime = $nextSlot;
        }
        
        return true;
    } catch (Exception $e) {
        error_log('Error generating slots: ' . $e->getMessage());
        return false;
    }
}

function getAvailableSlots($barberId, $date) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM available_slots 
            WHERE barber_id = ? AND slot_date = ? AND is_booked = 0 
            ORDER BY slot_time
        ");
        $stmt->execute([$barberId, $date]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching slots: ' . $e->getMessage());
        return [];
    }
}

function checkSlotAvailability($barberId, $date, $time) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count FROM available_slots 
            WHERE barber_id = ? AND slot_date = ? AND slot_time = ? AND is_booked = 0
        ");
        $stmt->execute([$barberId, $date, $time]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    } catch (Exception $e) {
        error_log('Error checking slot: ' . $e->getMessage());
        return false;
    }
}

/**
 * Booking Functions
 */

function createBooking($customerId, $barberId, $serviceId, $date, $time) {
    global $pdo;
    try {
        // Check if slot is available
        if (!checkSlotAvailability($barberId, $date, $time)) {
            return ['success' => false, 'message' => 'Selected slot is not available'];
        }
        
        // Check for double booking
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count FROM bookings 
            WHERE barber_id = ? AND booking_date = ? AND booking_time = ? 
            AND status IN ('pending', 'confirmed')
        ");
        $stmt->execute([$barberId, $date, $time]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            return ['success' => false, 'message' => 'This slot is no longer available'];
        }
        
        // Create booking
        $stmt = $pdo->prepare("
            INSERT INTO bookings (customer_id, barber_id, service_id, booking_date, booking_time, status) 
            VALUES (?, ?, ?, ?, ?, 'confirmed')
        ");
        
        if ($stmt->execute([$customerId, $barberId, $serviceId, $date, $time])) {
            // Mark slot as booked
            $pdo->prepare("UPDATE available_slots SET is_booked = 1 WHERE barber_id = ? AND slot_date = ? AND slot_time = ?")
                ->execute([$barberId, $date, $time]);
            
            $bookingId = $pdo->lastInsertId();
            return ['success' => true, 'booking_id' => $bookingId];
        }
    } catch (Exception $e) {
        error_log('Error creating booking: ' . $e->getMessage());
    }
    
    return ['success' => false, 'message' => 'Failed to create booking'];
}

function getCustomerBookings($customerId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, u.name as barber_name, s.name as service_name, s.price 
            FROM bookings b
            JOIN users u ON b.barber_id = u.id
            JOIN services s ON b.service_id = s.id
            WHERE b.customer_id = ?
            ORDER BY b.booking_date DESC, b.booking_time DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching customer bookings: ' . $e->getMessage());
        return [];
    }
}

function getBarberBookings($barberId, $startDate = null, $endDate = null) {
    global $pdo;
    try {
        if (!$startDate) {
            $startDate = date('Y-m-d');
        }
        if (!$endDate) {
            $endDate = date('Y-m-d', strtotime('+30 days'));
        }
        
        $stmt = $pdo->prepare("
            SELECT b.*, u.name as customer_name, u.email as customer_email, s.name as service_name, s.duration 
            FROM bookings b
            JOIN users u ON b.customer_id = u.id
            JOIN services s ON b.service_id = s.id
            WHERE b.barber_id = ? AND b.booking_date BETWEEN ? AND ?
            ORDER BY b.booking_date ASC, b.booking_time ASC
        ");
        $stmt->execute([$barberId, $startDate, $endDate]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching barber bookings: ' . $e->getMessage());
        return [];
    }
}

function cancelBooking($bookingId, $customerId = null) {
    global $pdo;
    try {
        // Get booking details
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            return false;
        }
        
        // Check authorization
        if ($customerId && $booking['customer_id'] != $customerId) {
            return false;
        }
        
        // Update booking status
        $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?")->execute([$bookingId]);
        
        // Free up the slot
        $pdo->prepare("UPDATE available_slots SET is_booked = 0 WHERE barber_id = ? AND slot_date = ? AND slot_time = ?")
            ->execute([$booking['barber_id'], $booking['booking_date'], $booking['booking_time']]);
        
        return true;
    } catch (Exception $e) {
        error_log('Error cancelling booking: ' . $e->getMessage());
        return false;
    }
}

function getBookingStats($barberId = null) {
    global $pdo;
    try {
        if ($barberId) {
            // Barber-specific stats
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(s.price) as total_revenue
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                WHERE b.barber_id = ? AND b.booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            $stmt->execute([$barberId]);
        } else {
            // System-wide stats
            $stmt = $pdo->query("
                SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(s.price) as total_revenue
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                WHERE b.booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
        }
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log('Error fetching stats: ' . $e->getMessage());
        return null;
    }
}

/**
 * Booking Status Management
 */

function updateBookingStatus($bookingId, $newStatus, $authorizedRole = null, $userId = null) {
    global $pdo;
    try {
        // Validate status
        $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        // Get booking
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            return ['success' => false, 'message' => 'Booking not found'];
        }
        
        // Check authorization
        if ($authorizedRole === 'barber' && $booking['barber_id'] != $userId) {
            return ['success' => false, 'message' => 'Not authorized'];
        }
        if ($authorizedRole === 'customer' && $booking['customer_id'] != $userId) {
            return ['success' => false, 'message' => 'Not authorized'];
        }
        
        // Update status
        $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$newStatus, $bookingId]);
        
        // If cancelled, free up the slot
        if ($newStatus === 'cancelled') {
            $pdo->prepare("UPDATE available_slots SET is_booked = 0 WHERE barber_id = ? AND slot_date = ? AND slot_time = ?")
                ->execute([$booking['barber_id'], $booking['booking_date'], $booking['booking_time']]);
        }
        
        return ['success' => true, 'message' => 'Booking updated successfully'];
    } catch (Exception $e) {
        error_log('Error updating booking status: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error'];
    }
}

function getBookingById($bookingId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, u.name as barber_name, s.name as service_name, s.price, c.name as customer_name, c.email as customer_email
            FROM bookings b
            JOIN users u ON b.barber_id = u.id
            JOIN services s ON b.service_id = s.id
            JOIN users c ON b.customer_id = c.id
            WHERE b.id = ?
        ");
        $stmt->execute([$bookingId]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log('Error fetching booking: ' . $e->getMessage());
        return null;
    }
}

function getAllBookings($filter = null, $limit = 50, $offset = 0) {
    global $pdo;
    try {
        $query = "
            SELECT b.*, u.name as barber_name, s.name as service_name, s.price, c.name as customer_name
            FROM bookings b
            JOIN users u ON b.barber_id = u.id
            JOIN services s ON b.service_id = s.id
            JOIN users c ON b.customer_id = c.id
        ";
        
        if ($filter === 'pending') {
            $query .= " WHERE b.status = 'pending'";
        } elseif ($filter === 'upcoming') {
            $query .= " WHERE b.status = 'confirmed' AND b.booking_date >= CURDATE()";
        } elseif ($filter === 'today') {
            $query .= " WHERE b.booking_date = CURDATE()";
        }
        
        $query .= " ORDER BY b.booking_date DESC, b.booking_time DESC LIMIT ? OFFSET ?";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching all bookings: ' . $e->getMessage());
        return [];
    }
}

function getAnalyticsReport() {
    global $pdo;
    try {
        $report = [];
        
        // Total by status
        $stmt = $pdo->query("
            SELECT status, COUNT(*) as count FROM bookings 
            WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY)
            GROUP BY status
        ");
        $report['by_status'] = $stmt->fetchAll();
        
        // Top services
        $stmt = $pdo->query("
            SELECT s.name, COUNT(*) as count, SUM(s.price) as revenue 
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            WHERE b.booking_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY)
            GROUP BY s.id
            ORDER BY count DESC LIMIT 5
        ");
        $report['top_services'] = $stmt->fetchAll();
        
        // Top barbers
        $stmt = $pdo->query("
            SELECT u.name, COUNT(*) as bookings, SUM(s.price) as revenue 
            FROM bookings b
            JOIN users u ON b.barber_id = u.id
            JOIN services s ON b.service_id = s.id
            WHERE b.booking_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY)
            GROUP BY u.id
            ORDER BY bookings DESC LIMIT 5
        ");
        $report['top_barbers'] = $stmt->fetchAll();
        
        // Daily totals
        $stmt = $pdo->query("
            SELECT DATE(booking_date) as date, COUNT(*) as bookings, SUM(s.price) as revenue
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(booking_date)
            ORDER BY date DESC
        ");
        $report['daily'] = $stmt->fetchAll();
        
        return $report;
    } catch (Exception $e) {
        error_log('Error generating analytics: ' . $e->getMessage());
        return [];
    }
}

?>

<?php
/**
 * Data Handler
 * Fetches data from MySQL database
 */

require_once __DIR__ . '/database.php';

/**
 * Get all services
 */
function getServices() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT id, name, duration, price FROM services ORDER BY name");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching services: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get all barbers
 */
function getBarbers() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT id, email, name, specialization FROM users WHERE role = 'barber' ORDER BY name");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching barbers: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get barber services
 */
function getBarberServices($barberId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT s.id, s.name, s.duration, s.price 
            FROM services s
            INNER JOIN barber_services bs ON s.id = bs.service_id
            WHERE bs.barber_id = ?
            ORDER BY s.name
        ");
        $stmt->execute([$barberId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching barber services: ' . $e->getMessage());
        return [];
    }
}

/**
 * Add barber service
 */
function addBarberService($barberId, $serviceId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO barber_services (barber_id, service_id) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE barber_id=barber_id
        ");
        return $stmt->execute([$barberId, $serviceId]);
    } catch (Exception $e) {
        error_log('Error adding barber service: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get sample available slots (demo)
 */
function getSampleSlots() {
    return ['10:00', '10:30', '11:00', '11:30', '12:00', '14:00', '14:30', '15:00', '15:30', '16:00'];
}

// Load all data
$services = getServices();
$barbers = getBarbers();
$sampleSlots = getSampleSlots();

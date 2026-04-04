<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

requireRole('customer');

$customerId = getCurrentUser();
$bookings = getCustomerBookings($customerId);

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $bookingId = intval($_POST['cancel_booking']);
    if (cancelBooking($bookingId, $customerId)) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

$pageTitle = 'My Bookings';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl p-8 mb-8 text-white shadow-lg">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold mb-2">📅 My Bookings</h1>
            <p class="text-pink-100">View and manage your appointments</p>
        </div>
        <a href="book_appointment.php" class="bg-yellow-400 text-gray-800 px-5 py-2 rounded-lg font-bold hover:shadow-lg transition">
            ➕ New Booking
        </a>
    </div>
</section>

<?php if (count($bookings) === 0): ?>
    <div class="bg-white rounded-xl p-12 text-center shadow-lg border-2 border-pink-200">
        <p class="text-gray-700 mb-4 text-lg font-semibold">✨ You don't have any bookings yet.</p>
        <a href="book_appointment.php" class="inline-block bg-gradient-to-r from-pink-500 to-rose-500 text-white px-6 py-3 rounded-lg font-bold hover:shadow-lg transition">
            📅 Book an Appointment
        </a>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($bookings as $booking): 
            $bookingDate = strtotime($booking['booking_date']);
            $isUpcoming = $bookingDate >= strtotime('today');
            $statusIcons = [
                'pending' => '⏳',
                'confirmed' => '✓',
                'completed' => '✔️',
                'cancelled' => '✗'
            ];
            $statusBg = [
                'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                'confirmed' => 'bg-green-100 text-green-700 border-green-300',
                'completed' => 'bg-blue-100 text-blue-700 border-blue-300',
                'cancelled' => 'bg-red-100 text-red-700 border-red-300'
            ];
            $statusIcon = $statusIcons[$booking['status']] ?? '•';
            $bgClass = $statusBg[$booking['status']] ?? 'bg-gray-100 text-gray-700 border-gray-300';
        ?>
            <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-pink-200 hover:shadow-xl transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">✂️ <?php echo htmlspecialchars($booking['service_name']); ?></h3>
                        <p class="text-gray-600 text-sm font-semibold">with 👨‍💼 <?php echo htmlspecialchars($booking['barber_name']); ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-bold border-2 <?php echo $bgClass; ?>">
                        <?php echo $statusIcon; ?> <?php echo ucfirst($booking['status']); ?>
                    </span>
                </div>
                
                <div class="grid md:grid-cols-4 gap-4 mb-5 bg-pink-50 p-4 rounded-lg">
                    <div>
                        <p class="text-xs font-bold text-pink-600 uppercase">📅 Date</p>
                        <p class="font-bold text-gray-800"><?php echo date('M d, Y', $bookingDate); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-rose-600 uppercase">🕐 Time</p>
                        <p class="font-bold text-gray-800"><?php echo htmlspecialchars($booking['booking_time']); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-pink-600 uppercase">💰 Price</p>
                        <p class="font-bold text-pink-600 text-lg">$<?php echo number_format($booking['price'], 2); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-rose-600 uppercase">📝 Booked</p>
                        <p class="font-bold text-gray-800 text-sm"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></p>
                    </div>
                </div>
                
                <?php if ($isUpcoming && $booking['status'] === 'confirmed'): ?>
                    <form method="POST" class="inline-block">
                        <button type="submit" name="cancel_booking" value="<?php echo $booking['id']; ?>" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-bold transition"
                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                            ✗ Cancel Booking
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

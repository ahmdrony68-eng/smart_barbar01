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

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold mb-2">My Bookings</h1>
            <p class="text-slate-600 text-sm">View and manage your appointments</p>
        </div>
        <a href="book_appointment.php" class="bg-blue-600 text-white px-4 py-2 rounded font-medium hover:bg-blue-700">
            + New Booking
        </a>
    </div>
</section>

<?php if (count($bookings) === 0): ?>
    <div class="bg-white border border-slate-200 rounded-xl p-8 text-center">
        <p class="text-slate-600 mb-4">You don't have any bookings yet.</p>
        <a href="book_appointment.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded font-medium hover:bg-blue-700">
            Book an Appointment
        </a>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($bookings as $booking): 
            $bookingDate = strtotime($booking['booking_date']);
            $isUpcoming = $bookingDate >= strtotime('today');
            $statusColors = [
                'pending' => 'yellow',
                'confirmed' => 'green',
                'completed' => 'blue',
                'cancelled' => 'red'
            ];
            $statusColor = $statusColors[$booking['status']] ?? 'gray';
        ?>
            <div class="bg-white border border-slate-200 rounded-xl p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                        <p class="text-slate-600 text-sm">with <?php echo htmlspecialchars($booking['barber_name']); ?></p>
                    </div>
                    <span class="px-3 py-1 bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-700 rounded-full text-xs font-medium">
                        <?php echo ucfirst($booking['status']); ?>
                    </span>
                </div>
                
                <div class="grid md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-slate-600 uppercase">Date</p>
                        <p class="font-medium"><?php echo date('M d, Y', $bookingDate); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 uppercase">Time</p>
                        <p class="font-medium"><?php echo htmlspecialchars($booking['booking_time']); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 uppercase">Price</p>
                        <p class="font-medium text-green-600">$<?php echo number_format($booking['price'], 2); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 uppercase">Booked</p>
                        <p class="font-medium text-sm"><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></p>
                    </div>
                </div>
                
                <?php if ($isUpcoming && $booking['status'] === 'confirmed'): ?>
                    <form method="POST" class="inline-block">
                        <button type="submit" name="cancel_booking" value="<?php echo $booking['id']; ?>" 
                                class="text-red-600 text-sm font-medium hover:text-red-700"
                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                            Cancel Booking
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

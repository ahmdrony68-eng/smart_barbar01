<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

requireRole('barber');

$barberId = getCurrentUser();
$bookings = getBarberBookings($barberId);

$pageTitle = 'My Appointments';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl p-8 mb-8 text-white shadow-lg">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold mb-2">📅 Your Appointments</h1>
            <p class="text-blue-100">Upcoming bookings from customers</p>
        </div>
        <div class="flex gap-2">
            <a href="manage_roster.php" class="bg-white text-blue-600 px-5 py-2 rounded-lg font-bold hover:shadow-lg transition">
                📋 Manage Roster
            </a>
            <a href="manage_bookings.php" class="bg-yellow-400 text-gray-800 px-5 py-2 rounded-lg font-bold hover:shadow-lg transition">
                ✓ Manage Bookings
            </a>
        </div>
    </div>
</section>

<?php if (count($bookings) === 0): ?>
    <div class="bg-white rounded-xl p-12 text-center shadow-lg border-2 border-blue-200">
        <p class="text-gray-700 text-lg font-semibold">📭 No appointments scheduled yet.</p>
    </div>
<?php else: ?>
    <div class="grid gap-4">
        <?php foreach ($bookings as $booking): 
            $bookingDate = strtotime($booking['booking_date']);
            $isUpcoming = $bookingDate >= strtotime('today');
            $statusBg = $isUpcoming ? 'bg-green-100 text-green-700 border-green-300' : 'bg-gray-100 text-gray-700 border-gray-300';
            $statusLabel = $isUpcoming ? '🔔 Upcoming' : '✓ Past';
        ?>
            <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-blue-200 hover:shadow-xl transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">✂️ <?php echo htmlspecialchars($booking['service_name']); ?></h3>
                        <p class="text-gray-600 text-sm font-semibold">👤 Customer: <?php echo htmlspecialchars($booking['customer_name']); ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border-2 <?php echo $statusBg; ?>">
                        <?php echo $statusLabel; ?>
                    </span>
                </div>
                
                <div class="grid md:grid-cols-5 gap-3 mb-4 pb-4 border-b-2 border-blue-200 bg-blue-50 p-3 rounded-lg">
                    <div>
                        <p class="text-xs font-bold text-blue-600 uppercase">📅 Date</p>
                        <p class="font-bold text-gray-800"><?php echo date('M d, Y', $bookingDate); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-cyan-600 uppercase">🕐 Time</p>
                        <p class="font-bold text-gray-800"><?php echo htmlspecialchars($booking['booking_time']); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-blue-600 uppercase">⏱️ Duration</p>
                        <p class="font-bold text-gray-800"><?php echo $booking['duration']; ?> mins</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-cyan-600 uppercase">📧 Contact</p>
                        <p class="font-bold text-gray-800 text-sm break-all"><?php echo htmlspecialchars($booking['customer_email']); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-blue-600 uppercase">Status</p>
                        <p class="font-bold text-gray-800 capitalize"><?php echo $booking['status']; ?></p>
                    </div>
                </div>
                
                <div class="text-sm font-bold text-blue-600 bg-blue-50 p-3 rounded-lg">
                    <p>💰 <span class="text-blue-700">Payment:</span> <span class="text-2xl text-blue-600">$<?php echo number_format($booking['price'] ?? 0, 2); ?></span> <span class="text-xs text-gray-600">(<?php echo ucfirst($booking['status']); ?>)</span></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

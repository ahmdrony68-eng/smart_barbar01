<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

requireRole('barber');

$barberId = getCurrentUser();
$bookings = getBarberBookings($barberId);

$pageTitle = 'My Appointments';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold mb-2">Your Appointments</h1>
            <p class="text-slate-600 text-sm">Upcoming bookings from customers</p>
        </div>
        <a href="manage_roster.php" class="bg-blue-600 text-white px-4 py-2 rounded font-medium hover:bg-blue-700">
            Manage Roster
        </a>
    </div>
</section>

<?php if (count($bookings) === 0): ?>
    <div class="bg-white border border-slate-200 rounded-xl p-8 text-center">
        <p class="text-slate-600 mb-4">No appointments scheduled yet.</p>
    </div>
<?php else: ?>
    <div class="grid gap-4">
        <?php foreach ($bookings as $booking): 
            $bookingDate = strtotime($booking['booking_date']);
            $isUpcoming = $bookingDate >= strtotime('today');
        ?>
            <div class="bg-white border border-slate-200 rounded-xl p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                        <p class="text-slate-600 text-sm">Customer: <?php echo htmlspecialchars($booking['customer_name']); ?></p>
                    </div>
                    <span class="px-3 py-1 <?php echo $isUpcoming ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'; ?> rounded-full text-xs font-medium">
                        <?php echo $isUpcoming ? 'Upcoming' : 'Past'; ?>
                    </span>
                </div>
                
                <div class="grid md:grid-cols-5 gap-4 mb-4 pb-4 border-b border-slate-200">
                    <div>
                        <p class="text-xs text-slate-600 uppercase">Date</p>
                        <p class="font-medium"><?php echo date('M d, Y', $bookingDate); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 uppercase">Time</p>
                        <p class="font-medium"><?php echo htmlspecialchars($booking['booking_time']); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 uppercase">Duration</p>
                        <p class="font-medium"><?php echo $booking['duration']; ?> mins</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 uppercase">Contact</p>
                        <p class="font-medium text-sm"><?php echo htmlspecialchars($booking['customer_email']); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-600 uppercase">Status</p>
                        <p class="font-medium capitalize"><?php echo $booking['status']; ?></p>
                    </div>
                </div>
                
                <div class="text-sm text-slate-600">
                    <p><strong>Payment:</strong> $<?php echo number_format($booking['price'] ?? 0, 2); ?> (<?php echo ucfirst($booking['status']); ?>)</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

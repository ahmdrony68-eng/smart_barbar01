<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

$pageTitle = 'Booking Management';
require __DIR__ . '/partials/header.php';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['new_status'])) {
    $bookingId = intval($_POST['booking_id']);
    $newStatus = $_POST['new_status'];
    
    $result = updateBookingStatus($bookingId, $newStatus, getCurrentRole(), getCurrentUser());
    $message = $result;
}

// Check authorization
$isDashboard = false;
if (hasRole('admin')) {
    $isDashboard = true;
    $bookings = getAllBookings();
} elseif (hasRole('barber')) {
    $isDashboard = true;
    $barberId = getCurrentUser();
    $bookings = getBarberBookings($barberId);
} else {
    header('Location: index.php');
    exit;
}

$filter = $_GET['filter'] ?? 'all';
?>

<section class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl p-8 mb-6 text-white shadow-lg">
    <h1 class="text-3xl font-bold mb-2">Booking Management</h1>
    <p class="text-purple-100">Manage all appointments and bookings</p>
</section>

<?php if (isset($message)): ?>
    <div class="mb-4 p-4 rounded-lg <?php echo $message['success'] ? 'bg-green-100 border-2 border-green-500 text-green-700' : 'bg-red-100 border-2 border-red-500 text-red-700'; ?>">
        <?php echo htmlspecialchars($message['message']); ?>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="mb-6 flex gap-2 flex-wrap">
    <a href="?filter=all" class="px-4 py-2 rounded-lg font-medium <?php echo ($filter === 'all') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?>">All</a>
    <a href="?filter=pending" class="px-4 py-2 rounded-lg font-medium <?php echo ($filter === 'pending') ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?>">Pending</a>
    <a href="?filter=upcoming" class="px-4 py-2 rounded-lg font-medium <?php echo ($filter === 'upcoming') ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?>">Upcoming</a>
    <a href="?filter=today" class="px-4 py-2 rounded-lg font-medium <?php echo ($filter === 'today') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?>">Today</a>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-gray-200">
    <?php if (count($bookings) === 0): ?>
        <div class="p-8 text-center">
            <p class="text-gray-600">No bookings found.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Customer</th>
                        <th class="px-6 py-3 text-left">Barber</th>
                        <th class="px-6 py-3 text-left">Service</th>
                        <th class="px-6 py-3 text-left">Date & Time</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($bookings as $booking): 
                        $isUpcoming = strtotime($booking['booking_date']) >= strtotime('today');
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-2 border-yellow-300',
                            'confirmed' => 'bg-green-100 text-green-800 border-2 border-green-300',
                            'completed' => 'bg-blue-100 text-blue-800 border-2 border-blue-300',
                            'cancelled' => 'bg-red-100 text-red-800 border-2 border-red-300'
                        ];
                    ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-indigo-600">#<?php echo $booking['id']; ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($booking['barber_name'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4">
                                <span class="text-sm"><?php echo htmlspecialchars($booking['service_name']); ?></span><br>
                                <span class="text-xs text-gray-600">$<?php echo number_format($booking['price'], 2); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></span><br>
                                <span class="text-sm text-gray-600"><?php echo $booking['booking_time']; ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $statusColors[$booking['status']] ?? 'bg-gray-100'; ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <?php if ($booking['status'] === 'pending' && (hasRole('barber') || hasRole('admin'))): ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="new_status" value="confirmed">
                                            <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-medium transition">
                                                ✓ Confirm
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array($booking['status'], ['pending', 'confirmed']) && $isUpcoming): ?>
                                        <form method="POST" class="inline" onsubmit="return confirm('Cancel this booking?')">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="new_status" value="cancelled">
                                            <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-medium transition">
                                                ✕ Cancel
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($booking['status'] === 'completed'): ?>
                                        <span class="text-xs text-gray-500">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

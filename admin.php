<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

// Require admin role
requireRole('admin');

$pageTitle = 'Admin Dashboard';
require __DIR__ . '/partials/header.php';

// Get all data
$stats = getBookingStats();
$barberList = getUsersByRole('barber');
$customerList = getUsersByRole('customer');
$analytics = getAnalyticsReport();
$recentBookings = getAllBookings(null, 10);
?>

<section class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-xl p-8 mb-8 text-white shadow-lg">
    <h1 class="text-4xl font-bold mb-2">✓ Admin Dashboard</h1>
    <p class="text-blue-100">Complete system overview and control</p>
</section>

<!-- Statistics Cards -->
<div class="grid md:grid-cols-4 gap-5 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition">
        <p class="text-blue-100 text-sm font-semibold">Total Bookings</p>
        <p class="text-4xl font-bold mt-2"><?php echo $stats['total_bookings'] ?? 0; ?></p>
        <p class="text-blue-200 text-xs mt-2">📊 Last 30 days</p>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition">
        <p class="text-green-100 text-sm font-semibold">Confirmed</p>
        <p class="text-4xl font-bold mt-2"><?php echo $stats['confirmed'] ?? 0; ?></p>
        <p class="text-green-200 text-xs mt-2">✓ Active bookings</p>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition">
        <p class="text-purple-100 text-sm font-semibold">Completed</p>
        <p class="text-4xl font-bold mt-2"><?php echo $stats['completed'] ?? 0; ?></p>
        <p class="text-purple-200 text-xs mt-2">✓ Finished services</p>
    </div>
    
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition">
        <p class="text-amber-100 text-sm font-semibold">Total Revenue</p>
        <p class="text-3xl font-bold mt-2">$<?php echo number_format($stats['total_revenue'] ?? 0, 0); ?></p>
        <p class="text-amber-200 text-xs mt-2">💰 Earnings</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl p-6 mb-8 shadow-lg border-2 border-gray-200">
    <h2 class="text-xl font-bold mb-4 text-indigo-600">🎯 Quick Actions</h2>
    <div class="grid md:grid-cols-4 gap-3">
        <a href="manage_bookings.php" class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white px-4 py-3 rounded-lg font-semibold hover:shadow-lg transition text-center">
            📋 Manage Bookings
        </a>
        <a href="manage_services.php" class="bg-gradient-to-r from-pink-500 to-pink-600 text-white px-4 py-3 rounded-lg font-semibold hover:shadow-lg transition text-center">
            🔧 Assign Services
        </a>
        <a href="manage_bookings.php?filter=pending" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-4 py-3 rounded-lg font-semibold hover:shadow-lg transition text-center">
            ⏳ Pending Approval
        </a>
        <a href="#" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3 rounded-lg font-semibold hover:shadow-lg transition text-center">
            📊 View Reports
        </a>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid md:grid-cols-3 gap-6 mb-8">
    <!-- Barber Management -->
    <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-purple-200">
        <h2 class="text-lg font-bold mb-4 text-purple-600">👨‍💼 Barbers (<?php echo count($barberList); ?>)</h2>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            <?php foreach ($barberList as $barber): ?>
                <div class="border-l-4 border-purple-500 pl-3 py-2">
                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($barber['name']); ?></p>
                    <p class="text-xs text-gray-600"><?php echo htmlspecialchars($barber['specialization'] ?? 'General'); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Customer Overview -->
    <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-blue-200">
        <h2 class="text-lg font-bold mb-4 text-blue-600">👥 Customers (<?php echo count($customerList); ?>)</h2>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            <?php foreach (array_slice($customerList, 0, 8) as $customer): ?>
                <div class="border-l-4 border-blue-500 pl-3 py-2">
                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($customer['name']); ?></p>
                    <p class="text-xs text-gray-600"><?php echo htmlspecialchars($customer['email']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Services -->
    <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-green-200">
        <h2 class="text-lg font-bold mb-4 text-green-600">🎨 Services (<?php echo count($services); ?>)</h2>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            <?php foreach ($services as $service): ?>
                <div class="border-l-4 border-green-500 pl-3 py-2">
                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($service['name']); ?></p>
                    <p class="text-xs text-gray-600">$<?php echo number_format($service['price'], 2); ?> • <?php echo $service['duration']; ?> mins</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Analytics Section -->
<?php if (!empty($analytics['top_services'])): ?>
<div class="grid md:grid-cols-2 gap-6 mb-8">
    <!-- Top Services -->
    <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-orange-200">
        <h3 class="text-lg font-bold mb-4 text-orange-600">⭐ Top Services</h3>
        <div class="space-y-3">
            <?php foreach ($analytics['top_services'] as $idx => $svc): ?>
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($svc['name']); ?></p>
                        <p class="text-xs text-gray-600"><?php echo $svc['count']; ?> bookings • $<?php echo number_format($svc['revenue'], 2); ?></p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-orange-600"><?php echo ($idx + 1); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Top Barbers -->
    <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-pink-200">
        <h3 class="text-lg font-bold mb-4 text-pink-600">🏆 Top Barbers</h3>
        <div class="space-y-3">
            <?php foreach ($analytics['top_barbers'] as $idx => $barber): ?>
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($barber['name']); ?></p>
                        <p class="text-xs text-gray-600"><?php echo $barber['bookings']; ?> bookings • $<?php echo number_format($barber['revenue'], 2); ?></p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-pink-600"><?php echo ($idx + 1); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Bookings -->
<div class="bg-white rounded-xl p-6 shadow-lg border-2 border-gray-200">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">📋 Recent Bookings</h3>
        <a href="manage_bookings.php" class="text-indigo-600 hover:text-indigo-700 text-sm font-semibold">View All →</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 border-b-2 border-gray-300">
                <tr>
                    <th class="px-4 py-2 text-left font-bold">ID</th>
                    <th class="px-4 py-2 text-left font-bold">Customer</th>
                    <th class="px-4 py-2 text-left font-bold">Barber</th>
                    <th class="px-4 py-2 text-left font-bold">Service</th>
                    <th class="px-4 py-2 text-left font-bold">Date</th>
                    <th class="px-4 py-2 text-left font-bold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach (array_slice($recentBookings, 0, 8) as $booking): 
                    $statusColors = [
                        'pending' => 'text-yellow-600 bg-yellow-100',
                        'confirmed' => 'text-green-600 bg-green-100',
                        'completed' => 'text-blue-600 bg-blue-100',
                        'cancelled' => 'text-red-600 bg-red-100'
                    ];
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-bold text-indigo-600">#<?php echo $booking['id']; ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($booking['barber_name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($booking['service_name']); ?></td>
                        <td class="px-4 py-2"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs font-bold <?php echo $statusColors[$booking['status']] ?? 'text-gray-600 bg-gray-100'; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

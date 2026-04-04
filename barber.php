<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

// Require barber role
requireRole('barber');

$pageTitle = 'Barber Dashboard';
require __DIR__ . '/partials/header.php';

// Get current barber's info
$currentBarber = getUserById(getCurrentUser());
$myServices = getBarberServices(getCurrentUser());
?>

<section class="bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl p-8 mb-8 text-white shadow-lg">
    <h1 class="text-3xl font-bold mb-2">👨‍💼 Barber Dashboard</h1>
    <p class="text-purple-100">Manage your profile, services, roster, and appointments</p>
</section>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <!-- Profile -->
    <section class="bg-white rounded-xl p-6 shadow-lg border-2 border-purple-200">
        <h2 class="text-lg font-bold mb-4 text-purple-600">👤 Your Profile</h2>
        <div class="space-y-3 text-sm bg-purple-50 p-4 rounded-lg">
            <div>
                <p class="text-purple-600 font-semibold text-xs">Name</p>
                <p class="font-bold text-gray-800"><?php echo htmlspecialchars($currentBarber['name']); ?></p>
            </div>
            <div>
                <p class="text-purple-600 font-semibold text-xs">Email</p>
                <p class="font-bold text-gray-800 break-all"><?php echo htmlspecialchars($currentBarber['email']); ?></p>
            </div>
            <div>
                <p class="text-purple-600 font-semibold text-xs">Specialization</p>
                <p class="font-bold text-gray-800"><?php echo htmlspecialchars($currentBarber['specialization'] ?? 'Not set'); ?></p>
            </div>
        </div>
        
        <div class="mt-5 flex flex-col gap-2">
            <a href="manage_roster.php" class="block text-center bg-gradient-to-r from-blue-500 to-cyan-500 text-white py-2 rounded-lg font-bold hover:shadow-lg transition">
                📅 Manage Roster
            </a>
            <a href="my_appointments.php" class="block text-center bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-2 rounded-lg font-bold hover:shadow-lg transition">
                📋 My Appointments
            </a>
            <a href="manage_bookings.php" class="block text-center bg-gradient-to-r from-orange-500 to-pink-500 text-white py-2 rounded-lg font-bold hover:shadow-lg transition">
                ✓ Confirm/Cancel Bookings
            </a>
        </div>
    </section>
    
    <!-- Services -->
    <section class="bg-white rounded-xl p-6 shadow-lg border-2 border-indigo-200">
        <h2 class="text-lg font-bold mb-4 text-indigo-600">🎨 Your Services</h2>
        <?php if (count($myServices) > 0): ?>
            <div class="space-y-2">
                <?php foreach ($myServices as $service): ?>
                    <div class="border-2 border-indigo-200 rounded-lg p-3 bg-indigo-50 text-sm hover:shadow-md transition">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-800">✂️ <?php echo htmlspecialchars($service['name']); ?></span>
                            <span class="font-bold text-indigo-600">$<?php echo number_format($service['price'], 2); ?></span>
                        </div>
                        <p class="text-xs text-indigo-600 font-semibold mt-1">⏱️ <?php echo $service['duration']; ?> mins</p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-sm font-semibold text-indigo-600 bg-indigo-50 p-3 rounded-lg">📌 No services assigned yet.</p>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

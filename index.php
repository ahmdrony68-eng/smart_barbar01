<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Smart Barber Booking - Home';
require __DIR__ . '/data.php';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-xl p-8 mb-8 text-white shadow-lg">
    <h1 class="text-4xl font-bold mb-3">✨ Smart Barber Booking System</h1>
    <p class="text-lg text-purple-100">Professional booking & management platform for modern barbershops</p>
    
    <?php if (isLoggedIn()): ?>
        <div class="mt-5 p-4 bg-white bg-opacity-20 border-2 border-white rounded-lg backdrop-blur">
            <p class="text-white font-bold">✓ Welcome, <?php echo htmlspecialchars(getCurrentUserName()); ?>! (<?php echo ucfirst(getCurrentRole()); ?>)</p>
            <?php if (hasRole('customer')): ?>
                <a href="book_appointment.php" class="text-white underline font-bold hover:text-yellow-200 mt-2 inline-block">📅 Book an appointment now →</a>
            <?php elseif (hasRole('barber')): ?>
                <a href="manage_roster.php" class="text-white underline font-bold hover:text-yellow-200 mt-2 inline-block">📋 Manage your roster →</a>
            <?php elseif (hasRole('admin')): ?>
                <a href="admin.php" class="text-white underline font-bold hover:text-yellow-200 mt-2 inline-block">📊 View admin dashboard →</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="mt-5 p-4 bg-white bg-opacity-20 border-2 border-white rounded-lg backdrop-blur">
            <p class="text-white font-bold">🔑 Ready to book? <a href="login.php" class="underline hover:text-yellow-200">Login here</a> or use demo credentials</p>
        </div>
    <?php endif; ?>
</section>

<section class="grid md:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-xl p-5 shadow-lg border-2 border-blue-200 hover:shadow-xl transition">
        <h2 class="font-bold mb-2 text-lg text-blue-600">✓ 🔐 Authentication</h2>
        <p class="text-sm text-gray-600">Role-based login with secure session management</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-lg border-2 border-green-200 hover:shadow-xl transition">
        <h2 class="font-bold mb-2 text-lg text-green-600">✓ 📅 Booking System</h2>
        <p class="text-sm text-gray-600">Full appointment scheduling with smart slot management</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-lg border-2 border-purple-200 hover:shadow-xl transition">
        <h2 class="font-bold mb-2 text-lg text-purple-600">✓ 📊 Dashboard</h2>
        <p class="text-sm text-gray-600">Analytics, roster management, and booking history</p>
    </div>
</section>

<section class="grid md:grid-cols-3 gap-5 mb-8">
    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl p-6 shadow-lg border-2 border-cyan-300">
        <h2 class="font-bold mb-2 text-lg text-cyan-700">👤 Customer Portal</h2>
        <p class="text-sm text-gray-700 mb-3">Browse barbers, view services, and book appointments instantly.</p>
        <a href="customer.php" class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-3 py-1 rounded font-bold hover:shadow-lg transition text-sm">
            Open Portal →
        </a>
    </div>
    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-6 shadow-lg border-2 border-purple-300">
        <h2 class="font-bold mb-2 text-lg text-purple-700">👨‍💼 Barber Dashboard</h2>
        <p class="text-sm text-gray-700 mb-3">Manage roster, view appointments, and confirm/cancel bookings.</p>
        <a href="barber.php" class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white px-3 py-1 rounded font-bold hover:shadow-lg transition text-sm">
            Open Dashboard →
        </a>
    </div>
    <div class="bg-gradient-to-br from-orange-50 to-pink-50 rounded-xl p-6 shadow-lg border-2 border-orange-300">
        <h2 class="font-bold mb-2 text-lg text-orange-700">👨‍💼 Admin Dashboard</h2>
        <p class="text-sm text-gray-700 mb-3">System analytics, barber management, and complete control.</p>
        <a href="admin.php" class="bg-gradient-to-r from-orange-500 to-pink-500 text-white px-3 py-1 rounded font-bold hover:shadow-lg transition text-sm">
            Open Dashboard →
        </a>
    </div>
</section>

<section class="bg-white rounded-xl p-6 shadow-lg border-2 border-green-200 mb-8">
    <h2 class="text-xl font-bold mb-4 text-green-600">🎨 Available Services</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($services as $service): ?>
            <div class="border-2 border-green-200 rounded-lg p-4 bg-green-50 hover:shadow-md transition">
                <h3 class="font-bold text-gray-800">✂️ <?php echo htmlspecialchars($service['name']); ?></h3>
                <p class="text-sm text-gray-700 mt-2">⏱️ Duration: <span class="font-bold"><?php echo htmlspecialchars($service['duration']); ?> mins</span></p>
                <p class="text-sm text-green-600 font-bold mt-1">💰 Price: $<?php echo number_format($service['price'], 2); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

// Require customer role
requireRole('customer');

$pageTitle = 'Customer Portal';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-gradient-to-r from-teal-500 to-cyan-500 rounded-xl p-8 mb-8 text-white shadow-lg">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold mb-2">✂️ Booking Portal</h1>
            <p class="text-teal-100">Welcome, <?php echo htmlspecialchars(getCurrentUserName()); ?>! Book your perfect appointment</p>
        </div>
        <div class="flex gap-3">
            <a href="my_bookings.php" class="bg-white text-teal-600 px-5 py-2 rounded-lg font-bold hover:shadow-lg transition">
                📅 My Bookings
            </a>
            <a href="book_appointment.php" class="bg-yellow-400 text-gray-800 px-5 py-2 rounded-lg font-bold hover:shadow-lg transition">
                ➕ Book Now
            </a>
        </div>
    </div>
</section>

<section class="grid md:grid-cols-2 gap-5 mb-8">
    <?php foreach ($barbers as $barber): ?>
        <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-teal-200 hover:shadow-xl hover:border-teal-400 transition">
            <div class="flex items-start justify-between mb-3">
                <h2 class="font-bold text-xl text-gray-800"><?php echo htmlspecialchars($barber['name']); ?></h2>
                <span class="text-2xl">👨‍💼</span>
            </div>
            <p class="text-sm mb-3">
                <span class="inline-block bg-teal-100 text-teal-700 px-3 py-1 rounded-full text-xs font-bold">
                    🎯 <?php echo htmlspecialchars($barber['specialization']); ?>
                </span>
            </p>
            
            <?php 
            $barberServices = getBarberServices($barber['id']);
            if (count($barberServices) > 0):
            ?>
            <div class="mt-4 pt-4 border-t-2 border-teal-200">
                <p class="text-xs font-bold text-teal-600 mb-3">✨ Services:</p>
                <div class="space-y-2">
                    <?php foreach ($barberServices as $service): ?>
                        <div class="text-sm text-gray-700 flex justify-between bg-teal-50 p-2 rounded">
                            <span><?php echo htmlspecialchars($service['name']); ?></span>
                            <span class="font-bold text-teal-600">$<?php echo number_format($service['price'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <p class="text-xs text-gray-500 mt-4 italic">No services listed yet.</p>
            <?php endif; ?>
            
            <a href="book_appointment.php?barber=<?php echo $barber['id']; ?>" class="mt-4 block w-full bg-gradient-to-r from-teal-500 to-cyan-500 text-white py-2 rounded-lg font-bold hover:shadow-lg transition text-center">
                📅 Book Appointment
            </a>
        </div>
    <?php endforeach; ?>
</section>

<section class="bg-white rounded-xl p-6 shadow-lg border-2 border-cyan-200">
    <h2 class="text-lg font-bold mb-4 text-cyan-600">⏰ Sample Available Slots</h2>
    <div class="flex flex-wrap gap-2 mb-4">
        <?php foreach ($sampleSlots as $slot): ?>
            <span class="px-3 py-1.5 bg-gradient-to-r from-cyan-100 to-teal-100 border-2 border-cyan-400 rounded-lg text-sm font-semibold text-cyan-700">
                🕐 <?php echo htmlspecialchars($slot); ?>
            </span>
        <?php endforeach; ?>
    </div>
    <p class="text-sm text-cyan-600 font-semibold">💡 Click "Book Now" to reserve your appointment with your preferred barber!</p>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

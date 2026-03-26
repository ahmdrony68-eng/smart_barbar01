<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

// Require customer role
requireRole('customer');

$pageTitle = 'Customer Portal';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold mb-2">Booking Portal</h1>
            <p class="text-slate-600 text-sm">Welcome, <?php echo htmlspecialchars(getCurrentUserName()); ?>!</p>
        </div>
        <div class="flex gap-3">
            <a href="my_bookings.php" class="bg-slate-600 text-white px-4 py-2 rounded font-medium hover:bg-slate-700">
                My Bookings
            </a>
            <a href="book_appointment.php" class="bg-blue-600 text-white px-4 py-2 rounded font-medium hover:bg-blue-700">
                + Book Now
            </a>
        </div>
    </div>
</section>

<section class="grid md:grid-cols-2 gap-4 mb-6">
    <?php foreach ($barbers as $barber): ?>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <h2 class="font-semibold text-lg"><?php echo htmlspecialchars($barber['name']); ?></h2>
            <p class="text-sm text-slate-600 mt-2">
                <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded">
                    <?php echo htmlspecialchars($barber['specialization']); ?>
                </span>
            </p>
            
            <?php 
            $barberServices = getBarberServices($barber['id']);
            if (count($barberServices) > 0):
            ?>
            <div class="mt-3 pt-3 border-t border-slate-200">
                <p class="text-xs font-medium text-slate-600 mb-2">Services:</p>
                <div class="space-y-1">
                    <?php foreach ($barberServices as $service): ?>
                        <div class="text-sm text-slate-700 flex justify-between">
                            <span><?php echo htmlspecialchars($service['name']); ?></span>
                            <span class="font-medium">$<?php echo number_format($service['price'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <p class="text-xs text-slate-500 mt-3">No services listed yet.</p>
            <?php endif; ?>
            
            <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded font-medium hover:bg-blue-700 text-sm">
                Book Appointment
            </button>
        </div>
    <?php endforeach; ?>
</section>

<section class="bg-white border border-slate-200 rounded-xl p-6">
    <h2 class="text-lg font-semibold mb-3">Sample Available Slots</h2>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($sampleSlots as $slot): ?>
            <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-sm"><?php echo htmlspecialchars($slot); ?></span>
        <?php endforeach; ?>
    </div>
    <p class="text-xs text-slate-500 mt-3">No live booking yet. This is static UI only.</p>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

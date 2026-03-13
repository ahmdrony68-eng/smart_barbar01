<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

// Require customer role
requireRole('customer');

$pageTitle = 'Customer Portal';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Customer Booking Portal</h1>
    <p class="text-slate-600 text-sm">Welcome, <?php echo htmlspecialchars(getCurrentUserName()); ?>! Browse and book your appointments.</p>
</section>

<section class="grid md:grid-cols-2 gap-4 mb-6">
    <?php foreach ($barbers as $barber): ?>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <h2 class="font-semibold"><?php echo htmlspecialchars($barber['name']); ?></h2>
            <p class="text-sm text-slate-600 mt-1">Specialization: <?php echo htmlspecialchars($barber['specialization']); ?></p>
            <p class="text-sm text-slate-600">Availability: <?php echo htmlspecialchars($barber['availability']); ?></p>
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

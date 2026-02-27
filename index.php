<?php
$pageTitle = 'Smart Barber Booking - Home';
require __DIR__ . '/data.php';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Smart Barber Booking & Management System</h1>
    <p class="text-slate-600">Basic week-1 starter in simple PHP and Tailwind CSS (no MySQL connection yet).</p>
</section>

<section class="grid md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <h2 class="font-semibold mb-2">Customer Portal</h2>
        <p class="text-sm text-slate-600">Browse barbers by specialization and view sample slots.</p>
        <a href="customer.php" class="text-blue-600 text-sm mt-2 inline-block">Open customer page</a>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <h2 class="font-semibold mb-2">Barber Dashboard</h2>
        <p class="text-sm text-slate-600">Placeholder panel for roster and upcoming bookings.</p>
        <a href="barber.php" class="text-blue-600 text-sm mt-2 inline-block">Open barber page</a>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <h2 class="font-semibold mb-2">Admin Dashboard</h2>
        <p class="text-sm text-slate-600">Placeholder panel for reports and system controls.</p>
        <a href="admin.php" class="text-blue-600 text-sm mt-2 inline-block">Open admin page</a>
    </div>
</section>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-3">Sample Services (Static Data)</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <?php foreach ($services as $service): ?>
            <div class="border border-slate-200 rounded-lg p-3">
                <h3 class="font-medium"><?php echo htmlspecialchars($service['name']); ?></h3>
                <p class="text-sm text-slate-600 mt-1">Duration: <?php echo htmlspecialchars($service['duration']); ?></p>
                <p class="text-sm text-slate-600">Price: <?php echo htmlspecialchars($service['price']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="bg-white border border-slate-200 rounded-xl p-6">
    <h2 class="text-lg font-semibold mb-3">Implementation Timeline (From Proposal)</h2>
    <ul class="space-y-2 text-sm text-slate-700">
        <li><span class="font-medium">Week 1:</span> Requirements, ERD, system design (current setup phase)</li>
        <li><span class="font-medium">Week 2:</span> Authentication and role-based access</li>
        <li><span class="font-medium">Week 3:</span> Barber profiles, specialization, services module</li>
        <li><span class="font-medium">Week 4:</span> Roster setup, slot generation, double-booking prevention</li>
        <li><span class="font-medium">Week 5:</span> Booking workflow, barber dashboard updates</li>
        <li><span class="font-medium">Week 6:</span> Admin reports, analytics, testing</li>
    </ul>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

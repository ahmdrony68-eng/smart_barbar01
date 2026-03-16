<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Smart Barber Booking - Home';
require __DIR__ . '/data.php';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Smart Barber Booking & Management System</h1>
    <?php if (isLoggedIn()): ?>
        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-800"><strong>✓ You are logged in as:</strong> <?php echo htmlspecialchars(getCurrentUserName()); ?> (<?php echo ucfirst(getCurrentRole()); ?>)</p>
        </div>
    <?php else: ?>
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800"><strong>Login Required:</strong> <a href="login.php" class="underline font-medium">Click here to login</a> to access the system.</p>
        </div>
    <?php endif; ?>
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
    <h2 class="text-lg font-semibold mb-3">Available Services</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <?php foreach ($services as $service): ?>
            <div class="border border-slate-200 rounded-lg p-3">
                <h3 class="font-medium"><?php echo htmlspecialchars($service['name']); ?></h3>
                <p class="text-sm text-slate-600 mt-1">Duration: <?php echo htmlspecialchars($service['duration']); ?> mins</p>
                <p class="text-sm text-slate-600">Price: $<?php echo number_format($service['price'], 2); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
        <li><span class="font-medium">Week 6:</span> Admin reports, analytics, testing</li>
    </ul>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

// Require admin role
requireRole('admin');

$pageTitle = 'Admin Dashboard';
require __DIR__ . '/partials/header.php';

// Get statistics
$stats = getBookingStats();
$barberList = getUsersByRole('barber');
$customerList = getUsersByRole('customer');
?>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Admin Dashboard</h1>
    <p class="text-slate-600 text-sm">Welcome, <?php echo htmlspecialchars(getCurrentUserName()); ?>! System management and oversight.</p>
</section>

<!-- Statistics -->
<div class="grid md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <p class="text-slate-600 text-sm mb-2">Total Bookings</p>
        <p class="text-3xl font-bold text-blue-600"><?php echo $stats['total_bookings'] ?? 0; ?></p>
        <p class="text-xs text-slate-500 mt-2">Last 30 days</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <p class="text-slate-600 text-sm mb-2">Confirmed</p>
        <p class="text-3xl font-bold text-green-600"><?php echo $stats['confirmed'] ?? 0; ?></p>
        <p class="text-xs text-slate-500 mt-2">Active bookings</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <p class="text-slate-600 text-sm mb-2">Completed</p>
        <p class="text-3xl font-bold text-purple-600"><?php echo $stats['completed'] ?? 0; ?></p>
        <p class="text-xs text-slate-500 mt-2">Finished services</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <p class="text-slate-600 text-sm mb-2">Revenue</p>
        <p class="text-3xl font-bold text-amber-600">$<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></p>
        <p class="text-xs text-slate-500 mt-2">Total earnings</p>
    </div>
</div>

<section class="grid md:grid-cols-2 gap-6 mb-6">
    <!-- Barber Management -->
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Barber Management</h2>
        <?php $barberList = getUsersByRole('barber'); ?>
        <div class="space-y-3">
            <?php foreach ($barberList as $barber): ?>
                <div class="border border-slate-200 rounded p-3">
                    <p class="font-medium text-sm"><?php echo htmlspecialchars($barber['name']); ?></p>
                    <p class="text-xs text-slate-600"><?php echo htmlspecialchars($barber['email']); ?></p>
                    <?php if ($barber['specialization']): ?>
                        <p class="text-xs text-slate-500 mt-1"><?php echo htmlspecialchars($barber['specialization']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if (count($barberList) === 0): ?>
                <p class="text-sm text-slate-500">No barbers registered yet.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Service Management -->
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Services</h2>
        <div class="space-y-2 mb-4">
            <?php foreach ($services as $service): ?>
                <div class="border border-slate-200 rounded p-3 text-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium"><?php echo htmlspecialchars($service['name']); ?></p>
                            <p class="text-xs text-slate-600"><?php echo $service['duration']; ?> mins</p>
                        </div>
                        <p class="font-semibold">$<?php echo number_format($service['price'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (count($services) === 0): ?>
                <p class="text-sm text-slate-500">No services configured.</p>
            <?php endif; ?>
        </div>
        <a href="manage_services.php" class="block w-full text-center bg-blue-600 text-white py-2 rounded font-medium hover:bg-blue-700">
            Manage Service Assignments
        </a>
    </div>
</section>

<section class="grid md:grid-cols-2 gap-6">
    <!-- Customer Overview -->
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Customers</h2>
        <?php $customerList = getUsersByRole('customer'); ?>
        <p class="text-2xl font-bold text-blue-600 mb-2"><?php echo count($customerList); ?></p>
        <p class="text-sm text-slate-600">Total registered customers</p>
    </div>
    
    <!-- System Stats -->
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">System</h2>
        <div class="space-y-2 text-sm">
            <p><span class="text-slate-600">Barbers:</span> <span class="font-medium"><?php echo count($barberList); ?></span></p>
            <p><span class="text-slate-600">Services:</span> <span class="font-medium"><?php echo count($services); ?></span></p>
            <p><span class="text-slate-600">Customers:</span> <span class="font-medium"><?php echo count($customerList); ?></span></p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

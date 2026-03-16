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

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Dashboard</h1>
    <p class="text-slate-600 text-sm">Manage your profile, services, and appointments.</p>
</section>

<div class="grid md:grid-cols-2 gap-6 mb-6">
    <!-- Profile -->
    <section class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Your Profile</h2>
        <div class="space-y-3 text-sm">
            <div>
                <p class="text-slate-600">Name</p>
                <p class="font-medium"><?php echo htmlspecialchars($currentBarber['name']); ?></p>
            </div>
            <div>
                <p class="text-slate-600">Email</p>
                <p class="font-medium"><?php echo htmlspecialchars($currentBarber['email']); ?></p>
            </div>
            <div>
                <p class="text-slate-600">Specialization</p>
                <p class="font-medium"><?php echo htmlspecialchars($currentBarber['specialization'] ?? 'Not set'); ?></p>
            </div>
        </div>
    </section>
    
    <!-- Services -->
    <section class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Your Services</h2>
        <?php if (count($myServices) > 0): ?>
            <div class="space-y-2">
                <?php foreach ($myServices as $service): ?>
                    <div class="border border-slate-200 rounded p-2 text-sm">
                        <div class="flex justify-between">
                            <span><?php echo htmlspecialchars($service['name']); ?></span>
                            <span class="font-medium">$<?php echo number_format($service['price'], 2); ?></span>
                        </div>
                        <p class="text-xs text-slate-600"><?php echo $service['duration']; ?> mins</p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-slate-600">No services assigned yet.</p>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

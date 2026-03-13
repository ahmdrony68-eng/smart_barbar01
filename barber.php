<?php
require __DIR__ . '/auth.php';

// Require barber role
requireRole('barber');

$pageTitle = 'Barber Dashboard';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Barber Dashboard</h1>
    <p class="text-slate-600 text-sm">Welcome, <?php echo htmlspecialchars(getCurrentUserName()); ?>! Manage your roster and appointments.</p>
</section>

<section class="grid md:grid-cols-2 gap-4">
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <h2 class="font-semibold mb-2">Roster Setup</h2>
        <ul class="text-sm text-slate-600 list-disc pl-5 space-y-1">
            <li>Set working days</li>
            <li>Set start and end time</li>
            <li>Save weekly schedule</li>
        </ul>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <h2 class="font-semibold mb-2">Upcoming Appointments</h2>
        <ul class="text-sm text-slate-600 list-disc pl-5 space-y-1">
            <li>View pending bookings</li>
            <li>Mark appointment completed</li>
            <li>Update booking status</li>
        </ul>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

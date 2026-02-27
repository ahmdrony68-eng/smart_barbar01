<?php
$pageTitle = 'Admin Dashboard';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Admin Dashboard (Basic)</h1>
    <p class="text-slate-600 text-sm">Placeholder for week-6 reporting and management features.</p>
</section>

<section class="grid md:grid-cols-3 gap-4">
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <h2 class="font-semibold mb-2">Barber Management</h2>
        <p class="text-sm text-slate-600">Add / edit / remove barber accounts (UI placeholder).</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <h2 class="font-semibold mb-2">Service Management</h2>
        <p class="text-sm text-slate-600">Manage service types, durations, and pricing (UI placeholder).</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <h2 class="font-semibold mb-2">Reports & Analytics</h2>
        <p class="text-sm text-slate-600">Daily/weekly bookings, estimated revenue, busiest barber.</p>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

// Require admin role
requireRole('admin');

$pageTitle = 'Service Management';
require __DIR__ . '/partials/header.php';

$barberList = getUsersByRole('barber');

// Handle service assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barber_id'], $_POST['service_id'])) {
    $barberId = intval($_POST['barber_id']);
    $serviceId = intval($_POST['service_id']);
    
    if (addBarberService($barberId, $serviceId)) {
        $success = "Service assigned successfully!";
    } else {
        $error = "Failed to assign service.";
    }
}
?>

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Service Management</h1>
    <p class="text-slate-600 text-sm">Assign services to barbers</p>
</section>

<?php if (isset($success)): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
        ✓ <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
        ✗ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<section class="grid md:grid-cols-2 gap-6 mb-6">
    <!-- Assign Service -->
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Assign Service to Barber</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label for="barber_id" class="block text-sm font-medium mb-2">Select Barber</label>
                <select name="barber_id" id="barber_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                    <option value="">-- Choose Barber --</option>
                    <?php foreach ($barberList as $barber): ?>
                        <option value="<?php echo $barber['id']; ?>">
                            <?php echo htmlspecialchars($barber['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="service_id" class="block text-sm font-medium mb-2">Select Service</label>
                <select name="service_id" id="service_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required>
                    <option value="">-- Choose Service --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>">
                            <?php echo htmlspecialchars($service['name']); ?> ($<?php echo number_format($service['price'], 2); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700">
                Assign Service
            </button>
        </form>
    </div>
    
    <!-- Barber Services Overview -->
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Barber Services</h2>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            <?php foreach ($barberList as $barber): 
                $barberServices = getBarberServices($barber['id']);
            ?>
                <div class="border border-slate-200 rounded p-3 text-sm">
                    <p class="font-medium text-slate-900"><?php echo htmlspecialchars($barber['name']); ?></p>
                    <?php if (count($barberServices) > 0): ?>
                        <ul class="text-xs text-slate-600 mt-1 space-y-1">
                            <?php foreach ($barberServices as $svc): ?>
                                <li>• <?php echo htmlspecialchars($svc['name']); ?> ($<?php echo number_format($svc['price'], 2); ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-xs text-slate-500 mt-1">No services assigned</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

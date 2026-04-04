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

<section class="bg-gradient-to-r from-indigo-500 to-pink-500 rounded-xl p-8 mb-8 text-white shadow-lg">
    <h1 class="text-3xl font-bold mb-2">🎨 Service Management</h1>
    <p class="text-indigo-100">Assign services to barbers</p>
</section>

<?php if (isset($success)): ?>
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg mb-4 font-bold shadow-md">
        ✓ <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-4 font-bold shadow-md">
        ✗ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<section class="grid md:grid-cols-2 gap-6 mb-6">
    <!-- Assign Service -->
    <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-indigo-200">
        <h2 class="text-lg font-bold mb-4 text-indigo-600">✂️ Assign Service to Barber</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label for="barber_id" class="block text-sm font-bold text-indigo-700 mb-2">👨‍💼 Select Barber</label>
                <select name="barber_id" id="barber_id" class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 font-medium" required>
                    <option value="">-- Choose Barber --</option>
                    <?php foreach ($barberList as $barber): ?>
                        <option value="<?php echo $barber['id']; ?>">
                            👨‍💼 <?php echo htmlspecialchars($barber['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="service_id" class="block text-sm font-bold text-pink-700 mb-2">🎨 Select Service</label>
                <select name="service_id" id="service_id" class="w-full px-4 py-2 border-2 border-pink-300 rounded-lg focus:outline-none focus:border-pink-500 focus:ring-2 focus:ring-pink-200 font-medium" required>
                    <option value="">-- Choose Service --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>">
                            🎨 <?php echo htmlspecialchars($service['name']); ?> ($<?php echo number_format($service['price'], 2); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-indigo-500 to-pink-500 text-white py-3 rounded-lg font-bold hover:shadow-lg transition text-lg">
                ✓ Assign Service
            </button>
        </form>
    </div>
    
    <!-- Barber Services Overview -->
    <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-pink-200">
        <h2 class="text-lg font-bold mb-4 text-pink-600">👨‍💼 Barber Services</h2>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            <?php foreach ($barberList as $barber): 
                $barberServices = getBarberServices($barber['id']);
            ?>
                <div class="border-2 border-pink-200 rounded-lg p-3 text-sm bg-pink-50 hover:shadow-md transition">
                    <p class="font-bold text-gray-800">👨‍💼 <?php echo htmlspecialchars($barber['name']); ?></p>
                    <?php if (count($barberServices) > 0): ?>
                        <ul class="text-xs text-gray-700 mt-2 space-y-1">
                            <?php foreach ($barberServices as $svc): ?>
                                <li class="font-semibold">✂️ <?php echo htmlspecialchars($svc['name']); ?> <span class="text-pink-600 font-bold">$<?php echo number_format($svc['price'], 2); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-xs text-gray-600 mt-2 italic">📌 No services assigned</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>

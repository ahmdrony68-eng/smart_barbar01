<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

requireRole('customer');

$customerId = getCurrentUser();
$barbers = getBarbers();
$services = getServices();
$booking_result = null;

// Handle booking creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book') {
    $barberId = intval($_POST['barber_id'] ?? 0);
    $serviceId = intval($_POST['service_id'] ?? 0);
    $bookingDate = $_POST['booking_date'] ?? '';
    $bookingTime = $_POST['booking_time'] ?? '';
    
    if ($barberId && $serviceId && $bookingDate && $bookingTime) {
        // Generate slots for the date if not exists
        generateSlotsForDate($barberId, $bookingDate);
        
        // Create booking
        $booking_result = createBooking($customerId, $barberId, $serviceId, $bookingDate, $bookingTime);
    }
}

// Handle slot retrieval via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'get_slots') {
    header('Content-Type: application/json');
    $barberId = intval($_POST['barber_id'] ?? 0);
    $date = $_POST['date'] ?? '';
    
    if ($barberId && $date) {
        generateSlotsForDate($barberId, $date);
        $slots = getAvailableSlots($barberId, $date);
        echo json_encode($slots);
    } else {
        echo json_encode([]);
    }
    exit;
}

$pageTitle = 'Book Appointment';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl p-8 mb-8 text-white shadow-lg">
    <h1 class="text-3xl font-bold mb-2">✂️ Book Your Appointment</h1>
    <p class="text-green-100">Select a barber, service, date, and time to book your perfect appointment</p>
</section>

<?php if ($booking_result): ?>
    <div class="<?php echo ($booking_result['success'] ? 'bg-green-50 border-l-4 border-green-500 text-green-700' : 'bg-red-50 border-l-4 border-red-500 text-red-700'); ?> px-6 py-4 rounded-lg mb-6 shadow-md font-bold">
        <?php if ($booking_result['success']): ?>
            ✓ <strong>Booking Confirmed!</strong> Your appointment has been confirmed. You can view it in your booking history.
        <?php else: ?>
            ✗ <?php echo htmlspecialchars($booking_result['message'] ?? 'Booking failed'); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="grid md:grid-cols-3 gap-6">
    <!-- Booking Form -->
    <div class="md:col-span-2">
        <form method="POST" class="bg-white rounded-xl p-6 shadow-lg border-2 border-green-200 space-y-5">
            <input type="hidden" name="action" value="book">
            
            <div>
                <label for="barber_id" class="block text-sm font-bold text-green-700 mb-2">👨‍💼 Select Barber</label>
                <select name="barber_id" id="barber_id" class="w-full px-4 py-2 border-2 border-green-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 font-medium" required onchange="updateServices(this.value); updateSlots();">
                    <option value="">-- Choose Barber --</option>
                    <?php foreach ($barbers as $barber): ?>
                        <option value="<?php echo $barber['id']; ?>">
                            👨‍💼 <?php echo htmlspecialchars($barber['name'] . ' - ' . $barber['specialization']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="service_id" class="block text-sm font-bold text-emerald-700 mb-2">🎨 Select Service</label>
                <select name="service_id" id="service_id" class="w-full px-4 py-2 border-2 border-emerald-300 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 font-medium" required>
                    <option value="">-- Choose Service --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>">
                            🎨 <?php echo htmlspecialchars($service['name'] . ' - $' . number_format($service['price'], 2) . ' (' . $service['duration'] . ' mins)'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="booking_date" class="block text-sm font-bold text-green-700 mb-2">📅 Select Date</label>
                <input 
                    type="date" 
                    name="booking_date" 
                    id="booking_date" 
                    min="<?php echo date('Y-m-d'); ?>"
                    max="<?php echo date('Y-m-d', strtotime('+90 days')); ?>"
                    class="w-full px-4 py-2 border-2 border-green-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200 font-medium" 
                    required
                    onchange="updateSlots()"
                >
            </div>
            
            <div>
                <label for="booking_time" class="block text-sm font-bold text-emerald-700 mb-2">🕐 Select Time</label>
                <select name="booking_time" id="booking_time" class="w-full px-4 py-2 border-2 border-emerald-300 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 font-medium" required disabled>
                    <option value="">-- Choose Time --</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-500 text-white py-3 rounded-lg font-bold hover:shadow-lg transition text-lg">
                ✓ Confirm Booking
            </button>
        </form>
    </div>
    
    <!-- Booking Summary -->
    <div class="bg-gradient-to-b from-green-50 to-emerald-50 rounded-xl p-6 shadow-lg border-2 border-green-300">
        <h3 class="font-bold text-lg mb-4 text-green-700">📋 Booking Summary</h3>
        <div class="space-y-4 text-sm">
            <div class="pb-3 border-b-2 border-green-200">
                <p class="text-green-600 font-semibold text-xs">Barber</p>
                <p class="font-bold text-gray-800 text-base" id="summary_barber">-</p>
            </div>
            <div class="pb-3 border-b-2 border-green-200">
                <p class="text-emerald-600 font-semibold text-xs">Service</p>
                <p class="font-bold text-gray-800 text-base" id="summary_service">-</p>
            </div>
            <div class="pb-3 border-b-2 border-green-200">
                <p class="text-green-600 font-semibold text-xs">Date</p>
                <p class="font-bold text-gray-800 text-base" id="summary_date">-</p>
            </div>
            <div class="pb-3 border-b-2 border-green-200">
                <p class="text-emerald-600 font-semibold text-xs">Time</p>
                <p class="font-bold text-gray-800 text-base" id="summary_time">-</p>
            </div>
            <div class="pt-3">
                <p class="text-green-600 font-semibold text-xs">Price</p>
                <p class="font-bold text-2xl text-green-600" id="summary_price">-</p>
            </div>
        </div>
    </div>
</div>

<script>
function updateServices(barberId) {
    document.getElementById('summary_barber').textContent = document.querySelector(`#barber_id option[value="${barberId}"]`)?.textContent || '-';
}

function updateSlots() {
    const barberId = document.getElementById('barber_id').value;
    const date = document.getElementById('booking_date').value;
    const timeSelect = document.getElementById('booking_time');
    
    // Reset time select
    timeSelect.innerHTML = '<option value="">-- Choose Time --</option>';
    timeSelect.disabled = true;
    
    if (!barberId) {
        timeSelect.innerHTML = '<option disabled>Please select a barber first</option>';
        return;
    }
    
    if (!date) {
        timeSelect.innerHTML = '<option disabled>Please select a date</option>';
        return;
    }
    
    document.getElementById('summary_date').textContent = new Date(date).toLocaleDateString();
    
    // Show loading message
    timeSelect.innerHTML = '<option disabled>Loading times...</option>';
    
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_slots&barber_id=' + encodeURIComponent(barberId) + '&date=' + encodeURIComponent(date)
    })
    .then(r => {
        if (!r.ok) throw new Error('Network error: ' + r.status);
        return r.json();
    })
    .then(slots => {
        const select = document.getElementById('booking_time');
        select.innerHTML = '<option value="">-- Choose Time --</option>';
        
        if (!Array.isArray(slots) || slots.length === 0) {
            select.disabled = true;
            select.innerHTML = '<option disabled>❌ No available slots for this date</option>';
        } else {
            slots.forEach(slot => {
                const opt = document.createElement('option');
                opt.value = slot.slot_time;
                opt.textContent = slot.slot_time;
                select.appendChild(opt);
            });
            select.disabled = false;
        }
    })
    .catch(error => {
        console.error('Slot loading error:', error);
        timeSelect.innerHTML = '<option disabled>❌ Error loading slots</option>';
        timeSelect.disabled = true;
    });
}

document.getElementById('service_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    document.getElementById('summary_service').textContent = option.text || '-';
    
    // Extract price from option text
    const price = option.text.match(/\$[\d.]+/)?.[0] || '-';
    document.getElementById('summary_price').textContent = price;
});

document.getElementById('booking_time').addEventListener('change', function() {
    document.getElementById('summary_time').textContent = this.value || '-';
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>

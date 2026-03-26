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

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Book Your Appointment</h1>
    <p class="text-slate-600 text-sm">Select a barber, service, date, and time to book your appointment</p>
</section>

<?php if ($booking_result): ?>
    <div class="<?php echo ($booking_result['success'] ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'); ?> border px-4 py-3 rounded-lg mb-4">
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
        <form method="POST" class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <input type="hidden" name="action" value="book">
            
            <div>
                <label for="barber_id" class="block text-sm font-medium mb-2">Select Barber</label>
                <select name="barber_id" id="barber_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required onchange="updateServices(this.value)">
                    <option value="">-- Choose Barber --</option>
                    <?php foreach ($barbers as $barber): ?>
                        <option value="<?php echo $barber['id']; ?>">
                            <?php echo htmlspecialchars($barber['name'] . ' - ' . $barber['specialization']); ?>
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
                            <?php echo htmlspecialchars($service['name'] . ' - $' . number_format($service['price'], 2) . ' (' . $service['duration'] . ' mins)'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="booking_date" class="block text-sm font-medium mb-2">Select Date</label>
                <input 
                    type="date" 
                    name="booking_date" 
                    id="booking_date" 
                    min="<?php echo date('Y-m-d'); ?>"
                    max="<?php echo date('Y-m-d', strtotime('+90 days')); ?>"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg" 
                    required
                    onchange="updateSlots()"
                >
            </div>
            
            <div>
                <label for="booking_time" class="block text-sm font-medium mb-2">Select Time</label>
                <select name="booking_time" id="booking_time" class="w-full px-3 py-2 border border-slate-300 rounded-lg" required disabled>
                    <option value="">-- Choose Time --</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded font-medium hover:bg-green-700">
                Confirm Booking
            </button>
        </form>
    </div>
    
    <!-- Booking Summary -->
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h3 class="font-semibold mb-4">Booking Summary</h3>
        <div class="space-y-3 text-sm">
            <div>
                <p class="text-slate-600">Barber</p>
                <p class="font-medium" id="summary_barber">-</p>
            </div>
            <div>
                <p class="text-slate-600">Service</p>
                <p class="font-medium" id="summary_service">-</p>
            </div>
            <div>
                <p class="text-slate-600">Date</p>
                <p class="font-medium" id="summary_date">-</p>
            </div>
            <div>
                <p class="text-slate-600">Time</p>
                <p class="font-medium" id="summary_time">-</p>
            </div>
            <div class="border-t border-slate-200 pt-3">
                <p class="text-slate-600">Price</p>
                <p class="font-semibold text-lg text-green-600" id="summary_price">-</p>
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
    
    if (!barberId || !date) {
        document.getElementById('booking_time').disabled = true;
        return;
    }
    
    document.getElementById('summary_date').textContent = new Date(date).toLocaleDateString();
    
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_slots&barber_id=' + barberId + '&date=' + date
    })
    .then(r => r.json())
    .then(slots => {
        const select = document.getElementById('booking_time');
        select.innerHTML = '<option value="">-- Choose Time --</option>';
        
        if (slots.length === 0) {
            select.disabled = true;
            select.innerHTML += '<option disabled>No available slots</option>';
        } else {
            slots.forEach(slot => {
                const opt = document.createElement('option');
                opt.value = slot.slot_time;
                opt.textContent = slot.slot_time;
                select.appendChild(opt);
            });
            select.disabled = false;
        }
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

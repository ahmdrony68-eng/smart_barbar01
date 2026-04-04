<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/data.php';

requireRole('barber');

$barberId = getCurrentUser();
$currentRoster = getBarberRoster($barberId);
$success = '';
$error = '';

// Convert to array by day for easier form handling
$rosterByDay = [];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

foreach ($days as $day) {
    $rosterByDay[$day] = [
        'enabled' => false,
        'start' => '09:00',
        'end' => '18:00'
    ];
}

foreach ($currentRoster as $item) {
    $rosterByDay[$item['day_of_week']] = [
        'enabled' => true,
        'start' => substr($item['start_time'], 0, 5),
        'end' => substr($item['end_time'], 0, 5)
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roster = [];
    foreach ($days as $day) {
        $key = 'day_' . strtolower($day);
        if (isset($_POST[$key])) {
            $roster[$day] = [
                'enabled' => true,
                'start' => $_POST[$key . '_start'] ?? '09:00',
                'end' => $_POST[$key . '_end'] ?? '18:00'
            ];
        } else {
            $roster[$day] = [
                'enabled' => false,
                'start' => '09:00',
                'end' => '18:00'
            ];
        }
    }
    
    if (saveBarberRoster($barberId, $roster)) {
        $success = "Roster updated successfully!";
        $rosterByDay = $roster;
    } else {
        $error = "Failed to update roster.";
    }
}

$pageTitle = 'Manage Roster';
require __DIR__ . '/partials/header.php';
?>

<section class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-8 mb-8 text-white shadow-lg">
    <h1 class="text-3xl font-bold mb-2">📅 Manage Your Roster</h1>
    <p class="text-amber-100">Set your working hours for each day of the week</p>
</section>

<?php if ($success): ?>
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg mb-4 font-bold shadow-md">
        ✓ <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-4 font-bold shadow-md">
        ✗ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white rounded-xl p-6 shadow-lg border-2 border-amber-200 mb-6">
    <div class="space-y-3">
        <?php foreach ($days as $idx => $day): 
            $dayEmojis = ['Monday' => '🌟', 'Tuesday' => '⭐', 'Wednesday' => '✨', 'Thursday' => '🌙', 'Friday' => '🎉', 'Saturday' => '🎊', 'Sunday' => '😴'];
            $emoji = $dayEmojis[$day] ?? '📅';
        ?>
            <div class="border-2 border-amber-200 rounded-lg p-4 bg-amber-50 hover:shadow-md transition">
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="day_<?php echo strtolower($day); ?>"
                                <?php echo $rosterByDay[$day]['enabled'] ? 'checked' : ''; ?>
                                class="w-5 h-5 text-amber-600 rounded focus:ring-2 focus:ring-amber-400"
                                onchange="toggleDayInputs(this)"
                            >
                            <span class="font-bold text-lg text-gray-800 w-20"><?php echo $emoji; ?> <?php echo $day; ?></span>
                        </label>
                    </div>
                    
                    <div class="flex gap-2 items-center">
                        <div>
                            <label class="block text-xs font-bold text-amber-600 mb-1">Start</label>
                            <input 
                                type="time" 
                                name="day_<?php echo strtolower($day); ?>_start"
                                value="<?php echo htmlspecialchars($rosterByDay[$day]['start']); ?>"
                                class="px-3 py-2 border-2 border-amber-300 rounded-lg font-semibold focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                                <?php echo !$rosterByDay[$day]['enabled'] ? 'disabled' : ''; ?>
                            >
                        </div>
                        <span class="font-bold text-gray-600">→</span>
                        <div>
                            <label class="block text-xs font-bold text-orange-600 mb-1">End</label>
                            <input 
                                type="time" 
                                name="day_<?php echo strtolower($day); ?>_end"
                                value="<?php echo htmlspecialchars($rosterByDay[$day]['end']); ?>"
                                class="px-3 py-2 border-2 border-orange-300 rounded-lg font-semibold focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                                <?php echo !$rosterByDay[$day]['enabled'] ? 'disabled' : ''; ?>
                            >
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <button type="submit" class="mt-6 w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-lg font-bold hover:shadow-lg transition text-lg">
        💾 Save Roster
    </button>
</form>

<script>
function toggleDayInputs(checkbox) {
    const dayName = checkbox.name.replace('day_', '').replace(/_/g, ' ');
    const startInput = document.querySelector('input[name="' + checkbox.name + '_start"]');
    const endInput = document.querySelector('input[name="' + checkbox.name + '_end"]');
    
    if (startInput) startInput.disabled = !checkbox.checked;
    if (endInput) endInput.disabled = !checkbox.checked;
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>

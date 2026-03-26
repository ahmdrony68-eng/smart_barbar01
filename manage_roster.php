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

<section class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-2">Manage Your Roster</h1>
    <p class="text-slate-600 text-sm">Set your working hours for each day of the week</p>
</section>

<?php if ($success): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
        ✓ <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
        ✗ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
    <div class="space-y-4">
        <?php foreach ($days as $day): ?>
            <div class="border border-slate-200 rounded-lg p-4">
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="day_<?php echo strtolower($day); ?>"
                                <?php echo $rosterByDay[$day]['enabled'] ? 'checked' : ''; ?>
                                class="w-4 h-4"
                                onchange="toggleDayInputs(this)"
                            >
                            <span class="font-medium w-20"><?php echo $day; ?></span>
                        </label>
                    </div>
                    
                    <div class="flex gap-2 items-center">
                        <input 
                            type="time" 
                            name="day_<?php echo strtolower($day); ?>_start"
                            value="<?php echo htmlspecialchars($rosterByDay[$day]['start']); ?>"
                            class="px-3 py-2 border border-slate-300 rounded"
                            <?php echo !$rosterByDay[$day]['enabled'] ? 'disabled' : ''; ?>
                        >
                        <span>to</span>
                        <input 
                            type="time" 
                            name="day_<?php echo strtolower($day); ?>_end"
                            value="<?php echo htmlspecialchars($rosterByDay[$day]['end']); ?>"
                            class="px-3 py-2 border border-slate-300 rounded"
                            <?php echo !$rosterByDay[$day]['enabled'] ? 'disabled' : ''; ?>
                        >
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <button type="submit" class="mt-6 w-full bg-blue-600 text-white py-2 rounded font-medium hover:bg-blue-700">
        Save Roster
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

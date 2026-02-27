<?php
$title = $pageTitle ?? 'Smart Barber Booking';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="index.php" class="text-xl font-semibold">Smart Barber</a>
            <nav class="flex gap-4 text-sm">
                <a href="index.php" class="hover:text-blue-600">Home</a>
                <a href="customer.php" class="hover:text-blue-600">Customer</a>
                <a href="barber.php" class="hover:text-blue-600">Barber</a>
                <a href="admin.php" class="hover:text-blue-600">Admin</a>
            </nav>
        </div>
    </header>
    <main class="max-w-6xl mx-auto px-4 py-8">

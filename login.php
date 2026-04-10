<?php
require __DIR__ . '/auth.php';

// If already logged in, redirect to home
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        if (authenticateUser($email, $password)) {
            // Redirect based on role
            $role = getCurrentRole();
            if ($role === 'customer') {
                header('Location: customer.php');
            } elseif ($role === 'barber') {
                header('Location: barber.php');
            } elseif ($role === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Login - Smart Barber Booking';
$isLoginPage = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 text-gray-900 min-h-screen">
    <header class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 py-5 flex items-center justify-between">
            <a href="index.php" class="text-3xl font-bold text-white">✂️ Smart Barber</a>
            <nav class="flex gap-4 text-sm">
                <a href="index.php" class="text-white hover:text-yellow-200 font-semibold transition">🏠 Home</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <section class="bg-white rounded-2xl p-8 mb-6 shadow-2xl border-2 border-indigo-200">
                <h1 class="text-3xl font-bold mb-2 text-indigo-600">🔑 Login</h1>
                <p class="text-gray-600 mb-6">Access your barber booking account</p>

                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm font-bold">
                        ✗ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-bold text-indigo-700 mb-1">📧 Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
                               class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 font-medium"
                               required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-bold text-purple-700 mb-1">🔐 Password</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-500 font-medium"
                               required>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg font-bold hover:shadow-lg transition text-lg">
                        ✓ Login
                    </button>
                </form>
            </section>

            <section class="bg-white rounded-2xl p-6 shadow-xl border-2 border-blue-200">
                <h2 class="font-bold mb-4 text-lg text-blue-600">🎫 Demo Credentials</h2>
                <div class="space-y-2 text-sm">
                    <div class="border-l-4 border-cyan-500 pl-4 bg-cyan-50 p-2 rounded">
                        <p class="font-bold text-cyan-700">👤 Customer 1</p>
                        <p class="text-gray-700 font-semibold text-xs">📧 customer1@email.com / 🔐 customer123</p>
                    </div>
                    <div class="border-l-4 border-cyan-500 pl-4 bg-cyan-50 p-2 rounded">
                        <p class="font-bold text-cyan-700">👤 Customer 2</p>
                        <p class="text-gray-700 font-semibold text-xs">📧 customer2@email.com / 🔐 customer456</p>
                    </div>
                    <div class="border-l-4 border-cyan-500 pl-4 bg-cyan-50 p-2 rounded">
                        <p class="font-bold text-cyan-700">👤 Customer 3</p>
                        <p class="text-gray-700 font-semibold text-xs">📧 customer3@email.com / 🔐 customer789</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4 bg-green-50 p-2 rounded">
                        <p class="font-bold text-green-700">👨‍💼 Barber 1</p>
                        <p class="text-gray-700 font-semibold text-xs">📧 barber1@email.com / 🔐 barber123</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4 bg-green-50 p-2 rounded">
                        <p class="font-bold text-green-700">👨‍💼 Barber 2</p>
                        <p class="text-gray-700 font-semibold text-xs">📧 barber2@email.com / 🔐 barber456</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4 bg-green-50 p-2 rounded">
                        <p class="font-bold text-green-700">👨‍💼 Barber 3</p>
                        <p class="text-gray-700 font-semibold text-xs">📧 barber3@email.com / 🔐 barber789</p>
                    </div>
                    <div class="border-l-4 border-orange-500 pl-4 bg-orange-50 p-2 rounded">
                        <p class="font-bold text-orange-700">👨‍💼 Admin</p>
                        <p class="text-gray-700 font-semibold text-xs">📧 admin@email.com / 🔐 admin123</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white mt-12 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 py-4 text-center text-sm font-semibold">
            ✂️ Smart Barber Booking System - Professional Appointment Management
        </div>
    </footer>
</body>
</html>

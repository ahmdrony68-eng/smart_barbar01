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
<body class="bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="index.php" class="text-xl font-semibold">Smart Barber</a>
            <nav class="flex gap-4 text-sm">
                <a href="index.php" class="hover:text-blue-600">Home</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <section class="bg-white border border-slate-200 rounded-xl p-8 mb-6">
                <h1 class="text-2xl font-bold mb-2">Login</h1>

                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium mb-1">Password</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700">
                        Login
                    </button>
                </form>
            </section>

            <section class="bg-white border border-slate-200 rounded-xl p-6">
                <h2 class="font-semibold mb-3">Demo Credentials</h2>
                <div class="space-y-3 text-sm">
                    <div class="border-l-4 border-blue-500 pl-3">
                        <p class="font-medium text-slate-900">Customer</p>
                        <p class="text-slate-600">Email: customer1@email.com</p>
                        <p class="text-slate-600">Password: customer123</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-3">
                        <p class="font-medium text-slate-900">Barber</p>
                        <p class="text-slate-600">Email: barber1@email.com</p>
                        <p class="text-slate-600">Password: barber123</p>
                    </div>
                    <div class="border-l-4 border-purple-500 pl-3">
                        <p class="font-medium text-slate-900">Admin</p>
                        <p class="text-slate-600">Email: admin@email.com</p>
                        <p class="text-slate-600">Password: admin123</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 mt-12">
        <div class="max-w-6xl mx-auto px-4 py-4 text-center text-xs text-slate-500">
            Smart Barber Booking System - Week 2 (Authentication)
        </div>
    </footer>
</body>
</html>

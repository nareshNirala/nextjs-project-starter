<?php
require_once 'config.php';
require_once 'db.php';
require_once 'php/auth.php';

// Handle admin login
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (adminLogin($username, $password)) {
        header('Location: admin.php');
        exit();
    } else {
        $login_error = 'Invalid username or password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['is_admin']);
    header('Location: admin.php');
    exit();
}

// Handle quote operations
$message = '';
if (isAdmin()) {
    // Add new quote
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_quote'])) {
        $quote_text = $_POST['quote_text'] ?? '';
        $author = $_POST['author'] ?? '';
        
        if (!empty($quote_text) && !empty($author)) {
            if (addQuote($quote_text, $author)) {
                $message = 'Quote added successfully!';
            } else {
                $message = 'Error adding quote';
            }
        }
    }
    
    // Delete quote
    if (isset($_GET['delete'])) {
        $quote_id = intval($_GET['delete']);
        if (deleteQuote($quote_id)) {
            $message = 'Quote deleted successfully!';
        } else {
            $message = 'Error deleting quote';
        }
    }
}

// Get all quotes
$quotes = [];
if (isAdmin()) {
    $quotes = getAllQuotes();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen">
        <?php if (!isAdmin()): ?>
            <!-- Admin Login -->
            <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 to-purple-50">
                <div class="max-w-md w-full mx-4">
                    <div class="text-center mb-8">
                        <div class="bg-indigo-600 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center shadow-lg">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Login</h1>
                        <p class="text-gray-600">Manage quotes and application settings</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <?php if ($login_error): ?>
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                                <?php echo htmlspecialchars($login_error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <input type="text" name="username" required 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input type="password" name="password" required 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <button type="submit" name="admin_login" 
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg font-semibold transition duration-200">
                                Sign In
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Admin Dashboard -->
            <div class="min-h-screen">
                <!-- Header -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center py-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                                <p class="text-sm text-gray-600">Manage quotes for <?php echo APP_NAME; ?></p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <a href="index.php" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">View Site</a>
                                <a href="admin.php?logout=1" 
                                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main Content -->
                <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 rounded-lg <?php echo strpos($message, 'success') !== false ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center">
                                <div class="bg-indigo-100 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Quotes</p>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo count($quotes); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center">
                                <div class="bg-green-100 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        <?php
                                        $pdo = getDBConnection();
                                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                                        $result = $stmt->fetch();
                                        echo $result['count'];
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <div class="flex items-center">
                                <div class="bg-purple-100 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">App Name</p>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo APP_NAME; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add New Quote -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Add New Quote</h2>
                        <form method="POST" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quote Text</label>
                                <textarea name="quote_text" rows="3" required 
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                          placeholder="Enter the quote text..."></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                                <input type="text" name="author" required 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Enter the author name...">
                            </div>
                            
                            <button type="submit" name="add_quote" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                                Add Quote
                            </button>
                        </form>
                    </div>

                    <!-- Existing Quotes -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Existing Quotes</h2>
                        
                        <?php if (empty($quotes)): ?>
                            <p class="text-gray-500 text-center py-8">No quotes found. Add your first quote above!</p>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quote</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($quotes as $quote): ?>
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                                    <?php echo htmlspecialchars($quote['quote_text']); ?>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    <?php echo htmlspecialchars($quote['author']); ?>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    <?php echo date('M j, Y', strtotime($quote['created_at'])); ?>
                                                </td>
                                                <td class="px-6 py-4 text-sm">
                                                    <a href="admin.php?delete=<?php echo $quote['id']; ?>" 
                                                       onclick="return confirm('Are you sure you want to delete this quote?')"
                                                       class="text-red-600 hover:text-red-900 font-medium">
                                                        Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </main>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

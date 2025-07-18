<?php
require_once 'config.php';
require_once 'db.php';
require_once 'php/auth.php';

// Require user to be logged in
requireLogin();

// Get current user and today's quote
$user = getCurrentUser();
$todaysQuote = getTodaysQuote();

if (!$todaysQuote) {
    $todaysQuote = [
        'quote_text' => 'Welcome to My Daily Quote! Add some quotes in the admin panel.',
        'author' => 'System'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .quote-text { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle fixed top-4 left-4 z-50 bg-white p-2 rounded-lg shadow-lg md:hidden">
            <div class="w-6 h-0.5 bg-gray-600 mb-1"></div>
            <div class="w-6 h-0.5 bg-gray-600 mb-1"></div>
            <div class="w-6 h-0.5 bg-gray-600"></div>
        </button>

        <!-- Sidebar -->
        <div class="sidebar bg-white w-64 shadow-lg flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-600 rounded-lg w-10 h-10 flex items-center justify-center">
                        <div class="text-white font-bold text-lg">Q</div>
                    </div>
                    <h1 class="text-xl font-bold text-gray-900"><?php echo APP_NAME; ?></h1>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-6">
                <ul class="space-y-2">
                    <li>
                        <a href="index.php" class="bg-indigo-50 text-indigo-700 group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></div>
                            <span class="font-semibold">Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="customize.php" class="text-gray-700 hover:bg-gray-50 group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                            <div class="w-2 h-2 bg-gray-400 rounded-sm mr-3"></div>
                            <span>Customize</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin.php" class="text-gray-700 hover:bg-gray-50 group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                            <div class="w-2 h-2 bg-gray-400 rounded mr-3"></div>
                            <span>Admin</span>
                        </a>
                    </li>
                </ul>
            <!-- User Profile -->
            <div class="p-6 border-t border-gray-200">
                <div class="flex items-center space-x-3 mb-4">
                    <img class="h-10 w-10 rounded-full object-cover" 
                         src="<?php echo htmlspecialchars($user['picture_url'] ?? 'https://via.placeholder.com/40'); ?>" 
                         alt="Profile">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            <?php echo htmlspecialchars($user['name']); ?>
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </p>
                    </div>
                </div>
                <a href="logout.php" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium transition duration-200 flex items-center justify-center">
                    <div class="w-1 h-3 bg-gray-500 mr-2"></div>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-8">
                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Today's Quote</h2>
                    <p class="text-gray-600">Get inspired with your daily dose of wisdom</p>
                </div>

                <!-- Quote Card -->
                <div class="max-w-4xl mx-auto">
                    <div id="quote-card" class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl p-12 text-white shadow-2xl relative overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <svg class="w-full h-full" viewBox="0 0 100 100" fill="none">
                                <defs>
                                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                                    </pattern>
                                </defs>
                                <rect width="100" height="100" fill="url(#grid)" />
                            </svg>
                        </div>

                        <!-- Quote Content -->
                        <div class="relative z-10 text-center">
                            <div class="mb-8">
                                <svg class="w-16 h-16 mx-auto mb-6 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                                </svg>
                            </div>
                            
                            <blockquote class="quote-text text-2xl md:text-3xl lg:text-4xl font-medium leading-relaxed mb-8">
                                "<?php echo htmlspecialchars($todaysQuote['quote_text']); ?>"
                            </blockquote>
                            
                            <cite class="text-xl font-semibold opacity-90">
                                — <?php echo htmlspecialchars($todaysQuote['author']); ?>
                            </cite>
                        </div>
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mt-8 justify-center">
                        <button onclick="downloadQuoteImage()" 
                                class="btn-loading bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-xl font-semibold transition duration-200 flex items-center justify-center space-x-3 shadow-lg"
                                data-loading-text="Generating...">
                            <div class="w-1 h-4 bg-white mr-2"></div>
                            <span>Download as Image</span>
                        </button>
                        
                        <a href="customize.php?quote_id=<?php echo $todaysQuote['id'] ?? 0; ?>" 
                           class="bg-white hover:bg-gray-50 text-gray-900 border-2 border-gray-200 px-8 py-4 rounded-xl font-semibold transition duration-200 flex items-center justify-center space-x-3 shadow-lg">
                            <div class="w-2 h-2 bg-gray-600 rounded-sm rotate-45 mr-2"></div>
                            <span>Customize This Quote</span>
                        </a>
                    </div>
                </div>
                <!-- Stats or Additional Info -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    <div class="bg-white rounded-2xl p-6 shadow-lg text-center">
                        <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4">
                            <div class="w-4 h-4 bg-blue-600 rounded-full"></div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Daily Inspiration</h3>
                        <p class="text-gray-600 text-sm">Get a new quote every day to inspire your journey</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-lg text-center">
                        <div class="bg-green-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4">
                            <div class="w-5 h-1 bg-green-600 rounded-full"></div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Easy Download</h3>
                        <p class="text-gray-600 text-sm">Save quotes as beautiful images to share anywhere</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-lg text-center">
                        <div class="bg-purple-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4">
                            <div class="w-3 h-3 bg-purple-600 rounded-sm rotate-45"></div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Customize</h3>
                        <p class="text-gray-600 text-sm">Personalize quotes with your own style and colors</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadQuoteImage() {
            const quoteCard = document.getElementById('quote-card');
            
            // Show loading state
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generating...';
            button.disabled = true;
            
            html2canvas(quoteCard, {
                backgroundColor: null,
                scale: 2,
                useCORS: true,
                allowTaint: true
            }).then(canvas => {
                // Create download link
                const link = document.createElement('a');
                link.download = 'daily-quote-' + new Date().toISOString().split('T')[0] + '.png';
                link.href = canvas.toDataURL();
                link.click();
                
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
            }).catch(error => {
                console.error('Error generating image:', error);
                alert('Error generating image. Please try again.');
                
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
    </script>
</body>
</html>

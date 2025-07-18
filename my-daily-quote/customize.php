<?php
require_once 'config.php';
require_once 'db.php';
require_once 'php/auth.php';

// Require user to be logged in
requireLogin();

// Get current user
$user = getCurrentUser();

// Get quote to customize (from URL parameter or today's quote)
$quote_id = $_GET['quote_id'] ?? null;
if ($quote_id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM quotes WHERE id = ? AND is_active = 1");
    $stmt->execute([$quote_id]);
    $quote = $stmt->fetch();
} else {
    $quote = getTodaysQuote();
}

if (!$quote) {
    $quote = [
        'quote_text' => 'Welcome to My Daily Quote! Add some quotes in the admin panel.',
        'author' => 'System'
    ];
}

// Handle file upload
$upload_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['background'])) {
    $upload_dir = 'uploads/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($_FILES['background']['error'] === UPLOAD_ERR_OK) {
        $file_type = $_FILES['background']['type'];
        $file_size = $_FILES['background']['size'];
        
        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
            $file_extension = pathinfo($_FILES['background']['name'], PATHINFO_EXTENSION);
            $new_filename = 'bg_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['background']['tmp_name'], $upload_path)) {
                $upload_message = 'Background uploaded successfully!';
                $uploaded_bg = $upload_path;
            } else {
                $upload_message = 'Failed to upload file.';
            }
        } else {
            $upload_message = 'Invalid file type or size too large.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize Quote - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .quote-text { font-family: 'Playfair Display', serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-roboto { font-family: 'Roboto', sans-serif; }
        .font-opensans { font-family: 'Open Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-white w-64 shadow-lg flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="bg-indigo-600 rounded-lg w-10 h-10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-gray-900"><?php echo APP_NAME; ?></h1>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-6">
                <ul class="space-y-2">
                    <li>
                        <a href="index.php" class="text-gray-700 hover:bg-gray-50 group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                            <svg class="text-gray-400 mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="customize.php" class="bg-indigo-50 text-indigo-700 group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                            <svg class="text-indigo-500 mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a1 1 0 01-1-1V9a1 1 0 011-1h1a2 2 0 100-4H4a1 1 0 01-1-1V4a1 1 0 011-1h3a1 1 0 001-1z"></path>
                            </svg>
                            Customize
                        </a>
                    </li>
                    <li>
                        <a href="admin.php" class="text-gray-700 hover:bg-gray-50 group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                            <svg class="text-gray-400 mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Admin
                        </a>
                    </li>
                </ul>
            </nav>

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
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-8">
                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Customize Your Quote</h2>
                    <p class="text-gray-600">Personalize your quote with custom backgrounds, fonts, and colors</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Customization Controls -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Background Options -->
                        <div class="bg-white rounded-2xl p-6 shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Background</h3>
                            
                            <!-- Preset Backgrounds -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preset Backgrounds</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button onclick="changeBackground('gradient1')" class="h-16 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 border-2 border-transparent hover:border-indigo-300"></button>
                                    <button onclick="changeBackground('gradient2')" class="h-16 rounded-lg bg-gradient-to-br from-pink-500 to-rose-500 border-2 border-transparent hover:border-pink-300"></button>
                                    <button onclick="changeBackground('gradient3')" class="h-16 rounded-lg bg-gradient-to-br from-green-400 to-blue-500 border-2 border-transparent hover:border-green-300"></button>
                                    <button onclick="changeBackground('gradient4')" class="h-16 rounded-lg bg-gradient-to-br from-yellow-400 to-orange-500 border-2 border-transparent hover:border-yellow-300"></button>
                                    <button onclick="changeBackground('gradient5')" class="h-16 rounded-lg bg-gradient-to-br from-purple-400 to-pink-400 border-2 border-transparent hover:border-purple-300"></button>
                                    <button onclick="changeBackground('solid')" class="h-16 rounded-lg bg-gray-800 border-2 border-transparent hover:border-gray-600"></button>
                                </div>
                            </div>

                            <!-- Upload Custom Background -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Custom Background</label>
                                <form method="POST" enctype="multipart/form-data" class="space-y-2">
                                    <input type="file" name="background" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-indigo-700">Upload</button>
                                </form>
                                <?php if ($upload_message): ?>
                                    <p class="text-sm mt-2 <?php echo strpos($upload_message, 'success') !== false ? 'text-green-600' : 'text-red-600'; ?>">
                                        <?php echo htmlspecialchars($upload_message); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Font Options -->
                        <div class="bg-white rounded-2xl p-6 shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Typography</h3>
                            
                            <!-- Font Family -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Font Family</label>
                                <select id="fontFamily" onchange="changeFontFamily()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="font-playfair">Playfair Display</option>
                                    <option value="font-inter">Inter</option>
                                    <option value="font-roboto">Roboto</option>
                                    <option value="font-opensans">Open Sans</option>
                                </select>
                            </div>

                            <!-- Font Size -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Font Size</label>
                                <input type="range" id="fontSize" min="20" max="60" value="32" onchange="changeFontSize()" class="w-full">
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>Small</span>
                                    <span>Large</span>
                                </div>
                            </div>

                            <!-- Text Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Text Color</label>
                                <input type="color" id="textColor" value="#ffffff" onchange="changeTextColor()" class="w-full h-10 rounded-lg border border-gray-300">
                            </div>
                        </div>

                        <!-- Download Button -->
                        <button onclick="downloadCustomQuote()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 px-6 rounded-xl font-semibold transition duration-200 flex items-center justify-center space-x-3 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Download Custom Quote</span>
                        </button>
                    </div>

                    <!-- Preview -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl p-6 shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview</h3>
                            
                            <div class="flex justify-center">
                                <div id="custom-quote-card" class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl p-12 text-white shadow-2xl relative overflow-hidden max-w-2xl w-full">
                                    <!-- Background Pattern -->
                                    <div class="absolute inset-0 opacity-10">
                                        <svg class="w-full h-full" viewBox="0 0 100 100" fill="none">
                                            <defs>
                                                <pattern id="custom-grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                                                </pattern>
                                            </defs>
                                            <rect width="100" height="100" fill="url(#custom-grid)" />
                                        </svg>
                                    </div>

                                    <!-- Quote Content -->
                                    <div class="relative z-10 text-center">
                                        <div class="mb-8">
                                            <svg class="w-16 h-16 mx-auto mb-6 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                                            </svg>
                                        </div>
                                        
                                        <blockquote id="preview-quote" class="font-playfair leading-relaxed mb-8" style="font-size: 32px; color: #ffffff;">
                                            "<?php echo htmlspecialchars($quote['quote_text']); ?>"
                                        </blockquote>
                                        
                                        <cite id="preview-author" class="text-xl font-semibold opacity-90">
                                            — <?php echo htmlspecialchars($quote['author']); ?>
                                        </cite>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changeBackground(type) {
            const card = document.getElementById('custom-quote-card');
            
            switch(type) {
                case 'gradient1':
                    card.className = card.className.replace(/bg-gradient-to-br from-\S+ to-\S+/, 'bg-gradient-to-br from-indigo-500 to-purple-600');
                    break;
                case 'gradient2':
                    card.className = card.className.replace(/bg-gradient-to-br from-\S+ to-\S+/, 'bg-gradient-to-br from-pink-500 to-rose-500');
                    break;
                case 'gradient3':
                    card.className = card.className.replace(/bg-gradient-to-br from-\S+ to-\S+/, 'bg-gradient-to-br from-green-400 to-blue-500');
                    break;
                case 'gradient4':
                    card.className = card.className.replace(/bg-gradient-to-br from-\S+ to-\S+/, 'bg-gradient-to-br from-yellow-400 to-orange-500');
                    break;
                case 'gradient5':
                    card.className = card.className.replace(/bg-gradient-to-br from-\S+ to-\S+/, 'bg-gradient-to-br from-purple-400 to-pink-400');
                    break;
                case 'solid':
                    card.className = card.className.replace(/bg-gradient-to-br from-\S+ to-\S+/, 'bg-gray-800');
                    break;
            }
        }

        function changeFontFamily() {
            const fontFamily = document.getElementById('fontFamily').value;
            const quote = document.getElementById('preview-quote');
            const author = document.getElementById('preview-author');
            
            // Remove existing font classes
            quote.className = quote.className.replace(/font-\w+/g, '');
            author.className = author.className.replace(/font-\w+/g, '');
            
            // Add new font class
            quote.classList.add(fontFamily);
            author.classList.add(fontFamily);
        }

        function changeFontSize() {
            const fontSize = document.getElementById('fontSize').value;
            const quote = document.getElementById('preview-quote');
            
            quote.style.fontSize = fontSize + 'px';
        }

        function changeTextColor() {
            const textColor = document.getElementById('textColor').value;
            const quote = document.getElementById('preview-quote');
            const author = document.getElementById('preview-author');
            
            quote.style.color = textColor;
            author.style.color = textColor;
        }

        function downloadCustomQuote() {
            const quoteCard = document.getElementById('custom-quote-card');
            
            // Show loading state
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generating...';
            button.disabled = true;
            
            html2canvas(quoteCard, {
                backgroundColor: null,
                scale: 2,
                useCORS: true,
                allowTaint: true,
                width: quoteCard.offsetWidth,
                height: quoteCard.offsetHeight
            }).then(canvas => {
                // Create download link
                const link = document.createElement('a');
                link.download = 'custom-quote-' + new Date().toISOString().split('T')[0] + '.png';
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

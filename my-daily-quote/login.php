<?php
require_once 'config.php';
require_once 'db.php';
require_once 'php/auth.php';
require_once 'php/google-config.php';

// If user is already logged in, redirect to home
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error_message = '';
$success_message = '';

// Handle Google OAuth callback
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Exchange code for access token
    $token_data = getGoogleAccessToken($code);
    
    if ($token_data && isset($token_data['access_token'])) {
        // Get user info from Google
        $user_info = getGoogleUserInfo($token_data['access_token']);
        
        if ($user_info) {
            // Create or get user from database
            $user = getOrCreateUser(
                $user_info['id'],
                $user_info['name'],
                $user_info['email'],
                $user_info['picture']
            );
            
            if ($user) {
                // Login user
                loginUser($user);
                header('Location: index.php');
                exit();
            } else {
                $error_message = 'Failed to create user account.';
            }
        } else {
            $error_message = 'Failed to get user information from Google.';
        }
    } else {
        $error_message = 'Failed to authenticate with Google.';
    }
}

// Generate OAuth state for CSRF protection
$oauth_state = generateOAuthState();
$google_login_url = getGoogleOAuthURL() . '&state=' . $oauth_state;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="bg-white rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center shadow-lg">
                <div class="text-2xl font-bold text-indigo-600">Q</div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo APP_NAME; ?></h1>
            <p class="text-gray-600">Get inspired with daily quotes</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-semibold text-gray-900 text-center mb-6">Welcome Back</h2>
            
            <?php if ($error_message): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-red-500 rounded-full mr-3 flex-shrink-0"></div>
                        <div><?php echo htmlspecialchars($error_message); ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3 flex-shrink-0"></div>
                        <div><?php echo htmlspecialchars($success_message); ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Google Login Button -->
            <a href="<?php echo htmlspecialchars($google_login_url); ?>" 
               class="w-full bg-white border-2 border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 hover:border-gray-400 transition duration-200 flex items-center justify-center space-x-3 group">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span class="group-hover:text-gray-900">Continue with Google</span>
            </a>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    By signing in, you agree to our terms of service and privacy policy.
                </p>
            </div>
        </div>

        <!-- Features -->
        <div class="mt-8 text-center">
            <div class="grid grid-cols-3 gap-4 text-sm text-gray-600">
                <div class="flex flex-col items-center">
                    <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center shadow-md mb-2">
                        <div class="w-3 h-3 bg-indigo-600 rounded-full"></div>
                    </div>
                    <span class="font-medium">Daily Quotes</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center shadow-md mb-2">
                        <div class="w-4 h-1 bg-indigo-600 rounded-full"></div>
                    </div>
                    <span class="font-medium">Download</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center shadow-md mb-2">
                        <div class="w-2 h-2 bg-indigo-600 rounded-sm rotate-45"></div>
                    </div>
                    <span class="font-medium">Customize</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

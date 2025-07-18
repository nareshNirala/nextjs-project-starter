<?php
require_once 'config.php';

/**
 * Database connection using PDO
 * Returns PDO instance for database operations
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Get today's quote (random or by date)
 */
function getTodaysQuote() {
    $pdo = getDBConnection();
    
    // Get a random quote for today (you can modify this logic)
    $stmt = $pdo->prepare("SELECT * FROM quotes WHERE is_active = 1 ORDER BY RAND() LIMIT 1");
    $stmt->execute();
    
    return $stmt->fetch();
}

/**
 * Get all active quotes
 */
function getAllQuotes() {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM quotes WHERE is_active = 1 ORDER BY created_at DESC");
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Add a new quote
 */
function addQuote($quote_text, $author) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("INSERT INTO quotes (quote_text, author) VALUES (?, ?)");
    return $stmt->execute([$quote_text, $author]);
}

/**
 * Delete a quote
 */
function deleteQuote($id) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("UPDATE quotes SET is_active = 0 WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Get or create user from Google OAuth data
 */
function getOrCreateUser($google_id, $name, $email, $picture_url) {
    $pdo = getDBConnection();
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
    $stmt->execute([$google_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO users (google_id, name, email, picture_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$google_id, $name, $email, $picture_url]);
        
        // Get the newly created user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->execute([$google_id]);
        $user = $stmt->fetch();
    } else {
        // Update existing user info
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, picture_url = ?, updated_at = CURRENT_TIMESTAMP WHERE google_id = ?");
        $stmt->execute([$name, $email, $picture_url, $google_id]);
    }
    
    return $user;
}
?>

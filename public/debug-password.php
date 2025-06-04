<?php
// Buat file: public/debug-password.php
// Akses via: http://localhost:8080/debug-password.php

// Connect ke database
$host = 'localhost';
$dbname = 's_pu';
$username = 'postgres';
$password = '180802';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    
    // Ambil user data
    $stmt = $pdo->prepare("SELECT id, email, username, password_hash, active FROM users WHERE email = ?");
    $stmt->execute(['ivan.adhi@pu.go.id']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h3>User Data:</h3>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Active: " . $user['active'] . "<br>";
        echo "Password Hash: " . $user['password_hash'] . "<br><br>";
        
        // Test password
        $testPassword = 'Meltedbrain@911'; // Password yang baru
        $hashFromDB = $user['password_hash'];
        
        echo "<h3>Password Test:</h3>";
        echo "Test Password: " . $testPassword . "<br>";
        echo "Hash Length: " . strlen($hashFromDB) . "<br>";
        echo "Hash Type: " . (strpos($hashFromDB, '$2y$') === 0 ? 'bcrypt' : 'unknown') . "<br>";
        
        if (password_verify($testPassword, $hashFromDB)) {
            echo "<strong style='color: green;'>✅ PASSWORD MATCH!</strong><br>";
        } else {
            echo "<strong style='color: red;'>❌ PASSWORD NO MATCH</strong><br>";
            
            // Test dengan hash baru
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            echo "<br><h3>New Hash Test:</h3>";
            echo "New Hash: " . $newHash . "<br>";
            
            if (password_verify($testPassword, $newHash)) {
                echo "<strong style='color: green;'>✅ NEW HASH WORKS!</strong><br>";
                
                // Update database dengan hash baru
                $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
                if ($updateStmt->execute([$newHash, 'ivan.adhi@pu.go.id'])) {
                    echo "<br><strong style='color: blue;'>🔄 PASSWORD UPDATED IN DATABASE!</strong><br>";
                    echo "Silahkan test API lagi.";
                }
            }
        }
        
    } else {
        echo "User tidak ditemukan!";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
    <?php
// Session start karna zaroori hai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'staykart_db';

// Connection
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check Connection
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>
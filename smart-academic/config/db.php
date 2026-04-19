<?php
// =============================================
// Database Configuration | Bit Brains
// =============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change if needed
define('DB_PASS', '');           // Your MySQL password
define('DB_NAME', 'smart_academic');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// GPA Calculation Function
function calculateGPA($total) {
    if ($total >= 80) return ['point' => 4.00, 'letter' => 'A+'];
    if ($total >= 75) return ['point' => 3.75, 'letter' => 'A'];
    if ($total >= 70) return ['point' => 3.50, 'letter' => 'A-'];
    if ($total >= 65) return ['point' => 3.25, 'letter' => 'B+'];
    if ($total >= 60) return ['point' => 3.00, 'letter' => 'B'];
    if ($total >= 55) return ['point' => 2.75, 'letter' => 'B-'];
    if ($total >= 50) return ['point' => 2.50, 'letter' => 'C+'];
    if ($total >= 45) return ['point' => 2.25, 'letter' => 'C'];
    if ($total >= 40) return ['point' => 2.00, 'letter' => 'D'];
    return ['point' => 0.00, 'letter' => 'F'];
}
?>

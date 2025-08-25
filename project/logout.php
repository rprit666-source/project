<?php
// logout.php

// Step 1: Session ko start karna zaroori hai
// Isse hum current session ko access kar payenge
session_start();

// Step 2: Sabhi session variables ko unset karna
// Isse $_SESSION array khaali ho jayega
$_SESSION = array();

// Step 3: Session ko destroy karna
// Yeh server se session ki file ko delete kar deta hai
session_destroy();

// Step 4: User ko homepage ya login page par redirect karna
// Logout hone ke baad user ko homepage par bhej dena sahi rehta hai
header("location: index.php");
exit;
?>
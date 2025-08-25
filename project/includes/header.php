<?php
// includes/header.php (FINAL SIMPLE VERSION)
include_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StayKart - Curated Rentals</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/style_extended.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body> 
    <header class="main-header">
      <div class="container">
        <a href="index.php" class="logo">StayKart.</a>
        <nav class="main-nav">
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="index.php#categories">Categories</a></li>
            <li><a href="index.php#featured">Residences</a></li>
          </ul>
        </nav>
        
        <div class="header-actions" style="display: flex; align-items: center; gap: 1rem;">
            <?php if (isset($_SESSION['user_id'])): // If user is logged in ?>
                
                <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
                <a href="logout.php" class="btn btn-accent">Logout</a>

            <?php else: // If user is a guest ?>

                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-accent">Sign Up</a>

            <?php endif; ?>
        </div>
      </div>
    </header>
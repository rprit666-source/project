<?php
include_once '../includes/db.php';

// Admin access check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">StayKart Admin</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href="manage_properties.php">Manage Properties</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
        </ul>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
</nav>
<div class="container mt-4">    
<?php
// Corrected register.php

// Step 1: Database connection include karna
// Yeh file sabse upar honi chahiye
include_once 'includes/db.php'; 
$error = '';
$success = '';

// Step 2: Check karna ki form submit hua hai ya nahi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Step 3: Form se data lena aur use safe karna
    $full_name = mysqli_real_escape_string($conn, $_POST['Full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $password = $_POST['Pass']; // Abhi plain password lenge

    // Step 4: Validation - Check karna ki koi field khaali to nahi hai
    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "All fields are required. Please fill out the entire form.";
    } 
    // Email format check karna
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format. Please enter a valid email.";
    }
    else {
        // Step 5: Check karna ki email pehle se database mein hai ya nahi
        $check_sql = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "This email address is already registered. Please try another one.";
        } else {
            // Step 6: Password ko hash karna (sabse zaroori security step)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Step 7: Database mein naya user insert karna
            $sql = "INSERT INTO users (full_name, email, password) VALUES ('$full_name', '$email', '$hashed_password')";
            
            // Step 8: Query run karna aur check karna
            if (mysqli_query($conn, $sql)) {
                // Agar successful ho, to login page par bhej do
                header("Location: login.php");
                exit();
            } else {
                // Agar koi database error aaye, to use dikhana (DEBUGGING KE LIYE)
                $error = "Registration failed. Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register | StayKart</title>
    <link rel="stylesheet" href="assets/css/style_login.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Lora:wght@600&family=Poppins:wght@400;500&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <div class="auth-container">
      <div class="promo-panel">
        <div class="promo-overlay">
          <a href="index.php" class="logo">StayKart.</a>
          <p>Find your perfect escape.</p>
        </div>
      </div>

      <div class="form-panel">
        <div class="form-box">
          <div class="form-header">
            <h2>Create Account</h2>
            <p>Join our community of unique stays.</p>
          </div>

          <?php if (!empty($error)): ?>
            <div style="color: #e74c3c; background-color: #fdd; border: 1px solid #e74c3c; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center;">
                <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form action="register.php" method="POST">
            <div class="input-group">
              <label for="fullname">Full Name</label>
              <input type="text" name="Full_name" id="fullname" required />
            </div>
            <div class="input-group">
              <label for="email">Email Address</label>
              <input type="email" name="Email" id="email" required />
            </div>
            <div class="input-group">
              <label for="password">Password</label>
              <input type="password" name="Pass" id="password" required />
            </div>
            <button type="submit" class="action-btn">Create Account</button>
          </form>
          <div class="form-switch">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
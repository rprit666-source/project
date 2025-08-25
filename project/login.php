<?php
// login.php (Redirects to Dashboard)

include_once 'includes/db.php';
$error = '';

// Jab form submit hoga
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $password = $_POST['Pass'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Admin ko admin panel, aur normal user ko dashboard par bhejo
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php"); // YAHAN BADLAV KIYA GAYA HAI
            }
            exit();
        } else { $error = "Incorrect password."; }
    } else { $error = "No account found with that email."; }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Login | StayKart</title>
    <link rel="stylesheet" href="assets/css/style_login.css" />
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
          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="form-header"><h2>You are already logged in.</h2></div>
            <a href="dashboard.php" class="action-btn" style="text-align: center; display: block;">Go to Dashboard</a>
          <?php else: ?>
            <div class="form-header"><h2>Welcome Back</h2><p>Please sign in.</p></div>
            <?php if (!empty($error)): ?><p style="color: red; text-align: center;"><?php echo $error; ?></p><?php endif; ?>
            <form action="login.php" method="POST">
                <div class="input-group">
                  <label for="email">Email</label>
                  <input type="email" name="Email" id="email" required />
                </div>
                <div class="input-group">
                  <label for="password">Password</label>
                  <input type="password" name="Pass" id="password" required />
                </div>
                <button type="submit" class="action-btn">Sign In</button>
            </form>
            <div class="form-switch">
              <p>No account? <a href="register.php">Sign Up</a></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </body>
</html>
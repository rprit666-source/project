<?php
include "config.php";


$Full_name = $_POST['Full_name'];
$Email = $_POST['Email'];
$password = $_POST['Pass'];

    $sql = "SELECT full_name FROM user WHERE full_name = '{$Full_name}'";
    $result = mysqli_query($conn, $sql) or die("Query failed.");

    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color:red;text-align:center;margin:10px 0;'>Username already exists.</p>";
    } else {
        $sql1 = "INSERT INTO user (full_name, email, password)
                 VALUES ('{$Full_name}','{$Email}','{$password}')";
        if (mysqli_query($conn, $sql1)) {
            header("Location: /project/login.php");
            exit();
        }
    }


//     if (isset($_POST['login'])) {
//     include "config.php";
//     $Email = mysqli_real_escape_string($conn, $_POST['Email']);
//     $pass = md5($_POST['Pass']);

//     $sql1 = "SELECT id, full_name, email, password FROM user WHERE email = '{$Email}' AND    = '{$pass}'";
//     $result1 = mysqli_query($conn, $sql1) or die("Login Query Failed");

//     if (mysqli_num_rows($result1) > 0) {
//         session_start();
//         while ($row = mysqli_fetch_assoc($result1)) {
//             $_SESSION["username"] = $row['username'];
//             $_SESSION["user_role"] = $row['role'];
//             $_SESSION["user_id"] = $row['user_id'];
//             header("Location: /project/index.html");
//             exit();
//         }
//     } else {
//         echo '<div class="alert alert-danger">Username and password are not matched</div>';
//     }
// }
?>
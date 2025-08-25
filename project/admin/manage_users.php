<?php
// admin/manage_users.php

// Header file include karna, jismein database connection aur admin check hai
include 'header.php';

// User ko delete karne ka logic
// Agar URL mein 'delete_id' hai, to user ko delete karo
if (isset($_GET['delete_id'])) {
    $user_id_to_delete = $_GET['delete_id'];
    
    // Security check: Admin apne aap ko delete na kar sake
    if ($user_id_to_delete == $_SESSION['user_id']) {
        echo "<div class='alert alert-danger'>Error: You cannot delete your own admin account.</div>";
    } else {
        // User se judi properties ko bhi delete karna (ya unhe NULL set karna)
        // Yahan hum user ko delete kar rahe hain, isliye unki properties bhi delete ho jayengi (database constraint ke kaaran)
        $delete_sql = "DELETE FROM users WHERE id = ?";
        
        if ($stmt = $conn->prepare($delete_sql)) {
            $stmt->bind_param("i", $user_id_to_delete);
            
            if ($stmt->execute()) {
                echo "<script>alert('User and their properties have been deleted successfully.'); window.location.href='manage_users.php';</script>";
            } else {
                echo "<div class='alert alert-danger'>Failed to delete the user.</div>";
            }
            $stmt->close();
        }
    }
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Users</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>User ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Database se sabhi users ka data fetch karna
                        $sql = "SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC";
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo ($row['role'] == 'admin') ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo ucfirst($row['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php
                                    // Admin ko delete karne ka button nahi dikhega
                                    if ($row['id'] != $_SESSION['user_id']) {
                                    ?>
                                        <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user? All of their listed properties will also be deleted.')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    <?php } else {
                                        echo "N/A";
                                    } ?>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
// Font Awesome icons ke liye script (agar header mein nahi hai to)
echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>';
include 'footer.php'; 
?>  
<?php
// dashboard.php (DELETE FUNCTIONALITY FIXED)

include_once 'includes/header.php';

// Check karna ki user logged in hai ya nahi
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// User ki ID lena
$user_id = $_SESSION['user_id'];

// === DELETE LOGIC SHURU ===
// Check karna ki URL mein 'action=delete' aur 'id' hai ya nahi
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    
    $property_id_to_delete = $_GET['id'];

    // --- Security Check: Sunishchit karein ki property isi user ki hai ---
    $check_sql = "SELECT user_id FROM properties WHERE id = '$property_id_to_delete'";
    $check_result = mysqli_query($conn, $check_sql);
    $property_owner = mysqli_fetch_assoc($check_result);

    if ($property_owner && $property_owner['user_id'] == $user_id) {
        // Agar property isi user ki hai, tabhi delete karo

        // 1. Pehle property se judi images ko folder se delete karna
        $img_sql = "SELECT image_path FROM property_images WHERE property_id = '$property_id_to_delete'";
        $img_result = mysqli_query($conn, $img_sql);
        while($row = mysqli_fetch_assoc($img_result)){
            if(file_exists('assets/images/' . $row['image_path'])){
                unlink('assets/images/' . $row['image_path']); // Image file delete karna
            }
        }

        // 2. Ab 'property_images' table se image records delete karna
        $delete_img_records_sql = "DELETE FROM property_images WHERE property_id = '$property_id_to_delete'";
        mysqli_query($conn, $delete_img_records_sql);

        // 3. Aakhir mein 'properties' table se property delete karna
        $delete_property_sql = "DELETE FROM properties WHERE id = '$property_id_to_delete'";
        if (mysqli_query($conn, $delete_property_sql)) {
            // Success message dikhana aur page ko refresh karna
            echo "<script>alert('Property deleted successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error deleting property.');</script>";
        }

    } else {
        // Agar koi dusre user ki property delete karne ki koshish kare
        echo "<script>alert('You do not have permission to delete this property.');</script>";
    }
}
// === DELETE LOGIC KHATM ===
?>

<div class="container" style="padding-top: 3rem; padding-bottom: 3rem;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-family: var(--font-heading); color: var(--primary-color);">My Dashboard</h1>
        <a href="add_property.php" class="btn btn-accent">Add New Property +</a>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f7f9f9;">
            <tr>
                <th style="padding: 1rem; text-align: left;">Property Title</th>
                <th style="padding: 1rem; text-align: left;">Status</th>
                <th style="padding: 1rem; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Sirf is user ki properties fetch karna
            $sql = "SELECT id, title, status FROM properties WHERE user_id = '$user_id' ORDER BY created_at DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <tr style="border-bottom: 1px solid #ecf0f1;">
                        <td style="padding: 1rem;"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td style="padding: 1rem;"><?php echo ucfirst($row['status']); ?></td>
                        <td style="padding: 1rem; text-align: right;">
                            <a href="edit_property.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: var(--primary-color); margin-right: 15px;">Edit</a>
                            <a href="dashboard.php?action=delete&id=<?php echo $row['id']; ?>" style="text-decoration: none; color: var(--secondary-color);" onclick="return confirm('Are you sure you want to delete this property? This action cannot be undone.')">Delete</a>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='3' style='padding: 2rem; text-align: center;'>You have not listed any properties yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
include_once 'includes/footer.php';
?>
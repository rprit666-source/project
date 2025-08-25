<?php
// edit_property.php (FINAL - EVERYTHING IS EDITABLE)

include_once 'includes/header.php';

// Check karna ki user logged in hai ya nahi
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'];

// --- IMAGE DELETE LOGIC ---
if (isset($_GET['action']) && $_GET['action'] == 'delete_image' && isset($_GET['image_id'])) {
    $image_id_to_delete = $_GET['image_id'];

    // Security check: Image usi property ki hai ya nahi
    $sql_check = "SELECT pi.image_path FROM property_images pi JOIN properties p ON pi.property_id = p.id WHERE pi.id = '$image_id_to_delete' AND p.user_id = '$user_id'";
    $result_check = mysqli_query($conn, $sql_check);
    if($row = mysqli_fetch_assoc($result_check)){
        // Folder se image delete karna
        if(file_exists('assets/images/' . $row['image_path'])){
            unlink('assets/images/' . $row['image_path']);
        }
        // Database se image ka record delete karna
        mysqli_query($conn, "DELETE FROM property_images WHERE id = '$image_id_to_delete'");
        $success = "Image deleted successfully.";
    }
}


// --- PROPERTY UPDATE LOGIC (JAB FORM SUBMIT HOGA) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form se naya data lena
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $amenities = isset($_POST['amenities']) ? implode(', ', $_POST['amenities']) : '';

    // Step 1: Text details ko UPDATE karna
    $sql_update = "UPDATE properties SET 
                        title = '$title', 
                        property_type = '$type', 
                        location = '$location', 
                        price = '$price', 
                        description = '$description', 
                        amenities = '$amenities',
                        status = 'pending'  -- Har update ke baad status 'pending' ho jayega
                   WHERE id = '$property_id' AND user_id = '$user_id'"; // Security check

    if (mysqli_query($conn, $sql_update)) {
        $success = "Property details updated successfully!";

        // Step 2: Nayi images upload karna (agar select ki gayi hain)
        if (!empty($_FILES['new_images']['name'][0])) {
            $upload_dir = 'assets/images/'; 
            foreach ($_FILES['new_images']['name'] as $key => $name) {
                if ($_FILES['new_images']['error'][$key] == 0) {
                    $tmp_name = $_FILES['new_images']['tmp_name'][$key];
                    $file_name = time() . '_' . basename($name);
                    $target_file = $upload_dir . $file_name;
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $img_sql = "INSERT INTO property_images (property_id, image_path) VALUES ('$property_id', '$file_name')";
                        mysqli_query($conn, $img_sql);
                    }
                }
            }
            $success .= " New images uploaded.";
        }
        $success .= " It is now pending for admin re-approval.";

    } else {
        $error = "Error updating property: " . mysqli_error($conn);
    }
}


// --- Property ki purani details database se nikalna ---
$sql_fetch = "SELECT * FROM properties WHERE id = '$property_id' AND user_id = '$user_id'";
$result = mysqli_query($conn, $sql_fetch);

if (mysqli_num_rows($result) > 0) {
    $property = mysqli_fetch_assoc($result);
    $current_amenities = explode(', ', $property['amenities']);
} else {
    echo "<div class='container'><p>Property not found or you do not have permission to edit it.</p></div>";
    include_once 'includes/footer.php';
    exit();
}

// Saari possible amenities ka ek array
$all_amenities = ["AC", "Wi-Fi", "Swimming Pool", "Home Theater", "Free Parking", "Kitchen", "Private Garden", "BBQ Grill", "Power Backup", "Pet Friendly"];
?>

<main class="page-main-content">
  <div class="container">
    <div class="page-title">
      <h1>Edit Your Property</h1>
      <p>Update the details of your property below.</p>
    </div>

    <?php if ($success): ?><div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; text-align:center;"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error): ?><div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; text-align:center;"><?php echo $error; ?></div><?php endif; ?>

    <form class="add-property-form glass-form-card" action="edit_property.php?id=<?php echo $property_id; ?>" method="POST" enctype="multipart/form-data">
      
      <div class="form-section">
        <h3><i class="fas fa-home"></i> Property Basics</h3>
        <div class="form-grid">
          <div class="form-group"><label for="title">Property Title</label><input type="text" id="title" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required /></div>
          <div class="form-group"><label for="type">Property Type</label><select id="type" name="type" required><option value="Farmhouse" <?php if($property['property_type'] == 'Farmhouse') echo 'selected'; ?>>Farmhouse</option><option value="Pool Farmhouse" <?php if($property['property_type'] == 'Pool Farmhouse') echo 'selected'; ?>>Pool Farmhouse</option><option value="Home" <?php if($property['property_type'] == 'Home') echo 'selected'; ?>>Home</option></select></div>
          <div class="form-group"><label for="location">Location</label><input type="text" id="location" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required /></div>
          <div class="form-group"><label for="price">Price (per night)</label><input type="number" id="price" name="price" value="<?php echo htmlspecialchars($property['price']); ?>" required /></div>
        </div>
        <div class="form-group"><label for="description">Description</label><textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($property['description']); ?></textarea></div>
      </div>

      <div class="form-section">
        <h3><i class="fas fa-images"></i> Manage Photos</h3>
        <label>Current Photos (Click '×' to delete)</label>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <?php
            $img_sql = "SELECT id, image_path FROM property_images WHERE property_id = '$property_id'";
            $images_result = mysqli_query($conn, $img_sql);
            while($img_row = mysqli_fetch_assoc($images_result)):
            ?>
                <div style="position: relative;">
                    <img src="assets/images/<?php echo $img_row['image_path']; ?>" style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px;" />
                    <a href="edit_property.php?id=<?php echo $property_id; ?>&action=delete_image&image_id=<?php echo $img_row['id']; ?>" 
                       style="position: absolute; top: 5px; right: 5px; background: red; color: white; border-radius: 50%; width: 25px; height: 25px; display: grid; place-items: center; text-decoration: none; font-weight: bold;"
                       onclick="return confirm('Are you sure you want to delete this image?')">&times;</a>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="form-group">
          <label>Upload New Photos</label>
          <div class="image-upload-box">
            <i class="fas fa-cloud-upload-alt"></i><p><span>Browse</span> to add more photos.</p>
            <input type="file" name="new_images[]" class="file-input" multiple />
          </div>
        </div>
      </div>

      <div class="form-section">
        <h3><i class="fas fa-concierge-bell"></i> Services / Amenities</h3>
        <div class="amenities-grid">
            <?php foreach ($all_amenities as $amenity): ?>
                <label class="custom-checkbox"><?php echo $amenity; ?>
                    <input type="checkbox" name="amenities[]" value="<?php echo $amenity; ?>" 
                           <?php if(in_array($amenity, $current_amenities)) echo 'checked'; ?> />
                    <span class="checkmark"></span></label>
            <?php endforeach; ?>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-accent">Update Property <i class="fas fa-sync-alt"></i></button>
      </div>
    </form>
  </div>
</main>

<?php
include_once 'includes/footer.php';
?>
<?php
// add_property.php (WITH 5 IMAGE UPLOAD LIMIT)

include_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    // === YAHAN BADLAV KIYA GAYA HAI ===
    // Step 1: Check karna ki kitni images upload ki gayi hain
    $image_count = count($_FILES['images']['name']);

    if ($image_count > 5) {
        $error = "You can upload a maximum of 5 images only.";
    } else {
        // Form se baaki ka data lena
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $amenities = isset($_POST['amenities']) ? implode(', ', $_POST['amenities']) : '';

        // Property ki details database mein save karna
        $sql = "INSERT INTO properties (user_id, title, description, property_type, location, price, amenities, status) 
                VALUES ('$user_id', '$title', '$description', '$type', '$location', '$price', '$amenities', 'pending')";

        if (mysqli_query($conn, $sql)) {
            $property_id = mysqli_insert_id($conn);

            // Images ko handle karna
            $upload_dir = 'assets/images/'; 
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $tmp_name = $_FILES['images']['tmp_name'][$key];
                    $file_name = time() . '_' . basename($name);
                    $target_file = $upload_dir . $file_name;

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $img_sql = "INSERT INTO property_images (property_id, image_path) VALUES ('$property_id', '$file_name')";
                        mysqli_query($conn, $img_sql);
                    }
                }
            }
            $success = "Property listed successfully! It is now pending for admin approval.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<main class="page-main-content">
  <div class="container">
    <div class="page-title">
      <h1>List Your Unique Property</h1>
      <p>Join our curated collection of unique stays. Fill in the details below.</p>
    </div>

    <?php if ($success): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; text-align:center;"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; text-align:center;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form class="add-property-form glass-form-card" action="add_property.php" method="POST" enctype="multipart/form-data">
        <div class="form-section">
        <h3><i class="fas fa-home"></i> Property Basics</h3>
        <div class="form-grid">
          <div class="form-group">
            <label for="title">Property Title</label>
            <input type="text" id="title" name="title" placeholder="e.g., The Serene Villa" required />
          </div>
          <div class="form-group">
            <label for="type">Property Type</label>
            <select id="type" name="type" required>
              <option value="Farmhouse">Farmhouse</option>
              <option value="Pool Farmhouse">Pool Farmhouse</option>
              <option value="Home">Home</option>
            </select>
          </div>
          <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" placeholder="e.g., Dumas, Surat" required />
          </div>
          <div class="form-group">
            <label for="price">Price (per night)</label>
            <input type="number" id="price" name="price" placeholder="e.g., 15000" required />
          </div>
        </div>
        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="5" placeholder="Tell us what makes your place special..." required></textarea>
        </div>
      </div>

      <div class="form-section">
        <h3><i class="fas fa-images"></i> Photos</h3>
        <div class="form-group">
          <div class="image-upload-box">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Drag & Drop up to 5 photos here, or <span>browse</span> to upload.</p>
            <input type="file" name="images[]" class="file-input" multiple required />
          </div>
        </div>
      </div>

      <div class="form-section">
        <h3><i class="fas fa-concierge-bell"></i> Services / Amenities</h3>
        <div class="amenities-grid">
          <label class="custom-checkbox">Air Conditioning<input type="checkbox" name="amenities[]" value="AC" /><span class="checkmark"></span></label>
          <label class="custom-checkbox">Wi-Fi<input type="checkbox" name="amenities[]" value="Wi-Fi" /><span class="checkmark"></span></label>
          <label class="custom-checkbox">Swimming Pool<input type="checkbox" name="amenities[]" value="Swimming Pool" /><span class="checkmark"></span></label>
          <label class="custom-checkbox">Home Theater<input type="checkbox" name="amenities[]" value="Home Theater" /><span class="checkmark"></span></label>
          <label class="custom-checkbox">Free Parking<input type="checkbox" name="amenities[]" value="Free Parking" /><span class="checkmark"></span></label>
          <label class="custom-checkbox">Kitchen<input type="checkbox" name="amenities[]" value="Kitchen" /><span class="checkmark"></span></label>
          <label class="custom-checkbox">Private Garden<input type="checkbox" name="amenities[]" value="Private Garden" /><span class="checkmark"></span></label>
          <label class="custom-checkbox">BBQ Grill<input type="checkbox" name="amenities[]" value="BBQ Grill" /><span class="checkmark"></span></label>
          <label class="custom-checkbox">Power Backup<input type="checkbox" name="amenities[]" value="Power Backup" /><span class="checkmark"></span></label>
          <label class="custom-checkbox">Pet Friendly<input type="checkbox" name="amenities[]" value="Pet Friendly" /><span class="checkmark"></span></label>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-accent">Submit Property <i class="fas fa-check"></i></button>
      </div>
    </form>
  </div>
</main>

<?php
include_once 'includes/footer.php';
?>
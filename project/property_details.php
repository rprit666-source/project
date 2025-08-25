<?php
// property_details.php (FINAL & WORKING)

include_once 'includes/header.php';

// STEP 1: URL se property ki ID lena
// Agar ID nahi hai, to user ko homepage par bhej do
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$property_id = $_GET['id'];

// STEP 2: Database se property ki saari details nikalna
// Hum properties table aur users table ko JOIN karenge taaki owner ka naam bhi mil jaaye
$sql = "SELECT p.*, u.full_name as owner_name 
        FROM properties p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.id = '$property_id' AND p.status = 'approved'";

$result = mysqli_query($conn, $sql);

// Check karna ki property mili ya nahi
if (mysqli_num_rows($result) > 0) {
    $property = mysqli_fetch_assoc($result);
} else {
    // Agar property nahi mili ya approved nahi hai, to error dikhana
    echo "<div class='container' style='padding: 3rem 0;'><p>Sorry, this property is not available or could not be found.</p></div>";
    include_once 'includes/footer.php';
    exit();
}

// STEP 3: Us property ki saari images nikalna
$img_sql = "SELECT image_path FROM property_images WHERE property_id = '$property_id'";
$img_result = mysqli_query($conn, $img_sql);
$images = [];
while ($row = mysqli_fetch_assoc($img_result)) {
    $images[] = $row['image_path'];
}
?>

<main class="page-main-content">
    <div class="container">
        <div class="property-header">
            <h1><?php echo htmlspecialchars($property['title']); ?></h1>
            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['location']); ?></p>
        </div>

        <div class="gallery-grid">
            <div class="main-image">
                <img src="assets/images/<?php echo htmlspecialchars($images[0] ?? 'default.jpg'); ?>" alt="Main property view" />
            </div>
            <div class="thumbnail-images">
                <img src="assets/images/<?php echo htmlspecialchars($images[1] ?? 'default.jpg'); ?>" alt="Thumbnail 1" />
                <img src="assets/images/<?php echo htmlspecialchars($images[2] ?? 'default.jpg'); ?>" alt="Thumbnail 2" />
                <img src="assets/images/<?php echo htmlspecialchars($images[3] ?? 'default.jpg'); ?>" alt="Thumbnail 3" />
                <div class="show-all-photos">
                    <i class="fas fa-th"></i>
                    <span><?php echo count($images); ?> Photos</span>
                </div>
            </div>
        </div>

        <div class="property-body-grid">
            <div class="property-description">
                <h2>Entire <?php echo strtolower($property['property_type']); ?> hosted by <?php echo htmlspecialchars($property['owner_name']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
            </div>
            <div class="booking-card glass-form-card">
                <h3><strong>₹<?php echo number_format($property['price']); ?></strong> / night</h3>
                <div class="form-group"><label>CHECK-IN</label><input type="date" /></div>
                <div class="form-group"><label>CHECK-OUT</label><input type="date" /></div>
                <div class="form-group"><label>GUESTS</label><select><option>1 guest</option><option>2 guests</option></select></div>
                <button class="btn btn-accent book-btn">Book Now</button>
            </div>
        </div>

        <div class="amenities-display-section">
            <h2>What this place offers</h2>
            <div class="amenities-list">
                <?php 
                // Amenities ko string se array mein badalna
                $amenities_array = explode(', ', $property['amenities']);
                foreach ($amenities_array as $amenity): 
                ?>
                    <div class="amenity-item">
                        <i class="fas fa-check-circle" style="color: var(--primary-color);"></i> <?php echo htmlspecialchars($amenity); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<?php
include_once 'includes/footer.php';
?>
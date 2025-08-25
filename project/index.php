<?php
// index.php (Restored to original header design)
include_once 'includes/db.php'; 
include_once 'includes/header.php'; 

?>

    <main>
    <section class="hero-section">
        <div class="container hero-container">
            <div class="hero-text"><h1>Find a Home That Inspires.</h1><p>Discover our curated collection of unique farmhouses, homes, and poolside retreats for your next getaway.</p><form class="search-form"><input type="text" placeholder="Enter location, e.g., Surat" /><button type="submit" class="btn btn-primary">Search</button></form></div>
            <div class="hero-image-showcase"><img src="https://images.pexels.com/photos/276724/pexels-photo-276724.jpeg" class="hero-img img-1" /><img src="https://images.pexels.com/photos/164558/pexels-photo-164558.jpeg" class="hero-img img-2" /><img src="https://images.pexels.com/photos/221540/pexels-photo-221540.jpeg" class="hero-img img-3" /></div>
        </div>
    </section>
    <section id="categories" class="categories-section">
        <div class="container">
            <div class="section-title"><span>Our Categories</span><h2>Choose Your Escape</h2></div>
            <div class="category-grid">
                <div class="category-card"><img src="https://images.pexels.com/photos/259580/pexels-photo-259580.jpeg" /><div class="card-content"><h3>Farmhouses</h3><p>Tranquility in nature's lap.</p></div></div>
                <div class="category-card"><img src="https://images.pexels.com/photos/261102/pexels-photo-261102.jpeg" /><div class="card-content"><h3>Pool Farmhouses</h3><p>Luxury with a splash.</p></div></div>
                <div class="category-card"><img src="https://images.pexels.com/photos/2089698/pexels-photo-2089698.jpeg" /><div class="card-content"><h3>Modern Homes</h3><p>Urban comfort and style.</p></div></div>
            </div>
        </div>
    </section>
    <section id="featured" class="featured-section">
        <div class="container">
            <div class="section-title"><span>Featured</span><h2>Handpicked Residences</h2></div>
            <div class="property-grid">
                <?php
                $sql = "SELECT p.*, (SELECT image_path FROM property_images WHERE property_id = p.id ORDER BY id ASC LIMIT 1) as image FROM properties p WHERE p.status = 'approved' ORDER BY p.created_at DESC LIMIT 6";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                        <div class="property-card"><div class="property-image"><img src="assets/images/<?php echo htmlspecialchars($row['image'] ?? 'default.jpg'); ?>" /><div class="property-tag"><?php echo htmlspecialchars($row['property_type']); ?></div></div><div class="property-details"><h4><?php echo htmlspecialchars($row['title']); ?></h4><p><?php echo htmlspecialchars(substr($row['description'], 0, 50)); ?>...</p><div class="property-price"><span><strong>₹<?php echo number_format($row['price']); ?></strong>/night</span><a href="property_details.php?id=<?php echo $row['id']; ?>" class="btn-view">View -></a></div></div></div>
                <?php
                    }
                } else { echo "<p>No featured properties available.</p>"; }
                ?>
            </div>
        </div>
    </section>
</main>

    <footer class="main-footer">
      <div class="container">
        <p>&copy; <?php echo date("Y"); ?> StayKart. Curated with ❤️ in Surat.</p>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
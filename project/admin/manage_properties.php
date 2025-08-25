<?php
include 'header.php';

// Handle Actions (Approve, Reject, Delete)
if (isset($_GET['action'])) {
    $property_id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        mysqli_query($conn, "UPDATE properties SET status='approved' WHERE id=$property_id");
    } elseif ($action == 'reject') {
        mysqli_query($conn, "UPDATE properties SET status='rejected' WHERE id=$property_id");
    } elseif ($action == 'delete') {
        // Pehle images delete karein, phir property
        mysqli_query($conn, "DELETE FROM property_images WHERE property_id=$property_id");
        mysqli_query($conn, "DELETE FROM properties WHERE id=$property_id");
    }
    header("Location: manage_properties.php");
    exit();
}
?>

<h2>Manage Properties</h2>
<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Owner</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT p.*, u.full_name FROM properties p JOIN users u ON p.user_id = u.id");
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['full_name']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['status'] == 'pending'): ?>
                <a href="?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-success">Approve</a>
                <a href="?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-warning">Reject</a>
                <?php endif; ?>
                <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
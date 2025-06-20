<?php
session_start();
require 'db_config.php';

if ($_SESSION['role'] != 'shop_admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get shop info
$shopQuery = $conn->prepare("SELECT * FROM repair_shops WHERE user_id = ?");
$shopQuery->bind_param("i", $user_id);
$shopQuery->execute();
$result = $shopQuery->get_result();
$shop = $result->fetch_assoc();

if (!$shop) {
    echo "No shop associated with your account.";
    exit();
}

$shop_id = $shop['shop_id'];

// Get counts for summary cards
function getCount($conn, $table, $shop_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table WHERE shop_id = ?");
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

$servicesTotal     = getCount($conn, 'services', $shop_id);
$partsTotal        = getCount($conn, 'parts', $shop_id);
$appointmentsTotal = getCount($conn, 'appointments', $shop_id);
$reviewsTotal      = getCount($conn, 'reviews', $shop_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shop Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container mt-5">
    <h2 class="mb-3">Welcome, <?php echo $_SESSION['name']; ?>!</h2>
    <p class="mb-4">Here is your shop overview and management panel.</p>

    <!-- Shop Info -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Your Shop Info</div>
        <div class="card-body">
            <p><strong>Shop Name:</strong> <?php echo htmlspecialchars($shop['shop_name']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($shop['address']); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($shop['status']); ?></p>
            <p><strong>Emergency Available:</strong> <?php echo $shop['emergency_available'] ? 'Yes' : 'No'; ?></p>
            <p><strong>Rating:</strong> <?php echo $shop['rating']; ?> ⭐</p>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5>Services</h5>
                    <p class="display-6"><?php echo $servicesTotal; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5>Parts</h5>
                    <p class="display-6"><?php echo $partsTotal; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5>Appointments</h5>
                    <p class="display-6"><?php echo $appointmentsTotal; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h5>Reviews</h5>
                    <p class="display-6"><?php echo $reviewsTotal; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="card">
        <div class="card-header">Manage Your Shop</div>
        <div class="card-body">
            <a href="manage_services.php" class="btn btn-outline-primary me-2">Manage Services</a>
            <a href="manage_parts.php" class="btn btn-outline-warning me-2">Manage Parts</a>
            <a href="view_appointments.php" class="btn btn-outline-success me-2">View Appointments</a>
            <a href="view_reviews.php" class="btn btn-outline-secondary me-2">Customer Reviews</a>
            <a href="view_reports.php" class="btn btn-outline-dark">View Reports</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>

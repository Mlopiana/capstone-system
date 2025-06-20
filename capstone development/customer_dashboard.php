<?php
session_start();
if ($_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit;
}
require 'db_config.php';

// Initialize message holder
$feedbackMessage = "";

// Book Appointment
if (isset($_POST['book_appointment'])) {
    $user_id = $_SESSION['user_id'];
    $shop_id = $_POST['shop_id'];
    $service_id = $_POST['service_id'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];

    $stmt = $conn->prepare("INSERT INTO appointments (user_id, shop_id, service_id, appointment_date, appointment_time) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $user_id, $shop_id, $service_id, $date, $time);
    
    if ($stmt->execute()) {
        $feedbackMessage = "<div class='alert alert-success mt-3'>Appointment booked successfully!</div>";
    } else {
        $feedbackMessage = "<div class='alert alert-danger mt-3'>Booking failed: " . $stmt->error . "</div>";
    }
}

// Submit Review
if (isset($_POST['submit_review'])) {
    $user_id = $_SESSION['user_id'];
    $shop_id = $_POST['review_shop_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, shop_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $shop_id, $rating, $comment);

    if ($stmt->execute()) {
        $feedbackMessage = "<div class='alert alert-success mt-3'>Review submitted!</div>";
    } else {
        $feedbackMessage = "<div class='alert alert-danger mt-3'>Failed to submit review: " . $stmt->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container mt-5">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
    <p>Book appointments, track locations, view services, parts, and reviews.</p>

    <?php echo $feedbackMessage; ?>

    <!-- Book Appointment Section -->
    <div class="mt-5">
        <h4>Book a Service Appointment</h4>
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Select Shop</label>
                    <select name="shop_id" class="form-control" required>
                        <option value="">-- Select Shop --</option>
                        <?php
                        $shops = $conn->query("SELECT shop_id, shop_name FROM repair_shops");
                        while ($shop = $shops->fetch_assoc()) {
                            echo "<option value='{$shop['shop_id']}'>{$shop['shop_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Select Service</label>
                    <select name="service_id" class="form-control" required>
                        <option value="">-- Select Service --</option>
                        <?php
                        $services = $conn->query("SELECT service_id, service_name FROM services");
                        while ($service = $services->fetch_assoc()) {
                            echo "<option value='{$service['service_id']}'>{$service['service_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Date</label>
                    <input type="date" name="appointment_date" class="form-control" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Time</label>
                    <input type="time" name="appointment_time" class="form-control" required>
                </div>
            </div>
            <button type="submit" name="book_appointment" class="btn btn-primary">Book Appointment</button>
        </form>
    </div>

    <!-- Submit Review Section -->
    <div class="mt-5">
        <h4>Leave a Review</h4>
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Select Shop</label>
                    <select name="review_shop_id" class="form-control" required>
                        <option value="">-- Select Shop --</option>
                        <?php
                        $shops = $conn->query("SELECT shop_id, shop_name FROM repair_shops");
                        while ($shop = $shops->fetch_assoc()) {
                            echo "<option value='{$shop['shop_id']}'>{$shop['shop_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Rating (1-5)</label>
                    <input type="number" name="rating" min="1" max="5" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Comment</label>
                    <input type="text" name="comment" class="form-control" required>
                </div>
            </div>
            <button type="submit" name="submit_review" class="btn btn-success">Submit Review</button>
        </form>
    </div>

    <!-- Services Section -->
    <div class="mt-5">
        <h4>Available Services</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Shop</th>
                        <th>Service</th>
                        <th>Description</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $services = $conn->query("SELECT s.service_name, s.description, s.price, r.shop_name 
                                              FROM services s 
                                              JOIN repair_shops r ON s.shop_id = r.shop_id");
                    while ($row = $services->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['shop_name']}</td>
                                <td>{$row['service_name']}</td>
                                <td>{$row['description']}</td>
                                <td>₱" . number_format($row['price'], 2) . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Parts Section -->
    <div class="mt-5">
        <h4>Available Parts</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Shop</th>
                        <th>Part Name</th>
                        <th>Price</th>
                        <th>Availability</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $parts = $conn->query("SELECT p.part_name, p.price, p.availability, r.shop_name 
                                           FROM parts p 
                                           JOIN repair_shops r ON p.shop_id = r.shop_id");
                    while ($row = $parts->fetch_assoc()) {
                        $availability = $row['availability'] ? 'Available' : 'Out of Stock';
                        echo "<tr>
                                <td>{$row['shop_name']}</td>
                                <td>{$row['part_name']}</td>
                                <td>₱" . number_format($row['price'], 2) . "</td>
                                <td>{$availability}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-5 mb-5">
        <h4>Recent Reviews</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Shop</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $reviews = $conn->query("SELECT r.rating, r.comment, r.created_at, u.name AS user_name, s.shop_name 
                                             FROM reviews r 
                                             JOIN users u ON r.user_id = u.user_id 
                                             JOIN repair_shops s ON r.shop_id = s.shop_id 
                                             ORDER BY r.created_at DESC LIMIT 10");
                    while ($row = $reviews->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['shop_name']}</td>
                                <td>{$row['user_name']}</td>
                                <td>{$row['rating']}/5</td>
                                <td>{$row['comment']}</td>
                                <td>" . date("M d, Y", strtotime($row['created_at'])) . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>

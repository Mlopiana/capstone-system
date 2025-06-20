<?php
session_start();
require 'db_config.php'; // Make sure this file connects to your MySQL DB

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role     = $_POST['role'];
    $contact  = trim($_POST['contact_number']);

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $conn->begin_transaction();

        try {
            // Insert into users
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, contact_number) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashedPassword, $role, $contact);
            $stmt->execute();
            $user_id = $stmt->insert_id;

            // Insert into repair_shops if role is shop_admin
            if ($role === 'shop_admin') {
                $shop_name    = trim($_POST['shop_name']);
                $shop_address = trim($_POST['shop_address']);
                $latitude     = floatval($_POST['latitude']);
                $longitude    = floatval($_POST['longitude']);

                $stmt2 = $conn->prepare("INSERT INTO repair_shops (user_id, shop_name, address, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
                $stmt2->bind_param("issdd", $user_id, $shop_name, $shop_address, $latitude, $longitude);
                $stmt2->execute();
            }

            $conn->commit();
            $success = "Account created successfully. You can now <a href='login.php'>log in</a>.";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AutoCare Register</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        label {
            font-size: 14px;
            color: #34495e;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }
        .role-selection {
            margin-bottom: 15px;
        }
        .role-selection input {
            margin-right: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2ecc71;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #27ae60;
        }
        .message {
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .error-message { color: red; }
        .success-message { color: green; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Create Account</h2>

        <?php if (!empty($error)): ?>
            <div class="message error-message"><?= $error ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="message success-message"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <label>Contact Number:</label>
            <input type="text" name="contact_number" required>

            <div class="role-selection">
                <label>Select Role:</label><br>
                <input type="radio" name="role" value="customer" checked onclick="toggleFields()"> Customer
                <input type="radio" name="role" value="shop_admin" onclick="toggleFields()"> Shop Owner
            </div>

            <div id="shopFields" style="display: none;">
                <label>Shop Name:</label>
                <input type="text" name="shop_name" id="shop_name">

                <label>Shop Address:</label>
                <input type="text" name="shop_address" id="shop_address">

                <label>Latitude:</label>
                <input type="text" name="latitude" id="latitude">

                <label>Longitude:</label>
                <input type="text" name="longitude" id="longitude">
            </div>

            <button type="submit">Register</button>
        </form>
        <div style="text-align: center; margin-top: 15px;">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </div>

    <script>
        function toggleFields() {
            const role = document.querySelector('input[name="role"]:checked').value;
            const shopFields = document.getElementById('shopFields');
            const show = role === 'shop_admin';

            shopFields.style.display = show ? 'block' : 'none';
            document.getElementById('shop_name').required = show;
            document.getElementById('shop_address').required = show;
            document.getElementById('latitude').required = show;
            document.getElementById('longitude').required = show;
        }
        window.onload = toggleFields;
    </script>
</body>
</html>

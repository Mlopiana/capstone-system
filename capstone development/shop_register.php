<?php
include 'db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$contact = $_POST['contact'];
$shop_name = $_POST['shop_name'];
$address = $_POST['address'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Insert into users
$sql1 = "INSERT INTO users (name, email, password, contact_number, role)
         VALUES ('$name', '$email', '$password', '$contact', 'shop_admin')";

if (mysqli_query($conn, $sql1)) {
    $user_id = mysqli_insert_id($conn);

    // Insert into repair_shops
    $sql2 = "INSERT INTO repair_shops (user_id, shop_name, address, latitude, longitude)
             VALUES ($user_id, '$shop_name', '$address', $latitude, $longitude)";
    if (mysqli_query($conn, $sql2)) {
        echo "Shop admin registered successfully!";
    } else {
        echo "Error in repair_shops: " . mysqli_error($conn);
    }

} else {
    echo "Error in users: " . mysqli_error($conn);
}
?>

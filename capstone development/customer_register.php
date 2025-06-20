<?php
include 'db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$contact = $_POST['contact'];

$sql = "INSERT INTO users (name, email, password, contact_number, role)
        VALUES ('$name', '$email', '$password', '$contact', 'customer')";

if (mysqli_query($conn, $sql)) {
    echo "Customer registered successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>

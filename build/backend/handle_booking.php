<?php
session_start();
require 'db_connection.php';
include("./utils/util.php");

$util = new Util();
$db = new Config();
$conn = $db->getConnection();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $pickup_date = $util->testInput($_POST['pickup_date']);
    $pickup_time = $util->testInput($_POST['pickup_time']);
    $service = $util->testInput($_POST['service']);
    $suggestions = $util->testInput($_POST['suggestions']);
    $fname = $util->testInput($_POST['fname']);
    $lname = $util->testInput($_POST['lname']);
    $phone_number = $util->testInput($_POST['phone_number']);
    $address = $util->testInput($_POST['address']);
    $shipping_method = $util->testInput($_POST['shipping_method']);

    // Error handling (basic)
    $errors = [];

    if (empty($pickup_date)) $errors[] = 'Pickup date is required';
    if (empty($pickup_time)) $errors[] = 'Pickup time is required';
    if (empty($service)) $errors[] = 'Service is required';
    if (empty($fname)) $errors[] = 'First name is required';
    if (empty($lname)) $errors[] = 'Last name is required';
    if (empty($phone_number)) $errors[] = 'Phone number is required';
    if (empty($address)) $errors[] = 'Address is required';
    if (empty($shipping_method)) $errors[] = 'Shipping method is required';

    if (count($errors) > 0) {
        $_SESSION['errors'] = $errors;
        header('Location: ../booking.php');
        exit();
    }

    // Insert into the booking table using PDO
    try {
        $user_id = $_SESSION['user_id']; // The logged-in user ID
        
        $sql = "INSERT INTO booking (user_id, pickup_date, pickup_time, service, suggestions, fname, lname, phone_number, address, shipping_method) 
                VALUES (:user_id, :pickup_date, :pickup_time, :service, :suggestions, :fname, :lname, :phone_number, :address, :shipping_method)";
        
        $stmt = $conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':pickup_date', $pickup_date, PDO::PARAM_STR);
        $stmt->bindParam(':pickup_time', $pickup_time, PDO::PARAM_STR);
        $stmt->bindParam(':service', $service, PDO::PARAM_STR);
        $stmt->bindParam(':suggestions', $suggestions, PDO::PARAM_STR);
        $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
        $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':shipping_method', $shipping_method, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            header('Location: ../customer-dashboard.php'); // Redirect to the dashboard after a successful booking
            exit();
        } else {
            $_SESSION['errors'] = ['Something went wrong. Please try again later.'];
            header('Location: ../booking.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['errors'] = ['Database error: ' . $e->getMessage()];
        header('Location: ../booking.php');
        exit();
    }
} else {
    header('Location: ../booking.php');
    exit();
}

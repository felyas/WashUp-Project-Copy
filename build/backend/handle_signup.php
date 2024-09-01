<?php
session_start();
require_once('./db_connection.php');
include('./utils/util.php');

$util = new Util();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $firstname = $util->testInput($_POST['fname']);
    $lastname = $util->testInput($_POST['lname']);
    $email = $util->testInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate passwords
    if ($password !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../signup.php");
        exit();
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email is already taken.";
        header("Location: ../signup.php");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Generate OTP
    $otp = mt_rand(1111, 9999); // Generate a random OTP
    $role = 'user'; // Default role

    // Insert the data into the database
    $sql = "INSERT INTO users (first_name, last_name, email, password, otp, verification_status, role) 
            VALUES (:first_name, :last_name, :email, :password, :otp, :verification_status, :role)";
    $stmt = $pdo->prepare($sql);
    $verification_status = 0; // Set initial verification status to 0 (not verified)

    $stmt->bindParam(':first_name', $firstname);
    $stmt->bindParam(':last_name', $lastname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':otp', $otp);
    $stmt->bindParam(':verification_status', $verification_status);
    $stmt->bindParam(':role', $role);

    if ($stmt->execute()) {
        // Send OTP to user's email
        $receiver = $email;
        $subject = "Verification Code";
        $body = "Your verification code is: $otp";
        $sender = "From: feelixbragais@gmail.com";

        if (mail($receiver, $subject, $body, $sender)) {
            $_SESSION['email'] = $email; // Store the user's email in session
            header("Location: ../verify.php"); // Redirect to OTP page
            exit();
        } else {
            $_SESSION['error'] = "Error sending OTP email.";
            header("Location: ../signup.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Error: " . $stmt->errorInfo()[2];
        header("Location: ../signup.php");
        exit();
    }
}
?>

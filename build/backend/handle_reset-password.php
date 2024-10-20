<?php
session_start();
require_once('./db_connection.php');

$db = new Config();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if token is provided
    if (isset($_GET['token']) && !empty($_GET['token'])) {
        $token = $_GET['token'];

        // Sanitize and validate passwords
        $new_password = filter_var($_POST['new_password'], FILTER_SANITIZE_STRING);
        $confirm_password = filter_var($_POST['confirm_password'], FILTER_SANITIZE_STRING);

        // Check if the password is at least 8 characters long
        if (strlen($new_password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters long.";
            header("Location: ../reset-password.php?token=" . urlencode($token));
            exit();
        }

        if ($new_password === $confirm_password) {
            // Fetch the token details from the database
            $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = :token");
            $stmt->execute(['token' => $token]);
            $reset_request = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($reset_request && $reset_request['expires_at'] >= date("U")) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update the user's password
                $stmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
                if ($stmt->execute([
                    'password' => $hashed_password,
                    'email' => $reset_request['email']
                ])) {
                    // Delete the token from the password_resets table
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
                    $stmt->execute(['token' => $token]);

                    $_SESSION['status'] = "Your password has been reset successfully.";
                    header("Location: ../login.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Failed to update the password.";
                    header("Location: ../reset-password.php?token=" . urlencode($token));
                    exit();
                }
            } else {
                $_SESSION['error'] = "The token is either invalid or has expired. Please request a new one";
                header("Location: ../forgot-password.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Passwords do not match.";
            header("Location: ../reset-password.php?token=" . urlencode($token));
            exit();
        }
    } else {
        $_SESSION['error'] = "Token not provided.";
        header("Location: ../forgot-password.php");
        exit();
    }
} else {
    header("Location: ../forgot-password.php");
    exit();
}

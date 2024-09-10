<?php
session_start();
require_once('./db_connection.php');

$db = new Config();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Sanitize the email
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

	// Validate the sanitized email
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// Check if the email exists in the database
		$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
		$stmt->execute(['email' => $email]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($user) {
			// Generate a unique token
			$token = bin2hex(random_bytes(50));
			$expires = date("U") + 1800; // Token expires in 30 minutes

			// Store the token in the database
			$stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at) ON DUPLICATE KEY UPDATE token=:token, expires_at=:expires_at");
			if ($stmt->execute([
				'email' => $email,
				'token' => $token,
				'expires_at' => $expires
			])) {
				// Prepare the password reset link
				$reset_link = "http://localhost/Washup-Project/build/reset-password.php?token=" . $token;

				// Prepare the email
				$receiver = $email;
				$subject = "Password Reset Request";
				
				// HTML email body
				$body = "
					<html>
					<head>
						<style>
							.button {
								background-color: #090f4d;
								color: #ffffff;
								padding: 10px 20px;
								text-decoration: none;
								border-radius: 5px;
								font-size: 16px;
								font-weight: 700;
							}
							.button:hover {
								background-color: #0E4483;
							}
						</style>
					</head>
					<body>
						<p>Hi,</p>
						<p>Please click on the button below to reset your password:</p>
						<p><a href='" . $reset_link . "' class='button'>Reset Password</a></p>
						<p>If you did not request a password reset, please ignore this email.</p>
					</body>
					</html>
				";

				// Set content-type header for HTML email
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= "From: feelixbragais@gmail.com" . "\r\n";

				if (mail($receiver, $subject, $body, $headers)) {
					$_SESSION['status'] = "Password reset link has been sent to your email.";
					header("Location: ../forgot-password.php");
					exit();
				} else {
					$_SESSION['error'] = "Failed to send password reset email.";
					header("Location: ../forgot-password.php");
					exit();
				}
			} else {
				$_SESSION['error'] = "Failed to store reset token.";
				header("Location: ../forgot-password.php");
				exit();
			}
		} else {
			$_SESSION['error'] = "Email doesn't exist.";
			header("Location: ../forgot-password.php");
			exit();
		}
	} else {
		$_SESSION['error'] = "Please enter a valid email.";
		header("Location: ../forgot-password.php");
		exit();
	}
} else {
	header("Location: ../forgot-password.php");
	exit();
}

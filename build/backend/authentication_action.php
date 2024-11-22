<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once './authentication_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// Handle Login Ajax Request
if (isset($_POST['login'])) {
  $email = $util->testInput($_POST['email']);
  $password = $_POST['password'];

  if (empty($email) && empty($password)) {
    echo json_encode(['error' => "Email and password is required"]);
    exit();
  }

  if (empty($email)) {
    echo json_encode(['error' => "Email is required"]);
    exit();
  }

  if (empty($password)) {
    echo json_encode(['error' => "Password is required"]);
    exit();
  }

  // Fetch The User From The Database
  $user = $db->fetchUser($email);
  if ($user && password_verify($password, $user['password'])) {
    if ($user['verification_status']) {
      // Set session variables
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['first_name'] = $user['first_name'];
      $_SESSION['last_name'] = $user['last_name'];
      $_SESSION['role'] = $user['role'];

      // Return JSON response based on role
      if ($user['role'] === 'admin') {
        echo json_encode(['redirect' => './admin-dashboard.php']);
      } else if ($user['role'] === 'delivery') {
        echo json_encode(['redirect' => './delivery-dashboard.php']);
      } else {
        echo json_encode(['redirect' => './customer-dashboard.php']);
      }
    } else {
      $_SESSION['email'] = $email;
      echo json_encode(['redirect' => './verify.php']);
    }
    exit();
  } else {
    echo json_encode(['error' => "Invalid email or password"]);
    exit();
  }
}

// Handle Signup Ajax Request
if (isset($_POST['signup'])) {
  $first_name = $util->testInput($_POST['fname']);
  $last_name = $util->testInput($_POST['lname']);
  $email = $util->testInput($_POST['email']);
  $phone_number = $util->testInput($_POST['phone_number']);
  $password = $_POST['password'];
  $cpassword = $_POST['confirm_password'];

  if (empty($first_name) || empty($last_name) || empty($email) || empty($phone_number) || empty($password) || empty($cpassword)) {
    echo json_encode(['error' => 'Please fill out all required fields before submitting the form.']);
    exit();
  }

  // Filter to allow only letters and spaces
  if (!preg_match("/^[a-zA-Z\s]*$/", $first_name)) {
    echo json_encode(['error' => 'First name must contain only letters']);
    exit();
  }

  if (!preg_match("/^[a-zA-Z\s]*$/", $last_name)) {
    echo json_encode(['error' => 'Last name must contain only letters']);
    exit();
  }

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email format']);
    exit();
  }

  // Validate phone number format
  if (!preg_match('/^09\d{9}$/', $phone_number)) {
    echo json_encode(['error' => 'Invalid phone number. Enter a valid 11-digit number starting with 09.']);
    exit();
  }

  // Validate passwords lenght
  if (strlen($password) < 8) {
    echo json_encode(['error' => 'Password must be at least 8 characters long']);
    exit();
  }

  // Validate passwords match
  if ($password !== $cpassword) {
    echo json_encode(['error' => 'Passwords do not match']);
    exit();
  }

  $emailExist = $db->checkEmailExists($email);
  if ($emailExist) {
    echo json_encode(['error' => 'Email is already taken']);
    exit();
  }

  // Password hashing
  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

  // Generate OTP
  $otp = mt_rand(1111, 9999);

  $userInserted = $db->insertUser($first_name, $last_name, $email, $phone_number, $hashedPassword, $otp);

  // Send OTP to user's email
  if ($userInserted) {
    $receiver = $email;
    $subject = "Verification Code";
    $body = "Your verification code is: $otp";

    $mailed = $util->sendEmail($receiver, $subject, $body);

    if ($mailed) {
      $_SESSION['email'] = $email;
      echo json_encode(['redirect' => './verify.php']);
      exit();
    } else {
      echo json_encode(['error' => 'Unable to send OTP,   Please try again']);
      exit();
    }
  } else {
    echo json_encode(['error' => 'Something went wrong, please try again!']);
    exit();
  }
}

// Handle Verify Email Ajax Request
if (isset($_POST['verify'])) {
  // Collect and combine OTP inputs
  $otp = isset($_POST['otp1']) ? $_POST['otp1'] : '';
  $otp .= isset($_POST['otp2']) ? $_POST['otp2'] : '';
  $otp .= isset($_POST['otp3']) ? $_POST['otp3'] : '';
  $otp .= isset($_POST['otp4']) ? $_POST['otp4'] : '';

  if (strlen($otp) < 4) {
    echo json_encode(['error' => 'Please fill in all OTP fields']);
    exit();
  }

  $otp = htmlspecialchars($otp);
  $email = $_SESSION['email'];

  $verification = $db->verification($email);
  if ($verification['verification_status']) {
    echo json_encode(['error' => 'Your account has already been verified']);
    exit();
  } elseif ($otp === $verification['otp']) {
    $verified = $db->verified($email);

    if ($verified) {
      $user = $db->fetchUser($email);
      if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['role'] = $user['role'];

        echo json_encode(['redirect' => './customer-dashboard.php']);
        exit();
      }
    } else {
      echo json_encode(['error' => 'Something went wrong, please try again']);
      exit();
    }
  } else {
    echo json_encode(['error' => 'Invalid OTP. Please try again']);
    exit();
  }
}

// Handle Forgot Password Ajax Request
if (isset($_POST['forgot-password'])) {
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $user = $db->fetchUser($email);
    if ($user) {
      // Generate a unique token
      $token = bin2hex(random_bytes(50));
      $expires = date("U") + 1800;

      // Store the token into database
      $storedToken = $db->storeToken($email, $token, $expires);
      if ($storedToken) {
        $reset_link = "https://main.washup-laundry.online/build/reset-password.php?token=" . $token;

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
					</body>
					</html>
				";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: feelixbragais@gmail.com" . "\r\n";

        $mailed = $util->sendEmail($receiver, $subject, $body);

        if ($mailed) {
          echo json_encode(['success' => 'Password reset link has been sent to ' . $email]);
          exit();
        } else {
          echo json_encode(['error' => 'Failed to send password reset email, please try again']);
          exit();
        }
      } else {
        echo json_encode(['error' => 'Failed to store reset token, please try again']);
        exit();
      }
    } else {
      echo json_encode(['error' => "Email doesn't exist"]);
      exit();
    }
  } else {
    echo json_encode(['error' => 'Please enter a valid email']);
    exit();
  }
} else {
  echo json_encode(['redirect' => './forgot-password.php']);
}

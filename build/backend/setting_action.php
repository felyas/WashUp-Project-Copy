<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once './setting_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

if (isset($_POST['save'])) {
  $user_id = $_SESSION['user_id'];
  $fname = $util->testInput($_POST['fname']);
  $lname = $util->testInput($_POST['lname']);
  $phone_number = $util->testInput($_POST['phone_number']);
  $email = $util->testInput($_POST['email']);

  $user = $db->userInfo($user_id);
  $current_email = $user['email'];



  // Validate phone number format
  if (!preg_match('/^09\d{9}$/', $phone_number)) {
    echo json_encode(['error' => 'Invalid phone number. Enter a valid 11-digit number starting with 09.']);
    exit();
  }

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email format']);
    exit();
  }

  if ($email === $current_email) {
    if ($db->udpateUserInfoWithoutEmail($user_id, $fname, $lname, $phone_number, $email)) {
      echo json_encode([
        'success' => 'User data updated successfully!',
      ]);
    }
  } else {
    // Generate OTP
    $otp = mt_rand(1111, 9999);

    $saved = $db->updateUserInfo($user_id, $fname, $lname, $phone_number, $email, $otp);

    if ($saved) {
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
}

if (isset($_POST['update-password'])) {
  $user_id = $_SESSION['user_id'];
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  $user = $db->userInfo($user_id);

  // Validate if the inputs are empty
  if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(['error' => 'Please fill out all required fields before submitting the form.']);
    exit();
  }

  // Validate If current_password matches on the current password
  if (!password_verify($current_password, $user['password'])) {
    echo json_encode([
      'error' => 'Incorrect password'
    ]);
    exit();
  }

  // Validate passwords lenght
  if (strlen($new_password) < 8) {
    echo json_encode(['error' => 'Password must be at least 8 characters long']);
    exit();
  }

  // Validate passwords match
  if ($new_password !== $confirm_password) {
    echo json_encode(['error' => 'Passwords do not match']);
    exit();
  }

  $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
  $updatedInfo = $db->updateUsersPassword($user_id, $hashed_password);
  if ($updatedInfo) {
    echo json_encode([
      'status' => 'success'
    ]);
  }
}

<?php
session_start();
require_once('db_connection.php');

$db = new Config();
$conn = $db->getConnection();

if (!isset($_SESSION['email'])) {
  header("Location: signup.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Collect and combine OTP inputs
  $otp = isset($_POST['otp1']) ? $_POST['otp1'] : '';
  $otp .= isset($_POST['otp2']) ? $_POST['otp2'] : '';
  $otp .= isset($_POST['otp3']) ? $_POST['otp3'] : '';
  $otp .= isset($_POST['otp4']) ? $_POST['otp4'] : '';

  // Check if all OTP fields are filled
  if (strlen($otp) < 4) {
    $_SESSION['error'] = "All OTP fields must be filled.";
    header("Location: ../verify.php");
    exit();
  }

  $otp = htmlspecialchars($otp);
  $email = $_SESSION['email'];

  // Fetch the OTP and verification status from the database
  $sql = "SELECT otp, verification_status FROM users WHERE email = :email";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':email', $email);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result['verification_status']) {
    $_SESSION['error'] = "Your account is already verified.";
    header("Location: ../verify.php");
    exit();
  } elseif ($otp === $result['otp']) {
    // Update the verification status
    $updateSql = "UPDATE users SET verification_status = TRUE WHERE email = :email";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bindParam(':email', $email);

    if ($updateStmt->execute()) {
      // Fetch user details to set session variables
      $detailsSql = "SELECT id, first_name, last_name FROM users WHERE email = :email";
      $detailsStmt = $conn->prepare($detailsSql);
      $detailsStmt->bindParam(':email', $email);
      $detailsStmt->execute();
      $userDetails = $detailsStmt->fetch(PDO::FETCH_ASSOC);

      // Set session variables
      $_SESSION['user_id'] = $userDetails['id'];
      $_SESSION['first_name'] = $userDetails['first_name'];
      $_SESSION['last_name'] = $userDetails['last_name'];

      header("Location: ../customer-dashboard.php");
      exit();
    } else {
      $_SESSION['error'] = "Error updating verification status.";
      header("Location: ../verify.php");
      exit();
    }
  } else {
    $_SESSION['error'] = "Invalid OTP. Please try again.";
    header("Location: ../verify.php");
    exit();
  }
}

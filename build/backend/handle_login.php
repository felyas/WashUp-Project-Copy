<?php
session_start();
require_once('./db_connection.php');
include('./utils/util.php');

$util = new Util();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $util->testInput($_POST['email']);
  $password = $_POST['password'];

  // Check if email or password is empty
  if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Please fill in both email and password.";
    header("Location: ../login.php");
    exit();
  }

  // Fetch the user from the database
  $sql = "SELECT id, password, verification_status, first_name, last_name, role FROM users WHERE email = :email";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':email', $email);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result && password_verify($password, $result['password'])) {
    if ($result['verification_status']) {
      $_SESSION['user_id'] = $result['id'];
      $_SESSION['first_name'] = $result['first_name'];
      $_SESSION['last_name'] = $result['last_name'];
      $_SESSION['role'] = $result['role'];

      // Redirect based on role
      if ($result['role'] === 'admin') {
        header("Location: ../admin-dashboard.php");
      } else if ($result['role'] === 'delivery') {
        header("Location: ../delivery-dashboard.php");
      } else {
        header("Location: ../customer-dashboard.php");
      }
    } else {
      $_SESSION['email'] = $email;
      header("Location: ../verify.php");
    }
    exit();
  } else {
    $_SESSION['error'] = "Invalid email or password.";
    header("Location: ../login.php");
    exit();
  }
}

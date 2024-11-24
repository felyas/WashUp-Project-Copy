<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once './landing_page_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

if(isset($_POST['new-message'])) {
  $name = $util->testInput($_POST['customer-name']);
  $email = $util->testInput($_POST['customer-email']);
  $message = $util->testInput($_POST['customer-message']);

  // Validate the email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Invalid email format'
  ]);
  exit();
}


  $receiver = 'feelixbragais@gmail.com';
  $subject = 'Customer Message';
  $mailBody = "FROM: $email\n" .
            "NAME: $name\n" .
            "MESSAGE: $message";

  if($util->sendEmail($receiver, $subject, $mailBody)){
    echo json_encode([
      'status' => 'success',
      'message' => 'Message sent successfully'
    ]);
  }

}
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

class Util
{

  private $mail;

  public function __construct()
  {
    // Initialize PHPMailer
    $this->mail = new PHPMailer(true);
    $this->mail->isSMTP();
    $this->mail->Host = 'smtp.gmail.com';
    $this->mail->SMTPAuth = true;
    $this->mail->Username = 'feelixbragais@gmail.com';
    $this->mail->Password = $_ENV['MAILER_PASSWORD'];
    $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $this->mail->Port = 465;
    $this->mail->setFrom('feelixbragais@gmail.com', 'Washup Laundry');
    $this->mail->addReplyTo('feelixbragais@gmail.com', 'Admin');
    $this->mail->isHTML(true);
  }

  // Method to send an email
  public function sendEmail($toEmail, $subject, $body)
  {
    try {
      // Set recipient
      $this->mail->addAddress($toEmail);

      // Set email content
      $this->mail->Subject = $subject;
      $this->mail->Body = $body;

      // Send email
      $this->mail->send();

      return [
        'status' => 'success',
        'message' => 'Email has been sent successfully',
      ];
    } catch (Exception $e) {
      return [
        'status' => 'error',
        'message' => 'Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo,
      ];
    }
  }

  // Method to sanitize inputs
  public function testInput($data)
  {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = strip_tags($data);

    return $data;
  }

  // Method to display Success Message
  public function showMessage($type, $message)
  {
    return '
      <div class="alert alert-' . htmlspecialchars($type) . ' alert-dismissible fade show" role="alert">
        <strong>' . htmlspecialchars($message) . '</strong> 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    ';
  }
}

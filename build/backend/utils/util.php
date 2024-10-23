<?php

class Util
{

  // Method to send an email
  public function sendEmail($receiver, $subject, $message)
  {
    $sender = "From: feelixbragais@gmail.com";

    // Debugging email sending process
    if (empty($receiver)) {
      error_log("Receiver email is empty.");
      return false;
    }

    if (mail($receiver, $subject, $message, $sender)) {
      return true;
    } else {
      error_log("Mail function failed for $receiver");
      return false;
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

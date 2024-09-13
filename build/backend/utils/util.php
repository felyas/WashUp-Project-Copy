<?php

class Util
{
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

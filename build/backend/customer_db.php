<?php

require_once './db_connection.php';

class Database extends Config
{

  // Insert New Booking Into Database
  public function insertBooking($user_id, $fname, $lname, $phone_number, $address, $pickup_date, $pickup_time, $service_selection, $suggestions, $service_type)
  {

    $sql = 'INSERT INTO booking (user_id, fname, lname, phone_number, address, pickup_date, pickup_time, service_selection, suggestions, service_type) VALUES (:user_id,:fname, :lname, :phone_number, :address, :pickup_date, :pickup_time, :service, :suggestions, :service_type)';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'user_id' => $user_id,
      'fname' => $fname,
      'lname' => $lname,
      'phone_number' => $phone_number,
      'address' => $address,
      'pickup_date' => $pickup_date,
      'pickup_time' => $pickup_time,
      'service' => $service_selection,
      'suggestions' => $suggestions,
      'service_type' => $service_type,
    ]);

    return true;
  }

  /// Fetch User All Booking From Database
  public function read($user_id)
  {
    $sql = 'SELECT * FROM booking WHERE user_id = :user_id ORDER BY id DESC LIMIT 4';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'user_id' => $user_id,
    ]);
    $result = $stmt->fetchAll();
    return $result;
  }
}

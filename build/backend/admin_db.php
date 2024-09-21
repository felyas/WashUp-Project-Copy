<?php
require_once './db_connection.php';

class Database extends Config 
{

  // Fetch All Pending Booking From Database
  public function fetchPendings () {
    $sql = "SELECT * FROM booking where status = 'pending'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
  }

  // Fetch All For Pickup Booking From Database
  public function fetchPickup () {
    $sql = "SELECT * FROM booking where status = 'for pick-up'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
  }

  // Fetch All For Delivery Booking From Database
  public function fetchDelivery () {
    $sql = "SELECT * FROM booking where status = 'for delivery'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
  }

}

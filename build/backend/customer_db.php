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

  // Fetch User Booking With Pagination and Search
  public function read($user_id, $search_query = '', $limit = 4, $offset = 0)
  {
    $sql = 'SELECT * FROM booking WHERE user_id = :user_id';

    // If there's a search query, add to SQL
    if (!empty($search_query)) {
      $sql .= ' AND (fname LIKE :search_query OR lname LIKE :search_query OR service_selection LIKE :search_query)';
    }

    $sql .= ' ORDER BY id DESC LIMIT :limit OFFSET :offset';

    $stmt = $this->conn->prepare($sql);

    // Bind values
    $stmt->bindValue(':user_id', $user_id);
    if (!empty($search_query)) {
      $stmt->bindValue(':search_query', "%$search_query%");
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  // Method to Count the Total Records for Pagination
  public function countAllBookings($user_id, $search_query = '')
  {
    $sql = 'SELECT COUNT(*) FROM booking WHERE user_id = :user_id';

    if (!empty($search_query)) {
      $sql .= ' AND (fname LIKE :search_query OR lname LIKE :search_query OR service_selection LIKE :search_query)';
    }

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':user_id', $user_id);
    if (!empty($search_query)) {
      $stmt->bindValue(':search_query', "%$search_query%");
    }

    $stmt->execute();
    return $stmt->fetchColumn(); // Returns the total count
  }



  // Fetch User Specific Booking From Database
  public function readOne($id)
  {
    $sql = 'SELECT * FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();
    return $result;
  }



  // Update Specific Booking From Database
  public function updateBooking($id, $fname, $lname, $pickup_date, $pickup_time, $phone_number, $address)
  {
    $sql = 'UPDATE booking SET fname = :fname, lname = :lname, pickup_date = :pickup_date, pickup_time = :pickup_time, phone_number = :phone_number, address = :address WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'id' => $id,
      'fname' => $fname,
      'lname' => $lname,
      'pickup_date' => $pickup_date,
      'pickup_time' => $pickup_time,
      'phone_number' => $phone_number,
      'address' => $address,
    ]);

    return true;
  }

  // Delete Specific Booking From Database
  public function deleteBooking($id)
  {
    $sql = 'DELETE FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);

    return true;
  }

  // Count the Total Based on Status from Database
  public function countByStatus($user_id, $status)
  {
    $sql = 'SELECT COUNT(*) as count FROM booking WHERE user_id = :user_id AND status = :status';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'status' => $status]);
    $result = $stmt->fetch();
    return $result['count'];
  }

  // Handle Notification Polling Request
  public function fetch_notification($lastCheck, $user_id)
  {
    $sql = 'SELECT id, status, status_updated_at FROM booking 
          WHERE status_updated_at > :last_check 
          AND is_read = 0 
          AND user_id = :user_id 
          ORDER BY id DESC';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'last_check' => $lastCheck,
      'user_id' => $user_id
    ]);

    // Fetch all unread notifications for bookings that have been updated
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $notifications;
  }



  // Handle marking a notification as read
  public function mark_as_read($notificationId)
  {
    $sql = 'UPDATE booking SET is_read = 1 WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $notificationId]);
  }

  //Future use, for admin side to change the status and also update the is_read flag
  // public function update_booking_status($bookingId, $newStatus)
  // {
  //   $sql = 'UPDATE booking SET status = :status, is_read = 0, status_updated_at = NOW() WHERE id = :id';
  //   $stmt = $this->conn->prepare($sql);
  //   $stmt->execute(['id' => $bookingId, 'status' => $newStatus]);
  // }
}

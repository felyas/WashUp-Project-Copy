<?php

require_once './db_connection.php';

class Database extends Config
{

  // FETCH PREVIOUS DATA FROM DATABASE
  public function previousData($user_id)
  {
    $sql = 'SELECT * FROM booking WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch();

    return $result;
  }


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
  public function updateBooking($id, $fname, $lname, $phone_number, $address)
  {
    $sql = 'UPDATE booking SET fname = :fname, lname = :lname, phone_number = :phone_number, address = :address WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'id' => $id,
      'fname' => $fname,
      'lname' => $lname,
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



  // Mark notification as read method
  public function mark_as_read($notificationId) {
    $sql = 'UPDATE booking SET is_read = 1 WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute(['id' => $notificationId]);
  }

  //CAlENDAR READ ALL
  public function fetchAllEvents()
  {
    $sql = 'SELECT * FROM calendar_event_master';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  // UPDATE THE STATUS FROM 'delivered' TO 'complete' INTO DATABASE
  public function updateToComplete($id)
  {
    $sql = 'UPDATE booking SET status = :status, is_read = :is_read WHERE id = :id ';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'complete',
      'is_read' => 0,
      'id' => $id
    ]);

    return true;
  }

  // PUT BACK THE STATUS FROM 'is receive' TO 'for delivery' INTO DATABASE
  public function updateToDeliveryAgain($id)
  {
    $sql = 'UPDATE booking SET status = :status, is_read = :is_read, delivery_is_read = :delivery_is_read WHERE id = :id ';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'for delivery',
      'is_read' => 0,
      'delivery_is_read' => 0,
      'id' => $id
    ]);

    return true;
  }

  // Fetch unavailable times for a specific date
  public function getUnavailableTimesForDate($date)
  {
    $sql = 'SELECT pickup_time FROM booking WHERE pickup_date = :pickup_date';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['pickup_date' => $date]);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN); // Fetch only the pickup_time column
    return $result;
  }

  // Add New Complain Request to DATABASE
  public function addComplaint($user_id, $first_name, $last_name, $phone_number, $email, $reason, $description)
  {
    $sql = 'INSERT INTO complaints (user_id, first_name, last_name, phone_number, email, reason, description) VALUES (:user_id, :first_name, :last_name, :phone_number, :email, :reason, :description)';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'user_id' => $user_id,
      'first_name' => $first_name,
      'last_name' => $last_name,
      'phone_number' => $phone_number,
      'email' => $email,
      'reason' => $reason,
      'description' => $description,
    ]);

    return true;
  }

  // INSERT NEW FEEDBACK INTO DATABASE
  public function insertFeedback($user_id, $first_name, $last_name, $rating, $description, $booking_id, $phone_number)
{
    // Determine the value of isGoodReview based on the rating
    $isGoodReview = $rating >= 3 ? 1 : 0;

    // Insert feedback into the feedback table with the isGoodReview column
    $sql = 'INSERT INTO feedback (user_id, first_name, last_name, rating, description, isGoodReview, booking_id) 
            VALUES (:user_id, :first_name, :last_name, :rating, :description, :isGoodReview, :booking_id)';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        'user_id' => $user_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'rating' => $rating,
        'description' => $description,
        'isGoodReview' => $isGoodReview,
        'booking_id' => $booking_id,
    ]);

    // If the rating is less than or equal to 2, also insert into the complaint table
    if ($rating <= 2) {
        $sqlComplaint = 'INSERT INTO complaints (user_id, first_name, last_name, email, phone_number, reason, description, booking_id) 
                         VALUES (:user_id, :first_name, :last_name, :email, :phone_number, :reason, :description, :booking_id)';
        $stmtComplaint = $this->conn->prepare($sqlComplaint);
        $stmtComplaint->execute([
            'user_id' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone_number' => $phone_number,
            'reason' => 'Low rate',
            'email' => 'No Email',
            'description' => $description,
            'booking_id' => $booking_id,
        ]);
    }

    return true;
}



  // FETCH 3 FEEDBACK FROM DARATABASE
  public function fetchFeedback()
  {
    $sql = 'SELECT * FROM feedback WHERE isGoodReview = 1 ORDER BY id DESC LIMIT 3';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
  }

  // GET EMAIL AND PHONE_NUMBER OF USER IN DATABASE
  public function getEmailPhone($user_id){
    $sql = 'SELECT * FROM booking WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch();

    return $result;
  }
}

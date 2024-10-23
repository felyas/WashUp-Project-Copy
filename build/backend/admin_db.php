<?php

require_once './db_connection.php';

class Database extends Config
{

  // FETCH SPECIFIC USER FROM DATABASE
  public function user($id)
  {
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();

    return $result;
  }

  // Fetch All Pending Booking with Pagination and Search
  public function fetchPendingsWithPagination($page, $limit, $query)
  {
    $start = ($page - 1) * $limit;
    $query = '%' . $query . '%';

    $sql = "SELECT * FROM booking WHERE status = 'pending' AND (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query OR address LIKE :query) ORDER BY id DESC LIMIT :start, :limit";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':query', $query, PDO::PARAM_STR);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  // Get Total Number of Pending Rows with Search Filter
  public function getTotalPendingRows($query)
  {
    $query = '%' . $query . '%';

    $sql = "SELECT COUNT(*) as total FROM booking WHERE status = 'pending' AND (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query OR address LIKE :query)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':query', $query, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch();
    return $result['total'];
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

  // Admit Booking From Database
  public function admitBooking($id)
  {
    $sql = 'UPDATE booking 
        SET status = :status, is_read = :is_read 
        WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'for pick-up',  // Update the status to 'for pick-up'
      'is_read' => 0,             // Set is_read to 0
      'id' => $id                 // Use the provided id to identify the row
    ]);

    return true;
  }

  // Denied Booking From Database
  public function deniedBooking($id)
  {
    $sql = 'DELETE FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'id' => $id
    ]);

    return true;
  }

  // Count the Total for Card Summary from Database
  public function totalCountByStatus($status)
  {
    $sql = 'SELECT COUNT(*) as count FROM booking WHERE status = :status';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['status' => $status]);
    $result = $stmt->fetch();

    return $result['count'];
  }

  // Count the Total User for Card from Database
  public function totalCountofUser($role)
  {
    $sql = 'SELECT COUNT(*) as count FROM users WHERE role = :role';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['role' => $role]);
    $result = $stmt->fetch();

    return $result['count'];
  }

  // Fetch all unread bookings
  public function fetch_new_bookings()
  {
    $sql = 'SELECT id, created_at FROM booking WHERE admin_is_read = 0 ORDER BY id DESC';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    // Fetch all new bookings
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $notifications;
  }

  // Mark booking as read for admin
  public function mark_admin_booking_as_read($bookingId)
  {
    $sql = 'UPDATE booking SET admin_is_read = 1 WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $bookingId]);
  }

  // Fetch total number of users per month
  public function fetchUserCountPerMonth()
  {
    $sql = "SELECT MONTHNAME(created_at) as month, COUNT(id) as total_users
          FROM users
          WHERE YEAR(created_at) = YEAR(CURDATE()) -- Only get users from the current year
          GROUP BY month
          ORDER BY MONTH(created_at)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  // Fetch Booking Data from Database
  public function fetchBookingData()
  {
    // Update the query to use `created-at` instead of `booking_date`
    $sql = "SELECT DATE_FORMAT(`created_at`, '%M') AS month, COUNT(*) AS total 
            FROM booking 
            GROUP BY MONTH(`created_at`) 
            ORDER BY MONTH(`created_at`)";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  //CAlENDAR CRUD OPERATION
  public function fetchAllEvents()
  {
    $sql = 'SELECT * FROM calendar_event_master';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  public function addEvent($title, $start, $end)
  {
    $sql = 'INSERT INTO calendar_event_master (event_name, event_start_date, event_end_date) VALUES (:title, :start, :end)';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['title' => $title, 'start' => $start, 'end' => $end]);

    // Return the ID of the newly created event
    return $this->conn->lastInsertId();
  }

  public function deleteEvent($event_id)
  {
    $sql = 'DELETE FROM calendar_event_master WHERE event_id = :event_id';
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute(['event_id' => $event_id]);
  }
}

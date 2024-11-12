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

  // FETCH ROWS WHO HAS A STATUS OF 'FOR PICK-UP' & 'FOR DELIVERY'
  public function readAll($start, $limit, $column, $order, $query, $status, $date)
  {
    $searchQuery = '%' . $query . '%';
    $statusCondition = $status ? 'AND status = :status' : '';
    $dateCondition = $date ? 'AND DATE(pickup_date) = :date' : '';

    $sql = "SELECT * FROM booking 
        WHERE (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query OR service_type LIKE :query OR address LIKE :query)
        AND status IN ('for pick-up', 'for delivery')
        $statusCondition
        $dateCondition
        ORDER BY $column $order 
        LIMIT :start, :limit";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if ($status) {
      $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    if ($date) {
      $stmt->bindValue(':date', $date, PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // COUNT TOTAL ROWS FOR PAGINATION BASED ON THE SEARCH QUERY AND STATUS
  public function getTotalRows($query, $status, $date)
  {
    $searchQuery = '%' . $query . '%';
    $statusCondition = $status ? 'AND status = :status' : '';
    $dateCondition = $date ? 'AND DATE(pickup_date) = :date' : '';

    $sql = "SELECT COUNT(*) as total FROM booking 
            WHERE (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query OR service_type LIKE :query)
            AND status IN ('for pick-up', 'for delivery')
            $statusCondition
            $dateCondition";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if ($status) {
      $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    if ($date) {
      $stmt->bindValue(':date', $date, PDO::PARAM_STR);
    }

    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['total'];
  }

  // Date From Claude
  public function getUniqueDates()
  {
    $sql = "SELECT DISTINCT DATE(pickup_date) as unique_date FROM booking 
            WHERE status IN ('for pick-up', 'for delivery') 
            ORDER BY pickup_date";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }

  // VIEW 1 ROW FROM DATABASE
  public function viewSummary($id)
  {
    $sql = 'SELECT * FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();

    return $result;
  }

  // UPDATE PICKUP STATUS FROM DATABASE
  public function updateDelivery($id)
  {
    $sql = 'UPDATE booking SET status = :status, is_read = :is_read WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'delivered',
      'is_read' => 0,
      'id' => $id
    ]);

    return true;
  }

  // Count the Total Based on Status from Database
  public function countByStatus($status)
  {
    $sql = 'SELECT COUNT(*) as count FROM booking WHERE status = :status';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['status' => $status]);
    $result = $stmt->fetch();
    return $result['count'];
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

  public function fetch_new_deliveries()
  {
    $sql = 'SELECT id, status, created_at 
            FROM booking 
            WHERE delivery_is_read = 0 
              AND (status = "for pick-up" OR status = "for delivery") 
            ORDER BY id DESC';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function fetch_new_bookings_delivery()
  {
    $sql = 'SELECT id, created_at 
            FROM booking 
            WHERE admin_is_read = 0 
            ORDER BY id DESC';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function mark_delivery_as_read($id)
  {
    $sql = 'UPDATE booking SET delivery_is_read = 1 WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute(['id' => $id]);
  }

  // public function mark_booking_as_read($id)
  // {
  //   $sql = 'UPDATE booking SET admin_is_read = 1 WHERE id = :id';
  //   $stmt = $this->conn->prepare($sql);
  //   return $stmt->execute(['id' => $id]);
  // }

  // Update Kilo and Proof of Kilo for an Existing Booking
  public function updateKiloAndProof($id, $kilo, $image_path)
  {
    $sql = 'UPDATE booking SET kilo = :kilo, image_proof = :image_path, status = :status,is_read = :is_read WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'kilo' => $kilo,
      'image_path' => $image_path,
      'status' => 'on process',
      'is_read' => 0,
      'id' => $id,
    ]);
    $result = $stmt->rowCount() > 0;

    return $result;
  }

  // Update Proof of Delivery and receipt in DATABASE
  public function updateProofAndReceipt($id, $proof_path, $receipt_path)
  {
    $sql = 'UPDATE booking 
            SET delivery_proof = :proof_path, 
                receipt = :receipt_path, 
                status = :status, 
                is_read = :is_read, 
                delivery_date = NOW() 
            WHERE id = :id';

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'proof_path' => $proof_path,
      'receipt_path' => $receipt_path,
      'status' => 'delivered',
      'is_read' => 0,
      'id' => $id,
    ]);
    $result = $stmt->rowCount() > 0;

    return $result;
  }


  // Fetch All Pending In DATABASE
  public function fetchPendingBooking($page, $limit, $query)
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

  // Update Pending Status to 'For Pickup' in DATABASE
  public function admit($id)
  {
    $sql = 'UPDATE booking SET status = :status, is_read = :is_read WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'for pick-up',
      'is_read' => 0,
      'id' => $id,
    ]);

    return true;
  }

  // Update Pending Status to 'For Pickup' in DATABASE
  public function denied($id)
  {
    $sql = 'DELETE FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
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

  // Update Booking Details in Database
  public function updateBooking($id, $pickup_date, $pickup_time)
  {
    $sql = 'UPDATE booking SET pickup_date = :pickup_date, pickup_time = :pickup_time WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'id' => $id,
      'pickup_date' => $pickup_date,
      'pickup_time' => $pickup_time,
    ]);

    return true;
  }

  public function pickupAndDeliveries($timeInterval)
  {
    $sql = "
        SELECT * FROM booking 
        WHERE (status = 'for pick-up' OR status = 'for delivery')
          AND pickup_date = CURDATE() -- Only today's bookings
          AND TIMESTAMPDIFF(
              MINUTE,
              NOW(),
              CONCAT(pickup_date, ' ', STR_TO_DATE(pickup_time, '%h:%i %p'))
          ) <= :timeInterval
          AND TIMESTAMPDIFF(
              MINUTE,
              NOW(),
              CONCAT(pickup_date, ' ', STR_TO_DATE(pickup_time, '%h:%i %p'))
          ) >= 0
    ";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':timeInterval', $timeInterval, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  // TOTAL BOOKING IN DATABASE
  public function getTotalBookings($period)
  {
    $query = "";
    $params = [];

    switch ($period) {
      case 'today':
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'complete' THEN 1 ELSE 0 END) as complete
                  FROM booking 
                  WHERE DATE(created_at) = CURDATE()";
        break;
      case 'yesterday':
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'complete' THEN 1 ELSE 0 END) as complete
                  FROM booking 
                  WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY";
        break;
      case 'last-week':
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'complete' THEN 1 ELSE 0 END) as complete
                  FROM booking 
                  WHERE DATE(created_at) >= CURDATE() - INTERVAL 1 WEEK";
        break;
      case 'last-month':
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'complete' THEN 1 ELSE 0 END) as complete
                  FROM booking 
                  WHERE DATE(created_at) >= CURDATE() - INTERVAL 1 MONTH";
        break;
      default:
        return false;
    }

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // TOTAL COMPLAINTS IN DATABASE
  public function getTotalComplaints($period)
  {
    $query = "";
    $params = [];

    switch ($period) {
      case 'today':
        $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
                      FROM complaints 
                      WHERE DATE(created_at) = CURDATE()";
        break;
      case 'yesterday':
        $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
                      FROM complaints 
                      WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY";
        break;
      case 'last-week':
        $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
                      FROM complaints 
                      WHERE DATE(created_at) >= CURDATE() - INTERVAL 1 WEEK";
        break;
      case 'last-month':
        $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
                      FROM complaints 
                      WHERE DATE(created_at) >= CURDATE() - INTERVAL 1 MONTH";
        break;
      default:
        return false;
    }

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // TOTAL USERS IN DATABASE WITH ROLE 'user'
  public function getTotalUsers($period)
  {
    $query = "";

    switch ($period) {
      case 'today':
        $query = "SELECT 
                        COUNT(*) as total
                      FROM users 
                      WHERE role = 'user' AND DATE(created_at) = CURDATE()";
        break;
      case 'yesterday':
        $query = "SELECT 
                        COUNT(*) as total
                      FROM users 
                      WHERE role = 'user' AND DATE(created_at) = CURDATE() - INTERVAL 1 DAY";
        break;
      case 'last-week':
        $query = "SELECT 
                        COUNT(*) as total
                      FROM users 
                      WHERE role = 'user' AND DATE(created_at) >= CURDATE() - INTERVAL 1 WEEK";
        break;
      case 'last-month':
        $query = "SELECT 
                        COUNT(*) as total
                      FROM users 
                      WHERE role = 'user' AND DATE(created_at) >= CURDATE() - INTERVAL 1 MONTH";
        break;
      default:
        return false;
    }

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}

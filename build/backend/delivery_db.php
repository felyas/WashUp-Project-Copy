<?php

require_once './db_connection.php';

class Database extends Config
{

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
  // public function updatePickup($id)
  // {
  //   $sql = 'UPDATE booking SET status = :status, is_read = :is_read WHERE id = :id';
  //   $stmt = $this->conn->prepare($sql);
  //   $stmt->execute([
  //     'status' => 'on process',
  //     'is_read' => 0,
  //     'id' => $id
  //   ]);

  //   return true;
  // }

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

  // Fetch deliveries with status 'for pick-up' or 'for delivery' and delivery_is_read = 0
  public function fetch_new_deliveries()
  {
    $sql = 'SELECT id, status, created_at 
            FROM booking 
            WHERE delivery_is_read = 0 
              AND (status = "for pick-up" OR status = "for delivery") 
            ORDER BY id DESC';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    // Fetch all relevant delivery notifications
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $notifications;
  }

  // Mark delivery notification as read
  public function mark_delivery_as_read($deliveryId)
  {
    $sql = 'UPDATE booking SET delivery_is_read = 1 WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $deliveryId]);
  }

  // Update Kilo and Proof of Kilo for an Existing Booking
  public function updateKiloAndProof($id, $kilo, $image_path) {
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

  public function updateProofAndReceipt($id, $proof_path, $receipt_path){
    $sql = 'UPDATE booking SET delivery_proof = :proof_path, receipt = :receipt_path, status = :status, is_read = :is_read WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'proof_path' => $proof_path,
      'receipt_path' => $receipt_path,
      'status' => 'delivered',
      'is_read' => 0,
      'id' =>$id,
    ]);
    $result = $stmt->rowCount() > 0;

    return $result;
  }



}

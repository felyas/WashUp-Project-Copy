<?php

require_once './db_connection.php';

class Database extends Config
{

  // FETCH ROWS WHO HAS A STATUS OF 'FOR PICK-UP' & 'FOR DELIVERY'
  public function readAll($start, $limit, $column, $order, $query, $status)
  {
    $searchQuery = '%' . $query . '%'; // Adding wildcards for search
    $statusCondition = $status ? 'AND status = :status' : ''; // Check if status filter is applied

    // Constructing the SQL query to search and order results
    $sql = "SELECT * FROM booking 
        WHERE (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query OR service_type LIKE :query OR address LIKE :query)
        AND status IN ('for pick-up', 'for delivery')
        $statusCondition
        ORDER BY $column $order 
        LIMIT :start, :limit";

    // Preparing the SQL statement
    $stmt = $this->conn->prepare($sql);

    // Binding values for the search query, pagination start point, and limit
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if ($status) {
      $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    // Executing the query
    $stmt->execute();

    // Fetching the result as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // COUNT TOTAL ROWS FOR PAGINATION BASED ON THE SEARCH QUERY AND STATUS
  public function getTotalRows($query, $status)
  {
    $searchQuery = '%' . $query . '%'; // Wildcards for search
    $statusCondition = $status ? 'AND status = :status' : ''; // Status condition

    // SQL to count the total number of matching rows
    $sql = "SELECT COUNT(*) as total FROM booking 
            WHERE (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query  OR service_type LIKE :query)
            AND status IN ('for pick-up', 'for delivery')
            $statusCondition";

    // Preparing the statement
    $stmt = $this->conn->prepare($sql);

    // Binding the search query value
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if ($status) {
      $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }

    // Executing the query
    $stmt->execute();

    // Fetching the total number of rows
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the total number of rows
    return $row['total'];
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
  public function updatePickup($id)
  {
    $sql = 'UPDATE booking SET status = :status, is_read = :is_read WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'on process',
      'is_read' => 0,
      'id' => $id
    ]);

    return true;
  }

  // UPDATE PICKUP STATUS FROM DATABASE
  public function updateDelivery($id)
  {
    $sql = 'UPDATE booking SET status = :status, is_read = :is_read WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'is receive',
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
}

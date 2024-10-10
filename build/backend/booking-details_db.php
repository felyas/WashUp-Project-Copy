<?php

require_once './db_connection.php';

class Database extends Config
{

  // Read All Row from Database with Sorting, Pagination, Search, and Status
  public function readAll($start, $limit, $column, $order, $query, $status)
  {
    $searchQuery = '%' . $query . '%'; // Adding wildcards for search
    $statusCondition = $status ? 'AND status = :status' : ''; // Check if status filter is applied

    // Constructing the SQL query to search and order results
    $sql = "SELECT * FROM booking 
        WHERE (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query OR address LIKE :query)
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

  // Count total rows for pagination based on the search query and status
  public function getTotalRows($query, $status)
  {
    $searchQuery = '%' . $query . '%'; // Wildcards for search
    $statusCondition = $status ? 'AND status = :status' : ''; // Status condition

    // SQL to count the total number of matching rows
    $sql = "SELECT COUNT(*) as total FROM booking 
            WHERE (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query OR address LIKE :query)
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

  // Fetch User Specific Booking From Database
  public function readOne($id)
  {
    $sql = 'SELECT * FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();

    return $result;
  }

  // UPDATE ON PROCESS STATUS FROM DATABASE
  public function done($id) {
    $sql = 'UPDATE booking SET status = :status, is_read = :is_read WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'for delivery',
      'is_read' => 0,            
      'id' => $id      
    ]);

    return true;
  }

  // DELETE BOOKING REQUEST FROM DATABASE
  public function deniedBooking($id) {
    $sql = 'DELETE FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);

    return true;
  }

  public function customerInfo($id) {
    $sql = 'SELECT * FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id ]);
    $result = $stmt->fetch();

    return $result;
  }

  public function updateInventory($item, $quantity) {
    $sql = 'UPDATE inventory
            SET quantity = quantity - :quantity
            WHERE product_name = :product_name';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'quantity' => $quantity,
      'product_name' => $item,
    ]);

    return true;
  }

  public function updateKiloAndItems($id, $kilo, $items) {
    $sql = 'UPDATE booking
            SET kilo = :kilo, item_used = :item_used
            WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $itemUsed = json_encode($items);
    $stmt->execute([
      'kilo' => $kilo,
      'item_used' => $itemUsed,
      'id' => $id,
    ]);

    return true;
  }

  public function getItemQuantity($item) {
    $sql = 'SELECT quantity FROM inventory WHERE product_name = :product_name';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['product_name' => $item]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['quantity'] : 0;
  }
  
}

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

  // Read All Row from Database with Sorting, Pagination, Search, and Status
  public function readAll($start, $limit, $column, $order, $query, $status, $service)
  {
    $searchQuery = '%' . $query . '%'; // Adding wildcards for search
    $statusCondition = $status ? 'AND status = :status' : ''; // Check if status filter is applied
    $serviceCondition = $service ? 'AND service_type = :service_type' : ''; // Check if status filter is applied

    // Constructing the SQL query to search and order results
    $sql = "SELECT * FROM booking 
        WHERE status IN ('for pick-up', 'on process', 'for delivery') AND (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query OR address LIKE :query)
        $statusCondition 
        $serviceCondition
        ORDER BY $column $order 
        LIMIT :start, :limit";

    // Preparing the SQL statement
    $stmt = $this->conn->prepare($sql);

    // Binding values for the search query, pagination start point, and limit
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if ($status) {
      $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    if ($service) {
      $stmt->bindValue(':service_type', $service, PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    // Executing the query
    $stmt->execute();

    // Fetching the result as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Count total rows for pagination based on the search query and status
  public function getTotalRows($query, $status, $service)
  {
    $searchQuery = '%' . $query . '%'; // Wildcards for search
    $statusCondition = $status ? 'AND status = :status' : ''; // Status condition
    $serviceCondition = $service ? 'AND service_type = :service_type' : ''; // Status condition

    // SQL to count the total number of matching rows
    $sql = "SELECT COUNT(*) as total FROM booking 
            WHERE status IN ('for pick-up', 'on process', 'for delivery') AND (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query OR address LIKE :query)
            $statusCondition
            $serviceCondition";

    // Preparing the statement
    $stmt = $this->conn->prepare($sql);

    // Binding the search query value
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if ($status) {
      $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    if ($service) {
      $stmt->bindValue(':service_type', $service, PDO::PARAM_STR);
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
  public function done($id)
  {
    $sql = 'UPDATE booking SET status = :status, is_read = :is_read, delivery_is_read = :delivery_is_read WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'for delivery',
      'is_read' => 0,
      'delivery_is_read' => 0,
      'id' => $id
    ]);

    return true;
  }

  // DELETE BOOKING REQUEST FROM DATABASE
  public function deniedBooking($id)
  {
    $sql = 'DELETE FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);

    return true;
  }

  public function customerInfo($id)
  {
    $sql = 'SELECT * FROM booking WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();

    return $result;
  }

  public function updateInventory($item, $quantity)
  {
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

  public function updateKiloAndItems($id, $items)
  {
    $sql = 'UPDATE booking
            SET item_used = :item_used
            WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $itemUsed = json_encode($items);
    $stmt->execute([
      'item_used' => $itemUsed,
      'id' => $id,
    ]);

    return true;
  }

  public function getItemQuantity($item)
  {
    $sql = 'SELECT quantity FROM inventory WHERE product_name = :product_name';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['product_name' => $item]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['quantity'] : 0;
  }

  // GET ALL THE ITEMS FROM INVENTORY TABLE
  public function fethcItems() {
    $sql = 'SELECT * FROM inventory';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
  }
}

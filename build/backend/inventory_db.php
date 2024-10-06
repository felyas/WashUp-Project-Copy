<?php

require_once './db_connection.php';

class Database extends Config
{

  // Insert New Item Into Database
  public function addItem($product_name, $bar_code, $quantity, $max_quantity)
  {
    $sql = 'INSERT INTO inventory (product_name, bar_code, quantity, max_quantity) VALUES (:product_name, :bar_code, :quantity, :max_quantity)';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'product_name' => $product_name,
      'bar_code' => $bar_code,
      'quantity' => $quantity,
      'max_quantity' => $max_quantity,
    ]);

    return true;
  }

  // Fetch All Row from Database with Sorting, Pagination, Search
  public function readAll($start, $limit, $column, $order, $query, $status)
  {
    $searchQuery = '%' . $query . '%'; // wild card for search
    $statusCondition = $status ? 'AND status = :status' : ''; // Check if status filter is applied

    $sql = "SELECT * FROM inventory
          WHERE (product_name LIKE :query OR quantity LIKE :query OR product_id LIKE :query) 
          $statusCondition
          ORDER BY $column $order LIMIT :start, :limit";  // Fixed LIMIT clause
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if ($status) {
      $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Count Total Row for Pagination Based on the Search Query
  public function getTotalRows($query, $status)
  {
    $searchQuery = '%' . $query . '%';
    $statusCondition = $status ? 'AND status = :status' : ''; // Status conditio
    $sql = "SELECT COUNT(*) as total FROM inventory
          WHERE (product_name LIKE :query OR quantity LIKE :query OR product_id LIKE :query)
          $statusCondition";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if ($status) {
      $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['total'];
  }

  // View Item Detail from Database
  public function itemDetail($product_id)
  {
    $sql = 'SELECT * FROM inventory WHERE product_id = :product_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['product_id' => $product_id]);
    $result = $stmt->fetch();

    return $result;
  }


  // Update Item from Database
  public function updateItem($product_id, $quantity)
  {
    $sql = 'UPDATE inventory SET quantity = :quantity WHERE product_id = :product_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'product_id' => $product_id,
      'quantity' => $quantity,
    ]);

    return true;
  }

  // Delete Item from Database
  public function deleteItem($product_id)
  {
    $sql = 'DELETE FROM inventory WHERE product_id = :product_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['product_id' => $product_id]);

    return true;
  }

  // Update Item Status in Database
  public function updateItemStatus($product_id, $status)
  {
    $sql = 'UPDATE inventory SET status = :status WHERE product_id = :product_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => $status,
      'product_id' => $product_id,
    ]);

    return true;
  }
}

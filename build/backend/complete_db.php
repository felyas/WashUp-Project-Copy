<?php

require_once './db_connection.php';

class Database extends Config
{

  // READ ALL COMPLETE BOOKING FROM DATABASE
  public function readAll($start, $limit, $column, $order, $query) {
    $searchQuery = '%' . $query . '%';

    $sql = "SELECT * FROM booking WHERE status = :status AND  (fname LIKE :query OR lname LIKE :query OR phone_number LIKE :query)
    ORDER BY $column $order LIMIT :start, :limit;
    ";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    $stmt->bindValue(':status', 'complete', PDO::PARAM_STR);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
  }

  // COUNT ALL TOTAL ROWS
  public function getTotalRows($query) {
    $searchQuery = '%' . $query . '%';

    $sql = "SELECT COUNT(*) AS total FROM booking WHERE status = 'complete' AND (fname LIKE :query OR lname LIke :query OR phone_number LIKE :query)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);

    $stmt->execute();
    $row = $stmt->fetch();

    return $row['total'];
  }

}
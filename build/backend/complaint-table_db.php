<?php

require_once './db_connection.php';

class Database extends Config
{

  // Fetch All Row from Database with Sorting, Pagination, Search
  public function readAll($user_id, $start, $limit, $column, $order, $query, $status)
  {
    $searchQuery = '%' . $query . '%'; // wild card for search
    $statusCondition = $status ? 'AND status = :status' : '';

    $sql = "SELECT * FROM complaints
            WHERE user_id = :user_id 
              AND (first_name LIKE :query OR last_name LIKE :query OR phone_number LIKE :query OR email LIKE :query)
              $statusCondition
            ORDER BY $column $order 
            LIMIT :start, :limit";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if ($status) {
      $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue('user_id', $user_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Count Total Row for Pagination Based on the Search Query
  public function getTotalRows($query, $status)
  {
    $searchQuery = '%' . $query . '%';
    $statusCondition = $status ? 'AND status = :status' : ''; // Status conditio
    $sql = "SELECT COUNT(*) as total FROM complaints
           WHERE (first_name LIKE :query OR last_name LIKE :query OR phone_number LIKE :query OR email)
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

  // READ ONE DATA ON DATABASE
  public function readInfo($id)
  {
    $sql = 'SELECT * FROM complaints WHERE complaint_id = :complaint_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['complaint_id' => $id]);
    $result = $stmt->fetch();

    return $result;
  }

  // Deleting Complaint Record on DATABASE
  public function delete($id)
  {
    $sql = 'DELETE from complaints WHERE complaint_id = ;complaint_id ';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['complaint_id' => $id]);

    return true;
  }
}

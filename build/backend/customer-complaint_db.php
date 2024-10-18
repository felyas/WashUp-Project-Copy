<?php

require_once './db_connection.php';

class Database extends Config
{

  // Fetch All Row from Database with Sorting, Pagination, Search
  public function readAll($start, $limit, $column, $order, $query, $status)
  {
    $searchQuery = '%' . $query . '%'; // wild card for search
    $statusCondition = $status ? 'AND status = :status' : '';

    $sql = "SELECT * FROM complaints
            WHERE (first_name LIKE :query OR last_name LIKE :query OR phone_number LIKE :query OR email)
            $statusCondition
            ORDER BY $column $order LIMIT :start, :limit";
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

  // UPDATE STATUS FROM DATABASE
  public function resolved($id)
  {
    $sql = 'UPDATE complaints SET status = :status WHERE complaint_id = :complaint_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'status' => 'resolved',
      'complaint_id' => $id,
    ]);

    return true;
  }

  // DELETE COMPLAINT RECORD ON DATABASE
  public function delete($id)
  {
    $sql = 'DELETE FROM complaints WHERE complaint_id = :complaint_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['complaint_id' => $id]);

    return true;
  }

  // Count the Total Based on Status from Database
  public function countByStatus($status)
  {
    $sql = 'SELECT COUNT(*) as count FROM complaints WHERE status = :status';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['status' => $status]);
    $result = $stmt->fetch();

    return $result['count']; // Return the count of rows
  }
}

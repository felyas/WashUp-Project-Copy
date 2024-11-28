<?php

require_once './db_connection.php';

class Database extends Config
{

  // ARCHIVING DATA FROM DATABASE
  public function archiveRecord($user_id, $origin, $key, $value)
  {
    $this->conn->beginTransaction();

    $sqlFetch = "SELECT * FROM $origin WHERE $key = :value";
    $stmtFetch = $this->conn->prepare($sqlFetch);
    $stmtFetch->execute(['value' => $value]);
    $result = $stmtFetch->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
      throw new Exception("Record not found in '. $origin .'");
    }

    $dataJson = json_encode($result);

    // INSERT INTO ARCHIVE TABLE
    $sqlArchive = "INSERT iNTO user_archive (user_id, origin_table, data) VALUES (:user_id, :origin, :data)";
    $stmtArchive = $this->conn->prepare($sqlArchive);
    $stmtArchive->execute([
      'origin' => $origin,
      'data' => $dataJson,
      'user_id' => $user_id
    ]);

    // DELETE THE RECORD FROM THE ORIGIN TABLE
    $sqlDelete = "DELETE FROM $origin WHERE $key = :value";
    $stmtDelete = $this->conn->prepare($sqlDelete);
    $stmtDelete->execute(['value' => $value]);

    // Commit the transaction
    $this->conn->commit();

    return true;
  }

  // RECOVER DATA FROM USER ARCHIVE
  public function recoverRecord($id) {
    $this->conn->beginTransaction();

    $sqlFetch = "SELECT * FROM user_archive WHERE id = :id";
    $stmtFetch = $this->conn->prepare($sqlFetch);
    $stmtFetch->execute(['id' => $id]);
    $result = $stmtFetch->fetch(PDO::FETCH_ASSOC);

    $origin = $result['origin_table'];
    $data = json_decode($result['data'], true);

    // RECOVERY QUERY
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_map(fn($col) => ":$col", array_keys($data)));

    // INSERT BACK INTO THE ORIGIN TABLE
    $sqlInsert = "INSERT INTO $origin ($columns) VALUES ($placeholders)";
    $stmtInsert = $this->conn->prepare($sqlInsert);
    $stmtInsert->execute($data);

    // DELETE THE RECORD FROM THE USER ARCHIVE TABLE
    $sqlDelete = "DELETE FROM user_archive WHERE id = :id";
    $stmtDelete = $this->conn->prepare($sqlDelete);
    $stmtDelete->execute(['id' => $id]);

    $this->conn->commit();

    return true;
  }

  // READ ALL DATA FROM ARCHIVE TABLE
  public function readAll($user_id, $start, $limit, $column, $order, $query, $origin)
  {

    $searchQuery = '%' . $query . '%';
    $statusCondition = $origin ? 'AND origin_table = :origin' : '';

    $sql = "SELECT * FROM user_archive
            WHERE user_id = :user_id
            AND (id LIKE :query OR user_id = :query OR origin_table = :query)
            $statusCondition
            ORDER BY $column $order
            LIMIT :start, :limit";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if($origin) {
      $stmt->bindValue(':origin', $origin, PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue('user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
  }

  // COUNT TOTAL ROW FOR PAGINATION BASED ON THE SEARCH QUERY
  public function getTotalRows($query, $origin) {
    $searchQuery = '%' . $query . '%';
    $statusCondition = $origin ? 'AND origin_table = :origin' : '';

    $sql = "SELECT COUNT(*) as total FROM user_archive
            WHERE (id LIKE :query OR user_id = :query OR origin_table = :query)
            $statusCondition";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':query', $searchQuery, PDO::PARAM_STR);
    if($origin) {
      $stmt->bindValue(':origin', $origin, PDO::PARAM_STR);
    }
    $stmt->execute();
    $row = $stmt->fetch();

    return $row['total'];
  }

  // PUBLIC FUNCTION DELETE RECORD IN DATABASE
  public function deleteRecord($id) {
    $sql = 'DELETE from user_archive WHERE id = :id ';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'id' => $id,
    ]);
    
    return true;
  }
}

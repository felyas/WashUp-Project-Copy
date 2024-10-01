<?php

require_once './db_connection.php';

class Database extends Config
{

  // Insert New Admin Account into Database
  public function insertAdmin($fname, $lname, $email, $role, $password, $cpassword)
  {
    if ($password !== $cpassword) {
      return 'Passwords do not match.';  // Return error message
    }

    // Check if email already exists
    $sql = 'SELECT * FROM users WHERE email = :email';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
      return 'Email is already taken.';  // Return error message
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into the database
    $sql2 = 'INSERT INTO users (first_name, last_name, email, password, verification_status, role, expires_at) 
               VALUES (:fname, :lname, :email, :password, :verification_status, :role, :expires_at)';
    $stmt = $this->conn->prepare($sql2);

    if ($stmt->execute([
      ':fname' => $fname,
      ':lname' => $lname,
      ':email' => $email,
      ':password' => $hashedPassword,
      ':verification_status' => 1,
      ':role' => $role,
      ':expires_at' => 0,
    ])) {
      return true;  // Return true on success
    }

    return 'Failed to add user. Please try again later.';  // Return error message if insertion fails
  }

  // Fetch All Row from Database with Sorting, Pagination, Search
  public function readAll($start, $limit, $column, $order, $query, $status)
  {
    $searchQuery = '%' . $query . '%'; // wild card for search
    $statusCondition = $status ? 'AND role = :status' : ''; // Check if status filter is applied

    $sql = "SELECT * FROM users
          WHERE (first_name LIKE :query OR last_name LIKE :query OR id LIKE :query OR email LIKE :query) 
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
    $statusCondition = $status ? 'AND role = :status' : ''; // Status conditio
    $sql = "SELECT COUNT(*) as total FROM users
          WHERE (first_name LIKE :query OR last_name LIKE :query OR email LIKE :query OR id LIKE :query)
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

  // View Users Detail from Database
  public function viewDetails($id) {
    $sql = 'SELECT * FROM users WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();

    return $result;
  }

  // Delete User from Database
  public function deleteUser($id) {
    $sql = 'DELETE FROM users WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id]);

    return true;
  }
}

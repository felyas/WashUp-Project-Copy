<?php

require_once './db_connection.php';

class Database extends Config
{

  // FETCH SINGLE USER FROM DATABASE
  public function fetchUser($email)
  {
    $sql = "SELECT id, password, verification_status, first_name, last_name, role FROM users WHERE email = :email";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result;
  }

  // CHECK IF EMAIL EXIST IN DATABASE
  public function checkEmailExists($email)
  {
    $sql = 'SELECT * FROM users WHERE email = :email';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['email' => $email]);
    $result = $stmt->rowCount() > 0;

    return $result;
  }

  // INSERT NEW USER INTO DATABASE
  public function insertUser($first_name, $last_name, $email, $phone_number, $hashedPassword, $otp)
  {
    $sql = "INSERT INTO users (first_name, last_name, email, phone_number,password, otp, verification_status, role)
            VALUES(:first_name, :last_name, :email, :phone_number, :password, :otp, :verification_status, :role)";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'first_name' => $first_name,
      'last_name' => $last_name,
      'email' => $email,
      'phone_number' => $phone_number,
      'password' => $hashedPassword,
      'otp' => $otp,
      'verification_status' => 0,
      'role' => 'user',
    ]);

    return true;
  }

  // CHECK IF EMAIL IS ALREADY VERIFIED IN DATABASE
  public function verification($email)
  {
    $sql = 'SELECT otp, verification_status FROM users WHERE email = :email';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result;
  }

  // UPDATE VERIFICATION_STATUS ON DATABASE
  public function verified($email) {
    $sql = 'UPDATE users SET verification_status = :verification_status WHERE email = :email';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'email' => $email,
      'verification_status' => 1,
    ]);

    return true;
  }

  // STORE THE TOKEN IN THE DATABASE
  public function storeToken($email, $token, $expires){
    $sql = 'INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at) ON DUPLICATE KEY UPDATE token = :token, expires_at = :expires_at';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'email' => $email,
      'token' => $token,
      'expires_at' => $expires
    ]);

    return true;
  }

  
}

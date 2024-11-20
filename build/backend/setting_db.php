<?php

require_once './db_connection.php';

class Database extends Config
{

  //FETCH USER DATA FROM DATABASE
  public function userInfo($user_id)
  {
    $sql = 'SELECT * FROM users WHERE id = :user_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch();

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

  // UPDATE USER INFO FROM DATABASE
  public function updateUserInfo($user_id, $fname, $lname, $phone_number, $email, $otp)
  {
    $sql = 'UPDATE users 
            SET first_name = :fname, 
                last_name = :lname, 
                phone_number = :phone_number, 
                email = :email,
                otp = :otp,
                verification_status = :verification_status
            WHERE id = :user_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'user_id' => $user_id,
      'fname' => $fname,
      'lname' => $lname,
      'phone_number' => $phone_number,
      'email' => $email,
      'otp' => $otp,
      'verification_status' => 0,
    ]);

    return true;
  }

  // UPDATE USER INFO WITHOUT CHANGING THE EMAIL ON DATABASE
  public function udpateUserInfoWithoutEmail($user_id, $fname, $lname, $phone_number, $email)
  {
    $sql = 'UPDATE users 
            SET first_name = :fname, 
                last_name = :lname, 
                phone_number = :phone_number, 
                email = :email
            WHERE id = :user_id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'user_id' => $user_id,
      'fname' => $fname,
      'lname' => $lname,
      'phone_number' => $phone_number,
      'email' => $email
    ]);

    return true;
  }

  public function updateUsersPassword($user_id, $hashed_password)
  {
    $sql = 'UPDATE users
            SET password = :password
            WHERE id = :id';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'password' => $hashed_password,
      'id' => $user_id,
    ]);

    return true;
  }
}

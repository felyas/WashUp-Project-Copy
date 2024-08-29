<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$db_name = 'washup_db';

//Create connection
$conn = new mysqli($servername, $username, $password, $db_name);

if(!$conn){
  die("Connection failed: " . $conn->connect_error);
}
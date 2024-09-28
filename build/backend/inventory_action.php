<?php
session_start();

require_once './customer_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// Handle Add Items Ajax Request
if(isset($_POST['add'])) {
  echo 'success';
}
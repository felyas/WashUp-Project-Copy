<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once './account_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// Handle Add Admin Ajax Request
if (isset($_POST['add'])) {

  $fname = $util->testInput($_POST['fname']);
  $lname = $util->testInput($_POST['lname']);
  $email = $util->testInput($_POST['email']);
  $role = $util->testInput($_POST['role']);
  $password = $util->testInput($_POST['password']);
  $cpassword = $util->testInput($_POST['cpassword']);

  // Call the method in db.php to handle the insertion
  $result = $db->insertAdmin($fname, $lname, $email, $role, $password, $cpassword);

  // Check the result and return a JSON response
  if ($result === true) {
    echo json_encode([
      'status' => 'success',
      'message' => 'User added successfully.'
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => $result  // Error message returned by the insertAdmin function
    ]);
  }
  exit();
}

// Handle View Users Ajax Request with Pagination, Sorting, and Search
if (isset($_GET['readAll'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 7;
  $start = ($page - 1) * $limit;

  $column = isset($_GET['column']) ? $_GET['column'] : 'id';
  $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
  $query = isset($_GET['query']) ? $_GET['query'] : '';
  $status = isset($_GET['status']) ? $_GET['status'] : '';

  // Get filtered and paginated users
  $users = $db->readAll($start, $limit, $column, $order, $query, $status);
  $totalRows = $db->getTotalRows($query, $status);
  $totalPages = ceil($totalRows / $limit);

  $output = '';
  if ($users) {
    foreach ($users as $row) {

      switch ($row['role']) {
        case 'user':
          $roleClasses = 'bg-yellow-400 text-yellow-700'; //oks
          break;
        case 'admin':
          $roleClasses = 'bg-[#A8C9D9] text-[#316988]'; // oks
          break;
        case 'delivery':
          $roleClasses = 'bg-[#A1B8D4] text-[#0E4483]';
          break;
        default:
          $roleClasses = 'bg-gray-400 text-gray-700';
          break;
      }

      $output .= '
        <tr class="border-b border-gray-200">
          <td class="px-4 py-2">' . $row['id'] . '</td>
          <td class="px-4 py-2">' . $row['first_name'] . '</td>
          <td class="px-4 py-2">' . $row['last_name'] . '</td>
          <td class="px-4 py-2">' . $row['email'] . '</td>
          
          <td class="px-4 py-2">
            <div class="w-auto py-1 px-2 ' . $roleClasses . ' font-bold rounded-lg text-center">
              ' . strtoupper($row['role']) . '
            </div>
          </td>

          <td class="min-w-[100px] h-auto flex items-center justify-start space-x-2 flex-grow">
            <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
              <img class="w-4 h-4" src="./img/icons/view.svg" alt="edit">
            </a>';

      // Only display the delete link for 'admin' and 'delivery' roles
      if ($row['role'] === 'admin' || $row['role'] === 'delivery') {
        $output .= '
          <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deleteLink">
            <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
          </a>';
      }

      $output .= '</td>
        </tr>';
    }

    $paginationOutput = '<div class="flex justify-center items-center space-x-2">';

    // Previous button
    if ($page > 1) {
      $paginationOutput .= '<a href="#" data-page="' . ($page - 1) . '" class="pagination-link bg-gray-200 text-gray-600 px-4 py-2 rounded">Previous</a>';
    } else {
      $paginationOutput .= '<span class="bg-gray-300 text-gray-500 px-4 py-2 rounded">Previous</span>';
    }

    // Next button
    if ($page < $totalPages) {
      $paginationOutput .= '<a href="#" data-page="' . ($page + 1) . '" class="pagination-link bg-gray-200 text-gray-600 px-4 py-2 rounded">Next</a>';
    } else {
      $paginationOutput .= '<span class="bg-gray-300 text-gray-500 px-4 py-2 rounded">Next</span>';
    }

    $paginationOutput .= '</div>';

    // Return JSON response
    echo json_encode([
      'users' => $output,
      'pagination' => $paginationOutput,
    ]);
  } else {
    echo json_encode([
      'items' => '<tr><td colspan="8" class="text-center py-4 text-gray-200">No Bookings Found!</td></tr>',
      'pagination' => ''
    ]);
  }
}

// Handle View Users Detail Ajax Request
if (isset($_GET['view'])) {
  $id = $_GET['id'];
  $userDetail = $db->viewDetails($id);

  echo json_encode($userDetail);
}

// Handle Delete Users Detail Ajax Request 
if (isset($_GET['delete'])) {
  $id = $_GET['id'];

  if ($db->deleteUser($id)) {
    echo $util->showMessage('success', 'User deleted successfully!');
  }
}

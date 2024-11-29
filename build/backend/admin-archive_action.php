<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once './admin-archive_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// HANDLE ARCHIVE DATA AJAX REQUEST
if (isset($_GET['archive'])) {
  $origin = $_GET['origin_table'];
  $key = $_GET['key'];
  $value = $_GET['value'];
  $user_id = $_SESSION['user_id'];

  $result = $db->archiveRecord($user_id, $origin, $key, $value);

  if ($result) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Record archived successfully'
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Failed to archive record'
    ]);
  }
  exit();
}

// HANDLE RECOVER DATA AJAX REQUEST
if (isset($_GET['recover'])) {
  $archiveId = $_GET['archive_id'];

  $result = $db->recoverRecord($archiveId);

  if ($result) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Record recovered successfully'
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Failed to recover record'
    ]);
  }
}

// READ ALL DATA AJAX REQUEST
if (isset($_GET['readAll'])) {
  $user_id = $_SESSION['user_id'];
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 5;
  $start = ($page - 1) * $limit;

  $column = isset($_GET['column']) ? $_GET['column'] : 'id';
  $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
  $query = isset($_GET['query']) ? $_GET['query'] : '';
  $origin = isset($_GET['origin']) ? $_GET['origin'] : '';

  $archiveList = $db->readAll($user_id, $start, $limit, $column, $order, $query, $origin);
  $totalRows = $db->getTotalRows($query, $origin);
  $totalPages = ceil($totalRows / $limit);

  $output = '';
  if ($archiveList) {
    foreach ($archiveList as $row) {
      $data = json_decode($row['data'], true);
      // $data_id = $data['id'];
      $origin = $row['origin_table'];

      $originClasses = '';
      switch ($row['origin_table']) {
        case 'booking':
          $originClasses = 'bg-sky-600 text-white';
          break;
        case 'inventory':
          $originClasses = 'bg-[#316988] text-white';
          break;
        case 'users':
          $originClasses = 'bg-[#0E4483] text-white';
          break;
      }

      // Dynamic display of data based on origin_table
      $id_column = '';
      if ($row['origin_table'] === 'users' || $row['origin_table'] === 'booking') {
        $id_column = $data['id'];
      } elseif ($row['origin_table'] === 'inventory') {
        $id_column = $data['product_id'];
      }

      $dataColumn = '';
      if ($row['origin_table'] === 'booking') {
        $fname = isset($data['fname']) ? $data['fname'] : 'Unknown';
        $lname = isset($data['lname']) ? $data['lname'] : 'Name';
        $dataColumn = $fname . ' ' . $lname;
      } elseif($row['origin_table'] === 'users') {
        $fname = isset($data['first_name']) ? $data['first_name'] : 'Unknown';
        $lname = isset($data['last_name']) ? $data['last_name'] : 'Name';
        $dataColumn = $fname . ' ' . $lname;
      } elseif ($row['origin_table'] === 'inventory') {
        $dataColumn = isset($data['product_name']) ? $data['product_name'] : 'Unnamed Product';
      }

      // ' . $dataColumn . '

      $output .= '
                  <tr>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['id'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $id_column . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $dataColumn . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">
                    <div class="w-auto py-1 px-2 text-xs ' . $originClasses . ' font-bold rounded-lg text-center">
                        ' . strtoupper($row['origin_table']) . '
                      </div>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle min-w-[150px]">
                      <div class="flex justify-center space-x-2">
                        <div class="flex relative group">
                          <a href="#" data-archiveId="' . $row['id'] . '" id="' . $row['id'] . '" class="px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition unarchiveLink">
                            <img class="w-4 h-4" src="./img/icons/unarchive.svg" alt="edit">
                          </a>
                          <!-- Tooltip -->
                          <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                            Recover
                          </span>
                        </div>
                        
                        <div class="flex relative group">
                          <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deleteLink">
                            <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                          </a>
                          <!-- Tooltip -->
                          <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                            Delete
                          </span>
                        </div>
                        
                      </div>
                    </td>
                  </tr>
      ';
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

    echo json_encode([
      'items' => $output,
      'pagination' => $paginationOutput,
    ]);
  } else {
    echo json_encode([
      'items' => '<tr><td colspan="8" class="text-center py-4 text-gray-200">No Archive Found!</td></tr>',
      'pagination' => ''
    ]);
  }
  exit();
}

// DELETE RECORD AJAX REQUEST
if(isset($_GET['delete'])) {
  $id = $_GET['id'];

  $result = $db->deleteRecord($id);

  if($result) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Record deleted successfully',
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Something went wrong'
    ]);
  }

}

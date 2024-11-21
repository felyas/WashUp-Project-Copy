<?php
session_start();

require_once './complaint-table_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();


if (isset($_GET['readAll'])) {
  $user_id = $_SESSION['user_id'];
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 5;
  $start = ($page - 1) * $limit;

  $column = isset($_GET['column']) ? $_GET['column'] : 'id';
  $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
  $query = isset($_GET['query']) ? $_GET['query'] : '';
  $status = isset($_GET['status']) ? $_GET['status'] : '';

  // Get filtered and paginated items
  $complaints = $db->readAll($user_id, $start, $limit, $column, $order, $query, $status);
  $totalRows = $db->getTotalRows($query, $status);
  $totalPages = ceil($totalRows / $limit);

  $output = '';
  if ($complaints) {
    foreach ($complaints as $row) {

      // Determine the color classes based on the status
      $statusClasses = '';
      switch ($row['status']) {
        case 'submitted':
          $statusClasses = 'bg-gray-700 text-white';
          break;
        case 'received':
          $statusClasses = 'bg-[#0E4483] text-white';
          break;
        case 'resolved':
          $statusClasses = 'bg-green-800 text-white';
          break;
        default:
          $statusClasses = 'bg-gray-700 text-white';
          break;
      }

      $output .= '
                  <tr>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['complaint_id'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['first_name'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['last_name'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['phone_number'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['email'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle font-semibold">
                      <div class="w-auto py-1 px-2 text-xs ' . $statusClasses . ' font-bold rounded-lg text-center">
                        ' . strtoupper($row['status']) . '
                      </div>
                    </td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle min-w-[120px]">
                      <div class="flex justify-start space-x-2">
                        <a href="#" id="' . $row['complaint_id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                          <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                        </a>';

      // Check if the status is not "resolved"
      if ($row['status'] === 'resolved') {
        $output .= '<a href="#" id="' . $row['complaint_id'] . '" class="px-3 py-2 bg-red-500 hover:bg-red-600 rounded-md transition deleteLink">
                          <img class="h-4 w-4" src="./img/icons/trash.svg" alt="">
                      </a>';
      }

      $output .= '</div></td></tr>';
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
      'items' => '<tr><td colspan="8" class="text-center py-4 text-gray-200">No Complaint Found!</td></tr>',
      'pagination' => ''
    ]);
  }
  exit();
}

// Handle View Complaint Info Ajax Request
if (isset($_GET['read'])) {
  $id = $_GET['id'];
  $info = $db->readInfo($id);

  echo json_encode($info);
}

// Handle Delete Complaint Record Ajax Request
if (isset($_GET['delete'])) {
  $id = $_GET['id'];

  if ($db->delete($id)) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Complaint record deleted successfully !'
    ]);
  }
}

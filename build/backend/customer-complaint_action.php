<?php
session_start();

require_once './customer-complaint_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

if (isset($_GET['readAll'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 5;
  $start = ($page - 1) * $limit;

  $column = isset($_GET['column']) ? $_GET['column'] : 'id';
  $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
  $query = isset($_GET['query']) ? $_GET['query'] : '';
  $status = isset($_GET['status']) ? $_GET['status'] : '';

  // Get filtered and paginated items
  $complaints = $db->readAll($start, $limit, $column, $order, $query, $status);
  $totalRows = $db->getTotalRows($query, $status);
  $totalPages = ceil($totalRows / $limit);

  $output = '';
  if ($complaints) {
    foreach ($complaints as $row) {
      $output .= '
                  <tr>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['complaint_id'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['first_name'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['last_name'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['phone_number'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['email'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle font-semibold">' . $row['status'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle min-w-[150px]">
                      <div class="flex justify-center space-x-2">
                        <a href="#" id="' . $row['complaint_id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                          <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                        </a>';

      // Check if the status is "resolved"
      if ($row['status'] === 'resolved') {
        // Show delete link if resolved
        $output .= '<a href="#" id="' . $row['complaint_id'] . '" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deleteLink">
                      <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                    </a>';
      } else {
        // Show resolved link if not resolved
        $output .= '<a href="#" id="' . $row['complaint_id'] . '" class="px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition resolvedLink">
                      <img class="w-4 h-4" src="./img/icons/check.svg" alt="resolved">
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

// Handle Updating Status from Pendin to Resolved Ajax Request
if (isset($_GET['resolved'])) {
  $id = $_GET['id'];

  if ($db->resolved($id)) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Customer complaint resolved!',
    ]);
  }
}

// Handle Delete Complaint Record Ajax Record
if (isset($_GET['delete'])) {
  $id = $_GET['id'];

  if($db->delete($id)) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Customer complaint record was deleted!',
    ]);
  }
}

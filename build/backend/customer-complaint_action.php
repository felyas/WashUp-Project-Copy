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
                    <td class="px-4 py-2 border-b border-gray-300">
                      <div class="w-auto py-1 px-2 text-xs ' . $statusClasses . ' font-bold rounded-lg text-center">
              ' . strtoupper($row['status']) . '
            </div>
                    </td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle min-w-[150px]">
                      <div class="flex justify-start space-x-2">
                        <a href="#" id="' . $row['complaint_id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                          <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                        </a>';

      // Check if the status is not "resolved"
      if ($row['status'] !== 'resolved') {
        // Show delete or resolved link based on the current status
        if ($row['status'] === 'received') {
          // Show delete link if the status is "received"
          $output .= '<a href="#" id="' . $row['complaint_id'] . '" class="px-3 py-2 bg-[#3b7da3] hover:bg-[#316988] rounded-md transition resolvedLink">
                        <div class="relative h-auto w-auto">
                          <img class="h-4 w-4" src="./img/icons/recycle.svg" alt="">
                          <img src="./img/icons/circle-check-solid.svg" class="absolute -top-[4px] -right-[4px] h-3 w-3" alt="">
                        </div>
                      </a>';
        } else {
          // Show resolved link if the status is not "received"
          $output .= '<a href="#" id="' . $row['complaint_id'] . '" class="px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition receivedLink">
                        <img class="w-4 h-4" src="./img/icons/check.svg" alt="resolved">
                      </a>';
        }
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
if (isset($_GET['onAction'])) {
  $id = $_GET['id'];

  if ($db->onAction($id)) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Customer complaint received!',
    ]);
  }
}

// Handle Delete Complaint Record Ajax Record
if (isset($_GET['resolved'])) {
  $id = $_GET['id'];

  if ($db->resolved($id)) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Customer complaint record was deleted!',
    ]);
  }
}

// HANDLE FETCH TOTAL COUNT FOR CARDS AJAX REQUEST
if (isset($_GET['count_all'])) {
  // Fetch counts for different statuses
  $pendingCount = $db->countByStatus('submitted');
  $resolvedCount = $db->countByStatus('resolved');

  // Return the counts as JSON
  echo json_encode([
    'pendingCount' => $pendingCount,
    'resolvedCount' => $resolvedCount,
  ]);
}

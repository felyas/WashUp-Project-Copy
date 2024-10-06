<?php
session_start();

require_once './delivery_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// Handle readAll Ajax Request
if (isset($_GET['readAll'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 5;
  $start = ($page - 1) * $limit;

  $column = isset($_GET['column']) ? $_GET['column'] : 'id';
  $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
  $query = isset($_GET['query']) ? $_GET['query'] : '';
  $status = isset($_GET['status']) ? $_GET['status'] : '';

  // Get filtered and paginated items
  $bookings = $db->readAll($start, $limit, $column, $order, $query, $status);
  $totalRows = $db->getTotalRows($query, $status);
  $totalPages = ceil($totalRows / $limit);

  $output = '';
  if ($bookings) {
    foreach ($bookings as $row) {

      // Determine the color classes based on the status
      $statusClasses = '';
      switch ($row['status']) {
        case 'for pick-up':
          $statusClasses = 'bg-[#A8C9D9] text-[#316988]'; // oks
          break;
        case 'for delivery':
          $statusClasses = 'bg-[#B3CCE6] text-[#0E4483]'; // oks
          break;
        default:
          $statusClasses = 'bg-gray-400 text-gray-700';
          break;
      }

      $output .= '
                  <tr>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['id'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['fname'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['lname'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['phone_number'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['address'] . '</td>
                    <td class="px-4 py-2">
                      <div class="w-auto py-1 px-2 ' . $statusClasses . ' font-bold rounded-lg text-center">
                        ' . strtoupper($row['status']) . '
                      </div>
                    </td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle min-w-[150px]">
                      <div class="flex justify-center space-x-2">
                        <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                          <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                        </a>';
      // If status is 'for pick-up', append pickupLink
      if ($row['status'] === 'for pick-up') {
        $output .= '
            <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-[#3b7da3] hover:bg-[#316988] rounded-md transition pickupLink">
              <div class="relative">
                <img class="absolute -top-1 -right-3 transform -translate-x-1/2 w-3 h-3" src="./img/icons/circle-check-solid.svg" alt="process done">
                <img class="w-4 h-4" src="./img/icons/box.svg" alt="edit">
              </div>
            </a>';
      }

      // If status is 'for for delivery', append deliveryLink
      if ($row['status'] === 'for delivery') {
        $output .= '
            <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-[#0E4483] hover:bg-[#0C376A] rounded-md transition deliveryLink">
              <div class="relative">
                <img class="absolute -top-1 -right-3 transform -translate-x-1/2 w-3 h-3" src="./img/icons/circle-check-solid.svg" alt="process done">
                <img class="w-4 h-4" src="./img/icons/pickup.svg" alt="edit">
              </div>
            </a>';
      }
      $output .= '
          </td>
        </tr>
      ';
    }

    // Pagination HTML
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
      'bookings' => $output,
      'pagination' => $paginationOutput,
    ]);
  } else {
    echo json_encode([
      'bookings' => '<tr><td colspan="8" class="text-center py-4 text-gray-200">No Bookings Found!</td></tr>',
      'pagination' => ''
    ]);
  }
}

// Handle View Summary Ajax Request
if (isset($_GET['view'])) {
  $id = $_GET['id'];
  $bookingSummary = $db->viewSummary($id);

  echo json_encode($bookingSummary);
}

// Handle Update Status From Pickup to On Process Ajax Request
if (isset($_GET['update-pickup'])) {
  $id = $_GET['id'];

  if ($db->updatePickup($id)) {
    echo $util->showMessage('success', 'Status updated successfully');
  }
}

// Handle Update Status From Pickup to On Process Ajax Request
if (isset($_GET['update-delivery'])) {
  $id = $_GET['id'];

  if ($db->updateDelivery($id)) {
    echo $util->showMessage('success', 'Status updated successfully');
  }
}

// Handle Fetch All Booking Counts Request
if (isset($_GET['count_all'])) {

  // Fetch counts for different statuses
  $completeCount = $db->countByStatus('pending');
  $pickupCount = $db->countByStatus('for pick-up');
  $deliveryCount = $db->countByStatus('for delivery');

  // Return the counts as JSON
  echo json_encode([
    'pendingCount' => $completeCount,
    'pickupCount' => $pickupCount,
    'deliveryCount' => $deliveryCount,
  ]);
}

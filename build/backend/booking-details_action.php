<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once './booking-details_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// Handle Fetch All Ajax Request
if (isset($_GET['readAll'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 8; // Number of rows per page
  $start = ($page - 1) * $limit;

  $column = isset($_GET['column']) ? $_GET['column'] : 'id'; // Sorting column
  $order = isset($_GET['order']) ? $_GET['order'] : 'desc';  // Sorting order
  $query = isset($_GET['query']) ? $_GET['query'] : ''; // Search query
  $status = isset($_GET['status']) ? $_GET['status'] : ''; // Status filter

  // Get filtered and paginated bookings
  $bookings = $db->readAll($start, $limit, $column, $order, $query, $status);
  $totalRows = $db->getTotalRows($query, $status); // Total rows matching search query and status
  $totalPages = ceil($totalRows / $limit);

  // Output data
  $output = '';
  if ($bookings) {
    foreach ($bookings as $row) {
      $output .= '
        <tr class="border-b border-gray-200">
          <td class="px-4 py-2">' . $row['id'] . '</td>
          <td class="px-4 py-2">' . $row['fname'] . ' ' . $row['lname'] . '</td>
          <td class="px-4 py-2">' . $row['phone_number'] . '</td>
          <td class="px-4 py-2">' . $row['address'] . '</td>
          <td class="px-4 py-2">' . $row['status'] . '</td>
          <td class="min-w-[150px] h-auto flex items-center justify-center space-x-2 flex-grow">
            <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
              <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
            </a>';

      // If status is 'pending', append admitLink and deniedLink
      if ($row['status'] === 'pending') {
        $output .= '
            <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition admitLink">
              <img class="w-4 h-4" src="./img/icons/check.svg" alt="admit">
            </a>
            <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deniedLink">
              <img class="w-4 h-4" src="./img/icons/decline.svg" alt="deny">
            </a>';
      }

      // If status is 'on process', append the doneProcessLink
      if ($row['status'] === 'on process') {
        $output .= '
            <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-[#0E4483] hover:bg-[#0C376A] rounded-md transition doneProcessLink">
              <div class="relative">
                <!-- Circle-check icon, positioned at the top -->
                <img class="absolute -top-1 -right-3 transform -translate-x-1/2 w-3 h-3" src="./img/icons/circle-check-solid.svg" alt="process done">
                <!-- Hourglass icon -->
                <img class="w-4 h-4" src="./img/icons/hourglass-end-solid.svg" alt="edit">
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

// handle View Booking Summary Ajax Request
if (isset($_GET['view'])) {
  $id = $_GET['id'];
  $bookingSummary = $db->readOne($id);
  echo json_encode($bookingSummary);
}

// Handle Done Process Ajax Request
if (isset($_GET['done'])) {
  $id = $_GET['id'];

  if ($db->done($id)) {
    echo $util->showMessage('success', 'Booking status updated successfully');
  }

}

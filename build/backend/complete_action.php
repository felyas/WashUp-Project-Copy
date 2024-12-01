<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once './complete_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

if (isset($_GET['readAll'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 8;
  $start = ($page - 1) * $limit;

  $column = isset($_GET['column']) ? $_GET['column'] : 'id';
  $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
  $query = isset($_GET['query']) ? $_GET['query'] : '';

  $bookings = $db->readAll($start, $limit, $column, $order, $query);
  $totalRows = $db->getTotalRows($query);
  $totalPages = ceil($totalRows / $limit);

  $output = '';
  if ($bookings) {
    foreach ($bookings as $row) {
      $output .= '
                  <tr class="border-b border-gray-200 h-5 items-center justify-center">
                    <td class="px-4 py-2">' . $row['id'] . '</td>
                    <td class="px-4 py-2">' . $row['fname'] . ' ' . $row['lname'] . '</td>
                    <td class="px-4 py-2">' . $row['phone_number'] . '</td>
                    <td class="px-4 py-2 max-w-24 text-wrap">' . $row['address'] . '</td>
                    <td class="px-4 py-2">' . $row['service_type'] . '</td>
                    <td class="px-4 py-2">' . $row['pickup_date'] . ' | ' . $row['pickup_time'] . '</td>
                    <td class="px-4 py-2">' . $row['delivery_date'] . '</td>
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

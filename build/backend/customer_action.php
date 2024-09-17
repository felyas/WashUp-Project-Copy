<?php
session_start();

require_once './customer_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// Handle Add New Booking Ajax Request
if (isset($_POST['add'])) {

  $user_id = $_SESSION['user_id'];
  $fname = $util->testInput($_POST['fname']);
  $lname = $util->testInput($_POST['lname']);
  $phone_number = $util->testInput($_POST['phone_number']);
  $address = $util->testInput($_POST['address']);
  $pickup_date = $util->testInput($_POST['pickup_date']);
  $pickup_time = $util->testInput($_POST['pickup_time']);
  $service_selection = $util->testInput($_POST['service_selection']);
  $suggestions = $util->testInput($_POST['suggestions']);
  $service_type = $util->testInput($_POST['service_type']);

  if ($db->insertBooking($user_id, $fname, $lname, $phone_number, $address, $pickup_date, $pickup_time, $service_selection, $suggestions, $service_type)) {
    echo $util->showMessage('success', 'Booked successfully');
  }
}

if (isset($_GET['read'])) {
  $user_id = $_SESSION['user_id'];
  $search_query = isset($_GET['search']) ? $_GET['search'] : '';
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 4;
  $offset = ($page - 1) * $limit;

  // Get the total number of bookings for pagination
  $total_records = $db->countAllBookings($user_id, $search_query);

  // Fetch bookings with pagination and search
  $booking = $db->read($user_id, $search_query, $limit, $offset);
  $output = '';

  if ($booking) {
    foreach ($booking as $row) {
      $output .= '
              <tr>
                  <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['id'] . '</td>
                  <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['fname'] . ' ' . $row['lname'] . '</td>
                  <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['created_at'] . '</td>
                  <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['service_selection'] . '</td>
                  <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['service_type'] . '</td>
                  <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle font-semibold">' . $row['status'] . '</td>
                  <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">
                      <div class="flex justify-center space-x-2">';

      if ($row['status'] == 'pending') {
        $output .= '
                  <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition editLink">
                      <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                  </a>';
      }

      if ($row['status'] == 'pending') {
        $output .= '
                  <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deleteLink">
                      <img class="w-4 h-4" src="./img/icons/trash.svg" alt="edit">
                  </a>';
      }

      $output .= '
              <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                  <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
              </a>
          </div>
      </td>
  </tr>';
    }

    // Pagination Logic
    $total_pages = ceil($total_records / $limit);
    $current_page = $page;
    $pagination = '<div class="pagination text-center py-2">';

    // Previous button
    if ($current_page > 1) {
      $pagination .= '<a href="#" class="px-3 py-1 m-1 text-sm border border-federal bg-white text-federal rounded-md hover:bg-federal hover:text-white transition page-link" data-page="' . ($current_page - 1) . '">&#60; Prev</a>';
    }

    // Next button
    if ($current_page < $total_pages) {
      $pagination .= '<a href="#" class="px-3 py-1 m-1 text-sm border border-federal bg-white text-federal rounded-md hover:bg-federal hover:text-white transition page-link" data-page="' . ($current_page + 1) . '">Next &#62;</a>';
    }

    $pagination .= '</div>';

    echo json_encode(['rows' => $output, 'pagination' => $pagination]);
  }
}




// Handle Edit Booking Ajax Request
if (isset($_GET['edit'])) {
  $id = $_GET['id'];

  $booking = $db->readOne($id);
  echo json_encode($booking);
}

// Handle Update Booking Ajax Request
if (isset($_POST['update'])) {
  $id = $util->testInput($_POST['id']);
  $fname = $util->testInput($_POST['fname']);
  $lname = $util->testInput($_POST['lname']);
  $pickup_date = $util->testInput($_POST['pickup_date']);
  $pickup_time = $util->testInput($_POST['pickup_time']);
  $phone_number = $util->testInput($_POST['phone_number']);
  $address = $util->testInput($_POST['address']);

  if ($db->updateBooking($id, $fname, $lname, $pickup_date, $pickup_time, $phone_number, $address)) {
    echo $util->showMessage('success', 'Booking updated successfully');
  }
}

// Handle Delete Booking Ajax Request 
if (isset($_GET['delete'])) {
  $id = $_GET['id'];

  if ($db->deleteBooking($id)) {
    echo $util->showMessage('success', 'Booking deleted successfully');
  }
}

// Handle Fetch All Booking Counts Request
if (isset($_GET['count_all'])) {
  $user_id = $_SESSION['user_id'];

  // Fetch counts for different statuses
  $pickupCount = $db->countByStatus($user_id, 'for pick-up');
  $deliveryCount = $db->countByStatus($user_id, 'for delivery');
  $completeCount = $db->countByStatus($user_id, 'complete');

  // Return the counts as JSON
  echo json_encode([
    'pickupCount' => $pickupCount,
    'deliveryCount' => $deliveryCount,
    'completeCount' => $completeCount,
  ]);
}

// Handle Fetch Notifications
if (isset($_GET['fetch_notifications'])) {
  $lastCheck = $_GET['last_check'];

  // Fetch notifications for booking status updates
  $notifications = $db->fetch_notification($lastCheck);

  // Send the notifications back as a JSON response
  echo json_encode($notifications);
}


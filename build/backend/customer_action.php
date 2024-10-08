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

// Handle Fetch Booking Ajax Request
if (isset($_GET['read'])) {
  $user_id = $_SESSION['user_id'];
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 4; // Rows per page
  $search_query = isset($_GET['search']) ? $_GET['search'] : '';

  $booking = $db->read($user_id, $search_query, $limit, ($page - 1) * $limit);
  $total_records = $db->countAllBookings($user_id, $search_query);
  $total_pages = ceil($total_records / $limit);

  $output = '';
  if ($booking) {
    foreach ($booking as $row) {
      // Determine the color classes based on the status
      $statusClasses = '';
      switch ($row['status']) {
        case 'pending':
          $statusClasses = 'bg-yellow-400 text-yellow-700'; //oks
          break;
        case 'for pick-up':
          $statusClasses = 'bg-[#A8C9D9] text-[#316988]'; // oks
          break;
        case 'on process':
          $statusClasses = 'bg-[#A1B8D4] text-[#0E4483]';
          break;
        case 'for delivery':
          $statusClasses = 'bg-[#B3CCE6] text-[#0E4483]'; // oks
          break;
        case 'isreceive':
          $statusClasses = 'bg-orange-400 text-orange-700'; // oks
          break;
        case 'complete':
          $statusClasses = 'bg-green-500 text-green-800'; // oks
          break;
        default:
          $statusClasses = 'bg-gray-400 text-gray-700';
          break;
      }

      $output .= '
        <tr class="border-b border-gray-200">
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['id'] . '</td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['fname'] . ' ' . $row['lname'] . '</td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['created_at'] . '</td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['service_selection'] . '</td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['service_type'] . '</td>
          <td class="px-4 py-2">
            <div class="w-auto py-1 px-2 ' . $statusClasses . ' font-bold rounded-lg text-center">
              ' . strtoupper($row['status']) . '
            </div>
          </td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">
            <div class="flex justify-center space-x-2 min-w-[150px]">';

      // Action buttons based on status
      if ($row['status'] == 'pending') {
        $output .= '
          <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition editLink">
            <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
          </a>
          <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deleteLink">
            <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
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
  }

  // Pagination HTML
  $pagination = '<div class="flex justify-center items-center space-x-2">';

  // Previous button
  if ($page > 1) {
    $pagination .= '<a href="#" data-page="' . ($page - 1) . '" class="page-link bg-gray-200 text-gray-600 px-4 py-2 rounded">Previous</a>';
  } else {
    $pagination .= '<span class="bg-gray-300 text-gray-500 px-4 py-2 rounded">Previous</span>';
  }

  // Next button
  if ($page < $total_pages) {
    $pagination .= '<a href="#" data-page="' . ($page + 1) . '" class="page-link bg-gray-200 text-gray-600 px-4 py-2 rounded">Next</a>';
  } else {
    $pagination .= '<span class="bg-gray-300 text-gray-500 px-4 py-2 rounded">Next</span>';
  }

  $pagination .= '</div>';

  // Return JSON response
  echo json_encode(['rows' => $output, 'pagination' => $pagination]);
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
  $user_id = $_SESSION['user_id'];

  // Fetch notifications for booking status updates
  $notifications = $db->fetch_notification($lastCheck, $user_id);

  // Send the notifications back as a JSON response
  echo json_encode($notifications);
}

// Handle marking notification as read
if (isset($_GET['mark_as_read'])) {
  $notificationId = $_GET['id'];

  // Mark the specific notification as read
  $db->mark_as_read($notificationId);

  // Send a success response
  echo json_encode(['success' => true]);
}

// Handle fetch events Ajax Request
if (isset($_GET['fetch_events'])) {
  $events = $db->fetchAllEvents();
  echo json_encode($events);
}
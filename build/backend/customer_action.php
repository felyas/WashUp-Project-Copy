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

// Handle Fetch All Users Ajax Request
if (isset($_GET['read'])) {
  $user_id = $_SESSION['user_id'];
  $booking = $db->read($user_id);
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

      // Conditionally render edit link if the status is "pending"
      if ($row['status'] == 'pending') {
        $output .= '
                        <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition editLink">
                          <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                        </a>';
      }

      // Conditionally render edit link if the status is "pending"
      if ($row['status'] == 'pending') {
        $output .= '
                        <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deleteLink">
                          <img class="w-4 h-4" src="./img/icons/trash.svg" alt="edit">
                        </a>';
      }

      // Always render view link
      $output .= '
                        <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                          <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                        </a>
                      </div>
                    </td>
                  </tr>
      ';
    }

    echo $output;
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
if(isset($_GET['delete'])) {
  $id = $_GET['id'];

  if($db->deleteBooking($id)){
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


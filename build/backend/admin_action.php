<?php
session_start();

require_once './admin_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// Handle Fetch All Pending Booking Ajax Request
if (isset($_GET['readPending'])) {
  $pendingBooking = $db->fetchPendings();
  $output = '';
  if ($pendingBooking) {
    foreach ($pendingBooking as $row) {
      $output .= '
                  <tr class="border-b border-gray-200">
                    <td class="px-4 py-2">' . $row['id'] . '</td>
                    <td class="px-4 py-2 text-nowrap">' . $row['fname'] . ' ' . $row['lname'] . '</td>
                    <td class="px-4 py-2 text-nowrap">' . $row['phone_number'] . '</td>
                    <td class="px-4 py-2 text-nowrap">' . $row['address'] . '</td>
                    <td class="px-4 py-2 text-nowrap">' . $row['pickup_date'] . '</td>
                    <td class="px-4 py-2 text-nowrap">' . $row['pickup_time'] . '</td>
                    <td class="min-w-[168px] flex items-center justify-center space-x-2 flex-grow">
                      <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="edit">
                      </a>
                      <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition admitLink">
                        <img class="w-4 h-4" src="./img/icons/check.svg" alt="edit">
                      </a>
                      <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deniedLink">
                        <img class="w-4 h-4" src="./img/icons/decline.svg" alt="edit">
                      </a>
                    </td>
                  </tr>
      ';
    }
    echo $output;
  } else {
    echo '<tr>
            <td colspan="7" class="text-center py-4 text-gray-200">
              No Pending Booking Found in the Database!
            </td>
          </tr>';
  }
}

// Handle Fetch All For Pickup Booking Ajax Request
if (isset($_GET['readPickup'])) {
  $forPickupBooking = $db->fetchPickup();
  $output = '';
  if ($forPickupBooking) {
    foreach ($forPickupBooking as $row) {
      $output .= '
                  <tr class="border-b border-gray-200">
                    <td class="px-4 py-2">' . $row['id'] . '</td>
                    <td class="px-4 py-2 text-nowrap">' . $row['fname'] . ' ' . $row['lname'] . '</td>
                    <td class="px-4 py-2 text-nowrap">' . $row['phone_number'] . '</td>
                    <td class="px-4 py-2 text-nowrap">' . $row['pickup_date'] . '</td>
                    <td class="px-4 py-2 text-nowrap">' . $row['pickup_time'] . '</td>
                    <td class="min-w-[100px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="edit">
                      </a>
                    </td>
                  </tr>
      ';
    }
    echo $output;
  } else {
    echo '<tr>
            <td colspan="7" class="text-center py-4 text-gray-200">
              No For Pick-Up Booking Found in the Database!
            </td>
          </tr>';
  }
}

// Handle Fetch All For Delivery Booking Ajax Request
if (isset($_GET['readDelivery'])) {
  $deliveryBooking = $db->fetchDelivery();
  $output = '';
  if ($deliveryBooking) {
    foreach ($deliveryBooking as $row) {
      $output .= '
                  <tr class="border-b border-gray-200">
                    <td class="px-4 py-2 text-nowrap">'. $row['id'] .'</td>
                    <td class="px-4 py-2 text-nowrap">'. $row['fname'] . ' ' . $row['lname'] .'</td>
                    <td class="px-4 py-2 text-nowrap">'. $row['phone_number'] .'</td>
                    <td class="px-4 py-2 text-nowrap">'. $row['address'] .'</td>
                    <td class="min-w-[100px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="edit">
                      </a>
                    </td>
                  </tr>
      ';
    }
    echo $output;
  } else {
    echo '<tr>
            <td colspan="7" class="text-center py-4 text-gray-200">
              No For Pick-Up Booking Found in the Database!
            </td>
          </tr>';
  }
}

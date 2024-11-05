<?php
session_start();

require_once './customer_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// HANDLE PREVIOUS DATA AJAX REQUEST
if (isset($_GET['previous-data'])) {
  $user_id = $_SESSION['user_id'];

  $data = $db->previousData($user_id);
  if($data) {
    echo json_encode($data);
  } else {
    echo json_encode([
      'status' => 'no data',
    ]);
  }
}

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
        <tr class="border-b border-gray-200" data-status="' . $row['status'] . '" data-id="' . $row['id'] . '">
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['id'] . '</td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['fname'] . ' ' . $row['lname'] . '</td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['created_at'] . '</td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['service_selection'] . '</td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['service_type'] . '</td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">
            ';

      // Check if 'image_proof' is not empty and display image or fallback text
      if (!empty($row['image_proof'])) {
        $output .= '
                <img class="w-12 h-12 cursor-pointer image-proof" src="./backend/' . $row['image_proof'] . '" alt="">
              ';
      } else {
        $output .= '
                <p class="py-1 px-3 rounded-lg bg-red-400 text-red-700" >No upload yet</p>
              ';
      }

      $output .= '
          </td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">
            ';

      // Check if 'delivery_proof' is not empty and display image or fallback text
      if (!empty($row['delivery_proof'])) {
        $output .= '
                <img class="w-12 h-12 cursor-pointer image-proof" src="./backend/' . $row['delivery_proof'] . '" alt="">
              ';
      } else {
        $output .= '
                <p class="py-1 px-3 rounded-lg bg-red-400 text-red-700" >No upload yet</p>
              ';
      }

      $output .= '
          </td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">
            ';

      // Check if 'receipt' is not empty and display image or fallback text
      if (!empty($row['receipt'])) {
        $output .= '
                <img class="w-12 h-12 cursor-pointer image-proof" src="./backend/' . $row['receipt'] . '" alt="">
              ';
      } else {
        $output .= '
                <p class="py-1 px-3 rounded-lg bg-red-400 text-red-700" >No upload yet</p>
              ';
      }

      $output .= '
          </td>
          <td class="px-4 py-2">
            <div class="w-auto py-1 px-2 ' . $statusClasses . ' font-bold rounded-lg text-center">
              ' . strtoupper($row['status']) . '
            </div>
          </td>
          <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">
            <div class="flex justify-start space-x-2 min-w-[150px]">
              <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
              </a>';

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
  $phone_number = $util->testInput($_POST['phone_number']);
  $address = $util->testInput($_POST['address']);

  if ($db->updateBooking($id, $fname, $lname, $phone_number, $address)) {
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

// Handle isReceive Confirm Yes Ajax Request 
if (isset($_GET['confirmYes'])) {
  $id = $_GET['id'];

  if ($db->updateToComplete($id)) {
    echo $util->showMessage('success', 'Booking status updated successfully!');
  }
}

// Handle isReceive Confirm No Ajax Request 
if (isset($_GET['confirmNo'])) {
  $id = $_GET['id'];

  if ($db->updateToDeliveryAgain($id)) {
    echo $util->showMessage('success', 'Booking status put back to for delivery successfully!');
  }
}

// Handle fetching unavailable times for a specific date
if (isset($_GET['get_unavailable_times'])) {
  $date = $_GET['date'];
  $unavailableTimes = $db->getUnavailableTimesForDate($date);
  echo json_encode($unavailableTimes);
}

if (isset($_POST['add-complaint'])) {
  $user_id = $_SESSION['user_id'];
  $first_name = $util->testInput($_POST['fname']);
  $last_name = $util->testInput($_POST['lname']);
  $phone_number = $util->testInput($_POST['phone_number']);
  $email = $util->testInput($_POST['email']);
  $reason = $util->testInput($_POST['reason']);
  $description = $util->testInput($_POST['description']);

  $result = $db->addComplaint($user_id, $first_name, $last_name, $phone_number, $email, $reason, $description);

  if ($result === true) {
    echo json_encode([
      'status' => 'success',
      'message' => 'New complaint request send successfully!',
    ]);
  } else {
    echo json_encode([
      'status' => 'success',
      'message' => 'Failed to send complaint.',
    ]);
  }

  exit();
}

// Handle Submit Feedback Ajax Request
if (isset($_POST['new-feedback'])) {
  $user_id = $_SESSION['user_id'];
  $first_name = $_SESSION['first_name'];
  $last_name = $_SESSION['last_name'];
  $rating = $util->testInput($_POST['rating']);
  $description = $util->testInput($_POST['description']);
  $booking_id = $util->testInput($_POST['booking_id']);
  
  // Fetch email and phone number from the latest booking
  $booking_data = $db->getEmailPhone($user_id);
  $phone_number = $booking_data['phone_number'];  // Remove $ from key

  if ($db->insertFeedback($user_id, $first_name, $last_name, $rating, $description, $booking_id, $phone_number)) {
    echo json_encode([
      'status' => 'success',
      'message' => 'Thank you, your feedback was recorded!',
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Something went wrong!',
    ]);
  }
}


// Handle Fetch Feedback Ajax Request
if (isset($_GET['fetch-feedback'])) {
  $output = '';
  $feedback = $db->fetchFeedback();

  if ($feedback) {
    foreach ($feedback as $row) {
      // Start building the star rating output
      $starOutput = '';
      for ($i = 0; $i < $row['rating']; $i++) {
        $starOutput .= '<img class="w-7 h-7 mb-2" src="./img/icons/star-rating.svg" alt="Star Rating">';
      }

      $output .= '
            <div class="border border-solid border-polynesian rounded-lg p-4 m-2 flex flex-col items-center w-full">
                <div class="mb-4 flex flex-col items-center">
                  <div class="w-full h-auto flex items-center justify-center space-x-2">
                    ' . $starOutput . ' <!-- Output the dynamic stars here -->
                  </div>
                    <p id="feedback-fullname" class="text-polynesian font-semibold text-md">' . $row['first_name'] . ' ' . $row['last_name'] . '</p>
                </div>
                <div class="h-auto flex items-center justify-center relative w-full px-8">
                    <img class="w-10 h-10 absolute top-0 left-0 -translate-y-1/2" src="./img/icons/quote-left.svg" alt="Quote Left">
                    <p id="feedback-description" class="text-gray-500 text-center">' . $row['description'] . '</p>
                    <img class="w-10 h-10 absolute -bottom-5 right-0 -translate-y-1/2" src="./img/icons/quote-right.svg" alt="Quote Right">
                </div>
            </div>
        ';
    }
    echo $output;
  } else {
    echo $output = '
            <div id="feedback-container" class="w-full h-full p-2 flex flex-col items-center">
              <p id="feedback-description" class="text-gray-500 text-center">No feedback yet!</p>
            </div>
    ';
  }
}

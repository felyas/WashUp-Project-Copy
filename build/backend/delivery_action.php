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
  $date = isset($_GET['date']) && $_GET['date'] !== '' ? $_GET['date'] : null;

  // Get filtered and paginated items
  $bookings = $db->readAll($start, $limit, $column, $order, $query, $status, $date);
  $totalRows = $db->getTotalRows($query, $status, $date);
  $totalPages = ceil($totalRows / $limit);

  // Get unique dates for the filter
  $dates = $db->getUniqueDates();

  $output = '';
  if ($bookings) {
    foreach ($bookings as $row) {

      // Determine the color classes based on the status
      $statusClasses = '';
      switch ($row['status']) {
        case 'for pick-up':
          $statusClasses = 'bg-sky-600 text-white'; // oks
          break;
        case 'for delivery':
          $statusClasses = 'bg-[#0E4483] text-white'; // oks
          break;
        default:
          $statusClasses = 'bg-gray-700 text-white';
          break;
      }

      $output .= '
                  <tr>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['id'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['fname'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['lname'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['phone_number'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['address'] . '</td>
                    <td class="px-4 py-2 border-b border-gray-300">
                      <div class="w-auto py-1 px-2 text-xs ' . $statusClasses . ' font-bold rounded-lg text-center">
                        ' . strtoupper($row['status']) . '
                      </div>
                    </td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">' . $row['pickup_date'] . '</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle min-w-[200px]">
                      <div class="flex justify-center space-x-2">
                        <div class="flex relative group">
                          <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                            <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                          </a>
                          <!-- Tooltip -->
                          <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                            View
                          </span>
                        </div>
                        
                        <div class="flex relative group">
                          <a href="#" id="' . $row['id'] . '" data-address=" ' . $row['address'] . ' " class="showModalTrigger show-map-btn px-3 py-2 bg-[#0E4483] hover:bg-[#0C376A] rounded-md transition mapLink">
                            <img class="w-4 h-4" src="./img/icons/map-location-white.svg" alt="edit">
                          </a>
                          <!-- Tooltip -->
                          <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                            Map
                          </span>
                        </div>';
      // If status is 'for pick-up', append pickupLink
      if ($row['status'] === 'for pick-up') {
        $output .= '
            <div class="flex relative group">
              <a href="#" id="' . $row['id'] . '" class="updateKiloTrigger px-3 py-2 bg-[#3b7da3] hover:bg-[#316988] rounded-md transition pickupLink">
                <div class="relative">
                  <img class="absolute -top-1 -right-3 transform -translate-x-1/2 w-3 h-3" src="./img/icons/circle-check-solid.svg" alt="process done">
                  <img class="w-4 h-4" src="./img/icons/box.svg" alt="edit">
                </div>
              </a>
              <!-- Tooltip -->
              <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                Update Kilo
              </span>
            </div>';
      }

      // If status is 'for for delivery', append deliveryLink
      if ($row['status'] === 'for delivery') {
        $output .= '
            <div class="flex relative group">
              <a href="#" id="' . $row['id'] . '" class="updateProofOfDeliveryTrigger px-3 py-2 bg-[#0E4483] hover:bg-[#0C376A] rounded-md transition deliveryLink">
                <div class="relative">
                  <img class="absolute -top-1 -right-3 transform -translate-x-1/2 w-3 h-3" src="./img/icons/circle-check-solid.svg" alt="process done">
                  <img class="w-4 h-4" src="./img/icons/pickup.svg" alt="edit">
                </div>
              </a>
              <!-- Tooltip -->
              <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                Proof of Delivery
              </span>
            </div>';
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
      'dates' => $dates,
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

// Handle fetch events Ajax Request
if (isset($_GET['fetch_events'])) {
  $events = $db->fetchAllEvents();
  echo json_encode($events);
}

// delivery_action.php
if (isset($_GET['fetch_new_deliveries'])) {
  $notifications = $db->fetch_new_deliveries();
  echo json_encode($notifications);
}

if (isset($_GET['mark_as_read']) && isset($_GET['id'])) {
  $id = $_GET['id'];
  $db->mark_delivery_as_read($id);
  echo json_encode(['success' => true]);
}

// admin_action.php
if (isset($_GET['fetch_new_bookings_delivery'])) {
  $notifications = $db->fetch_new_bookings_delivery();
  echo json_encode($notifications);
}

// if (isset($_GET['mark_as_read']) && isset($_GET['id'])) {
//   $id = $_GET['id'];
//   $db->mark_booking_as_read($id);
//   echo json_encode(['success' => true]);
// }

// Handle Update Kilo Info Ajax Request
if (isset($_GET['info-for-kilo-update'])) {
  $id = $_GET['id'];
  $view = $db->viewSummary($id);

  echo json_encode($view);
}

if (isset($_POST['add-kilo'])) {
  $booking_id = $util->testInput($_POST['booking_id']);
  $kilo = $util->testInput($_POST['kilo']);
  $id = $db->viewSummary($booking_id);
  $user_id = $id['user_id'];
  $user = $db->user($user_id);

  // Handle file upload
  $target_dir = "uploads/";
  $target_file = $target_dir . basename($_FILES["file-upload"]["name"]);
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  // Validate file as an image
  if (getimagesize($_FILES["file-upload"]["tmp_name"]) !== false) {
    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES["file-upload"]["tmp_name"], $target_file)) {
      // Update booking in the database
      $result = $db->updateKiloAndProof($booking_id, $kilo, $target_file);

      if ($result === true) {
        $receiver = $user['email'];
        $subject = "Booking Update";
        $message = "The proof of kilo for your booking with ID " . $booking_id . " has been added. We're now processing your laundry.";
        $util->sendEmail($receiver, $subject, $message);

        echo json_encode([
          'status' => 'success',
          'message' => 'Kilo and image updated successfully!'
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'Failed to update booking!'
        ]);
      }
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'Error uploading image!'
      ]);
    }
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'File is not valid image!'
    ]);
  }
  exit();
}

if (isset($_GET['info-for-proof-receipt'])) {
  $id = $_GET['id'];
  $view = $db->viewSummary($id);

  echo json_encode($view);
}

if (isset($_POST['add-receipt'])) {
  $booking_id = $util->testInput($_POST['booking_id']);
  $id = $db->viewSummary($booking_id);
  $user_id = $id['user_id'];
  $user = $db->user($user_id);

  //Set up file upload directories
  $target_dir = "uploads/receipt/";
  $proof_file = $target_dir . basename($_FILES["file-proof-upload"]["name"]);
  $receipt_file = $target_dir . basename($_FILES["file-receipt-upload"]["name"]);

  // Validate file type (esure image)
  $imageFileTypeProof = strtolower(pathinfo($proof_file, PATHINFO_EXTENSION));
  $imageFileTypeReceipt = strtolower(pathinfo($receipt_file, PATHINFO_EXTENSION));

  // Check if both files are images
  if (getimagesize($_FILES["file-proof-upload"]["tmp_name"]) !== false && getimagesize($_FILES["file-receipt-upload"]["tmp_name"]) !== false) {

    // Attempt to move the uploaded files to the target directory
    $proof_upload_success = move_uploaded_file($_FILES["file-proof-upload"]["tmp_name"], $proof_file);
    $receipt_upload_success = move_uploaded_file($_FILES["file-receipt-upload"]["tmp_name"], $receipt_file);

    if ($proof_upload_success && $receipt_upload_success) {
      // Update booking in the database
      $result = $db->updateProofAndReceipt($booking_id, $proof_file, $receipt_file);

      if ($result === true) {
        $receiver = $user['email'];
        $subject = "Booking Update";
        $message = "Your booking with ID " . $booking_id . " has been successfully delivered. A receipt and proof of delivery have been sent to your dashboard.";
        $util->sendEmail($receiver, $subject, $message);

        echo json_encode([
          'status' => 'success',
          'message' => 'Delivery proof and receipt updated successfully!',
        ]);
      } else {
        echo json_encode([
          'status' => 'error',
          'message' => 'Failed to update booking',
        ]);
      }
    } else {
      echo json_encode([
        'status' => 'error',
        'message' => 'Error uploading images!',
      ]);
    }
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'One or both files are not valid images!'
    ]);
  }
  exit();
}

if (isset($_GET['read-pending'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 10; // Rows per page
  $query = isset($_GET['query']) ? $_GET['query'] : '';

  $pendingBooking = $db->fetchPendingBooking($page, $limit, $query);
  $totalRows = $db->getTotalPendingRows($query);
  $totalPages = ceil($totalRows / $limit);

  $output = '';
  if ($pendingBooking) {
    foreach ($pendingBooking as $row) {
      $output .= '
        <tr class="border-b border-gray-200">
          <td class="px-4 py-2">' . $row['id'] . '</td>
          <td class="px-4 py-2 text-nowrap">' . $row['fname'] . ' ' . $row['lname'] . '</td>
          <td class="px-4 py-2 text-nowrap">' . $row['address'] . '</td>
          <td class="min-w-[180px] flex items-center justify-start space-x-2 flex-grow">
            <div class="flex relative group">
              <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
              </a>
              <!-- Tooltip -->
              <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                View
              </span>
            </div>
            
            <div class="flex relative group">
              <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition editLink">
                <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
              </a>
              <!-- Tooltip -->
              <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                Edit
              </span>
            </div>
            
            <div class="flex relative group">
              <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition admitLink">
                <img class="w-4 h-4" src="./img/icons/check.svg" alt="admit">
              </a>
              <!-- Tooltip -->
              <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                Admit
              </span>
            </div>
            
            <div class="flex relative group">
              <a href="#" data-origin="booking" data-key="id" data-value=" '.$row['id'].' " id="' . $row['id'] . '" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deniedLink">
                <img class="w-4 h-4" src="./img/icons/decline.svg" alt="denied">
              </a>
              <!-- Tooltip -->
              <span class="absolute hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded shadow-md top-9 right-0 transform -translate-x-1/2 whitespace-nowrap z-50">
                Denied
              </span>
            </div>
            
          </td>
        </tr>
      ';
    }
  } else {
    $output = '<tr>
      <td colspan="7" class="text-center py-4 text-gray-200">
        No Pending Booking Found!
      </td>
    </tr>';
  }

  // Pagination HTML for Previous/Next
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
    'pagination' => $paginationOutput
  ]);
}

// Handle Admit Ajax Request
if (isset($_GET['admit'])) {
  $booking_id = $_GET['id'];
  $id = $db->viewSummary($booking_id);
  $user_id = $id['user_id'];
  $user = $db->user($user_id);

  if ($db->admit($booking_id)) {
    $receiver = $user['email'];
    $subject = "Booking Update";
    $message = "Your booking with ID " . $booking_id . " was admitted and is now marked as 'For Pickup'.";
    $util->sendEmail($receiver, $subject, $message);

    echo json_encode([
      'status' => 'success',
      'message' => 'Booking admitted successfully',
    ]);
  }
}

// Handle Denied Ajax Request
if (isset($_GET['denied'])) {
  $booking_id = $_GET['id'];
  $id = $db->viewSummary($booking_id);
  $user_id = $id['user_id'];
  $user = $db->user($user_id);

  if ($db->denied($booking_id)) {
    $receiver = $user['email'];
    $subject = "Booking Update";
    $message = "Your booking with ID " . $booking_id . " has been denied due to invalid inputs or other reason.";
    $util->sendEmail($receiver, $subject, $message);

    echo json_encode([
      'status' => 'success',
      'message' => 'Booking denied successfully !',
    ]);
  }
}

// Handle fetching unavailable times for a specific date
if (isset($_GET['get_unavailable_times'])) {
  $date = $_GET['date'];
  $unavailableTimes = $db->getUnavailableTimesForDate($date);
  echo json_encode($unavailableTimes);
}

// Handle fetching unavailable times for a specific date
if (isset($_GET['get_unavailable_times'])) {
  $date = $_GET['date'];
  $unavailableTimes = $db->getUnavailableTimesForDate($date);
  echo json_encode($unavailableTimes);
}

if (isset($_GET['edit-info'])) {
  $id = $_GET['id'];
  $info = $db->viewSummary($id);

  echo json_encode($info);
}

if (isset($_POST['update'])) {
  $id = $util->testInput($_POST['id']);
  $pickup_date = $util->testInput($_POST['pickup-date']);
  $pickup_time = $util->testInput($_POST['pickup_time']);

  if ($db->updateBooking($id, $pickup_date, $pickup_time)) {
    echo $util->showMessage('success', 'Booking updated successfully!');
  }
}

// Handle Display Pickup & Deliveries Ajax Request
if (isset($_GET['pickup-and-deliveries'])) {
  $timeInterval = isset($_GET['time_interval']) ? intval($_GET['time_interval']) : 30; // default to 30 if not set
  $pickupAndDeliveries = $db->pickupAndDeliveries($timeInterval);
  $output = '';

  if ($pickupAndDeliveries) {
    foreach ($pickupAndDeliveries as $row) {
      $output .= '
        <div class="border text-sm border-solid border-celestial rounded-md p-2 flex items-center justify-between w-full mb-2">
          <div class="flex flex-col items-start">
            <p>Customer Name:</p>
            <p>Date & Time:</p>
            <p>Address:</p>
            <p>Status:</p>
          </div>
          <div class="flex flex-col items-end">
            <p class="js-customer-fullname">' . $row['fname'] .  ' ' . $row['lname'] . '</p>
            <p class="js-date-time">' . $row['pickup_date'] .  ' - ' . $row['pickup_time'] . '</p>
            <p class="js-customer-address">' . $row['address'] . '</p>
            <p class="js-status">' . $row['status'] . '</p>
          </div>
        </div>
      ';
    }
    echo $output;
  } else {
    echo '<div class="border text-sm border-solid border-celestial rounded-md p-2 flex items-center justify-center w-full h-full mb-2">
            <p>No pickup & delivery yet</p>
          </div>';
  }
}

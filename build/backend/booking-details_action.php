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
  $service = isset($_GET['service']) ? $_GET['service'] : ''; // Service filter

  // Get filtered and paginated bookings
  $bookings = $db->readAll($start, $limit, $column, $order, $query, $status, $service);
  $totalRows = $db->getTotalRows($query, $status, $service); // Total rows matching search query and status
  $totalPages = ceil($totalRows / $limit);

  // Output data
  $output = '';
  if ($bookings) {
    foreach ($bookings as $row) {
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
        <tr class="border-b border-gray-200 h-5 items-center justify-center">
          <td class="px-4 py-2">' . $row['id'] . '</td>
          <td class="px-4 py-2">' . $row['fname'] . ' ' . $row['lname'] . '</td>
          <td class="px-4 py-2">' . $row['phone_number'] . '</td>
           <td class="px-4 py-2">
            <div class="w-auto py-1 px-2 font-bold rounded-lg text-start">
              ' . strtoupper($row['service_type']) . '
            </div>
          </td>
          <td class="px-4 py-2">' . $row['address'] . '</td>
          <td class="px-4 py-2 border-b border-left text-sm border-gray-200 align-middle">
            ';

      // Check if 'image_proof' is not empty and display image or fallback text
      if (!empty($row['image_proof'])) {
        $output .= '
                <img class="w-12 h-12 cursor-pointer image-proof" src="./backend/' . $row['image_proof'] . '" alt="">
              ';
      } else {
        $output .= '
                <p class="py-1 px-3 rounded-lg bg-gray-200 text-gray-500" >No upload yet</p>
              ';
      }

      $output .= '
          </td>
          <td class="px-4 py-2 border-b border-left text-sm border-gray-200 align-middle">
            ';

      // Check if 'delivery_proof' is not empty and display image or fallback text
      if (!empty($row['delivery_proof'])) {
        $output .= '
                <img class="w-12 h-12 cursor-pointer image-proof" src="./backend/' . $row['delivery_proof'] . '" alt="">
              ';
      } else {
        $output .= '
                <p class="py-1 px-3 rounded-lg bg-gray-200 text-gray-500" >No upload yet</p>
              ';
      }

      $output .= '
          </td>
          <td class="px-4 py-2 border-b border-left text-sm border-gray-200 align-middle">
            ';

      // Check if 'receipt' is not empty and display image or fallback text
      if (!empty($row['receipt'])) {
        $output .= '
                <img class="w-12 h-12 cursor-pointer image-proof" src="./backend/' . $row['receipt'] . '" alt="">
              ';
      } else {
        $output .= '
                <p class="py-1 px-3 rounded-lg bg-gray-200 text-gray-500" >No upload yet</p>
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
              <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800   rounded-md transition viewLink">
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
            </a>
            <a href="#" id="' . $row['id'] . '" class="kiloModalTrigger px-3 py-2 bg-[#090f4d] hover:bg-[#1a2479] rounded-md transition kiloLink">
              <div class="relative">
                <!-- Circle-check icon, positioned at the top -->
                <img class="absolute -top-1 -right-3 transform -translate-x-1/2 w-3 h-3" src="./img/icons/circle-check-solid.svg" alt="process done">
                <!-- Hourglass icon -->
                <img class="w-4 h-4" src="./img/icons/jug-detergent.svg" alt="edit">
              </div>
            </a>';
      }


      $output .= '
            </div>
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
  $booking_id = $_GET['id'];
  $id = $db->readOne($booking_id);
  $user_id = $id['user_id'];
  $user = $db->user($user_id);

  if ($db->done($booking_id)) {
    $receiver = $user['email'];
    $subject = "Booking Update";
    $message = "Your booking with ID " . $booking_id . " has been updated to 'For delivery'.";
    $util->sendEmail($receiver, $subject, $message);

    echo $util->showMessage('successs', 'Booking status updated successfully');
  }
}

// Handle Denied Booking Summary Ajax Request
if (isset($_GET['denied'])) {
  $id = $_GET['id'];

  if ($db->deniedBooking($id)) {
    echo $util->showMessage('success', 'Booking denied !');
  }
}

// CUSTOMER INFO AJAX REQUEST
if (isset($_GET['customer-info'])) {
  $id = $_GET['id'];
  $customerInfo = $db->customerInfo($id);
  echo json_encode($customerInfo);
}

if (isset($_POST['updatekilo'])) {
  $id = $util->testInput($_POST['bookingId']);

  $items = [];

  // First, validate all items and quantities before processing
  for ($i = 1; $i <= 3; $i++) {
    if (isset($_POST["item{$i}"]) && isset($_POST["quantity{$i}"])) {
      $item = $util->testInput($_POST["item{$i}"]);
      $quantity = $util->testInput($_POST["quantity{$i}"]);

      $currentStock = $db->getItemQuantity($item);

      // Check if the item is out of stock
      if ($currentStock <= 0) {
        echo json_encode([
          'status' => 'out of stock',
          'item' => $item,
          'message' => "Item $item is out of stock.",
        ]);
        exit();
      }

      // Check if the requested quantity exceeds the current stock
      if ($currentStock < $quantity) {
        echo json_encode([
          'status' => 'insufficient',
          'item' => $item,
          'message' => "Item $item has insufficient stock. Current stock: $currentStock",
        ]);
        exit();
      }

      // Add item to list if it passes validation
      $items[] = [
        'item' => $item,
        'quantity' => $quantity,
      ];
    }
  }

  // If all items are valid, then proceed to update the inventory and booking
  foreach ($items as $itemData) {
    $item = $itemData['item'];
    $quantity = $itemData['quantity'];

    // Update inventory after validation
    $db->updateInventory($item, $quantity);
  }

  // Insert kilo and items used into booking table
  $db->updateKiloAndItems($id, $items);

  // Return success message
  echo json_encode([
    'status' => 'success',
    'message' => 'Booking and inventory updated successfully',
    'items' => $items,
  ]);
  exit();
}

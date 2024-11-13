<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once './admin_db.php';
require_once './utils/util.php';

$db = new Database();
$util = new Util();

// Handle Fetch All Pending Booking with Search and Pagination
if (isset($_GET['readPending'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 10; // Rows per page
  $query = isset($_GET['query']) ? $_GET['query'] : '';

  $pendingBooking = $db->fetchPendingsWithPagination($page, $limit, $query);
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
          <td class="min-w-[150px] flex items-center justify-start space-x-2 align-middle flex-grow">
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

// Handle View Booking Ajax Request
if (isset($_GET['view'])) {
  $id = $_GET['id'];

  $bookingSummary = $db->readOne($id);
  echo json_encode($bookingSummary);
}

// Handle Admit Booking Ajax Request
if (isset($_GET['admit'])) {
  $booking_id = $_GET['id'];
  $id = $db->readOne($booking_id);
  $user_id = $id['user_id'];
  $user = $db->user($user_id);

  if ($db->admitBooking($booking_id)) {


    $receiver = $user['email'];
    $subject = "Booking Update";
    $message = "Your booking with ID " . $booking_id . " has been updated to 'For Pickup'.";
    $util->sendEmail($receiver, $subject, $message);

    echo $util->showMessage('success', 'Booking admited successfully');
  }
}

// Handle Denied Booking Ajax Request
if (isset($_GET['denied'])) {
  $booking_id = $_GET['id'];
  $id = $db->readOne($booking_id);
  $user_id = $id['user_id'];
  $user = $db->user($user_id);

  if ($db->deniedBooking($booking_id)) {
    $receiver = $user['email'];
    $subject = "Booking Update";
    $message = "Your booking with ID " . $booking_id . " has been denied due to invalid inputs.";
    $util->sendEmail($receiver, $subject, $message);

    echo $util->showMessage('success', 'Booking denied successfully');
  }
}

// Handle Total Count by Status for Summary Card Ajax Request
if (isset($_GET['count_all'])) {
  // echo json_encode(['message => success']);

  $pendingCount = $db->totalCountByStatus('pending');
  $pickupCount = $db->totalCountByStatus('for pick-up');
  $deliveryCount = $db->totalCountByStatus('for delivery');
  $completeCount = $db->totalCountByStatus('complete');

  echo json_encode([
    'pendingCount' => $pendingCount,
    'pickupCount' => $pickupCount,
    'deliveryCount' => $deliveryCount,
    'completeCount' => $completeCount,
  ]);
}

// Handle Total Count of User for Card Ajax Request
if (isset($_GET['count_user_total'])) {
  $usersCount = $db->totalCountofUser('user');
  echo json_encode(['usersCount' => $usersCount]);
}

// Handle Fetch New Booking Notifications
if (isset($_GET['fetch_new_bookings'])) {

  // Fetch new booking requests that haven't been read
  $notifications = $db->fetch_new_bookings();

  // Send the notifications back as a JSON response
  echo json_encode($notifications);
}

// admin_action.php
if (isset($_GET['fetch_new_bookings_delivery'])) {
  $notifications = $db->fetch_new_bookings_delivery();
  echo json_encode($notifications);
}

// Handle marking booking as read (admin viewed notification)
if (isset($_GET['mark_admin_booking_read'])) {
  $bookingId = $_GET['id'];

  // Mark the specific booking as read by the admin
  $db->mark_admin_booking_as_read($bookingId);

  // Send a success response
  echo json_encode(['success' => true]);
}

if (isset($_GET['mark_as_read']) && isset($_GET['id'])) {
  $id = $_GET['id'];
  $db->mark_booking_as_read($id);
  echo json_encode(['success' => true]);
}

// Handle AJAX request to fetch total users per month
if (isset($_GET['fetchUsersPerMonth'])) {
  $usersPerMonth = $db->fetchUserCountPerMonth();
  echo json_encode($usersPerMonth); // Return data as JSON
}

// Handle Fetch Booking Data for Doughnut Chart
if (isset($_GET['fetchBookingData'])) {
  $bookingData = $db->fetchBookingData();
  echo json_encode($bookingData); // Return the JSON-encoded data
}

// Handle fetch events Ajax Request
if (isset($_GET['fetch_events'])) {
  $events = $db->fetchAllEvents();
  echo json_encode($events);
}

// Handle Add Event Ajax Request
if (isset($_GET['add_event'])) {
  $data = json_decode(file_get_contents('php://input'), true);

  $title = $data['title'];
  $start = $data['start'];
  $end = $data['end'];

  // Insert the new event into the database
  $event_id = $db->addEvent($title, $start, $end);

  if ($event_id) {
    echo json_encode(['success' => true, 'event_id' => $event_id]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Failed to add event.']);
  }
}

// Handle Delete Event Ajax Request
if (isset($_GET['delete_event'])) {
  $event_id = $_GET['event_id'];

  // Call the function to delete the event from the database
  $isDeleted = $db->deleteEvent($event_id);

  if ($isDeleted) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete event.']);
  }
}

if (isset($_GET['readAllFeedback'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = 7;
  $start = ($page - 1) * $limit;

  $column = isset($_GET['column']) ? $_GET['column'] : 'id';
  $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
  $query = isset($_GET['query']) ? $_GET['query'] : '';

  $feedback = $db->readAllFeedback($start, $limit, $column, $order, $query);
  $totalRows = $db->getTotalRows($query);
  $totalPages = ceil($totalRows / $limit);

  $output = '';
  if ($feedback) {
    foreach ($feedback as $row) {
      $output .= '
            <tr class="border-b border-gray-200">
                <td class="px-4 py-2">' . $row['user_id'] . '</td>
                <td class="px-4 py-2">' . $row['first_name'] . ' ' . $row['last_name'] . '</td>
                <td class="px-4 py-2">' . $row['rating'] . '</td>
                <td class="min-w-[100px] h-auto flex items-center justify-start space-x-2 flex-grow">';

      // Check the value of `page-display`
      if ($row['page-display'] == 1) {
        // Display only the view link button
        $output .= '
                <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition feedbackView">
                    <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                </a>';
      } else {
        // Display both view link and toDisplayLink buttons
        $output .= '
                <a href="#" id="' . $row['id'] . '" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition feedbackView">
                    <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                </a>
                <a href="#" id="' . $row['id'] . '" class="px-3 py-2 bg-[#0E4483] hover:bg-[#0C376A] rounded-md transition toDisplayLink">
                    <img class="w-4 h-4" src="./img/icons/feedback-display.svg" alt="display">
                </a>';
      }

      // Close the table cell and row
      $output .= '
                </td>
            </tr>';
    }

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

    echo json_encode([
      'feedback' => $output,
      'pagination' => $paginationOutput,
    ]);
  } else {
    echo json_encode([
      'feedback' => '<tr><td colspan="8" class="text-center py-4 text-gray-200">No Feedback Found!</td></tr>',
      'pagination' => ''
    ]);
  }
  exit();
}

if (isset($_GET['feedback-view'])) {
  $id = $_GET['id'];

  $feedbackDetails = $db->getFeedbackDetails($id);

  if ($feedbackDetails) {
    echo json_encode([
      'status' => 'success',
      'details' => $feedbackDetails,
    ]);
  } else {
    echo json_encode(['status' => 'error']);
  }
}

if (isset($_GET['display-feed'])) {
  $id = $_GET['id'];

  $result = $db->updatePageDisplay($id);

  if ($result) {
    echo json_encode([
      'status' => 'success'
    ]);
  } else {
    echo json_encode([
      'status' => 'error'
    ]);
  }
}

if (isset($_GET['fetch-testimonials'])) {
  $testimonials = $db->getTestimonials();

  $output = '';  // Initialize an empty string for the output
  if ($testimonials) {
    foreach ($testimonials as $row) {
      // Initialize the stars output
      $stars = '';
      // Loop through and add the star image based on the rating
      for ($i = 0; $i < 5; $i++) {
        if ($i < $row['rating']) {
          // Add a full star if the current index is less than the rating
          $stars .= '<img src="./img/icons/star-rating.svg" class="w-5 h-5" alt="Star">';
        } else {
          // Add an empty star if the current index is greater than or equal to the rating
          $stars .= '<img src="./img/icons/star-rating-empty.svg" class="w-5 h-5" alt="Empty Star">';
        }
      }

      // Append each testimonial to the output
      $output .= '
        <div class="my-4">
          <div class="bg-federal p-8 sm:p-12 rounded-3xl text-white h-auto flex flex-col items-center justify-center relative w-full">

            <div class="w-full flex items-center justify-center p-4">
              ' . $stars . '  <!-- Display the star rating here -->
            </div>

            <!-- Left Quote Image -->
            <img class="w-12 h-12 absolute top-5 left-3 -translate-y-1/2" src="./img/icons/quote-left-sunrise.svg" alt="Quote Left">
        
            <!-- Testimonial Description -->
            <p id="feedback-description" class="text-white text-center text-xl sm:text-2xl mt-2">
              '. $row['description'] .'
            </p>
        
            <!-- Right Quote Image -->
            <img class="w-12 h-12 absolute -bottom-5 right-3 -translate-y-1/2" src="./img/icons/quote-right-sunrise.svg" alt="Quote Right">
          </div>
          <div class="w-full flex justify-end p-2 italic text-xl sm:text-2xl text-right mt-2 text-ashblack">
            <p>&#8212; '. $row['first_name']. ' ' . $row['last_name'] . '</p>
          </div>
        </div>
      ';
    }
    echo $output;  // Output the concatenated HTML
  }
}


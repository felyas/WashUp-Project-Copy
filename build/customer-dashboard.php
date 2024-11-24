<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Check if the user is an admin
if ($_SESSION['role'] !== 'user') {
  header("Location: ./404.php"); // Redirect to the 404 page
  exit();
}
?>




<!DOCTYPE html>
<html lang="en" class="sm:scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">
  <link rel="stylesheet" href="./css/rating.css">

  <!-- FullCalendar CDN -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <link rel="stylesheet" href="./css/customer-calendar.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>

<body class="bg-white min-h-screen font-poppins">
  <div class="flex h-full">
    <!-- Sidebar -->
    <div id="sidebar" class="w-64 z-50 bg-gray-800 text-white flex-col flex lg:flex lg:w-64 fixed lg:relative top-0 bottom-0 transition-transform transform lg:translate-x-0 -translate-x-full">
      <div class="p-4 text-lg font-bold border-b border-gray-700">
        <div class="flex justify-center items-center w-[180px]">
          <img src="./img/logo-white.png" alt="" class="w-12 h-10 mr-1">
          <h1 class="text-base font-bold text-wrap leading-4">
            WASHUP LAUNDRY
          </h1>
        </div>
      </div>
      <nav class="flex flex-col flex-1 p-4 space-y-4 text-md">
        <a href="./customer-dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/dashboard.svg" alt="">
          <p>Dashboard</p>
        </a>
        <a href="./complaint-table.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/report-white.svg" alt="">
          <p>Customer Complaints</p>
        </a>

        <div class="flex items-center justify-center pt-72">
          <!-- Close Button -->
          <button id="close-sidebar" class="lg:hidden p-6 text-white rounded-full bg-gray-900 hover:bg-gray-700">
            <img class="h-6 w-6 mx-auto" src="./img/icons/close-button.svg" alt="">
          </button>
        </div>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-federal shadow p-4">
        <div class="flex justify-between items-center lg:justify-end">
          <!-- Hamburger Menu -->
          <button id="hamburger" class="lg:hidden px-4 py-2 text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
          </button>

          <!--Notifications & Logout Section-->
          <div class="flex items-center justify-between lg:space-x-4 text-sm">
            <p class="js-current-time text-white"></p>
            <div class="flex items-center justify-between">
              <div class="relative">
                <button class="js-notification-button flex items-center justify-center px-4 py-2 relative">
                  <!-- Notification Bell Icon -->
                  <img src="./img/icons/notification-bell.svg" alt="Notification Bell" class="w-5 h-5">

                  <!-- Red Dot for New Notifications (hidden by default) -->
                  <span class="js-notification-dot hidden absolute top-[5px] right-[14px] h-3 w-3 bg-red-600 rounded-full"></span>
                </button>

                <!-- Notification Dropdown -->
                <div class="js-notification hidden h-auto min-w-72 sm:w-96 z-50 absolute top-[54px] -right-[68px] text-nowrap border border-gray-200 border-solid bg-white flex flex-col items-center shadow-lg text-ashblack">
                  <div class="w-full p-4 flex items-center justify-between">
                    <h1 class="text- text-lg font-semibold">Notification</h1>
                    <p class="js-total-notifications"><!-- Dynamic Total Notification  -->0</p>
                  </div>
                  <hr class="w-full py-0">

                  <div class="js-notification-messages p-2 w-full text-wrap">
                    <!-- Dynamic Real-time Notification -->

                    <!-- <div class="p-2 flex items-center justify-between bg-gray-200 mb-1">
                      <p class="w-auto">Booking Status with no.34 was updated to "for delivery"</p>
                      <button class="w-12 p-0 border-none font-bold">&#10005;</button>
                    </div> -->
                  </div>
                </div>
              </div>

              <div class="h-full flex flex-col items-center relative">
                <button id="js-setting-button" class="cursor-pointer px-4 py-2">
                  <img src="./img/icons/setting.svg" alt="setting" class="w-5 h-5">
                </button>

                <div id="js-setting" class="absolute hidden top-[54px] -right-4 w-32 h-auto bg-white border border-solid border-gray-200">
                  <div class="flex flex-col">
                    <div class="flex items-center justify-start w-full hover:bg-gray-200 p-1">
                      <button type="button" id="js-account-setting" class="flex items-center justify-center px-2 py-2">
                        <div class="flex space-x-2">
                          <img src="./img/icons/user-black.svg" alt="Logout Icon" class="w-5 h-5">
                          <p>Account</p>
                        </div>
                      </button>
                    </div>

                    <div class="justify-start w-full hover:bg-gray-200 p-1">
                      <form action="./backend/handle_logout.php" method="POST" class="p-0 m-0">
                        <button type="submit" class="flex items-center justify-center px-2 py-2">
                          <div class="flex space-x-2">
                            <img src="./img/icons/logout-black.svg" alt="Logout Icon" class="w-5 h-5">
                            <p>Log out</p>
                          </div>
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </header>

      <!-- Main Content Area -->
      <!-- Toaster -->
      <div id="toaster" class="fixed top-4 hidden right-4 ml-4 text-white shadow-lg z-50">
        <!-- Dynamic Toaster Content -->
      </div>

      <main class="flex-1 px-6">
        <div class="w-full h-auto flex lg:flex-row justify-between items-center mt-2 mb-4 p-4 border border-solid border-gray-200 rounded-lg flex-col-reverse">
          <div class="w-full lg:w-1/4 lg:mb-0">
            <h1 class="text-lg lg:text-md font-semibold">
              Welcome back, <span><?php echo $_SESSION['first_name']; ?></span>
            </h1>
            <p class="text-gray-400 text-sm mt-2 lg:mt-1">
              Track the status of your laundry booking with ease and stay updated on every step of the process.
            </p>
          </div>
          <div class="w-full lg:w-auto flex justify-end items-center mb-2 sm:mb-0">
            <button class="js-book-now border-2 text-md font-semibold border-federal py-2 px-6 rounded-md shadow-lg text-federal hover:bg-federal hover:text-white transition">
              BOOK NOW
            </button>
          </div>
        </div>

        <!-- Grid for Booking Summaries -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 lg:gap-x-12 gap-y-4 mb-4">
          <!-- Pending Booking Card -->
          <div class="h-36 rounded-lg bg-white shadow-md flex justify-center items-center border border-solid border-gray-200">
            <div class="grid grid-cols-2 lg:space-x-2">
              <div class="flex items-center justify-center">
                <div class="rounded-[50%] bg-celestial p-4 flex items-center justify-center">
                  <div class="relative">
                    <img class="h-6 w-6" src="./img/icons/hand-holding-solid.svg" alt="">
                    <img src="./img/icons/box.svg" class="absolute top-0 right-[5px] h-3 w-3" alt="">
                  </div>
                </div>
              </div>
              <div class="flex flex-col items-center justify-center">
                <p class="text-lg md:text-3xl font-semibold" id="js-for-pickup"><!-- total count --></p>
                <p class="text-sm md:text-md text-wrap px-2 sm:px-0">For pickup</p>
              </div>
            </div>
          </div>

          <!-- On Pick-up Booking Card -->
          <div class="h-36 rounded-lg bg-white shadow-md flex justify-center items-center border border-solid border-gray-200">
            <div class="grid grid-cols-2 lg:space-x-2">
              <div class="flex items-center justify-center">
                <div class="rounded-[50%] bg-polynesian p-4 flex items-center justify-center">
                  <img class="h-6 w-6" src="./img/icons/pickup.svg" alt="">
                </div>
              </div>
              <div class="flex flex-col items-center justify-center">
                <p class="text-lg md:text-3xl font-semibold" id="js-for-delivery"><!-- total count --></p>
                <p class="text-sm md:text-md text-wrap">For Delivery</p>
              </div>
            </div>
          </div>

          <!-- On Delivery Booking Card -->
          <div class="h-36 rounded-lg bg-white shadow-md flex justify-center items-center col-span-2 sm:col-span-1 border border-solid border-gray-200">
            <div class="grid grid-cols-2 lg:space-x-2">
              <div class="flex items-center justify-center">
                <div class="rounded-[50%] bg-green-700 p-4 flex items-center justify-center">
                  <img class="h-6 w-6" src="./img/icons/check.svg" alt="">
                </div>
              </div>
              <div class="flex flex-col items-center justify-center">
                <p class="text-lg md:text-3xl font-semibold" id="js-for-complete-booking"><!-- total count --></p>
                <p class="text-sm md:text-md text-wrap">Complete Booking</p>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <!-- Second div taking 1/4 of the width on large screens -->
          <div class="w-auto h-72 bg-white border border-solid border-gray-200 shadow-lg">
            <div id="calendar" class="p-2">
              <!-- Calendar goes here -->
            </div>
          </div>

          <div class="h-72 flex flex-col items-center border border-solid border-gray-200 rounded-md shadow-md">
            <div class="py-4 w-full bg-polynesian h-12 flex items-center justify-center">
              <p class="font-semibold text-white">Customers Feedback</p>
            </div>
            <div id="feedback-container" class="w-full h-full max-h-72 overflow-y-auto p-2 flex flex-col items-center">
              <!-- Dynamic Feedback -->
            </div>
          </div>

        </div>


        <!--List-->
        <div class="h-auto w-full grid grid-cols-1 text-sm mb-4">
          <!-- First div taking 3/4 of the width on large screens -->
          <div class="col-span-4 lg:col-span-3 h-auto w-full rounded-sm">
            <div class="bg-white border border-solid border-gray-200 shadow-lg pb-2"> <!-- Outer container -->
              <div class="h-auto p-2 rounded-t-sm flex flex-col justify-center"> <!-- Added border-b for a clean separation -->
                <p class="text-md font-semibold text-ashblack py-2">MANAGE BOOKING</p>
                <div class="flex justify-between items-center relative">
                  <input id="search-input" type="text" placeholder="Search " class="w-1/2 py-2 rounded-lg pl-14 outline-none border border-solid border-gray-200">
                  <button class="absolute left-0 top-0 h-full px-4 bg-federal rounded-l-lg">
                    <img src="./img/icons/search.svg" class="w-4 h-4" alt="search">
                  </button>
                </div>
              </div>
              <div class="overflow-x-auto h-auto px-2">
                <table id="booking-list" class="text-nowrap w-full text-left text-ashblack border-collapse border border-solid border-gray-200">
                  <thead class="bg-gray-200">
                    <tr>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">ID</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">FULL NAME</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">BOOKING DATE</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">SERVICE</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">SERVICE TYPE</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">PROOF OF KILO</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">PROOF OF DELIVERY</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">RECEIPT</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">STATUS</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">ACTION</th>
                    </tr>
                  </thead>
                  <tbody id="users-booking-list">
                    <!-- Dynamic List -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container" class="w-full py-2 justify-center items-center flex text-sm">
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Modal for displaying the larger image -->
  <div class="fixed p-2 inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-30" id="imageModal">
    <div class="bg-white shadow-lg p-4 rounded-lg">
      <button id="closeImageModal" class="px-2 py-1 bg-gray-500 hover:bg-gray-700 text-white rounded-md">
        <img class="w-5 h-5" src="./img/icons/x.svg" alt="">
      </button>
      <img id="modal-image" class="w-full max-w-md h-auto mt-2" src="" alt="Large Proof Image">
    </div>
  </div>

  <!-- Delete Warning Modal Overlay -->
  <div id="delete-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
      <div class="grid grid-cols-4 mb-4">
        <!-- First child taking 1/4 of the parent's width -->
        <div class="col-span-1 flex items-center">
          <div class="flex justify-center items-center col-span-1 bg-red-500 rounded-full w-16 h-16">
            <img class="w-8 h-8" src="./img/icons/circle-error.svg" alt="">
          </div>
        </div>
        <!-- Second child taking 3/4 of the parent's width -->
        <div class="col-span-3">
          <h1 id="modal-title" class="text-lg font-bold mb-2 text-red-600">Warning !</h1>
          <p id="modal-message" class="text-md text-gray-500 text-wrap">Do you want to cancel the booking?</p>
        </div>
      </div>

      <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
        <button id="delete-confirm-modal" class="bg-red-600 border-2 border-solid border-red-600 text-white hover:bg-red-700 hover:border-red-700 py-2 px-4 rounded transition">
          Yes
        </button>
        <button id="delete-close-modal" class="bg-white border-2 border-solid border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white py-2 px-4 rounded transition">
          No
        </button>
      </div>
    </div>
  </div>

  <!-- Success Modal Overlay -->
  <div id="success-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
      <div class="grid grid-cols-4 mb-4">
        <!-- First child taking 1/4 of the parent's width -->
        <div class="col-span-1 flex items-center">
          <div class="flex justify-center items-center col-span-1 bg-green-600 rounded-full w-16 h-16">
            <img class="w-8 h-8" src="./img/icons/circle-success.svg" alt="">
          </div>
        </div>
        <!-- Second child taking 3/4 of the parent's width -->
        <div class="col-span-3">
          <h1 id="modal-title" class="text-lg font-bold mb-2 text-green-700">Success !</h1>
          <p id="modal-message" class="text-md text-gray-500 text-wrap">Booking updated successfully!</p>
        </div>
      </div>

      <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
        <button id="success-confirm-modal" class="hidden bg-green-700 border-2 border-solid border-green-700 text-white hover:bg-green-800 hover:border-green-800 py-2 px-4 rounded transition">
          Yes
        </button>
        <button id="success-close-modal" class="bg-green-700 border-2 border-solid border-green-700 text-white hover:bg-green-800 hover:border-green-800 py-2 px-4 rounded transition">
          Ok
        </button>
      </div>
    </div>
  </div>

  <!-- Error Modal Overlay -->
  <div id="error-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
      <div class="grid grid-cols-4 mb-4">
        <!-- First child taking 1/4 of the parent's width -->
        <div class="col-span-1 flex items-center">
          <div class="flex justify-center items-center col-span-1 bg-red-500 rounded-full w-16 h-16">
            <img class="w-8 h-8" src="./img/icons/circle-error.svg" alt="">
          </div>
        </div>
        <!-- Second child taking 3/4 of the parent's width -->
        <div class="col-span-3">
          <h1 id="modal-title" class="text-lg font-bold mb-2 text-red-600">Error !</h1>
          <p id="modal-message" class="text-md text-gray-500 text-wrap">Please complete all required fields !</p>
        </div>
      </div>

      <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
        <button id="error-confirm-modal" class="hidden bg-red-600 border-2 border-solid border-red-600 text-white hover:bg-red-700 hover:border-red-700 py-2 px-4 rounded transition">
          Yes
        </button>
        <button id="error-close-modal" class="bg-red-600 border-2 border-solid border-red-600 text-white hover:bg-red-700 hover:border-red-700 py-2 px-4 rounded transition">
          Ok
        </button>
      </div>
    </div>
  </div>


  <!-- Modal for Edit -->
  <div class="toEditBookingModal fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-20">
    <div class="bg-white shadow-lg p-6 w-full max-w-lg rounded-3xl m-2">
      <div class="flex justify-between items-center border-b pb-2">

        <h2 class="text-lg font-semibold text-gray-500">Booking Information</h2>
        <button class="closeEditBookingModal text-gray-500 hover:text-gray-800">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <form id="edit-booking-form" class="mt-4" novalidate>
        <input type="hidden" name="id" id="id">
        <div class="grid grid-cols-2 gap-2">
          <div class="mb-4">
            <label for="fname" class="block text-sm text-gray-500">First Name</label>
            <input type="text" id="fname" name="fname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" placeholder=" First Name: " required>
            <div class="text-red-500 text-sm hidden">First name is required!</div>
          </div>
          <div class="mb-4">
            <label for="lname" class="block text-sm text-gray-500">Last Name</label>
            <input type="text" id="lname" name="lname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" placeholder=" Last Name: " required>
            <div class="text-red-500 text-sm hidden">Last name is required!</div>
          </div>
        </div>
        <div class="mb-4">
          <label for="phone_number" class="block text-sm text-gray-500">Phone Number</label>
          <input type="text" id="phone_number" name="phone_number" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" placeholder="e.g., +63 912 345 6789" required>
          <div class="text-red-500 text-sm hidden">Phone number is required!</div>
        </div>
        <div class="mb-4">
          <label for="address" class="block text-sm text-gray-500">Address</label>
          <input type="text" id="address" name="address" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" placeholder="e.g., Villa Rizza, Blk 2 Lot 3, Paciano Rizal" required>
          <div class="text-red-500 text-sm hidden">Address is required!</div>
        </div>

        <input type="submit" id="add-complaint-btn" value="Save" class="px-4 py-2 w-full bg-green-700 hover:bg-green-800 text-white font-semibold rounded-md">
      </form>
    </div>
  </div>

  <!-- Modal for View -->
  <div class="toViewBookingModal p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-20">
    <div class="bg-white shadow-lg p-6 w-full max-w-lg rounded-3xl m-2">
      <div class="w-full h-auto py-2 flex flex-col items-center text-nowrap text-gray-500">
        <h4 class="text-lg font-bold">WASHUP LAUNDRY</h4>
        <p class="text-sm">Blk 1 lot 2 morales subdivision, Calamba Laguna</p>
        <p class="text-sm">Phone: +63 930 520 5088</p>
      </div>

      <div class="grid grid-cols-2 gap-2 mb-4 text-gray-500">
        <div class="flex justify-start">
          <p class="text-sm">Date: </p>
        </div>
        <div class="flex justify-end">
          <p id="created_at" class="text-sm"><!-- Dynamic Date --></p>
        </div>
      </div>

      <div class="w-full text-ashblack text-md font-semibold mb-2">
        <p class="justify-start">Booking Summary</p>
      </div>

      <div class="w-full text-gray-500 text-sm flex flex-col mb-6 space-y-2">
        <div class="grid grid-cols-2 gap-2">
          <p>Customer Name:</p>
          <p id="display-full-name" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Phone Number:</p>
          <p id="display-phone-number" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Address:</p>
          <p id="display-address" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Pick-up Date:</p>
          <p id="display-pickup-date" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Pick-up Time:</p>
          <p id="display-pickup-time" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Service:</p>
          <p id="display-service-selection" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Service Type:</p>
          <p id="display-service-type" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Suggestions:</p>
          <p id="display-suggestions" class="justify-end flex"><!-- dynamic data --></p>
        </div>
      </div>

      <div class="flex justify-center items-center w-full">
        <button type="button" class="closeViewBookingModal2 px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white rounded-md mr-2">Close</button>
      </div>
    </div>
  </div>

  <!-- Modal for Confirmation -->
  <div class="toConfirmReceiveModal p-2 fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex justify-center items-center z-20">
    <div class="bg-white shadow-lg p-6 w-full max-w-lg rounded-3xl m-2">
      <div class="w-full h-auto py-2 flex flex-col items-center text-wrap text-gray-500">
        <div class="relative">
          <div class="w-44 h-44 p-4 rounded-full border-8 border-solid border-federal flex items-center justify-center">
            <img class="w-32 h-28" src="./img/original-logo.png" alt="">
          </div>

          <div class="absolute top-0 right-0 bg-white rounded-full">
            <img class="w-16 h-16 rounded-full object-cover" src="./img/check-federal.svg" alt="">
          </div>

        </div>
        <h1 class="text-2xl text-federal font-bold mb-4">Washup Laundry</h1>

        <p class="text-md text-center text-gray-500">Did you receive your laundry with an ID of <span id="bookingId-text"></span>?</p>
      </div>

      <form action="" class="w-full flex justify-center items-center space-x-4">
        <input id="bookingId-input" class="hidden" type="text"> <!-- Include the booking ID here -->

        <input id="confirmYes" class="feedbackModalTrigger transition py-2 px-4 bg-federal hover:bg-[#070c3f] rounded-md text-white font-semibold cursor-pointer" type="button" value="Yes!">
        <button id="confirmNo" class="transition py-2 px-4 bg-gray-700 hover:bg-gray-600 rounded-md text-white font-semibold" type="button">Not Yet</button>
      </form>
    </div>
  </div>

  <!-- Modal for Complaint Request -->
  <div class="toRequestComplianceModal p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center  z-20 hidden">
    <div class="bg-white shadow-lg p-6 w-full max-w-lg rounded-3xl m-2 relative ">
      <button class="closeRequestComplianceModal text-gray-500 hover:text-gray-800 absolute top-5 right-5">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      <div class="w-full h-auto py-2 flex flex-col items-center text-nowrap text-gray-500">
        <div class="flex items-center justify-center">
          <img class="10 h-10" src="./img/original-logo.png" alt="">
        </div>
        <h4 class="text-lg font-bold text-federal">WASHUP LAUNDRY</h4>
        <hr class="w-full my-2">
      </div>

      <div class="h-auto w-full overflow-y-auto max-h-72 flex flex-col items-center">
        <p class="text-gray-500 text-md font-semibold mb-2">Customer Complaint Form</p>
        <form action="" id="report-complain-form" class="mt-2" novalidate>
          <div id="top" class="grid grid-cols-2 gap-2">
            <div class="mb-4">
              <label for="fname" class="block text-sm text-gray-500">First Name</label>
              <input type="text" id="complaint-fname" name="fname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" placeholder=" First Name: " required>
              <div class="text-red-500 text-sm hidden">First name is required!</div>
            </div>
            <div class="mb-4">
              <label for="lname" class="block text-sm text-gray-500">Last Name</label>
              <input type="text" id="complaint-lname" name="lname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" placeholder=" Last Name: " required>
              <div class="text-red-500 text-sm hidden">Last name is required!</div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div class="mb-4">
              <label for="phone_number" class="block text-sm text-gray-500">Phone Number</label>
              <input type="text" id="complaint-phone_number" name="phone_number" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" placeholder="Phone Number: " required>
              <div class="text-red-500 text-sm hidden">Phone number is required!</div>
            </div>
            <div class="mb-4">
              <label for="email" class="block text-sm text-gray-500">Email</label>
              <input type="text" id="complaint-email" name="email" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" placeholder=" Email: " required>
              <div class="text-red-500 text-sm hidden">Email is required!</div>
            </div>
          </div>

          <!-- Common Problem Dropdown -->
          <div class="mb-4">
            <label for="common_problem" class="block text-sm text-gray-500">Common Problem</label>
            <select id="complaint-reason" name="reason" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" required>
              <option value="" selected disabled>Select a common issue</option>
              <option value="delayed_pickup">Delayed Laundry Pickup</option>
              <option value="missing_items">Missing Items in Laundry</option>
              <option value="damaged_clothes">Clothes Damaged during Cleaning</option>
              <option value="stains_not_removed">Stains Not Removed Properly</option>
              <option value="incorrect_billing">Incorrect Billing or Payment Issues</option>
              <option value="other">Other</option>
            </select>

            <div class="text-red-500 text-sm hidden">Reason is required!</div>
          </div>

          <!-- Description Textarea -->
          <div class="mb-4">
            <label for="description" class="block text-sm text-gray-500">Describe Your Problem</label>
            <textarea name="description" id="complaint-description" rows="4" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid" placeholder="Describe the issue in detail" required></textarea>
            <div class="text-red-500 text-sm hidden">Description is required!</div>
          </div>

          <input type="submit" id="edit-booking-btn" value="Submit" class="px-4 py-2 w-full bg-federal hover:bg-[#1b266b] text-white font-semibold rounded-md transition">
        </form>
      </div>
    </div>
  </div>

  <!-- Modal for Feedback -->
  <div class="toViewFeedbackModal hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-20">
    <div class="bg-white shadow-lg p-6 w-full max-w-lg rounded-3xl m-2 relative">
      <button class="closeFeedbackModal text-gray-500 hover:text-gray-800 absolute top-5 right-5">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>

      <div class="w-full h-auto py-2 flex flex-col items-center text-nowrap">
        <div class="w-32 h-32 p-4 rounded-full border-8 border-solid border-[#0E4483] flex items-center justify-center">
          <img class="w-24 h-20" src="./img/icons/comment-solid.svg" alt="">
        </div>
        <h1 class="text-2xl text-[#0E4483] font-bold mb-4">Your Feedback Matters!</h1>
      </div>

      <div class="h-auto w-full flex flex-col items-center justify-center">
        <!-- Feedback Form -->
        <form action="" id="feedback-form" class="mt-2 w-full" novalidate>
          <!-- Star Rating Section -->
          <div class="mb-4 w-full">
            <label for="rating" class="block text-sm font-medium text-gray-500">Rate Our Service</label>
            <div class="flex space-x-2 mt-2">
              <!-- Star Rating using radio buttons -->
              <div class="flex">
                <input type="radio" id="star1" name="rating" value="1" class="hidden" />
                <label for="star1" class="starRating cursor-pointer text-gray-300 hover:text-yellow-500 transition-colors duration-200">
                  ★
                </label>

                <input type="radio" id="star2" name="rating" value="2" class="hidden" />
                <label for="star2" class="starRating cursor-pointer text-gray-300 hover:text-yellow-500 transition-colors duration-200">
                  ★
                </label>

                <input type="radio" id="star3" name="rating" value="3" class="hidden" checked />
                <label for="star3" class="starRating cursor-pointer text-gray-300 hover:text-yellow-500 transition-colors duration-200">
                  ★
                </label>

                <input type="radio" id="star4" name="rating" value="4" class="hidden" />
                <label for="star4" class="starRating cursor-pointer text-gray-300 hover:text-yellow-500 transition-colors duration-200">
                  ★
                </label>

                <input type="radio" id="star5" name="rating" value="5" class="hidden" />
                <label for="star5" class="starRating cursor-pointer text-gray-300 hover:text-yellow-500 transition-colors duration-200">
                  ★
                </label>
              </div>
            </div>
          </div>

          <!-- Description Textarea -->
          <div class="mb-4 w-full">
            <label for="description" class="block text-sm font-medium text-gray-500">Describe Your Experience</label>
            <textarea name="description" id="description" rows="4" class="mt-1 block w-full border-[#0E4483] rounded-sm py-2 px-2 border border-solid" placeholder="Tell us about your experience" required>Great service!</textarea>
            <div class="text-red-500 text-sm hidden">Description is required!</div>
          </div>

          <input type="submit" id="submit-review" value="Submit" class="px-4 py-2 w-full bg-[#0E4483] hover:bg-[#0C376A] text-white font-semibold rounded-md">
        </form>

      </div>
    </div>
  </div>

  <!-- Complain Section -->
  <div class="fixed bottom-4 right-4 flex items-center justify-center">
    <button id="js-report-compain" class="reportComplainTrigger w-10 h-10 rounded-md bg-red-700 flex items-center justify-center"><img class="w-5 h-5" src="./img/icons/report-white.svg" alt="report"></button>
  </div>




  <script type="module" src="./js/customer-dashboard.js"></script>
</body>


</html>
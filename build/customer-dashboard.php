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

  <!-- FullCalendar CDN -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <link rel="stylesheet" href="./css/customer-calendar.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-white min-h-screen font-poppins">
  <div class="flex min-h-screen">

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-federal shadow p-4">
        <div class="flex justify-between items-center">
          <!-- Hamburger Menu -->
          <div id="logo" class="text-white">
            <img class="w-10 h-8" src="./img/logo-white.png" alt="LOGO">
          </div>

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
                <div class="js-notification hidden h-auto w-80 z-10000 absolute top-[52px] -right-[68px] text-nowrap border border-gray-200 border-solid bg-white flex flex-col items-center shadow-lg text-ashblack">
                  <div class="w-full p-4 flex items-center justify-between">
                    <h1 class="text- text-lg font-semibold">Notification</h1>
                    <p class="js-total-notifications"><!-- Dynamic Total Notification  -->0</p>
                  </div>
                  <hr class="w-full py-0">

                  <div class="js-notification-messages p-4 w-full text-wrap">
                    <!-- Dynamic Real-time Notification -->

                    <!-- <div class="p-2 flex items-center justify-between bg-gray-200 mb-1">
                      <p class="w-auto">Booking Status with no.34 was updated to "for delivery"</p>
                      <button class="w-12 p-0 border-none font-bold">&#10005;</button>
                    </div> -->
                  </div>
                </div>
              </div>


              <form action="./backend/handle_logout.php" method="POST" class="p-0 m-0">
                <button type="submit" class="flex items-center justify-center px-4 py-2">
                  <img src="./img/icons/logout.svg" alt="Logout Icon" class="w-5 h-5">
                </button>
              </form>

            </div>
          </div>
        </div>
      </header>

      <!-- Main Content Area -->
      <!-- Toaster -->
      <div id="toaster" class="fixed top-4 right-4 hidden text-white shadow-lg z-50">
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


        <!--List-->
        <div class="h-auto grid grid-cols-1 lg:grid-cols-4 gap-2 text-sm mb-4">
          <!-- First div taking 3/4 of the width on large screens -->
          <div class="col-span-4 lg:col-span-3 h-auto w-full rounded-sm px-4">
            <div class="bg-white border border-solid border-gray-200 shadow-lg pb-2"> <!-- Outer container -->
              <div class="h-auto p-2 rounded-t-sm flex flex-col justify-center"> <!-- Added border-b for a clean separation -->
                <p class="text-md font-semibold text-ashblack py-2">MANAGE BOOKING</p>
                <div class="flex justify-between items-center relative">
                  <input id="search-input" type="text" placeholder="Search bookings..." class="w-1/2 py-2 rounded-lg pl-14 outline-none border border-solid border-gray-200">
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
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">STATUS</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack text-center border-b border-gray-200">ACTION</th>
                    </tr>
                  </thead>
                  <tbody id="users-booking-list">
                    <!-- Dynamic List -->
                    <tr>
                      <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">1</td>
                      <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">Felix Bragais</td>
                      <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">2024-10-2</td>
                      <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">Wash</td>
                      <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">Standard 2-days</td>
                      <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle font-semibold">for delivery</td>
                      <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">
                        <div class="flex justify-center space-x-2">
                          <a href="#" class="editModalTrigger px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition editLink">
                            <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                          </a>
                          <a href="#" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition deleteLink">
                            <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                          </a>
                          <a href="#" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                            <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                          </a>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container" class="w-full py-2 justify-center items-center flex text-sm">
            </div>
          </div>


          <!-- Second div taking 1/4 of the width on large screens -->
          <div class="col-span-4 lg:col-span-1 w-auto h-80 bg-white border border-solid border-gray-200 shadow-lg">
            <div id="calendar" class="p-2">
              <!-- Calendar goes here -->
            </div>
          </div>
        </div>

      </main>
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
      <form id="edit-booking-form" class="mt-4">
        <input type="hidden" name="id" id="id">
        <div class="grid grid-cols-2 gap-2">
          <div class="mb-4">
            <label for="fname" class="block text-sm font-medium text-gray-500">First Name</label>
            <input type="text" id="fname" name="fname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" First Name: ">
            <div class="text-red-500 text-sm hidden">First name is required!</div>
          </div>
          <div class="mb-4">
            <label for="lname" class="block text-sm font-medium text-gray-500">Last Name</label>
            <input type="text" id="lname" name="lname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" Last Name: ">
            <div class="text-red-500 text-sm hidden">Last name is required!</div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div class="mb-4">
            <label for="pickup_date" class="block text-sm font-medium text-gray-500">Pick-up Date</label>
            <input type="date" id="pickup_date" name="pickup_date" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" Pick-up Date: ">
            <div class="text-red-500 text-sm hidden">Pick-up date is required!</div>
          </div>
          <div class="mb-4">
            <label for="pickup_time" class="block text-sm font-medium text-gray-500">Pick-up Time</label>
            <input type="time" id="pickup_time" name="pickup_time" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" Pick-up Time: ">
            <div class="text-red-500 text-sm hidden">Pick-up time is required!</div>
          </div>
        </div>
        <div class="mb-4">
          <label for="phone_number" class="block text-sm font-medium text-gray-500">Phone Number</label>
          <input type="text" id="phone_number" name="phone_number" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="e.g., +63 912 345 6789">
          <div class="text-red-500 text-sm hidden">Phone number is required!</div>
        </div>
        <div class="mb-4">
          <label for="address" class="block text-sm font-medium text-gray-500">Address</label>
          <input type="text" id="address" name="address" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="e.g., Villa Rizza, Blk 2 Lot 3, Paciano Rizal">
          <div class="text-red-500 text-sm hidden">Address is required!</div>
        </div>

        <input type="submit" id="edit-booking-btn" value="Save" class="px-4 py-2 w-full bg-green-700 hover:bg-green-800 text-white font-semibold rounded-md">
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

  <script type="module" src="./js/customer-dashboard.js"></script>
</body>


</html>
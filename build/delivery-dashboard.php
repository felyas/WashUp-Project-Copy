<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Check if the user is an admin
if ($_SESSION['role'] !== 'delivery') {
  header("Location: ./404.php"); // Redirect to the 404 page
  exit();
}
?>




<!DOCTYPE html>
<html lang="en" class="sm:scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delivery Dashboard - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">

  <!-- FullCalendar CDN -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <link rel="stylesheet" href="./css/customer-calendar.css">

  <!-- Include Chart.js from CDN -->
  <script src="../node_modules/chart.js/dist/chart.umd.js" defer></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-white h-full font-poppins">
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
        <a href="./delivery-dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/dashboard.svg" alt="">
          <p>Dashboard</p>
        </a>
        <a href="./customer-complaints.php" class="flex items-center p-2 rounded hover:bg-gray-700">
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

          <!--Notifications-->
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
                <div class="js-notification hidden min-w-72 sm:w-96 z-50 absolute top-[54px] -right-[68px] text-nowrap border border-gray-200 border-solid bg-white flex flex-col items-center shadow-lg text-ashblack max-h-72 overflow-y-auto">
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
      <main class="flex-1 p-6 relative h-full">

        <div id="toaster" class="fixed top-4 right-4 ml-4 hidden text-white shadow-lg z-30">
          <!-- Dynamic Toaster Content -->
        </div>


        <div class="w-full flex flex-col justify-center mt-2 mb-4 p-4 border border-solid border-gray-200 rounded-lg ">
          <h1 class="text-lg lg:text-md font-semibold">
            Welcome back, <span><?php echo $_SESSION['first_name']; ?></span>
          </h1>
          <p class="text-gray-400 text-sm mt-2 lg:mt-1">
            Manage your delivery tasks efficiently and stay on top of every booking, from pickup to drop-off.
          </p>
        </div>

        <!-- Grid for Booking Summaries -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 lg:gap-x-12 gap-y-4 mb-4">
          <!-- Pending Booking Card -->
          <div class="h-36 rounded-lg bg-white shadow-md border border-solid border-gray-200 flex justify-center items-center">
            <div class="grid grid-cols-2 lg:space-x-2">
              <div class="flex items-center justify-center">
                <div class="rounded-[50%] bg-sky-600 p-4 flex items-center justify-center">
                  <img class="h-6 w-6" src="./img/icons/pending.svg" alt="">
                </div>
              </div>
              <div class="flex flex-col items-center justify-center">
                <p class="text-lg md:text-3xl font-semibold" id="js-pending-count">1<!-- total count --></p>
                <p class="text-sm md:text-md text-wrap">Pending Booking</p>
              </div>
            </div>
          </div>

          <!-- On Pick-up Booking Card -->
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
                <p class="text-lg md:text-3xl font-semibold" id="js-for-pickup">1<!-- total count --></p>
                <p class="text-sm md:text-md text-wrap px-2 sm:px-0">For pickup</p>
              </div>
            </div>
          </div>

          <!-- On Delivery Booking Card -->
          <div class="h-36 rounded-lg bg-white shadow-md flex justify-center items-center col-span-2 sm:col-span-1 border border-solid border-gray-200">
            <div class="grid grid-cols-2 lg:space-x-2">
              <div class="flex items-center justify-center">
                <div class="rounded-[50%] bg-polynesian p-4 flex items-center justify-center">
                  <img class="h-6 w-6" src="./img/icons/pickup.svg" alt="">
                </div>
              </div>
              <div class="flex flex-col items-center justify-center">
                <p class="text-lg md:text-3xl font-semibold" id="js-for-delivery">1<!-- total count --></p>
                <p class="text-sm md:text-md text-wrap">For Delivery</p>
              </div>
            </div>
          </div>
        </div>

        <!--List-->
        <div class="w-full h-auto grid grid-cols-1 lg:grid-cols-2 mt-16 md:mt-20 gap-y-2 sm:gap-2 text-sm mb-4">
          <div class="w-full border border-solid border-gray-200 shadow-md">
            <div class="h-auto p-2 rounded-t-sm flex flex-col justify-center border-solid border-ashblack">
              <p class="text-md font-semibold text-ashblack py-2">MANAGE BOOKING</p>
              <div class="flex justify-between items-center relative">
                <input id="js-search-bar" type="text" placeholder="Search " class="w-1/2 py-2 rounded-lg pl-14 outline-none border border-solid border-gray-200">
                <button class="absolute left-0 top-0 h-full px-4 bg-federal rounded-l-lg">
                  <img src="./img/icons/search.svg" class="w-4 h-4" alt="search">
                </button>
              </div>
            </div>
            <div class="overflow-x-auto h-auto min-h-72 px-2">
              <table id="booking-list" class="text-nowrap w-full text-left text-ashblack border-collapse border border-solid border-gray-200">
                <thead class="bg-gray-200">
                  <tr>
                    <th data-column="id" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                      ID
                      <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                    </th>
                    <th data-column="fname" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                      FIRST NAME
                      <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                    </th>
                    <th data-column="lname" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                      LAST NAME
                      <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                    </th>
                    <th data-column="phone_number" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                      PHONE NUMBER
                      <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                    </th>
                    <th data-column="address" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                      ADDRESS
                      <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                    </th>
                    <!-- Adding the status dropdown filter -->
                    <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">
                      <select id="status-filter" class="ml-2 px-2 py-1 text-sm border border-gray-300 rounded">
                        <option value="">Status: All</option>
                        <option value="for pick-up">Pickup</option>
                        <option value="for delivery">Delivery</option>
                      </select>
                    </th>
                    <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">
                      <select id="date-filter" class="ml-2 px-2 py-1 text-sm border border-gray-300 rounded">
                        <option value="">Date: All</option>
                        <!-- Options will be populated dynamically -->
                      </select>
                    </th>
                    <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 text-center">ACTION</th>
                  </tr>
                </thead>
                <tbody id="users-booking-list">
                  <!-- Dynamic List -->
                  <tr>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">1</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">Felix</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">Bragais</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">09691026692</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle">Address Example</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle font-semibold">for delivery</td>
                    <td class="px-4 py-2 border-b text-sm border-gray-300 align-middle min-w-[100px]">
                      <div class="flex justify-center space-x-2">
                        <a href="#" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition viewLink">
                          <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                        </a>
                        <a href="#" class="px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition deliveryLink">
                          <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                        </a>
                        <a href="#" class="px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition pickupLink">
                          <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                        </a>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container" class="w-full py-2 justify-center items-center flex text-sm">
            </div>
          </div>

          <div class="h-72 md:h-auto w-full bg-white border border-solid border-gray-200 shadow-md">
            <div class="border-b border-solid border-gray-200 p-2 flex items-center justify-between w-full">
              <p class="text-md sm: text-lg font-semibold text-ashblack ">Pickup & Deliveries</p>
              <div class="p-1 border border-solid border-celestial rounded-md">
                <select name="time" id="">
                  <option value="30">30 minutes</option>
                  <option value="20">20 minutes</option>
                  <option value="10">10 minutes</option>
                </select>
              </div>
            </div>
            <div id="delivery-displays" class="flex flex-col items-center p-2 max-h-60 lg:max-h-96 overflow-y-auto">
              <!-- Dynamic Data -->
            </div>
          </div>
        </div>


        <!-- Second List -->
        <div class="w-full h-auto grid grid-cols-1 lg:grid-cols-4 mt-16 md:mt-20 gap-y-2 sm:gap-2 text-sm mb-4">
          <div class="w-full col-span-3 border border-solid border-gray-200 shadow-md">
            <div class="h-auto p-2 rounded-t-sm flex flex-col justify-center border-solid border-ashblack">
              <p class="text-md font-semibold text-ashblack py-2">PENDING BOOKINGS</p>
              <div class="flex justify-between items-center relative">
                <input id="js-search-pending" type="text" placeholder="Search " class="w-1/2 py-2 rounded-lg pl-14 outline-none border border-solid border-gray-200">
                <button class="absolute left-0 top-0 h-full px-4 bg-federal rounded-l-lg">
                  <img src="./img/icons/search.svg" class="w-4 h-4" alt="search">
                </button>
              </div>
            </div>
            <div class="overflow-x-auto h-auto px-2">
              <table class="text-nowrap w-full text-left text-ashblack border-collapse border border-solid border-gray-200">
                <thead class="bg-gray-200">
                  <tr>
                    <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">ID</th>
                    <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">CUSTOMER NAME</th>
                    <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">ADDRESS</th>
                    <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">ACTION</th>
                  </tr>
                </thead>
                <tbody id="js-pending-tbody">
                  <!-- Dynamic Data -->
                </tbody>
              </table>
            </div>
            <div id="pagination-container-pending" class="bg-white w-full p-2 justify-center items-center flex text-sm">
              <!-- Pagination links will be populated here -->
            </div>
          </div>

          <div class="col-span-1 h-72 md:h-auto lg:col-span-1 w-full bg-white border border-solid border-gray-200 shadow-md p-2">
            <div id="calendar">
              <!-- Calendar goes here -->
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <?php include('./modal.php')  ?>


  <!-- Warning Modal Overlay -->
  <div id="warning-modal" class="hidden p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white px-4 py-4 rounded-md shadow-lg w-full max-w-sm flex items-center flex-col">
      <div class="grid grid-cols-4 mb-4">
        <!-- First child taking 1/4 of the parent's width -->
        <div class="col-span-1 flex items-center">
          <div class="flex justify-center items-center col-span-1 bg-[#f9d6a0] rounded-full w-16 h-16">
            <img class="w-8 h-8" src="./img/icons/triangle-warning.svg" alt="">
          </div>
        </div>
        <!-- Second child taking 3/4 of the parent's width -->
        <div class="col-span-3">
          <h1 id="modal-title" class="text-lg font-bold mb-2">Warning!</h1>
          <p id="modal-message" class="text-md text-gray-500 text-wrap">Do you really want to perform this action?</p>
        </div>
      </div>

      <div class="w-full flex justify-end items-center space-x-2 text-sm font-semibold">
        <button id="confirm-modal" class="bg-[#e69500] border-2 border-solid border-[#e69500] text-white hover:bg-[#cc8400] hover:border-[#cc8400] py-2 px-4 rounded transition">
          Yes
        </button>
        <button id="close-modal" class="bg-white border-2 border-solid border-gray-600 text-gray-600 hover:bg-gray-600 hover:text-white py-2 px-4 rounded transition">
          No
        </button>
      </div>
    </div>
  </div>

  <!-- Modal for View -->
  <div class="toViewBookingModal p-2 fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-20">
    <div class="bg-white shadow-lg p-6 w-full max-w-lg rounded-3xl m-2 max-h-96 overflow-y-auto">
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
          <p>ID:</p>
          <p id="display-id" class="justify-end flex"><!-- dynamic data --></p>
        </div>
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

  <!-- Modal for Edit -->
  <div class="toEditBookingModal fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-20">
    <div class="bg-white shadow-lg p-6 w-full max-w-lg rounded-3xl m-2">
      <div class="flex justify-between items-center border-b pb-2 mb-2">

        <h2 class="text-lg font-semibold text-gray-500">Booking Information</h2>
        <button class="closeEditBookingModal text-gray-500 hover:text-gray-800">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div class="mb-6">
        <div class="w-full text-gray-500 text-sm flex flex-col mb-6 space-y-2">
          <div class="grid grid-cols-2 gap-2">
            <p>ID:</p>
            <p id="display-id-editInfo" class="justify-end flex"><!-- dynamic data --></p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <p>Customer Name:</p>
            <p id="display-full-name-editInfo" class="justify-end flex"><!-- dynamic data --></p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <p>Phone Number:</p>
            <p id="display-phone-number-editInfo" class="justify-end flex"><!-- dynamic data --></p>
          </div>
        </div>
      </div>

      <form id="edit-booking-form" class="mt-4">
        <input type="hidden" name="id" id="id">
        <div class="grid grid-cols-2 gap-2 mb-4">
          <div class="">
            <label for="pickup-date" class="block text-sm font-medium text-gray-500">Pickup Date</label>
            <input type="date" id="pickup-date" name="pickup-date" class="pickup-date-editInfo mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack">
            <div class="text-red-500 text-sm hidden">Pickup date is required!</div>
          </div>
          <div class="grid grid-cols-1 justify-start">
            <label for="pickup-time" class="pickup-time-editInfo block text-sm font-medium text-gray-500">Pick-up Time</label>
            <select id="pickup-time" name="pickup_time" class="p-2 border border-ashblack rounded-md max-h-12 overflow-auto" required>
              <!-- Options will be dynamically populated by JavaScript -->
            </select>
            <div class="text-red-500 text-sm hidden">Pick-up time is required!</div>
          </div>
        </div>

        <input type="submit" id="edit-booking-btn" value="Save" class="px-4 py-2 w-full bg-green-700 hover:bg-green-800 text-white font-semibold rounded-md">
      </form>
    </div>
  </div>

  <!-- Modal for displaying the larger image -->
  <div class="fixed p-2 inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-30" id="imageModal">
    <div class="bg-white shadow-lg p-4 rounded-lg">
      <button id="closeImageModal" class="px-2 py-1 bg-gray-500 hover:bg-gray-700 text-white rounded-md"><img class="w-5 h-5" src="./img/icons/x.svg" alt=""></button>
      <img id="modal-image" class="w-full max-w-md h-auto mt-2" src="" alt="Large Proof Image">
    </div>
  </div>

  <!-- Modal for Upload Kilo -->
  <div class="toUpdateKiloModal hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-20">
    <div class="bg-white shadow-lg rounded-3xl m-2 w-full md:w-1/2">
      <div class="flex items-center py-6 px-4 bg-federal text-white text-lg font-semibold rounded-t-3xl">
        <div class="flex items-center justify-start space-x-2">
          <img class="w-7 h-7" src="./img/icons/weight-scale.svg" alt="">
          <h1 id="top">Update Kilo Details</h1>
        </div>
      </div>
      <div class="p-4 h-auto w-full overflow-y-auto max-h-64">
        <div class="w-full text-ashblack text-md font-semibold mb-2">
          <p class="justify-start">Booking info</p>
        </div>

        <div class="w-full text-gray-500 text-sm flex flex-col mb-6 space-y-2">
          <div class="grid grid-cols-2 gap-2">
            <p>ID:</p>
            <p id="display-id-forkilo" class="justify-end flex">1<!-- dynamic data --></p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <p>Customer Name:</p>
            <p id="display-full-name-forkilo" class="justify-end flex">Felix Bragais<!-- dynamic data --></p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <p>Phone Number:</p>
            <p id="display-phone-number-forkilo" class="justify-end flex">09691026692<!-- dynamic data --></p>
          </div>
        </div>

        <form action="" id="upload-kilo-form" enctype="multipart/form-data" novalidate>
          <!-- Image Upload Section -->
          <p class="text-md font-semibold mb-2 text-gray-500">Upload Image</p>
          <div class="w-auto border border-dashed border-gray-500 py-4 px-4 rounded-md mb-2">
            <input type="file" id="file-upload" name="file-upload" class="hidden" accept="image/*" required>
            <div class="text-red-500 text-center text-sm hidden">Image is required!</div>
            <label for="file-upload" class="z-20 flex flex-col-reverse items-center justify-center w-full h-full cursor-pointer">
              <p class="z-10 text-md text-center text-gray-500">Drag & Drop your files here</p>
              <img class="z-10 w-8 h-8" src="./img/icons/upload-image.svg" alt="">
            </label>
            <!-- Image preview -->
            <div class="mt-4 text-center flex w-full items-center justify-center">
              <img id="image-preview" class="hidden w-32 h-32 object-cover rounded-md border border-gray-300" alt="Image Preview">
            </div>
          </div>

          <div class="flex flex-col items-center justify-center mb-4">


            <!-- Laundry Kilo Section -->
            <div class="w-full">
              <label for="kilo" class="block text-sm font-medium text-gray-500">Laundry Kilos</label>
              <input required type="number" id="kilo" name="kilo" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="e.g., 3">
              <div class="text-red-500 text-sm hidden">Laundry kilo is required!</div>
            </div>
          </div>

          <div class="flex items-center justify-center space-x-2">
            <input id="update-kilo-button" type="submit" class="flex justify-center items-center px-4 py-2 bg-federal hover:bg-[#1a2479] text-white rounded-md mr-2" value="Submit">
            <button type="button" class="closeUpdateKiloModal2 px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white rounded-md mr-2">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal for Delivery Proof -->
  <div class="toUpdateDeliveryProofModal hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-20">
    <div class="bg-white shadow-lg rounded-3xl m-2 w-full md:w-1/2">
      <div class="flex items-center py-6 px-4 bg-federal text-white text-lg font-semibold rounded-t-3xl">
        <div class="flex items-center justify-start space-x-2">
          <img class="w-7 h-7" src="./img/icons/receipt.svg" alt="">
          <h1 id="top">Proof of delivery</h1>
        </div>
      </div>
      <div class="p-4 h-auto w-full overflow-y-auto max-h-64">
        <div class="w-full text-ashblack text-md font-semibold mb-2">
          <p class="justify-start">Booking info</p>
        </div>

        <div class="w-full text-gray-500 text-sm flex flex-col mb-6 space-y-2">
          <div class="grid grid-cols-2 gap-2">
            <p>ID:</p>
            <p id="display-id-forProof" class="justify-end flex">1<!-- dynamic data --></p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <p>Customer Name:</p>
            <p id="display-full-name-forProof" class="justify-end flex">Felix Bragais<!-- dynamic data --></p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <p>Phone Number:</p>
            <p id="display-phone-number-forProof" class="justify-end flex">09691026692<!-- dynamic data --></p>
          </div>
        </div>

        <form action="" id="upload-proofAndReceipt-form" enctype="multipart/form-data" novalidate>
          <!-- Image Upload Section -->
          <p class="text-md font-semibold mb-2 text-gray-500">Upload Proof of Delivery</p>
          <div class="w-auto border border-dashed border-gray-500 py-4 px-4 rounded-md mb-4">
            <input type="file" id="file-proof-upload" name="file-proof-upload" class="hidden" accept="image/*" required>
            <div class="text-red-500 text-center text-sm hidden">Proof of delivery is required!</div>
            <label for="file-proof-upload" class="z-20 flex flex-col-reverse items-center justify-center w-full h-full cursor-pointer">
              <p class="z-10 text-md text-center text-gray-500">Drag & Drop your files here</p>
              <img class="z-10 w-8 h-8" src="./img/icons/upload-image.svg" alt="">
            </label>
            <!-- Image preview -->
            <div class="mt-4 text-center flex w-full items-center justify-center">
              <img id="image-preview-delivery-proof" class="hidden w-32 h-32 object-cover rounded-md border border-gray-300" alt="Image Preview">
            </div>
          </div>

          <p class="text-md font-semibold mb-2 text-gray-500">Upload Receipt</p>
          <div class="w-auto border border-dashed border-gray-500 py-4 px-4 rounded-md mb-4">
            <input type="file" id="file-receipt-upload" name="file-receipt-upload" class="hidden" accept="image/*" required>
            <div class="text-red-500 text-center text-sm hidden">Receipt is required!</div>
            <label for="file-receipt-upload" class="z-20 flex flex-col-reverse items-center justify-center w-full h-full cursor-pointer">
              <p class="z-10 text-md text-center text-gray-500">Drag & Drop your files here</p>
              <img class="z-10 w-8 h-8" src="./img/icons/upload-image.svg" alt="">
            </label>
            <!-- Image preview -->
            <div class="mt-4 text-center flex w-full items-center justify-center">
              <img id="image-preview-receipt" class="hidden w-32 h-32 object-cover rounded-md border border-gray-300" alt="Image Preview">
            </div>
          </div>

          <div class="flex items-center justify-center space-x-2">
            <input id="update-delivery-proof-button" type="submit" class="flex justify-center items-center px-4 py-2 bg-federal hover:bg-[#1a2479] text-white rounded-md mr-2" value="Submit">
            <button type="button" class="closeUpdateDeliveryProofModal2 px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white rounded-md mr-2">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script type="module" src="./js/delivery-dashboard.js"></script>
</body>


</html>
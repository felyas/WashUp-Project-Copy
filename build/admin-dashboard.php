<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
  header("Location: ./404.php"); // Redirect to the 404 page
  exit();
}
?>




<!DOCTYPE html>
<html lang="en" class="sm:scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">

  <!-- FullCalendar CDN -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>



  <!-- Include Chart.js from CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>

<body class="bg-white min-h-screen font-poppins">
  <div class="flex min-h-screen">
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
        <a href="./admin-dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/dashboard.svg" alt="">
          <p>Dashboard</p>
        </a>
        <a href="./booking-details.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/table.svg" alt="">
          <p>Booking Details</p>
        </a>
        <a href="./complete-booking.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/check.svg" alt="">
          <p>Complete Booking</p>
        </a>
        <a href="./inventory.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/warehouse.svg" alt="">
          <p>Inventory</p>
        </a>
        <div>
          <a href="javascript:void(0);" id="account-dropdown" class="flex items-center p-2 rounded hover:bg-gray-700">
            <img class="h-4 w-4 mr-4" src="./img/icons/users.svg" alt="">
            <p>Account</p>
            <svg class="ml-auto h-4 w-4 transform transition-transform" id="dropdown-icon" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06-.02L10 10.94l3.71-3.75a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.23 8.25a.75.75 0 01-.02-1.06z" clip-rule="evenodd"></path>
            </svg>
          </a>

          <!-- Dropdown Options -->
          <div id="dropdown-menu" class="hidden flex flex-col ml-8 mt-2 space-y-2">
            <a href="./account.php" class="p-2 rounded hover:bg-gray-700 text-sm flex items-center">
              <img src="./img/icons/account-list.svg" alt="add" class="w-4 h-4 mr-2">
              <h2>List of Users</h2>
            </a>
            <a href="./add_account.php" class="p-2 rounded hover:bg-gray-700 text-sm flex items-center">
              <img src="./img/icons/user-plus.svg" alt="add" class="w-4 h-4 mr-2">
              <h2>Add User</h2>
            </a>
          </div>
        </div>
        <a href="./admin-archive.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/Archive.svg" alt="">
          <p>Archive</p>
        </a>
        <div class="flex items-center justify-center pt-12">
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

          <!-- <h1 class="text-2xl font-bold text-white hidden lg:block">Admin Dashboard</h1> -->
          <div class="flex items-center justify-between  lg:space-x-4 text-sm">
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
                <div class="js-notification hidden h-auto min-w-72 sm:w-96 z-10000 absolute top-[52px] -right-[68px] text-nowrap border border-gray-200 border-solid bg-white flex flex-col items-center shadow-lg text-ashblack">
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
      <main class="flex-1 p-6">
        <!-- Toaster -->
        <div id="toaster" class="fixed top-4 right-4 hidden ml-4 text-white shadow-lg z-50">
          <!-- Dynamic Toaster Content -->
        </div>

        <div class="w-full h-auto flex lg:flex-row justify-between items-center mt-2 mb-4 p-4 border border-solid border-gray-200 rounded-lg flex-col">
          <div class="w-full lg:w-1/2 lg:mb-0">
            <h1 class="text-lg lg:text-md font-semibold">
              Welcome back, <span><?php echo $_SESSION['first_name']; ?></span>
            </h1>
            <p class="text-gray-400 text-sm mt-2 lg:mt-1">
              Monitor and manage incoming laundry bookings effortlessly, ensuring a smooth and organized process for your business.
            </p>
          </div>

          <div class="w-full lg:w-auto flex justify-end items-center mt-2 sm:mt-0">
            <button id="js-open-report-modal" class="openGenerateReportModalTrigger w-full sm:w-auto border-2 text-md font-semibold border-federal py-2 px-6 rounded-md shadow-lg text-federal hover:bg-federal hover:text-white transition">
              GENERATE REPORT
            </button>
          </div>
        </div>

        <!-- Grid for Booking Summaries -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
          <!-- Pending Booking Card -->
          <div class="h-36 rounded-lg bg-white shadow-md border border-solid border-gray-200 flex justify-center items-center">
            <div class="grid grid-cols-2 lg:space-x-4">
              <div class="flex items-center justify-center">
                <div class="rounded-[50%] bg-sky-600 p-4 flex items-center justify-center">
                  <img class="h-6 w-6" src="./img/icons/pending.svg" alt="">

                </div>
              </div>
              <div class="flex flex-col items-center justify-center w-16">
                <p class="text-lg md:text-3xl font-semibold" id="js-pending-count"><!-- total count --></p>
                <p class="text-sm md:text-md text-wrap px-2">PENDING</p>
              </div>
            </div>
          </div>

          <!-- For Pickup Booking Card -->
          <div class="h-36 rounded-lg bg-white shadow-md border border-solid border-gray-200 flex justify-center items-center">
            <div class="grid grid-cols-2 lg:space-x-4">
              <div class="flex items-center justify-center">
                <div class="rounded-[50%] bg-celestial p-4 flex items-center justify-center">
                  <div class="relative">
                    <img class="h-6 w-6" src="./img/icons/hand-holding-solid.svg" alt="">
                    <img src="./img/icons/box.svg" class="absolute top-0 right-[5px] h-3 w-3" alt="">
                  </div>
                </div>
              </div>
              <div class="flex flex-col items-center justify-center w-16">
                <p class="text-lg md:text-3xl font-semibold" id="js-for-pickup-count"><!-- total count --></p>
                <p class="text-sm md:text-md text-wrap px-2">PICKUP</p>
              </div>
            </div>
          </div>

          <!-- For Delivery Booking Card -->
          <div class="h-36 rounded-lg bg-white shadow-md border border-solid border-gray-200 flex justify-center items-center">
            <div class="grid grid-cols-2 lg:space-x-4">
              <div class="flex items-center justify-center">
                <div class="rounded-[50%] bg-polynesian p-4 flex items-center justify-center">
                  <img class="h-6 w-6" src="./img/icons/pickup.svg" alt="">
                </div>
              </div>
              <div class="flex flex-col items-center justify-center w-16">
                <p class="text-lg md:text-3xl font-semibold" id="js-for-delivery-count"><!-- total count --></p>
                <p class="text-sm md:text-md text-wrap">DELIVERY</p>
              </div>
            </div>
          </div>

          <!-- Complete Booking Card -->
          <div class="h-36 rounded-lg bg-white shadow-md border border-solid border-gray-200 flex justify-center items-center">
            <div class="grid grid-cols-2 lg:space-x-4">
              <div class="flex items-center justify-center">
                <div class="rounded-[50%] bg-green-700 p-4 flex items-center justify-center">
                  <img class="h-6 w-6" src="./img/icons/check.svg" alt="">
                </div>
              </div>
              <div class="flex flex-col items-center justify-center w-16">
                <p class="text-lg md:text-3xl font-semibold" id="js-complete-count"><!-- total count -->24</p>
                <p class="text-sm md:text-md text-wrap">COMPLETE</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Grid for Chart -->
        <div class="w-full h-auto grid grid-cols-1 gap-4 mb-4 text-sm md:grid-cols-2 lg:grid-cols-4">
          <!-- Total Booking This Month Line Chart (3/4 width on large screens, 1/2 on medium, full width on small screens) -->
          <div class="w-full bg-white shadow-lg border border-solid border-gray-200 rounded-sm lg:col-span-3 md:col-span-1">
            <div class="p-2 bg-celestial text-white">
              <p class="text-md font-semibold">TOTAL BOOKING THIS MONTH</p>
            </div>
            <div class="p-4">
              <!-- Set height for the canvas to control container size -->
              <canvas id="totalBookingChart" class="w-full" style="height: 250px;"></canvas>
            </div>
          </div>

          <!-- Total User Doughnut Chart (1/4 width on large screens, 1/2 on medium, full width on small screens) -->
          <div class="w-full bg-white shadow-lg border border-solid border-gray-200 rounded-sm lg:col-span-1 md:col-span-1">
            <div class="p-2 bg-polynesian text-white">
              <p class="text-md font-semibold">TOTAL USER</p>
            </div>
            <div class="p-4 flex justify-center">
              <canvas id="userPerMonthChart" style="max-width: 100%; max-height: 250px; width: 100%; height: auto;"></canvas>
            </div>
          </div>
        </div>




        <!-- List of Pending Booking -->
        <div class="w-full h-auto grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm">
          <!-- First Div (Pending Bookings) -->
          <div class="h-auto w-full bg-white ">
            <div class="shadow-lg border border-solid border-gray-200 rounded-sm">
              <div class="h-auto p-2 rounded-t-sm flex flex-col justify-center border-solid border-ashblack">
                <p class="text-md font-semibold text-ashblack py-2">CUSTOMER FEEDBACK</p>
                <div class="flex justify-between items-center relative">
                  <input id="js-search-input" type="text" placeholder="Search" class="w-1/2 py-2 rounded-lg pl-14 outline-none border border-solid border-gray-200">
                  <button class="absolute left-0 top-0 h-full px-4 bg-federal rounded-l-lg">
                    <img src="./img/icons/search.svg" class="w-4 h-4" alt="search">
                  </button>
                </div>
              </div>
              <div class="overflow-x-auto h-auto min-h-72 px-2">
                <table class="text-nowrap w-full text-left text-ashblack border-collapse border border-solid border-gray-200">
                  <thead class="bg-gray-200">
                    <tr>
                      <th data-column="user_id" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                        USER ID
                        <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                      </th>
                      <th data-column="first_name" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                        CUSTOMER NAME
                        <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                      </th>
                      <th data-column="rating" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                        RATING
                        <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                      </th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">ACTION</th>
                    </tr>
                  </thead>
                  <tbody id="js-pending-tbody">
                    <tr class="class=" border-b border-gray-200">
                      <td class="px-4 py-2">1</td>
                      <td class="px-4 py-2">Felix</td>
                      <td class="px-4 py-2">Bragais</td>
                      <td class="px-4 py-2">2</td>
                      <td class="min-w-[100px] h-auto flex items-center justify-start space-x-2 flex-grow">
                        <a href="#" id="" class="viewModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition editLink">
                          <img class="w-4 h-4" src="./img/icons/view.svg" alt="edit">
                        </a>
                        <a href="#" id="" class="viewModalTrigger px-3 py-2 bg-[#0E4483] hover:bg-[#0C376A] rounded-md transition editLink">
                          <img class="w-4 h-4" src="./img/icons/feedback-display.svg" alt="edit">
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div id="pagination-container" class="bg-white w-full p-4 mb-4 justify-center items-center flex text-sm">
              <!-- Pagination links will be populated here -->
            </div>
          </div>

          <!-- Second Div (Calendar Section) -->
          <div class="w-full h-72 bg-white rounded-sm shadow-lg border border-solid border-gray-200">
            <div id="calendar" class="p-4">
              <!-- Calendar goes here -->
            </div>
          </div>
        </div>




      </main>

    </div>
  </div>

  <?php
  include('./modal.php');
  ?>


  <!-- Modal for View -->
  <div class="toViewBookingModal fixed hidden inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-20 p-2">
    <div class="bg-white shadow-lg p-6 w-full max-w-lg rounded-3xl m-2">

      <div class="w-full text-ashblack text-md font-semibold mb-2">
        <p class="justify-start">Feedback Summary</p>
      </div>

      <div class="w-full text-gray-500 text-sm flex flex-col mb-6 space-y-2">
        <div class="grid grid-cols-2 gap-2">
          <p>Date:</p>
          <p id="display-date-feedback" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>User ID:</p>
          <p id="display-user-id-feedback" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Customer Name:</p>
          <p id="display-full-name-feedback" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Rating:</p>
          <p id="display-rating-feedback" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Description:</p>
          <p id="display-description-feedback" class="justify-end flex"><!-- dynamic data --></p>
        </div>
      </div>

      <div class="flex justify-center items-center w-full">
        <button type="button" class="closeViewBookingModal2 px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white rounded-md mr-2">Close</button>
      </div>
    </div>
  </div>

  <!-- Add New Event Modal -->
  <form id="add-event-form" novalidate class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-20 p-2">
    <div class="bg-white shadow-lg w-full max-w-lg rounded-3xl m-2">
      <!-- Header Section -->
      <div class="p-4 w-full flex items-center justify-start bg-celestial">
        <img class="w-6 h-6 mr-2" src="./img/icons/calendar.svg" alt="Calendar Icon">
        <h3 id="form-title" class="text-lg text-white font-semibold">Add Event</h3>
      </div>


      <!-- Form Section -->
      <div class="p-4">
        <div class="flex flex-col mb-4">
          <label for="event-title" class="text-sm font-semibold text-gray-500">Event Title:</label>
          <input type="text" name="event_title" id="event-title" required class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200" placeholder="e.g., 50% off">
          <div class="text-red-500 text-sm hidden">Event Title is required!</div> <!-- Feedback -->
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col mb-4">
            <label for="event-start" class="text-sm font-semibold text-gray-500">Start Date:</label>
            <input type="date" name="event_start" id="event-start" required class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200">
            <div class="text-red-500 text-sm hidden">Start Date is required!</div> <!-- Feedback -->
          </div>

          <div class="flex flex-col mb-4">
            <label for="event-end" class="text-sm font-semibold text-gray-500">End Date:</label>
            <input type="date" name="event_end" id="event-end" required class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200">
            <div class="text-red-500 text-sm hidden">End Date is required!</div> <!-- Feedback -->
          </div>

          <!-- Hidden field to store event ID -->
          <div class="flex flex-col mb-4 hidden">
            <label for="event-end" class="text-sm font-semibold text-gray-500">End Date:</label>
            <input type="hidden" id="event-id" name="event_id">
            <div class="text-red-500 text-sm hidden">ID is required!</div> <!-- Feedback -->
          </div>

        </div>

        <div class="flex items-center justify-end mt-6 space-x-2">
          <button type="submit" id="save-event" class="px-4 py-2 bg-celestial hover:bg-[#2a5e7a] text-white rounded-md">Save Event</button>
          <button type="button" id="cancel-event" class="px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white rounded-md">Cancel</button>
        </div>
      </div>
    </div>
  </form>

  <!-- Modal overlay -->
  <div id="generate-report-modal" class="toOpenGenerateReportModal fixed hidden inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <!-- Modal content -->
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-ashblack">Generate Report</h2>
        <button id="close-modal-button" class="closeGenerateReport text-gray-500 hover:text-gray-700 font-bold text-3xl">&times;</button>
      </div>
      <div class="flex flex-col justify center w-full h-auto">
        <form id="generate-report-form" action="./backend/generate_report.php" method="POST" class="text-sm">
          <div class="w-full grid grid-cols-2 mb-2 text-gray-500">
            <div>Current Date: </div>
            <div class="flex justify-end">
              <p class="js-today-date-report">11/11/2024</p>
            </div>
          </div>

          <div class="w-full grid grid-cols-2 mb-4">
            <div class="text-gray-500">Period of time</div>
            <div class="flex justify-end">
              <!-- Dropdown menu for period selection -->
              <select name="period" class="border border-gray-300 rounded-md text-sm p-2 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="last-week">Last Week</option>
                <option value="last-month">Last Month</option>
              </select>
            </div>
          </div>
          <div class="flex justify-center">
            <input type="submit" id="confirm-generate-button" class="bg-green-700 hover:bg-green-800 text-white rounded-md px-4 py-2 mr-2" value="Confirm">
            <button type="button" id="close-modal-button-2" class="closeGenerateReport2 bg-gray-500 hover:bg-gray-700 text-white rounded-md px-4 py-2">Cancel</button>
          </div>
        </form>
      </div>

    </div>
  </div>






  <script type="module" src="./js/admin-dashboard.js"></script>
</body>


</html>
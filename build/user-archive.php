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
    <div id="sidebar" class="w-64 z-50 bg-gray-800 text-white flex-col flex lg:flex lg:w-64 fixed lg:relative top-0 bottom-0 transition-transform transform lg:translate-x-0 -translate-x-full h-screen">
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
        <a href="./user-archive.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/Archive.svg" alt="">
          <p>Archive</p>
        </a>

        <div class="flex items-center justify-center pt-52">
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

                <div id="js-setting" class="absolute hidden top-[54px] -right-4 w-32 h-auto bg-white border border-solid border-gray-200 z-50">
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

      <!-- Main Content Area -->
      <main class="flex-1 p-6 relative h-auto flex flex-col justify-center">

        <!--List-->
        <div class="h-auto w-full gap-2 text-sm mb-4">
          <!-- First div taking 3/4 of the width on large screens -->
          <div class="w-full rounded-sm bg-white border border-solid border-gray-200 shadow-md grid grid-cols-1">
            <div class="h-auto p-2 rounded-t-sm flex flex-col sm:flex-row justify-between border-solid border-ashblack">
              <p class="text-md font-semibold text-ashblack py-2">CUSTOMER ARCHIVE</p>
              <div class="flex justify-between items-center">
                <input id="js-search-bar" type="text" placeholder="Search " class="p-2 w-52 rounded-lg outline-none border border-solid border-gray-200">
              </div>
            </div>
            <div class="overflow-x-auto h-auto px-2 pb-2">
              <table id="complaint-list" class="text-nowrap w-full h-auto text-left text-ashblack border-collapse border border-solid border-gray-200">
                <thead class="bg-gray-200">
                  <tr>
                    <th data-column="id" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                      ID
                      <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                    </th>
                    <th class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">
                      BOOKING ID
                    </th>
                    <th class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">
                      CUSTOMER NAME
                    </th>
                    <!-- Adding the status dropdown filter -->
                    <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">
                      <select id="origin-filter" class="ml-2 px-2 py-1 text-sm border border-gray-300 rounded">
                        <option value="">Origin: All</option>
                        <option value="booking">Booking</option>
                      </select>
                    </th>
                    <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 text-center">ACTION</th>
                  </tr>
                </thead>
                <tbody id="customer-archive-list">
                  <!-- Dynamic List -->
                </tbody>
              </table>
            </div>


          </div>
          <!-- Pagination Container -->
          <div id="pagination-container" class="w-full py-2 justify-center items-center flex text-sm">
          </div>
        </div>
      </main>
    </div>
  </div>

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

  <script type="module" src="./js/user-archive.js"></script>
</body>


</html>
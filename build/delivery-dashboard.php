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

<body class="bg-white min-h-screen font-poppins">
  <div class="flex min-h-screen">

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-federal shadow p-4">
        <div class="flex justify-between items-center">
          <!-- Hamburger Menu -->
          <div id="logo" class="lg:hidden text-white">
            <img class="w-10 h-8" src="./img/logo-white.png" alt="LOGO">
          </div>

          <h1 class="text-2xl font-bold text-white hidden lg:block">Delivery Dashboard</h1>
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
                <div class="js-notification hidden h-auto w-96 z-50 absolute top-[52px] -right-[68px] text-nowrap border border-gray-200 border-solid bg-white flex flex-col items-center shadow-lg text-ashblack">
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
      <main class="flex-1 p-6 relative">

        <div id="toaster" class="fixed top-4 right-4 hidden text-white shadow-lg z-50">
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
        <div class="h-auto grid grid-cols-1 lg:grid-cols-4 gap-2 text-sm mb-4">
          <!-- First div taking 3/4 of the width on large screens -->
          <div class="col-span-4 lg:col-span-3 h-auto w-full rounded-sm bg-white px-4 py-2 border border-solid border-gray-200 shadow-lg">
            <div class="h-auto p-2 rounded-t-sm flex flex-col justify-center border-solid border-ashblack">
              <p class="text-md font-semibold text-ashblack py-2">MANAGE BOOKING</p>
              <div class="flex justify-between items-center relative">
                <input id="js-search-bar" type="text" placeholder="Search bookings..." class="w-1/2 py-2 rounded-lg pl-14 outline-none border border-solid border-gray-200">
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

          <!-- Second div taking 1/4 of the width on large screens -->
          <div class="col-span-4 lg:col-span-1 w-auto bg-white border border-solid border-gray-200 shadow-lg p-2">
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
        <div class="grid grid-cols-2 gap-2">
          <p>Kilo: </p>
          <p id="display-kilo" class="justify-end flex"><!-- dynamic data --></p>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <p>Proof Image:</p>
          <div class="justify-end flex">
            <img id="display-proof-image" class="w-16 h-16 object-cover cursor-pointer hidden" src="" alt="Proof Image"> <!-- Initially hidden -->
            <p id="proof-image-message" class="text-gray-500 hidden"></p>
          </div>
        </div>


      </div>

      <div class="flex justify-center items-center w-full">
        <button type="button" class="closeViewBookingModal2 px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white rounded-md mr-2">Close</button>
      </div>
    </div>
  </div>

  <!-- Modal for displaying the larger image -->
  <div class="fixed p-2 inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-30" id="imageModal">
    <div class="bg-white shadow-lg p-4 rounded-lg">
      <button id="closeImageModal" class="px-2 py-1 bg-gray-500 hover:bg-gray-700 text-white rounded-md"><img class="w-5 h-5" src="./img/icons/x.svg" alt=""></button>
      <img id="modal-image" class="w-full max-w-md h-auto mt-2" src="" alt="Large Proof Image">
    </div>
  </div>



  <!-- <p class="text-md font-semibold mb-2 text-gray-500">Upload Image</p> -->
  <!-- <div class="w-auto border border-dashed border-gray-500 py-4 px-4 rounded-md mb-2">
              <input type="file" id="file-upload" class="hidden" required>
              <div class="text-red-500 text-center text-sm hidden">Image is required!</div>
              <label for="file-upload" class="z-20 flex flex-col-reverse items-center justify-center w-full h-full cursor-pointer">
                <p class="z-10 text-md text-center text-gray-500">Drag & Drop your files here</p>
                <img class="z-10 w-8 h-8" src="./img/icons/upload-image.svg" alt="">
              </label>
            </div> -->

  <!-- Modal for Upload Kilo -->
  <div class="toUpdateKiloModal hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-20">
    <div class="bg-white shadow-lg rounded-3xl m-2 w-full md:w-1/2">
      <div class="flex items-center py-6 px-4 bg-federal text-white text-lg font-semibold rounded-t-3xl">
        <div class="flex items-center justify-start space-x-2">
          <img class="w-7 h-7" src="./img/icons/weight-scale.svg" alt="">
          <h1 id="#top">Update the kilo</h1>
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

  <script type="module" src="./js/delivery-dashboard.js"></script>
</body>


</html>
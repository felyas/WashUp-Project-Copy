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
  <title>Booking Details - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">

  <!-- Include Chart.js from CDN -->
  <script src="../node_modules/chart.js/dist/chart.umd.js" defer></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-white min-h-screen font-poppins">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar" class="w-64 bg-gray-800 text-white flex-col flex lg:flex lg:w-64 fixed lg:relative top-0 bottom-0 transition-transform transform lg:translate-x-0 -translate-x-full z-10">
      <div class="p-4 text-lg font-bold border-b border-gray-700">
        <div class="flex justify-center items-center w-[180px]">
          <img src="./img/logo-white.png" alt="" class="w-12 h-10 mr-1">
          <h1 class="text-base font-bold text-wrap leading-4">
            WASHUP LAUNDRY
          </h1>
        </div>
      </div>
      <nav class="flex flex-col flex-1 p-4 space-y-4">
        <a href="./admin-dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/dashboard.svg" alt="">
          <p>Dashboard</p>
        </a>
        <a href="./booking-details.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/table.svg" alt="">
          <p>Booking Details</p>
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
        <div class="flex items-center justify-center py-24">
          <!-- Close Button -->
          <button id="close-sidebar" class="lg:hidden p-6 text-white rounded-full bg-gray-900 hover:bg-gray-700">
            <img class="h-6 w-6 mx-auto" src="./img/icons/close-button.svg" alt="">
          </button>
        </div>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
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
      <main class="flex-1 p-6 flex flex-col items-center justify-center">
        <!-- Toaster -->
        <div id="toaster" class="fixed top-4 right-4 hidden text-white shadow-lg z-50 max-w-72 sm:max-w-max text-wrap sm:text-nowrap">
          <!-- Dynamic Toaster Content -->
        </div>

        <div class="flex items-center justify-between mb-2 w-full relative ">
          <div class="relative w-1/2">
            <input type="text" id="js-search-bar" class="w-full py-2 rounded-lg pl-14 outline-none border border-solid border-gray-200" placeholder="Search">
            <button class="absolute left-0 top-0 h-full px-4 bg-federal rounded-l-lg">
              <img src="./img/icons/search.svg" class="w-4 h-4" alt="search">
            </button>
          </div>
        </div>

        <!--List-->
        <div class="h-auto w-full grid grid-cols-1 text-sm">
          <!-- List of On Pick-up Booking -->
          <div class="w-full grid grid-cols-1 text-sm">
            <div class="h-auto w-full rounded-sm bg-white shadow-lg border border-solid border-gray-200">
              <div class="h-auto px-2 py-4 rounded-t-sm flex flex-col justify-center border-solid border-ashblack">
                <p class="text-md font-semibold text-ashblack">MANAGE BOOKING</p>
              </div>
              <div class="overflow-x-auto h-auto min-h-72 p-2 pt-0">
                <table class="text-nowrap w-full text-left text-ashblack  border-collapse border border-solid border-gray-200">
                  <thead class="bg-gray-200">
                    <tr>
                      <th data-column="id" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer relative">
                        ID
                        <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span> <!-- Down arrow by default -->
                      </th>
                      <th data-column="fname" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer  relative">
                        CUSTOMER NAME
                        <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                      </th>
                      <th data-column="phone_number" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer  relative">
                        PHONE NUMBER
                        <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                      </th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">
                        <select id="service-type-filter" class="ml-2 px-2 py-1 text-sm border border-gray-300 rounded">
                          <option value="">Service Type: All</option>
                          <option value="2-days Standard">2-days Standard</option>
                          <option value="Rush">Rush</option>
                        </select>
                      </th>
                      <th data-column="address" data-order="desc" class="sortable px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200 cursor-pointer  relative">
                        ADDRESS
                        <span class="sort-icon absolute top-[40%] right-1"><img class="h-[8px] w-[8px]" src="./img/icons/caret-down.svg" alt=""></span>
                      </th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-l border-gray-200">PROOF OF KILO</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-l border-gray-200">PROOF OF DELIVERY</th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-l border-gray-200">RECEIPT</th>
                      <!-- Adding the status dropdown filter -->
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">
                        <select id="status-filter" class="ml-2 px-2 py-1 text-sm border border-gray-300 rounded">
                          <option value="">Status: All</option>
                          <option value="pending">Pending</option>
                          <option value="for pick-up">For Pick-up</option>
                          <option value="on process">On Process</option>
                          <option value="for delivery">For Delivery</option>
                          <option value="complete">Complete</option>
                        </select>
                      </th>
                      <th class="px-4 py-2 font-medium text-sm text-ashblack border-b border-gray-200">ACTION</th>
                    </tr>
                  </thead>

                  <tbody id="js-list-tbody">
                    <!-- Dynamic Data -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div id="pagination-container" class="bg-white w-full p-2 justify-center items-center flex text-sm">
          <!-- Pagination links will be populated here -->
        </div>
      </main>
    </div>
  </div>




  <?php
  include './modal.php';
  ?>
  <!-- Modal for displaying the larger image -->
  <div class="fixed p-2 inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-30" id="imageModal">
    <div class="bg-white shadow-lg p-4 rounded-lg">
      <button id="closeImageModal" class="px-2 py-1 bg-gray-500 hover:bg-gray-700 text-white rounded-md">
        <img class="w-5 h-5" src="./img/icons/x.svg" alt="">
      </button>
      <img id="modal-image" class="w-full max-w-md h-auto mt-2" src="" alt="Large Proof Image">
    </div>
  </div>

  <!-- Modal for View -->
  <div class="toViewBookingModal fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-20">
    <div class="bg-white shadow-lg p-4 w-full max-w-lg rounded-3xl m-2">
      <div class="w-full h-auto py-2 flex flex-col items-center text-nowrap text-gray-500">
        <h4 class="text-lg font-bold">WASHUP LAUNDRY</h4>
        <p class="text-sm">Blk 1 lot 2 morales subdivision, Calamba Laguna</p>
        <p class="text-sm">Phone: +63 930 520 5088</p>
      </div>

      <div class="grid grid-cols-2 gap-2 mb-2 text-gray-500">
        <div class="flex justify-start">
          <p class="text-sm">Date: </p>
        </div>
        <div class="flex justify-end">
          <p id="booking-date" class="text-sm"><!-- Dynamic Date --></p>
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

  <!-- Modal to update Kilo -->
  <div class="toUpdateKiloModal hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-20">
    <div class="bg-white shadow-lg rounded-3xl m-2">
      <div class="flex items-center py-6 px-4 bg-federal text-white text-lg font-semibold rounded-t-3xl">
        <h1 id="#top">Update the kilo</h1>
      </div>
      <div class="p-4 h-auto w-full overflow-y-auto max-h-64">
        <div class="w-full text-ashblack text-md font-semibold mb-2">
          <p class="justify-start">Booking info</p>
        </div>

        <div class="w-full text-gray-500 text-sm flex flex-col mb-6 space-y-2">
          <div class="grid grid-cols-2 gap-2">
            <p>ID:</p>
            <p id="display-id-info" class="justify-end flex"><!-- dynamic data --></p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <p>Customer Name:</p>
            <p id="display-full-name-info" class="justify-end flex"><!-- dynamic data --></p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <p>Phone Number:</p>
            <p id="display-phone-number-info" class="justify-end flex"><!-- dynamic data --></p>
          </div>
        </div>

        <form action="" id="upload-kilo-form" novalidate>
          <!-- Dynamic Item and Quantity Section -->
          <div class="flex flex-col items-center mb-4">
            <p class="text-md font-semibold mb-2 text-gray-500">Items Used and Quantity</p>

            <div id="item-quantity-container" class="space-y-4 w-full">
              <!-- Initial input set already in the HTML -->
              <div class="grid grid-cols-2 gap-4 border border-solid border-gray-200 p-2 shadow-sm rounded-md" id="item-set-1">
                <div>
                  <label for="item-1" class="block text-sm font-medium text-gray-500">Item Used</label>
                  <input type="text" id="item-1" name="item1" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="e.g., Detergent" required>
                  <div class="text-red-500 text-sm hidden">Item is required!</div>
                </div>
                <div>
                  <label for="quantity-1" class="block text-sm font-medium text-gray-500">Quantity</label>
                  <input type="number" id="quantity-1" name="quantity1" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="e.g., 2" required>
                  <div class="text-red-500 text-sm hidden">Quantity is required!</div>
                </div>
              </div>
            </div>

            <!-- Button to Add More Items -->
            <div class="flex justify-end w-full">
              <button type="button" id="add-item" class="mt-4 px-3 py-2  bg-gray-500 hover:bg-gray-700 text-white rounded-md">
                <img class="w-5 h-5" src="./img/icons/add.svg" alt="">
              </button>
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








  <script type="module" src="./js/booking-details.js"></script>
</body>


</html>
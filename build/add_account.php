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
  <title>Account - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    <p class="js-total-notifications"><!-- Dynamic Total Notification  --></p>
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
      <main class="flex-1 p-6 flex items-center justify-center">
        <!-- Toaster -->
        <div id="toaster" class="fixed top-4 right-4 hidden ml-4 text-white shadow-lg z-50">
          <!-- Dynamic Toaster Content -->
        </div>

        <div class="flex flex-col box-border text-ashblack p-4 items-center border border-solid border-gray-200 shadow-lg">
          <p class="text-3xl my-4 font-semibold">Create an Account</p>

          <!-- Div to display success and errors. -->
          <div id="error-div" class="w-full hidden flex items-center justify-center py-2 px-4 text-sm text-red-800 rounded-lg">
            <p id='js-error-message'><!-- Dynamic Error --></p>
          </div>

          <!-- Div to display success and errors. -->
          <div id="success-div" class=" w-full hidden flex items-center justify-center py-2 px-4 rounded-lg text-green-700 text-sm">
            <p id='js-success-message'><!-- Dynamic Error --></p>
          </div>

          <form id="add-user-form" class="mt-4" novalidate>
            <input type="hidden" name="id" id="id">
            <div class="grid grid-cols-2 gap-2 mb-4">
              <div>
                <label for="fname" class="block text-sm font-medium text-gray-500">First Name</label>
                <input required type="text" id="fname" name="fname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" First Name: ">
                <div class="text-red-500 text-sm hidden">First Name is required!</div>
              </div>
              <div>
                <label for="lname" class="block text-sm font-medium text-gray-500">Last Name</label>
                <input required type="text" id="lname" name="lname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Last Name: ">
                <div class="text-red-500 text-sm hidden">Last Name is required!</div>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-4">
              <div class="flex flex-col">
                <label for="email" class="block text-sm font-medium text-gray-500">Email</label>
                <input required type="text" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" Email: ">
                <div class="text-red-500 text-sm hidden">Email is required!</div>
              </div>
              <div class="flex flex-col">
                <label for="Role" class="block text-sm font-medium text-gray-500">Role</label>
                <select id="status-filter" name="role" class="top-0 mt-1 px-2 py-1 w-full text-sm border h-full border-gray-300 rounded">
                  <option value="admin">Admin</option>
                  <option value="delivery">Delivery Man</option>
                </select>
              </div>
            </div>
            <div class="mb-4">
              <label for="password" class="block text-sm font-medium text-gray-500">Password</label>
              <div class="w-full relative">
                <input required type="password" id="js-password" name="password" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Password: ">
                <div class="text-red-500 text-sm hidden">Password is required!</div>
                <div class="flex items-center justify-center space-x-3 absolute top-2 right-2">
                  <button type="button" id="generate-password" class="w-auto h-auto">
                    <div class="flex flex-col items-center justify-center h-auto w-auto ">
                      <img src="./img/icons/lock.svg" alt="" class="w-4 h-4">
                      <div class="flex w-full items-center justify-between h-3">
                        <img src="./img/icons/asterisk.svg" alt="" class="w-2 h-2">
                        <img src="./img/icons/asterisk.svg" alt="" class="w-2 h-2">
                        <img src="./img/icons/asterisk.svg" alt="" class="w-2 h-2">
                      </div>
                    </div>
                  </button>

                  <img src="./img/icons/eye-close.svg" alt="Toggle Password Visibility" class="show-password w-5 h-5 cursor-pointer">
                </div>
              </div>
            </div>
            <div class="mb-4">
              <label for="cpassword" class="block text-sm font-medium text-gray-500">Confirm Password</label>
              <div class="w-full relative">
                <input required type="password" id="js-cpassword" name="cpassword" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Confirm Password: ">
                <div class="text-red-500 text-sm hidden">Confirm Password is required!</div>

                <img src="./img/icons/eye-close.svg" alt="Toggle Password Visibility" class="show-password w-5 h-5 absolute top-3 right-2 cursor-pointer">
              </div>
            </div>




            <input type="submit" id="add-user-btn" value="Add" class="px-4 py-2 w-full bg-polynesian text-white font-semibold rounded-md cursor-pointer">
          </form>
        </div>
      </main>
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

  <script type="module" src="./js/add_account.js"></script>
</body>


</html>
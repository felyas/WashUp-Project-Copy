<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
?>


<!DOCTYPE html>
<html lang="en" class="sm:scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Setting - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-white min-h-screen font-poppins flex flex-col">
  <header class="bg-federal text-white top-0 z-10">
    <section class="max-w-5xl mx-auto p-4 flex justify-between items-center">
      <a href="./customer-dashboard.php">
        <div class="flex items-center justify-center hover:opacity-90">
          <div class="flex justify-center items-center w-[180px]">
            <img src="./img/logo-white.png" alt="" class="w-12 h-10 mr-1">
            <h1 class="text-base font-bold text-wrap leading-4">
              WASHUP LAUNDRY
            </h1>
          </div>
        </div>
      </a>

      <!--Notifications-->
      <div class="flex items-center justify-between lg:space-x-4 text-sm">
        <div class="flex items-center justify-between">

          <div class="h-full flex flex-col items-center jsutify-center relative">
            <button id="js-setting-button" class="cursor-pointer px-4 py-2">
              <img src="./img/icons/setting.svg" alt="setting" class="w-5 h-5">
            </button>

            <div id="js-setting" class="absolute hidden top-[54px] -right-4 w-32 h-auto bg-white border border-solid border-gray-200 text-ashblack">
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
    </section>
  </header>

  <main class="flex-grow flex bg-white justify-center text-ashblack">
    <!-- Toaster -->
    <div id="toaster" class="fixed top-4 right-4 hidden ml-4 text-white shadow-lg z-50">
      <!-- Dynamic Toaster Content -->
    </div>

    <section class="flex flex-col items-center my-4 sm:mx-0 w-96 sm:w-3/4 p-2">
      <div class="w-full md:w-3/4 flex items-center justify-between mb-4 px-2 sm:px-0">
        <div id="top" class="flex flex-col justify-center">
          <h1 class="text-ashblack text-md sm:text-xl md:text-2xl font-semibold">
            Update Your Account Information
          </h1>
          <p class="js-current-time text-ashblack text-sm"></p>
        </div>

        <div class="justify-center items-center">
          <button id="back-to-dashboard-btn" class="flex items-center border-2 border-federal py-2 px-4 rounded-md shadow-lg text-federal hover:bg-federal hover:text-white transition">Back<span class="hidden lg:block ml-1">to Dashboard</span></button>
        </div>
      </div>

      <form id="update-info-form" class="w-full md:w-3/4 px-6 border border-solid bg-white shadow-lg" novalidate>
        <p class="pt-6 pb-2 text-md font-semibold">Update Account</p>

        <!-- Div to display errors. -->
        <div id="error-container" class="w-full hidden flex items-center justify-center text-sm pt-2 pb-4 text-wrap">
          <p class="text-red-700 text-center" id="error-message">Error<!-- Dynamic Error --></p>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div class="mb-4">
            <label for="fname" class="block text-sm font-medium text-gray-500">First Name</label>
            <input type="text" id="js-fname" name="fname" class="mt-1 block w-full border-gray-300 rounded-md py-2 px-2 border border-solid" placeholder=" First Name: " required>
            <div class="text-red-500 text-sm hidden">First name is required!</div>
          </div>
          <div class="mb-4">
            <label for="lname" class="block text-sm font-medium text-gray-500">Last Name</label>
            <input type="text" id="js-lname" name="lname" class="mt-1 block w-full border-gray-300 rounded-md py-2 px-2 border border-solid" placeholder=" Last Name: " required>
            <div class="text-red-500 text-sm hidden">Last name is required!</div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-2">
          <div class="mb-4">
            <label for="phone_number" class="block text-sm font-medium text-gray-500">Phone Number</label>
            <input type="text" id="js-phone_number" name="phone_number" class="mt-1 block w-full border-gray-300 rounded-md py-2 px-2 border border-solid" placeholder=" Phone Number: " required>
            <div class="text-red-500 text-sm hidden">Phone Number is required!</div>
          </div>
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-500">Email</label>
            <input type="text" id="js-email" name="email" class="mt-1 block w-full border-gray-300 rounded-md py-2 px-2 border border-solid" placeholder=" Email: " required>
            <div class="text-red-500 text-sm hidden">Email is required!</div>
          </div>
        </div>

        <div class="mt-2">
          <div class="flex items-center justify-start ">
            <div class="wh-full justify-end">
              <button class="change-password-trigger border-2 border-federal bg-federal py-2 px-4 rounded-md shadow-lg text-white hover:bg-federal hover:text-white text-sm transition">Change Password</button>
            </div>
          </div>
        </div>

        <div class="w-full mt-6">
          <input type="submit" id="save-info-btn" value="Save" class="bg-green-700 hover:bg-green-800 py-2 px-4 w-full text-white rounded-lg mb-6 cursor-pointer">
        </div>
      </form>

    </section>

  </main>


  <!-- Modal for Change Password -->
  <div class="toViewChangePasswordModal p-2 fixed hidden inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center  z-20">
    <div class="bg-white shadow-lg p-6 w-full max-w-lg rounded-3xl m-2 relative ">
      <button class="closeViewChangePasswordModal2 text-gray-500 hover:text-gray-800 absolute top-5 right-5">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      <div class="h-auto w-full flex flex-col items-center">
        <p class="text-gray-500 text-md font-semibold mt-6">Change Password</p>

        <!-- Div to display errors. -->
        <div id="error-container-update-password" class="w-full hidden flex items-center justify-center text-sm pt-2 pb-4 text-wrap">
          <p class="text-red-700 text-center" id="error-message-update-password">Error<!-- Dynamic Error --></p>
        </div>

        <!-- Div to display success. -->
        <div id="success-container-update-password" class="w-full hidden flex items-center justify-center text-sm pt-2 pb-4 text-wrap">
          <p class="text-green-700 text-center" id="success-message-update-password">Error<!-- Dynamic Error --></p>
        </div>

        <form action="" id="change-password-form" class=" w-full" novalidate>
          <div class="grid grid-cols-1 gap-1">
            <div class="">
              <div>
                <label for="password" class="text-sm text-gray-500">Current Password</label>
                <div class="relative">
                  <input type="password" class="w-full border border-solid border-federal rounded-lg p-2 pr-8" name="current_password" placeholder="Enter your current password " required>
                  <img src="./img/icons/eye-close.svg" alt="Toggle Password Visibility" class="show-password absolute top-1 right-0 flex items-center pr-3 w-8 h-8 cursor-pointer">
                </div>
              </div>
            </div>
            <div class="">
              <div>
                <label for="password" class="text-sm text-gray-500">New Password</label>
                <div class="relative">
                  <input type="password" class="w-full border border-solid border-federal rounded-lg p-2 pr-8" name="new_password" placeholder="Enter your new password" required>
                  <img src="./img/icons/eye-close.svg" alt="Toggle Password Visibility" class="show-password absolute top-1 right-0 flex items-center pr-3 w-8 h-8 cursor-pointer">
                </div>
              </div>
            </div>
            <div class="mb-4">
              <div>
                <label for="password" class="text-sm text-gray-500">Confirm Password Password</label>
                <div class="relative">
                  <input type="password" class="w-full border border-solid border-federal rounded-lg p-2 pr-8" name="confirm_password" placeholder="Confirm Password" required>
                  <img src="./img/icons/eye-close.svg" alt="Toggle Password Visibility" class="show-password absolute top-1 right-0 flex items-center pr-3 w-8 h-8 cursor-pointer">
                </div>
              </div>
            </div>
          </div>

          <input type="submit" id="change-password-btn" value="Update Password" class="px-4 py-2 w-full bg-green-700 hover:bg-green-800 text-white rounded-md transition">
        </form>
      </div>
    </div>
  </div>

  <!-- Warning Modal Overlay -->
  <div id="warning-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
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

  <script type="module" src="./js/user-setting.js"></script>
</body>


</html>
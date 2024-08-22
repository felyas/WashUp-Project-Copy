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

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-seasalt min-h-screen font-poppins">
  <div class="flex min-h-screen">

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-federal shadow p-4">
        <div class="flex justify-between items-center">
          <!-- Hamburger Menu -->
          <div id="logo" class="lg:hidden text-seasalt">
            <img class="w-10 h-8" src="./img/logo-white.png" alt="LOGO">
          </div>

          <h1 class="text-2xl font-bold text-seasalt hidden lg:block">Customer Dashboard</h1>
          <!--Notifications-->
          <div class="flex items-center justify-between lg:space-x-4 text-sm">
            <p class="js-current-time text-seasalt"></p>
            <div class="flex items-center justify-between">
              <div class="relative">
                <button class="js-notification-button flex items-center justify-center px-4 py-2">
                  <img src="./img/icons/notification-bell.svg" alt="Logout Icon" class="w-5 h-5">
                </button>
                <div class="js-notification hidden h-auto w-auto bg-seasalt z-10 absolute right-0 text-nowrap p-4 rounded-lg">
                  <h1 class="text-center mb-4 text-lg font-bold">Notifications</h1>
                  <p class="w-full mb-2">New booking request! <a href="./admin-dashboard.php" class="underline text-federal font-semibold">Check</a></p>
                  <p class="w-full mb-2">New booking request! <a href="./admin-dashboard.php" class="underline text-federal font-semibold">Check</a></p>
                  <p class="w-full mb-2">New booking request! <a href="./admin-dashboard.php" class="underline text-federal font-semibold">Check</a></p>
                </div>
              </div>
              <button class="flex items-center justify-center px-4 py-2">
                <img src="./img/icons/logout.svg" alt="Logout Icon" class="w-5 h-5">
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content Area -->
      <main class="flex-1 p-6">
        <!-- Grid for Booking Summaries -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
          <!-- Pending Booking Card -->
          <div class="lg:h-36 w-full rounded-lg bg-white shadow-lg">
            <div class="bg-celestial rounded-t-lg h-12 p-2 flex items-center">
              <img class="h-6 w-6 mr-2" src="./img/icons/pending.svg" alt="">
              <p class="text-md lg:text-lg font-semibold text-seasalt flex items-center">For Pick-up</p>
            </div>
            <div class="p-4 flex items-center justify-center w-full pt-8">
              <div class="flex items-center justify-center text-3xl font-bold">1</div>
            </div>
          </div>

          <!-- On Pick-up Booking Card -->
          <div class="h-36 w-full rounded-lg bg-white shadow-lg">
            <div class="bg-sunrise rounded-t-lg h-12 p-2 flex items-center">
              <img class="h-6 w-6 mr-2" src="./img/icons/pickup.svg" alt="">
              <p class="text-md lg:text-lg font-semibold text-seasalt">For Delivery</p>
            </div>
            <div class="p-4 flex items-center justify-center w-full pt-8">
              <div class="flex items-center justify-center text-3xl font-bold">0</div>
            </div>
          </div>

          <!-- On Delivery Booking Card -->
          <div class="h-36 w-full rounded-lg bg-white shadow-lg col-span-2 sm:col-span-1">
            <div class="bg-green-700 rounded-t-lg h-12 p-2 flex items-center">
              <img class="h-6 w-6 mr-2" src="./img/icons/check.svg" alt="">
              <p class="text-md lg:text-lg font-semibold text-seasalt">Total Booking</p>
            </div>
            <div class="p-4 flex items-center justify-center w-full pt-8">
              <div class="flex items-center justify-center text-3xl font-bold">4</div>
            </div>
          </div>
        </div>

        <!--List-->
        <div class="h-auto grid grid-cols-1 text-sm">
          <!-- List of On Pick-up Booking -->
          <div class="h-auto w-full rounded-sm bg-white shadow-lg">
            <div class="h-12 p-2 rounded-t-sm flex items-center border-solid border-ashblack">
              <p class="text-md font-semibold text-ashblack">MANAGE BOOKING</p>
            </div>
            <div class="overflow-x-auto min-h-[14rem]">
              <table class="text-nowrap w-full text-left text-ashblack">
                <thead class="bg-celestial">
                  <tr>
                    <th class="px-4 py-2 font-medium text-seasalt">#</th>
                    <th class="px-4 py-2 font-medium text-seasalt">Customer Name</th>
                    <th class="px-4 py-2 font-medium text-seasalt">Date</th>
                    <th class="px-4 py-2 font-medium text-seasalt">Pick-up Time</th>
                    <th class="px-4 py-2 font-medium text-seasalt">Status</th>
                    <th class="px-4 py-2 font-medium text-seasalt text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button id="openEditBookingModal" class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button id="openViewBookingModal" class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Completed</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button id="openEditBookingModal" class="px-4 py-2 bg-gray-500 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button id="openViewBookingModal" class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Completed</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button id="openEditBookingModal" class="px-4 py-2 bg-gray-500 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button id="openViewBookingModal" class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Completed</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button id="openEditBookingModal" class="px-4 py-2 bg-gray-500 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button id="openViewBookingModal" class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!--Prev & Next button-->
        <div class="flex items-center justify-center mt-4 w-full space-x-2">
          <button class="py-2 px-4 bg-federal rounded-lg text-seasalt">
            Prev
          </button>
          <button class="py-2 px-4 bg-federal rounded-lg text-seasalt">
            Next
          </button>
        </div>
      </main>


    </div>
  </div>




  <!-- Modal (hidden by default) -->
  <!-- Modal for Edit -->
  <div id="toEditBookingModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-20">
    <div class="bg-white rounded-sm shadow-lg p-6 w-full max-w-lg">
      <div class="flex justify-between items-center border-b pb-2">
        <!-- Put the user's Full Name here at the top -->
        <h2 class="text-lg font-semibold">User's Full Name Information</h2>
        <button id="closeEditBookingModal" class="text-gray-500 hover:text-gray-800">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <form id="editForm" class="mt-4">
        <div class="grid grid-cols-2 gap-2">
          <div class="mb-4">
            <label for="fname" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" id="fname" name="fname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" First Name: ">
          </div>
          <div class=" mb-4">
            <label for="lname" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" id="lname" name="lname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" Last Name: ">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div class="mb-4">
            <label for="pickupTime" class="block text-sm font-medium text-gray-700">Pick-up Time</label>
            <input type="text" id="pickupTime" name="pickupTime" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" Pick-up Time: ">
          </div>
          <div class=" mb-4">
            <label for="pickupDate" class="block text-sm font-medium text-gray-700">Pick-up Date</label>
            <input type="text" id="pickupDate" name="pickupDate" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" Pick-up Date: ">
          </div>
        </div>
        <div class=" mb-4">
          <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
          <input type="text" id="address" name="address" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder=" Address: ">
        </div>
        <div class=" mb-4">
          <label for="service" class="block text-sm font-medium text-gray-700">Service</label>
          <select id="service" name="service" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack">
            <option value="wash-dry-fold">Wash, Dry, Fold</option>
            <option value="wash-dry-press">Wash, Dry, Press</option>
            <option value="dry-clean">Dry Clean</option>
          </select>
        </div>

        <div class=" flex justify-end">
          <button type="button" id="closeEditBookingModal2" class="px-4 py-2 bg-gray-500 text-seasalt rounded-md mr-2">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-green-700 text-seasalt rounded-md">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal for View -->
  <div id="toViewBookingModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden z-20">
    <div class="bg-white rounded-sm shadow-lg p-6 w-full max-w-lg">
      <div class="flex justify-between items-center border-b pb-2">
        <!-- Put the user's Full Name here at the top -->
        <h2 class="text-lg font-semibold">View User's Information</h2>
        <button id="closeViewBookingModal" class="text-gray-500 hover:text-gray-800">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <form id="editForm" class="mt-4">
        <div class="grid grid-cols-2 gap-2">
          <div class="mb-4">
            <label for="fname" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" id="fname" name="fname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="First Name: " disabled>
          </div>
          <div class="mb-4">
            <label for="lname" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" id="lname" name="lname" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Last Name: " disabled>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div class="mb-4">
            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Phone Number: " disabled>
          </div>
          <div class=" mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="text" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Email: " disabled>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div class="mb-4">
            <label for="pickupTime" class="block text-sm font-medium text-gray-700">Pick-up Time</label>
            <input type="text" id="pickupTime" name="pickupTime" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Pick-up Time: " disabled>
          </div>
          <div class=" mb-4">
            <label for="pickupDate" class="block text-sm font-medium text-gray-700">Pick-up Date</label>
            <input type="text" id="pickupDate" name="pickupDate" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Pick-up Date: " disabled>
          </div>
        </div>
        <div class=" mb-4">
          <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
          <input type="text" id="address" name="address" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Address: " disabled>
        </div>
        <div class=" mb-4">
          <label for="service" class="block text-sm font-medium text-gray-700">Service Choosen</label>
          <input type="text" id="service" name="service" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Service Choosen: " disabled>
        </div>
        <div class=" mb-4">
          <label for="shipping_method" class="block text-sm font-medium text-gray-700">Shipping Method</label>
          <input type="text" id="shipping_method" name="shipping_method" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack" placeholder="Shipping Method: " disabled>
        </div>


        <div class=" flex justify-end">
          <button type="button" id="closeViewBookingModal2" class="px-4 py-2 bg-gray-500 text-seasalt rounded-md mr-2">Close</button>
        </div>
      </form>
    </div>
  </div>

  <script type="module" src="./js/customer-dashboard.js"></script>
</body>


</html>
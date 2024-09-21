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

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-seasalt min-h-screen font-poppins">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar" class="w-64 bg-gray-800 text-seasalt flex-col flex lg:flex lg:w-64 fixed lg:relative top-0 bottom-0 transition-transform transform lg:translate-x-0 -translate-x-full z-10">
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
        <a href="./account.php" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/users.svg" alt="">
          <p>Account</p>
        </a>
        <div class="flex items-center justify-center py-24">
          <!-- Close Button -->
          <button id="close-sidebar" class="lg:hidden p-6 text-seasalt rounded-full bg-gray-900 hover:bg-gray-700">
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
          <button id="hamburger" class="lg:hidden px-4 py-2 text-seasalt">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
          </button>

          <!-- <h1 class="text-2xl font-bold text-seasalt hidden lg:block">Admin Dashboard</h1> -->
          <div class="flex items-center justify-between  lg:space-x-4 text-sm">
            <p class="js-current-time text-seasalt"></p>
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
                    <p class="js-total-notifications"><!-- Dynamic Total Notification  --></p>
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
      <main class="flex-1 p-6">
        <div class="flex items-center justify-between mb-4 w-full relative">
          <div class="relative w-1/2">
            <input type="text" class="w-full py-2 rounded-lg pl-14 outline-none" placeholder="Search">
            <button class="absolute left-0 top-0 h-full px-4 bg-federal rounded-l-lg">
              <img src="./img/icons/search.svg" class="w-4 h-4" alt="search">
            </button>
          </div>
        </div>

        <!--List-->
        <div class="h-auto grid grid-cols-1 text-sm border border-solid border-gray-200">
          <!-- List of On Pick-up Booking -->
          <div class="h-auto w-full rounded-sm bg-white shadow-lg">
            <div class="h-12 p-2 rounded-t-sm flex items-center border-solid border-ashblack">
              <p class="text-md font-semibold text-ashblack">List of Booking</p>
            </div>
            <div class="overflow-x-auto h-80 min-h-80">
              <table class="text-nowrap w-full text-left text-ashblack">
                <thead class="bg-celestial">
                  <tr>
                    <th class="px-4 py-2 font-medium text-seasalt">#</th>
                    <th class="px-4 py-2 font-medium text-seasalt">CUSTOMER NAME</th>
                    <th class="px-4 py-2 font-medium text-seasalt">BOOKING DATE</th>
                    <th class="px-4 py-2 font-medium text-seasalt">BOOKING TIME</th>
                    <th class="px-4 py-2 font-medium text-seasalt">PHONE</th>
                    <th class="px-4 py-2 font-medium text-seasalt">ADDRESS</th>
                    <th class="px-4 py-2 font-medium text-seasalt">STATUS</th>
                    <th class="px-4 py-2 font-medium text-seasalt text-center">ACTION</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2">09691026692</td>
                    <td class="px-4 py-2">Pueblo Del Rio</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition editLink">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="edit">
                      </a>
                      <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition editLink">
                        <img class="w-4 h-4" src="./img/icons/check.svg" alt="edit">
                      </a>
                      <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-red-700 hover:bg-red-800 rounded-md transition editLink">
                        <img class="w-4 h-4" src="./img/icons/decline.svg" alt="edit">
                      </a>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2">09601026692</td>
                    <td class="px-4 py-2">Pueblo Del Rio</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition editLink">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="edit">
                      </a>
                      <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition editLink">
                        <img class="w-4 h-4" src="./img/icons/check.svg" alt="edit">
                      </a>
                      </a>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2">09601026692</td>
                    <td class="px-4 py-2">Pueblo Del Rio</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-md transition editLink">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="edit">
                      </a>
                      <a href="#" id="' . $row['id'] . '" class="editModalTrigger px-3 py-2 bg-green-700 hover:bg-green-800 rounded-md transition editLink">
                        <img class="w-4 h-4" src="./img/icons/check.svg" alt="edit">
                      </a>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!--Prev & Next -->
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
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-sm py-2 px-2 border border-solid border-ashblack">
            <option value="pickup">On Pick-up</option>
            <option value="process">On Process</option>
            <option value="delivery">For Delivery</option>
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



  <script type="module" src="./js/booking-details.js"></script>
</body>


</html>
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

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="bg-seasalt min-h-screen font-poppins">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar" class="w-64 bg-gray-800 text-white flex-col flex lg:flex lg:w-64 fixed lg:relative top-0 bottom-0 transition-transform transform lg:translate-x-0 -translate-x-full">
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
        <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/send.svg" alt="">
          <p>Upload Price</p>
        </a>
        <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
          <img class="h-4 w-4 mr-4" src="./img/icons/warehouse.svg" alt="">
          <p>Inventory</p>
        </a>
        <div class="flex items-center justify-center py-24">
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
        <div class="flex justify-between items-center">
          <!-- Hamburger Menu -->
          <button id="hamburger" class="lg:hidden px-4 py-2 text-seasalt">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
          </button>

          <h1 class="text-2xl font-bold text-seasalt">Admin Dashboard</h1>
          <div class="flex items-center justify-between lg:space-x-4 text-sm">
            <p class="js-current-time text-seasalt hidden md:block"></p>
            <button class="flex items-center justify-center px-4 py-2">
              <img src="./img/icons/logout.svg" alt="Logout Icon" class="w-5 h-5">
            </button>
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
              <p class="text-md lg:text-lg font-semibold text-seasalt flex items-center">Pending <span class="hidden md:block ml-2">Booking</span></p>
            </div>
            <div class="p-4 flex items-center justify-center w-full pt-8">
              <div class="flex items-center justify-center text-3xl font-bold">2</div>
            </div>
          </div>

          <!-- On Pick-up Booking Card -->
          <div class="h-36 w-full rounded-lg bg-white shadow-lg">
            <div class="bg-sunrise rounded-t-lg h-12 p-2 flex items-center">
              <img class="h-6 w-6 mr-2" src="./img/icons/pickup.svg" alt="">
              <p class="text-md lg:text-lg font-semibold text-seasalt">On Pick-up</p>
            </div>
            <div class="p-4 flex items-center justify-center w-full pt-8">
              <div class="flex items-center justify-center text-3xl font-bold">13</div>
            </div>
          </div>

          <!-- On Delivery Booking Card -->
          <div class="h-36 w-full rounded-lg bg-white shadow-lg">
            <div class="bg-green-700 rounded-t-lg h-12 p-2 flex items-center">
              <img class="h-6 w-6 mr-2" src="./img/icons/delivery.svg" alt="">
              <p class="text-md lg:text-lg font-semibold text-seasalt">On Delivery</p>
            </div>
            <div class="p-4 flex items-center justify-center w-full pt-8">
              <div class="flex items-center justify-center text-3xl font-bold">7</div>
            </div>
          </div>
        </div>

        <!-- Grid for Chart and Pending Booking List -->
        <div class="max-72 grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4 text-sm">
          <!-- Total Booking This Month Chart -->
          <div class="h-auto lg:h-48 w-full rounded-sm bg-white shadow-lg lg:col-span-1">
            <div class="h-12 p-2 rounded-t-sm flex items-center border-solid border-ashblack border-b">
              <p class="text-md font-semibold text-ashblack">Total Booking this Month</p>
            </div>
            <div class="p-4 flex items-center justify-center">
              <canvas id="bookingChart"></canvas>
            </div>
          </div>

          <!-- List of Pending Booking -->
          <div class="h-full w-full rounded-sm bg-white shadow-lg lg:col-span-3 overflow-x-auto">
            <div class="h-12 p-2 rounded-t-sm flex items-center border-solid border-ashblack">
              <p class="text-md font-semibold text-ashblack">List of Pending Booking</p>
            </div>
            <table class="text-nowrap w-full text-left text-ashblack">
              <thead class="bg-celestial border-b">
                <tr>
                  <th class="px-4 py-2 font-medium text-seasalt">#</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Customer Name</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Date</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Time</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Status</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b border-gray-400">
                  <td class="px-4 py-2">1</td>
                  <td class="px-4 py-2">John Doe</td>
                  <td class="px-4 py-2">2024-08-16</td>
                  <td class="px-4 py-2">10:00 AM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Pending</td>
                  <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                    <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                    </button>
                    <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                    </button>
                    <button class="px-4 py-2 bg-red-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                    </button>
                  </td>
                </tr>
                <tr class="border-b border-gray-400">
                  <td class="px-4 py-2">1</td>
                  <td class="px-4 py-2">John Doe</td>
                  <td class="px-4 py-2">2024-08-16</td>
                  <td class="px-4 py-2">10:00 AM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Pending</td>
                  <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                    <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                    </button>
                    <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                    </button>
                    <button class="px-4 py-2 bg-red-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                    </button>
                  </td>
                </tr>
                <tr class="border-b border-gray-400">
                  <td class="px-4 py-2">1</td>
                  <td class="px-4 py-2">John Doe</td>
                  <td class="px-4 py-2">2024-08-16</td>
                  <td class="px-4 py-2">10:00 AM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Pending</td>
                  <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                    <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                    </button>
                    <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                    </button>
                    <button class="px-4 py-2 bg-red-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                    </button>
                  </td>
                </tr>
                <tr class="border-b border-gray-400">
                  <td class="px-4 py-2">1</td>
                  <td class="px-4 py-2">John Doe</td>
                  <td class="px-4 py-2">2024-08-16</td>
                  <td class="px-4 py-2">10:00 AM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Pending</td>
                  <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                    <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                    </button>
                    <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                    </button>
                    <button class="px-4 py-2 bg-red-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                    </button>
                  </td>
                </tr>
                <tr class="border-b border-gray-400">
                  <td class="px-4 py-2">1</td>
                  <td class="px-4 py-2">John Doe</td>
                  <td class="px-4 py-2">2024-08-16</td>
                  <td class="px-4 py-2">10:00 AM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Pending</td>
                  <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                    <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                    </button>
                    <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                    </button>
                    <button class="px-4 py-2 bg-red-700 rounded-md flex-shrink-0">
                      <img class="w-4 h-4" src="./img/icons/trash.svg" alt="delete">
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Grid for On Pick-up and On Delivery Booking List -->
        <div class="h-auto grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm">
          <!-- List of On Pick-up Booking -->
          <div class="h-auto w-full rounded-sm bg-white shadow-lg overflow-x-auto">
            <div class="h-12 p-2 rounded-t-sm flex items-center border-solid border-ashblack border-b">
              <p class="text-md font-semibold text-ashblack">List of On Pick-up Booking</p>
            </div>
            <table class="text-nowrap w-full text-left text-ashblack">
              <thead class="bg-celestial border-b">
                <tr>
                  <th class="px-4 py-2 font-medium text-seasalt">#</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Customer Name</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Date</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Time</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr class="">
                  <td class="px-4 py-2">1</td>
                  <td class="px-4 py-2">John Doe</td>
                  <td class="px-4 py-2">2024-08-16</td>
                  <td class="px-4 py-2">10:00 AM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                </tr>
                <tr class="">
                  <td class="px-4 py-2">2</td>
                  <td class="px-4 py-2">Jane Smith</td>
                  <td class="px-4 py-2">2024-08-17</td>
                  <td class="px-4 py-2">11:30 AM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                </tr>
                <tr class="">
                  <td class="px-4 py-2">3</td>
                  <td class="px-4 py-2">Alice Johnson</td>
                  <td class="px-4 py-2">2024-08-18</td>
                  <td class="px-4 py-2">02:00 PM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- List of On Delivery Booking -->
          <div class="h-auto w-full rounded-sm bg-white shadow-lg overflow-x-auto">
            <div class="h-12 p-2 rounded-t-sm flex items-center border-solid border-ashblack border-b">
              <p class="text-md font-semibold text-ashblack">List of On Delivery Booking</p>
            </div>
            <table class="text-nowrap w-full text-left text-ashblack">
              <thead class="bg-celestial border-b">
                <tr>
                  <th class="px-4 py-2 font-medium text-seasalt">#</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Customer Name</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Date</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Time</th>
                  <th class="px-4 py-2 font-medium text-seasalt">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr class="">
                  <td class="px-4 py-2">1</td>
                  <td class="px-4 py-2">John Doe</td>
                  <td class="px-4 py-2">2024-08-16</td>
                  <td class="px-4 py-2">10:00 AM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Delivery</td>
                </tr>
                <tr class="">
                  <td class="px-4 py-2">2</td>
                  <td class="px-4 py-2">Jane Smith</td>
                  <td class="px-4 py-2">2024-08-17</td>
                  <td class="px-4 py-2">11:30 AM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Delivery</td>
                </tr>
                <tr class="">
                  <td class="px-4 py-2">3</td>
                  <td class="px-4 py-2">Alice Johnson</td>
                  <td class="px-4 py-2">2024-08-18</td>
                  <td class="px-4 py-2">02:00 PM</td>
                  <td class="px-4 py-2 text-yellow-600 font-semibold">Delivery</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>

    </div>
  </div>

  <script type="module" src="./js/admin-dashboard.js"></script>
</body>


</html>
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
        <div class="flex justify-between items-center">
          <!-- Hamburger Menu -->
          <button id="hamburger" class="lg:hidden px-4 py-2 text-seasalt">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
          </button>

          <h1 class="text-2xl font-bold text-seasalt hidden lg:block">Admin Dashboard</h1>
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
        <div class="flex items-center justify-between mb-4 w-full relative">
          <div class="relative w-1/2">
            <input type="text" class="w-full py-2 rounded-lg pl-14 outline-none" placeholder="Search">
            <button class="absolute left-0 top-0 h-full px-4 bg-federal rounded-l-lg">
              <img src="./img/icons/search.svg" class="w-4 h-4" alt="search">
            </button>
          </div>
          <button class="py-2 px-4 bg-federal rounded-lg text-seasalt ">
            Add Booking
          </button>
        </div>

        <!--List-->
        <div class="h-auto grid grid-cols-1 text-sm">
          <!-- List of On Pick-up Booking -->
          <div class="h-auto w-full rounded-sm bg-white shadow-lg">
            <div class="h-12 p-2 rounded-t-sm flex items-center border-solid border-ashblack">
              <p class="text-md font-semibold text-ashblack">List of On Pick-up Booking</p>
            </div>
            <div class="overflow-x-auto">
              <table class="text-nowrap w-full text-left text-ashblack">
                <thead class="bg-celestial">
                  <tr>
                    <th class="px-4 py-2 font-medium text-seasalt">#</th>
                    <th class="px-4 py-2 font-medium text-seasalt">Customer Name</th>
                    <th class="px-4 py-2 font-medium text-seasalt">Date</th>
                    <th class="px-4 py-2 font-medium text-seasalt">Time</th>
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
                      <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                  <tr class="">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">2024-08-16</td>
                    <td class="px-4 py-2">10:00 AM</td>
                    <td class="px-4 py-2 text-yellow-600 font-semibold">Pick-up</td>
                    <td class="min-w-[168px] h-auto flex items-center justify-center space-x-2 flex-grow">
                      <button class="px-4 py-2 bg-green-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/edit.svg" alt="edit">
                      </button>
                      <button class="px-4 py-2 bg-blue-700 rounded-md flex-shrink-0">
                        <img class="w-4 h-4" src="./img/icons/view.svg" alt="view">
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


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

  <script type="module" src="./js/booking-details.js"></script>
</body>


</html>
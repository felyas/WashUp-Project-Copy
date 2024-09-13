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
  <title>Booking - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">

  <!-- SweetAlert CDN -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-seasalt min-h-screen font-poppins flex flex-col">
  <header class="bg-federal text-seasalt sticky top-0 z-10">
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
          <div class="relative">
            <button class="js-notification-button flex items-center justify-center px-4 py-2">
              <img src="./img/icons/notification-bell.svg" alt="" class="w-5 h-5">
            </button>
            <div class="js-notification hidden h-auto w-auto bg-seasalt z-10 absolute right-0 text-nowrap p-4 rounded-lg">
              <h1 class="text-center mb-4 text-lg font-bold">Notifications</h1>
              <p class="w-full mb-2">New booking request! <a href="./admin-dashboard.php" class="underline text-federal font-semibold">Check</a></p>
              <p class="w-full mb-2">New booking request! <a href="./admin-dashboard.php" class="underline text-federal font-semibold">Check</a></p>
              <p class="w-full mb-2">New booking request! <a href="./admin-dashboard.php" class="underline text-federal font-semibold">Check</a></p>
            </div>
          </div>
          <form action="./backend/handle_logout.php" method="POST" class="p-0 m-0">
            <button type="submit" class="flex items-center justify-center px-4 py-2">
              <img src="./img/icons/logout.svg" alt="Logout Icon" class="w-5 h-5">
            </button>
          </form>

        </div>
      </div>
    </section>
  </header>

  <main class="flex-grow flex bg-seasalt justify-center text-ashblack">


    <section class="flex flex-col items-center my-4 sm:mx-0 w-96 sm:w-3/4">
      <div class="w-full flex items-center justify-between mb-4 px-2 sm:px-0">
        <div class="flex flex-col justify-center">
          <h1 class="text-ashblack text-md sm:text-xl md:text-2xl font-semibold">
            Laundry Service Booking Form
          </h1>
          <p class="js-current-time text-ashblack text-sm"></p>
        </div>

        <div class="justify-center items-center">
          <button id="back-to-dashboard-btn" class="flex items-center border-2 border-federal py-2 px-4 rounded-md shadow-lg text-federal hover:bg-federal hover:text-seasalt transition">Back<span class="hidden lg:block ml-1">to Dashboard</span></button>
        </div>
      </div>

      <form id="add-booking-form" class="w-full px-6 border border-solid bg-white shadow-lg" novalidate>
        <div class="w-full">
          <p class="pb-2 pt-6 text-lg font-semibold">Checkout Details</p>
        </div>
        <div class="grid grid-cols-2 gap-x-4 mb-2 align-top">
          <div class="grid grid-cols-1 justify-start">
            <label for="fname">First Name</label>
            <input type="text" name="fname" class="p-2 border border-ashblack rounded-md" placeholder="Felix" required>
            <div class="text-red-500 text-sm hidden">First name is required!</div>
          </div>
          <div class="grid grid-cols-1 justify-start">
            <label for="lname">Last Name</label>
            <input type="text" name="lname" class="p-2 border border-ashblack rounded-md" placeholder="Bragais" required>
            <div class="text-red-500 text-sm hidden">Last name is required!</div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 mb-2">
          <div class="grid grid-cols-1 justify-start">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" class="p-2 border border-ashblack rounded-md" placeholder="e.g., +63 912 345 6789" required>
            <div class="text-red-600 text-sm mt-1 hidden">Phone is required!</div>
          </div>
          <div class="grid grid-cols-1 justify-start">
            <label for="address">Address</label>
            <input type="text" name="address" class="p-2 border border-ashblack rounded-md" placeholder="e.g., Villa Rizza, Blk 2 Lot 3, Paciano Rizal" required>
            <div class="text-red-500 text-sm hidden">Address is required!</div>
          </div>
        </div>

        <div class="w-full">
          <p class="pb-2 pt-6 text-lg font-semibold">Choose your prefered pick-up date & time</p>
        </div>
        <div class="grid grid-cols-2 gap-x-4 mb-2">
          <div class="grid grid-cols-1 justify-start">
            <label for="pickup-date">Pick-up Date</label>
            <input type="date" name="pickup_date" class="p-2 border border-ashblack rounded-md" required>
            <div class="text-red-500 text-sm hidden">Pick-up date is required!</div>
          </div>
          <div class="grid grid-cols-1 justify-start">
            <label for="pickup-time">Pick-up Time</label>
            <input type="time" name="pickup_time" class="p-2 border border-ashblack rounded-md" required>
            <div class="text-red-500 text-sm hidden">Pick-up time is required!</div>
          </div>
        </div>
        <div class="h-12 sm:h-8 w-full px-2 bg-orange-100 border-orange-500 border border-solid text-orange-800 flex items-center rounded-full text-sm font-semibold mb-2">
          <img class="h-4 w-4" src="./img/icons/danger-icon.svg" alt="">
          <p class="ml-2">Pick-up hours are only available until 6pm</p>
        </div>

        <div class="w-full">
          <p class="pb-2 pt-6 text-lg font-semibold">Customize your service</p>
          <p class="text-md">Services:</p>
        </div>
        <div class="flex flex-col lg:flex-row items-center justify-between lg:w-3/5">
          <label class="cursor-pointer my-2">
            <input type="radio" class="peer sr-only" name="service_selection" value="wash, dry, fold" checked />
            <div class="w-auto max-w-xl rounded-md bg-white px-4 py-2 text-gray-600 ring-2 ring-transparent transition-all hover:shadow peer-checked:text-federal peer-checked:ring-federal peer-checked:ring-offset-2">
              <div class="flex">
                <p class="text-sm font-bold">Wash, Dry, Fold</p>
              </div>
            </div>
          </label>
          <label class="cursor-pointer my-2">
            <input type="radio" class="peer sr-only" name="service_selection" value="wash, dry, press"/>
            <div class="w-auto max-w-xl rounded-md bg-white px-4 py-2 text-gray-600 ring-2 ring-transparent transition-all hover:shadow peer-checked:text-federal peer-checked:ring-federal peer-checked:ring-offset-2">
              <div class="flex">
                <p class="text-sm font-bold">Wash, Dry, Press</p>
              </div>
            </div>
          </label>
          <label class="cursor-pointer my-2">
            <input type="radio" class="peer sr-only" name="service_selection" value="dry clean"/>
            <div class="w-auto max-w-xl rounded-md bg-white px-4 py-2 text-gray-600 ring-2 ring-transparent transition-all hover:shadow peer-checked:text-federal peer-checked:ring-federal peer-checked:ring-offset-2">
              <div class="flex">
                <p class="text-sm font-bold">Dry Clean</p>
              </div>
            </div>
          </label>
        </div>

        <p class="text-md">Other suggestions for my laundry:</p>
        <textarea name="suggestions" class="mb-2 bg-seasalt border border-solid border-gray-300 w-full h-32 rounded-md p-2 js-suggestion" value="none" placeholder="(Optional)"></textarea>

        <label class="text-sm mb-2" for="">Service Type:</label>
        <div class="flex flex-col lg:flex-row items-center justify-between py-2 lg:w-2/3">
          <label class="cursor-pointer my-2">
            <input type="radio" class="peer sr-only" value="standard" name="service_type" checked />
            <div class="w-72 max-w-xl rounded-md bg-white p-2 text-gray-600 ring-2 ring-transparent transition-all hover:shadow peer-checked:text-federal peer-checked:ring-federal peer-checked:ring-offset-2">
              <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between">
                  <p class="text-sm font-semibold uppercase text-gray-500">Standard</p>
                  <div>
                    <svg width="24" height="24" viewBox="0 0 24 24">
                      <path fill="currentColor" d="m10.6 13.8l-2.175-2.175q-.275-.275-.675-.275t-.7.3q-.275.275-.275.7q0 .425.275.7L9.9 15.9q.275.275.7.275q.425 0 .7-.275l5.675-5.675q.275-.275.275-.675t-.3-.7q-.275-.275-.7-.275q-.425 0-.7.275ZM12 22q-2.075 0-3.9-.788q-1.825-.787-3.175-2.137q-1.35-1.35-2.137-3.175Q2 14.075 2 12t.788-3.9q.787-1.825 2.137-3.175q1.35-1.35 3.175-2.138Q9.925 2 12 2t3.9.787q1.825.788 3.175 2.138q1.35 1.35 2.137 3.175Q22 9.925 22 12t-.788 3.9q-.787 1.825-2.137 3.175q-1.35 1.35-3.175 2.137Q14.075 22 12 22Z" />
                    </svg>
                  </div>
                </div>
                <div class="flex items-end justify-between">
                  <p><span class="text-lg font-bold">2</span> days</p>
                  <p class="text-sm font-bold">Free</p>
                </div>
              </div>
            </div>
          </label>

          <label class="cursor-pointer my-2">
            <input type="radio" class="peer sr-only" value="rush" name="service_type" />
            <div class="w-72 max-w-xl rounded-md bg-white p-2 text-gray-600 ring-2 ring-transparent transition-all hover:shadow peer-checked:text-federal peer-checked:ring-federal peer-checked:ring-offset-2">
              <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between">
                  <p class="text-sm font-semibold uppercase text-gray-500">Rush</p>
                  <div>
                    <svg width="24" height="24" viewBox="0 0 24 24">
                      <path fill="currentColor" d="m10.6 13.8l-2.175-2.175q-.275-.275-.675-.275t-.7.3q-.275.275-.275.7q0 .425.275.7L9.9 15.9q.275.275.7.275q.425 0 .7-.275l5.675-5.675q.275-.275.275-.675t-.3-.7q-.275-.275-.7-.275q-.425 0-.7.275ZM12 22q-2.075 0-3.9-.788q-1.825-.787-3.175-2.137q-1.35-1.35-2.137-3.175Q2 14.075 2 12t.788-3.9q.787-1.825 2.137-3.175q1.35-1.35 3.175-2.138Q9.925 2 12 2t3.9.787q1.825.788 3.175 2.138q1.35 1.35 2.137 3.175Q22 9.925 22 12t-.788 3.9q-.787 1.825-2.137 3.175q-1.35 1.35-3.175 2.137Q14.075 22 12 22Z" />
                    </svg>
                  </div>
                </div>
                <div class="flex items-end justify-between">
                  <p><span class="text-lg font-bold">1</span> Day</p>
                  <p class="text-sm font-bold">&#8369; 50</p>
                </div>
              </div>
            </div>
          </label>
        </div>
        <div class="h-12 sm:h-8 w-full px-2 bg-orange-100 border-orange-500 border border-solid text-orange-800 flex items-center rounded-full text-sm font-semibold mb-4">
          <img class="h-4 w-4" src="./img/icons/danger-icon.svg" alt="">
          <p class="ml-2">Note: Rush has additional 50 pesos fee.</p>
        </div>

        <div class="w-full">
          <input type="submit" id="add-booking-btn" value="Submit" class="bg-green-700 hover:bg-green-800 py-2 px-4 w-full text-seasalt rounded-lg mb-6 cursor-pointer">
        </div>

      </form>

    </section>

  </main>

  <script type="module" src="./js/booking.js"></script>
</body>


</html>
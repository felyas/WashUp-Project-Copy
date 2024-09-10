<?php
session_start();

$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
$lastName = isset($_SESSION['last_name']) ? $_SESSION['last_name'] : '';

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

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-seasalt min-h-screen font-poppins flex flex-col">
  <header class="bg-federal text-seasalt sticky top-0 z-10">
    <section class="max-w-5xl mx-auto p-4 flex justify-between items-center">
      <a href="./landing-page.html">
        <div class="flex items-center justify-center hover:opacity-90">
          <div class="flex justify-center items-center w-[180px]">
            <img src="./img/logo-white.png" alt="" class="w-12 h-10 mr-1">
            <h1 class="text-base font-bold text-wrap leading-4">
              WASHUP LAUNDRY
            </h1>
          </div>
        </div>
      </a>
    </section>
  </header>

  <main class="flex-grow flex bg-seasalt items-center justify-center text-ashblack">
    <section class="flex flex-col items-center justify-center my-4 border border-solid bg-white shadow-lg mx-2 sm:mx-0 w-96 sm:w-3/4">

      <div class="flex justify-evenly w-full mt-4 mb-2">
        <div id="step1" class="step w-12 h-12  sm:w-16 sm:h-16 flex items-center justify-center rounded-full bg-gray-500 font-bold">
          <img class="h-[20px] sm:h-[25px]" src="./img/icons/shirt-white.svg" alt="">
        </div>
        <div id="step2" class="step w-12 h-12  sm:w-16 sm:h-16 flex items-center justify-center rounded-full bg-gray-500 font-bold">
          <img class="h-[20px] sm:h-[25px]" src="./img/icons/map-location-white.svg" alt="">
        </div>
        <div id="step3" class="step w-12 h-12  sm:w-16 sm:h-16 flex items-center justify-center rounded-full bg-gray-500 font-bold">
          <img class="h-[20px] sm:h-[25px]" src="./img/icons/clipboard-white.svg" alt="">
        </div>
      </div>

      <div class="flex justify-evenly w-full">
        <div class="w-16 h-16 flex items-center justify-center font-semibold">
          <div class="flex flex-col items-center justify-center">
            <p class="text-sm  text-center">Step 1:</p>
            <p class="text-sm text-center">Services Details</p>
          </div>
        </div>
        <div class="w-16 h-16 flex items-center justify-center font-semibold">
          <div class="flex flex-col items-center justify-center">
            <p class="text-sm text-center">Step 2:</p>
            <p class="text-sm text-center">Checkout Details</p>
          </div>
        </div>
        <div class="w-16 h-16 flex items-start justify-center font-semibold">
          <div class="flex flex-col items-center justify-start h-full">
            <p class="text-sm text-center">Step 3:</p>
            <p class="text-sm text-center">Summary</p>
          </div>
        </div>

      </div>

      <!-- Step Forms -->
      <form id="wizardForm" class="w-full bg-white p-6" action="./backend/handle_booking.php" method="POST">
        <!-- Step 1 -->
        <div id="step1Content" class="step-content box-border hidden">
          <h2 class="text-lg sm:text-2xl font-bold mb-4">Choose you prefered pickup hours</h2>
          <div class="grid grid-cols-2 gap-2">
            <label class="text-sm" for="">Date</label>
            <label class="text-sm" for="">Time</label>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <input type="Date" name="pickup_date" class="w-full border border-solid border-ashblack rounded-lg mb-2 p-2 js-date">
            <input type="Time" name="pickup_time" class="w-full border border-solid border-ashblack rounded-lg mb-2 p-2
            js-time">
          </div>

          <div class="h-12 sm:h-8 w-full px-2 bg-red-100 border-red-500 border border-solid text-red-800 flex items-center rounded-full text-sm">
            <img class="h-4 w-4" src="./img/icons/danger-icon.svg" alt="">
            <p class="ml-2">Pick-up hours are only available until 6pm</p>
          </div>

          <hr class="w-full mb-2 mt-4">

          <h2 class="text-lg sm:text-2xl font-bold mb-4">Customize your desired service</h2>
          <p class="text-md">Services:</p>
          <div class="flex flex-col lg:flex-row items-center justify-between lg:w-3/5">
            <label class="flex items-center bg-seasalt border border-solid border-gray-300 rounded-lg p-4 mb-4 cursor-pointer hover:bg-gray-200 w-full lg:w-auto">
              <input type="radio" name="service" value="Wash, Dry, Fold" class="form-radio w-5 h-5 mr-4" checked>
              <span class="text-sm">Wash, Dry, Fold</span>
            </label>
            <label class="flex items-center bg-seasalt border border-solid border-gray-300 rounded-lg p-4 mb-4 cursor-pointer hover:bg-gray-200 w-full lg:w-auto">
              <input type="radio" name="service" value="Wash, Dry, Press" class="form-radio w-5 h-5 mr-4">
              <span class="text-sm">Wash, Dry, Press</span>
            </label>
            <label class="flex items-center bg-seasalt border border-solid border-gray-300 rounded-lg p-4 mb-4 cursor-pointer hover:bg-gray-200 w-full lg:w-auto">
              <input type="radio" name="service" value="Dry Clean" class="form-radio w-5 h-5 mr-4">
              <span class="text-sm">Dry Clean</span>
            </label>
          </div>


          <p class="text-md">Other suggestions for my laundry:</p>
          <textarea name="suggestions" class="mb-2 bg-seasalt border border-solid border-gray-300 w-full h-32 rounded-md p-2 js-suggestion" placeholder="(Optional)"></textarea>


          <div class="flex justify-end">
            <button id="nextToStep2" type="button" class="px-6 py-2 bg-federal text-seasalt rounded-lg hover:bg-fedHover transition text-lg font-bold">→</button>
          </div>
        </div>

        <!-- Step 2 -->
        <div id="step2Content" class="step-content">

          <h2 class="text-lg sm:text-2xl font-bold">Checkout Details</h2>
          <p class="text-md mb-2">Personal Details:</p>
          <div class="grid grid-cols-2 gap-2">
            <label class="text-sm" for="">First Name</label>
            <label class="text-sm" for="">Last Name</label>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <input type="text" name="fname" class="w-full border border-solid border-ashblack rounded-lg mb-2 p-2 js-fname-input" placeholder="First Name: " value="<?php echo htmlspecialchars($firstName); ?>">
            <input type="text" name="lname" class="w-full border border-solid border-ashblack rounded-lg mb-2 p-2 js-lname-input" placeholder="Last Name: " value="<?php echo htmlspecialchars($lastName); ?>">
          </div>


          <label class="text-sm" for="">Phone Number</label>
          <input type="text" name="phone_number" class="w-full border border-solid border-ashblack rounded-lg mb-2 p-2 js-phone-number" placeholder="Phone Number: 09691026692">


          <hr class="w-full my-2">

          <p class="text-md mb-2">Pickup &amp; Delivery Details:</p>

          <label class="text-sm" for="">Address</label>
          <input type="text" name="address" class="mb-4 w-full border border-solid border-ashblack rounded-lg p-2 js-address-input" placeholder="Street Name. Building. House No.* ">

          <label class="text-sm" for="">Shipping method</label>
          <div class="flex flex-col lg:flex-row items-center justify-between lg:w-1/3">
            <label class="flex items-center bg-seasalt border border-solid border-gray-300 rounded-lg p-4 mb-4 cursor-pointer hover:bg-gray-200 w-full lg:w-auto">
              <input type="radio" name="shipping_method" value="2-day Standard" class="form-radio w-5 h-5 mr-4" checked>
              <span class="text-sm">2-day Standard</span>
            </label>
            <label class="flex items-center bg-seasalt border border-solid border-gray-300 rounded-lg p-4 mb-4 cursor-pointer hover:bg-gray-200 w-full lg:w-auto">
              <input type="radio" name="shipping_method" value="Rush" class="form-radio w-5 h-5 mr-4">
              <span class="text-sm">Rush</span>
            </label>
          </div>

          <div class="flex justify-between">
            <button id="backToStep1" type="button" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition text-lg font-bold">←</button>
            <button id="nextToStep3" type="button" class="px-6 py-2 bg-federal text-seasalt rounded-lg hover:bg-fedHover transition text-lg font-bold">→</button>
          </div>
        </div>

        <!-- Step 3 -->
        <div id="step3Content" class="step-content hidden">
          <h2 class="text-2xl font-bold mb-4">Order Summary</h2>

          <div class="grid grid-cols-2 text-sm sm:text-md">
            <div class="space-y-4">
              <div>
                <p>First Name:</p>
                <p>Last Name:</p>
                <p>Phone Number:</p>
              </div>
              <div>
                <p>Date: </p>
                <p>Time: </p>
                <p>Address: </p>
                <p>Shipping Method: </p>
              </div>
              <div>
                <p>Pick-up Date:</p>
                <p>Pick-up Time:</p>
                <p>Services:</p>
                <p>Other Suggestions:</p>
              </div>
            </div>

            <div class="space-y-4">
              <div>
                <p class="js-fname"></p>
                <p class="js-lname"></p>
                <p class="js-phone_number"></p>
              </div>

              <div>
                <p class="js-current-date">2024-08-28</p>
                <p class="js-current-time">18:01</p>
                <p class="js-address"></p>
                <p class="js-shipping-method">2-day Standard</p>
              </div>
              <div>
                <p class="js-preffered-date">2024-08-28</p>
                <p class="js-preffered-time">18:01</p>
                <p class="js-service">Wash, Dry, Fold</p>
                <p class="js-other-suggestions">None</p>
              </div>
            </div>
          </div>

          <div class="flex justify-between mt-2">
            <button id="backToStep2" type="button" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition text-lg font-bold">←</button>
            <button type="submit" class="px-6 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 transition">Submit</button>
          </div>
        </div>
      </form>

    </section>

  </main>

  <footer id="footer" class="bg-federal text-seasalt text-base z-10">
    <section class="max-w-5xl mx-auto p-4 flex flex-col justify-center items-center sm:flex-row sm:justify-between">
      <div class="flex flex-col sm:text-left">
        <p class="font-bold">
          TAMANG LABA, TAMANG BANGO, TAMANG PRESYO
        </p>
      </div>

      <div class="flex flex-col sm:gap-1">
        <p class="text-right">&copy; <span id="year"></span> All Rights Reserved</p>
      </div>
    </section>
  </footer>

  <script type="module" src="./js/booking.js"></script>
</body>


</html>
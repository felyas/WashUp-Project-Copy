<!DOCTYPE html>
<html lang="en" class="sm:scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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
        <div id="step1" class="step w-12 h-12  sm:w-16 sm:h-16 flex items-center justify-center rounded-full bg-gray-300 font-bold">
          <img class="h-[20px] sm:h-[25px]" src="./img/icons/shirt.svg" alt="">
        </div>
        <div id="step2" class="step w-12 h-12  sm:w-16 sm:h-16 flex items-center justify-center rounded-full bg-gray-300 font-bold">
          <img class="h-[20px] sm:h-[25px]" src="./img/icons/map-location.svg" alt="">
        </div>
        <div id="step3" class="step w-12 h-12  sm:w-16 sm:h-16 flex items-center justify-center rounded-full bg-gray-300 font-bold">
          <img class="h-[20px] sm:h-[25px]" src="./img/icons/clipboard-list.svg" alt="">
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
      <form id="wizardForm" class="w-full bg-white p-6">
        <!-- Step 1 -->
        <div id="step1Content" class="step-content box-border">
          <h2 class="text-lg sm:text-2xl font-bold mb-4">Choose you prefered pickup hours</h2>
          <div class="grid grid-cols-2 gap-2">
            <label class="text-sm" for="">Date</label>
            <label class="text-sm" for="">Time</label>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <input type="Date" class="w-full border border-solid border-federal rounded-lg mb-2 p-2">
            <input type="Time" class="w-full border border-solid border-federal rounded-lg mb-2 p-2">
          </div>

          <hr class="w-full my-2">

          <h2 class="text-lg sm:text-2xl font-bold mb-4">Customize your desired service</h2>
          <p class="text-md">Services:</p>
          <div class="flex flex-col lg:flex-row items-center justify-between lg:w-3/5">
            <label class="flex items-center bg-seasalt border border-solid border-gray-300 rounded-lg p-4 mb-4 cursor-pointer hover:bg-gray-200 w-full lg:w-auto">
              <input type="radio" name="service" value="wash-dry-fold" class="form-radio w-5 h-5 mr-4">
              <span class="text-sm">Wash, Dry, Fold</span>
            </label>
            <label class="flex items-center bg-seasalt border border-solid border-gray-300 rounded-lg p-4 mb-4 cursor-pointer hover:bg-gray-200 w-full lg:w-auto">
              <input type="radio" name="service" value="wash-dry-press" class="form-radio w-5 h-5 mr-4">
              <span class="text-sm">Wash, Dry, Press</span>
            </label>
            <label class="flex items-center bg-seasalt border border-solid border-gray-300 rounded-lg p-4 mb-4 cursor-pointer hover:bg-gray-200 w-full lg:w-auto">
              <input type="radio" name="service" value="dry-clean" class="form-radio w-5 h-5 mr-4">
              <span class="text-sm">Dry Clean</span>
            </label>
          </div>

          <p class="text-md">Other suggestions for my laundry:</p>
          <textarea class="bg-seasalt border border-solid border-gray-300 w-full h-32 p-2 rounded-md" placeholder="Enter your text here">
          </textarea>


          <div class="flex justify-end">
            <button id="nextToStep2" type="button" class="px-6 py-2 bg-federal text-seasalt rounded-lg hover:opacity-90 transition">Next</button>
          </div>
        </div>

        <!-- Step 2 -->
        <div id="step2Content" class="step-content hidden">
          <h2 class="text-2xl font-bold mb-4">Step 2</h2>
          <input type="email" placeholder="Enter your email" class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:ring focus:border-blue-500" />
          <div class="flex justify-between">
            <button id="backToStep1" type="button" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">Back</button>
            <button id="nextToStep3" type="button" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">Next</button>
          </div>
        </div>

        <!-- Step 3 -->
        <div id="step3Content" class="step-content hidden">
          <h2 class="text-2xl font-bold mb-4">Step 3</h2>
          <input type="password" placeholder="Enter your password" class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:ring focus:border-blue-500" />
          <div class="flex justify-between">
            <button id="backToStep2" type="button" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">Back</button>
            <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Submit</button>
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
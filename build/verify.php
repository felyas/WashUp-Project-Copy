<?php
session_start();

if (!isset($_SESSION['email'])) {
  header("Location: ./login.php");
  exit();
}
?>



<!DOCTYPE html>
<html lang="en" class="sm:scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">
  <link rel="stylesheet" href="./css/verify.css">

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

  <main class="flex-grow flex bg-seasalt items-center justify-center">
    <section class="flex my-4 border border-solid justify-center items-center bg-white shadow-lg mx-2 sm:mx-0">
      <div class="flex flex-col box-border text-ashblack p-4 items-center justify-center">
        <p class="text-3xl my-2 font-semibold">Verify Your Account</p>

        <p class="text-md my-1 text-center w-3/4">
          An OTP has been sent to your email. Please check your inbox and enter the code to proceed.
        </p>


        <!-- Div to display errors. -->
        <div id="error-container" class="hidden w-full flex items-center justify-center text-sm pt-2 text-wrap">
          <p class="text-red-700 text-center" id="error-message"><!-- Dynamic Error --></p>
        </div>

        <form action="" id="otp-form" method="POST" autocomplete="off" class="text-center">
          <div class="flex justify-center items-center my-4">
            <input type="number"
              class="js-otp-field rounded-lg text-3xl sm:text-5xl h-[4rem] w-[4rem] sm:h-24 sm:w-24 border-3 border-gray-100 m-1 text-center font-semibold outline-none focus:border-darkblue focus:shadow-md appearance-none"
              name="otp1" placeholder="0" min="0" max="9" onpaste="false">

            <input type="number"
              class="js-otp-field rounded-lg text-3xl sm:text-5xl  h-[4rem] w-[4rem] sm:h-24 sm:w-24 border-3 border-gray-100 m-1 text-center font-semibold outline-none focus:border-darkblue focus:shadow-md appearance-none"
              name="otp2" placeholder="0" min="0" max="9" onpaste="false">

            <input type="number"
              class="js-otp-field rounded-lg text-3xl sm:text-5xl  h-[4rem] w-[4rem] sm:h-24 sm:w-24 border-3 border-gray-100 m-1 text-center font-semibold outline-none focus:border-darkblue focus:shadow-md appearance-none"
              name="otp3" placeholder="0" min="0" max="9" onpaste="false">

            <input type="number"
              class="js-otp-field rounded-lg text-3xl sm:text-5xl  h-[4rem] w-[4rem] sm:h-24 sm:w-24 border-3 border-gray-100 m-1 text-center font-semibold outline-none focus:border-darkblue focus:shadow-md appearance-none"
              name="otp4" placeholder="0" min="0" max="9" onpaste="false">
          </div>

          <input type="submit" id="otp-submit-button"
            class="w-full bg-federal text-seasalt font-semibold hover:opacity-90 rounded-lg mb-2 p-2 cursor-pointer"
            value="Send">
        </form>

      </div>
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

  <script type="module" src="./js/verify.js"></script>
</body>


</html>
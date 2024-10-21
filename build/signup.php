<?php
session_start();
?>




<!DOCTYPE html>
<html lang="en" class="sm:scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - WashUp Laundry</title>
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

  <main class="flex-grow flex bg-seasalt justify-center items-center">
    <section class="flex my-4 border border-solid justify-center items-center bg-white shadow-lg mx-2 sm:mx-0">
      <div class="flex flex-col w-full box-border text-ashblack p-4 items-center justify-center">
        <p class="text-3xl my-4 font-semibold">Create an Account</p>

        <!-- Div to display errors. -->
        <div id="error-container" class="hidden w-full flex items-center justify-center text-sm pt-2 text-wrap">
          <p class="text-red-700 text-center" id="error-message"><!-- Dynamic Error --></p>
        </div>

        <form action="" id="signup-form" method="POST" class="flex flex-col my-4" novalidate>
          <div class="grid grid-cols-2 gap-2">
            <label class="text-sm text-gray-500" for="fname">First Name</label>
            <label class="text-sm text-gray-500" for="lname">Last Name</label>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <input type="text" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" name="fname" placeholder="First Name: " required>
            <input type="text" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" name="lname" placeholder="Last Name: " required>
          </div>

          <label class="text-sm text-gray-500" for="email">Email</label>
          <input type="email" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" name="email" placeholder="Email: " required>

          <div class="mb-2">
            <label class="text-sm text-gray-500" for="password">Password</label>
            <div class="relative">
              <input type="password" class="w-full border border-solid border-federal rounded-lg mb-2 p-2 pr-10" name="password" placeholder="Password: " required>
              <img src="./img/icons/eye-close.svg" alt="Toggle Password Visibility" class="show-password absolute top-1 right-0 flex items-center pr-3 w-8 h-8 cursor-pointer">
            </div>
          </div>

          <div class="mb-2">
            <label class="text-sm text-gray-500" for="confirm_password">Confirm Password</label>
            <div class="relative">
              <input type="password" class="w-full border border-solid border-federal rounded-lg mb-2 p-2 pr-10" name="confirm_password" placeholder="Confirm Password: " required>
              <img src="./img/icons/eye-close.svg" alt="Toggle Password Visibility" class="show-password absolute top-1 right-0 flex items-center pr-3 w-8 h-8 cursor-pointer">
            </div>
          </div>

          <input type="submit" id="signup-button" class="w-full bg-federal text-seasalt font-semibold hover:opacity-90 rounded-lg mb-2 p-2 cursor-pointer" value="Sign Up">
        </form>

        <p class="text-ashblack text-sm">Have an account already? <a class="text-federal font-medium hover:underline" href="./login.php">Sign In</a></p>
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

  <script type="module" src="./js/signup.js"></script>
</body>


</html>
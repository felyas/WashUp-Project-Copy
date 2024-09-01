<?php
session_start();
?>



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

  <main class="flex-grow flex bg-seasalt items-center justify-center">
    <section class="flex my-4 border border-solid justify-center items-center bg-white shadow-lg mx-2 sm:mx-0">
      <div class="flex flex-col box-border text-ashblack p-4 items-center justify-center">
        <p class="text-3xl my-2 font-semibold">Forgot your Password ?</p>

        <p class="text-md my-1 text-center w-3/4">
          Enter your email address, and we'll send you a link to reset your password.
        </p>

        <!-- Div to display success and errors. -->
        <?php if (isset($_SESSION['status'])): ?>
          <div class="w-3/4 text-sm flex justify-center p-2 border bg-green-100 border-green-500 border-solid text-green-800 rounded-lg">
            <?php echo htmlspecialchars($_SESSION['status']); ?>
          </div>
          <?php unset($_SESSION['status']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
          <div class="w-3/4 text-sm flex justify-center p-2 border bg-red-100 border-red-500 border-solid text-red-800 rounded-lg">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
          </div>
          <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="./backend/handle_forgot-password.php" method="POST" class="flex flex-col my-4 w-3/4">
          <label class="text-sm" for="">Email</label>
          <input type="email" name="email" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" placeholder="Email: ">

          <input type="submit" class="w-full bg-federal text-seasalt font-semibold hover:opacity-90 rounded-lg mb-2 p-2 cursor-pointer" value="Send">
        </form>

        <p class="text-ashblack text-sm">Remembered your password? <a class="text-federal font-medium hover:underline" href="./login.php">Sign In</a>
        </p>
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

  <script type="module" src="./js/login.js"></script>
</body>


</html>
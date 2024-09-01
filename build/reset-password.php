<?php
session_start();
require_once('./backend/db_connection.php');

// Get the token from the URL query parameter
$token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '';

// Check if the token is provided
if (empty($token)) {
    $_SESSION['error'] = "Invalid request. No token provided.";
    header("Location: ../forgot-password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="sm:scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - WashUp Laundry</title>
  <link rel="icon" href="./img/logo-white.png">

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/palette.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body class="bg-seasalt min-h-screen font-poppins flex flex-col">
  <header class="bg-federal text-seasalt sticky top-0 z-10">
    <section class="max-w-5xl mx-auto p-4 flex justify-between items-center">
      <a href="./landing-page.html">
        <div class="flex items-center justify-center hover:opacity-90">
          <div class="flex justify-center items-center w-[180px]">
            <img src="./img/logo-white.png" alt="WashUp Laundry Logo" class="w-12 h-10 mr-1">
            <h1 class="text-base font-bold text-wrap leading-4">
              WASHUP LAUNDRY
            </h1>
          </div>
        </div>
      </a>
    </section>
  </header>

  <main class="flex-grow flex bg-seasalt justify-center items-center">
    <section class="flex my-4 border border-solid justify-center bg-white shadow-lg mx-2 sm:mx-0 max-w-1/2 sm:w-1/2">
      <div class="flex flex-col w-full box-border text-ashblack p-4 items-center">
        <p class="text-3xl my-4 font-semibold">Reset your password</p>

        <!-- Div to display errors. -->
        <?php if (isset($_SESSION['error'])): ?>
          <div class="w-full text-sm flex justify-center p-2 border bg-red-100 border-red-500 border-solid text-red-800 rounded-lg">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
          </div>
          <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Form for resetting the password -->
        <form action="./backend/handle_reset-password.php?token=<?php echo urlencode($token); ?>" method="POST" class="flex flex-col my-4 w-full">
          <label class="text-sm" for="new_password">New Password</label>
          <input type="password" id="new_password" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" name="new_password" placeholder="Enter your new Password: " required>

          <label class="text-sm" for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" name="confirm_password" placeholder="Confirm Password: " required>

          <input type="submit" class="w-full bg-federal text-seasalt font-semibold hover:opacity-90 rounded-lg mb-2 p-2 cursor-pointer" value="Save">
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

  <script type="module" src="./js/login.js"></script>
</body>
</html>
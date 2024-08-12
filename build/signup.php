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

  <main class="flex-grow flex bg-seasalt justify-center">
    <section class="flex my-4 border border-solid justify-center bg-white shadow-lg mx-2 sm:mx-0">
      <div class="flex flex-col w-full box-border text-ashblack p-4 items-center">
        <p class="text-3xl my-4 font-semibold">Create an Account</p>

        <form action="#" method="" class="flex flex-col my-4">
          <div class="grid grid-cols-2 gap-2">
            <label class="text-sm" for="">First Name</label>
            <label class="text-sm" for="">Last Name</label>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <input type="text" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" placeholder="First Name: ">
            <input type="text" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" placeholder="Last Name: ">
          </div>

          <label class="text-sm" for="">Email</label>
          <input type="email" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" placeholder="Email: ">

          <label class="text-sm" for="">Phone Number</label>
          <input type="text" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" placeholder="Mobile No. +63: ">

          <label class="text-sm" for="">Password</label>
          <input type="password" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" placeholder="Password: ">

          <label class="text-sm" for="">Confirm Password</label>
          <input type="password" class="w-full border border-solid border-federal rounded-lg mb-2 p-2" placeholder="Confirm Password: ">

          <input type="submit" class="w-full bg-federal text-seasalt font-semibold hover:opacity-90 rounded-lg mb-2 p-2 cursor-pointer" value="Sign Up">
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

  <script type="module" src="./js/login.js"></script>
</body>


</html>
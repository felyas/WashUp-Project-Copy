<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <link rel="icon" href="./img/logo-white.png">

    <!-- CSS -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/palette.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">


    <style>
        *{
            font-family: poppins, arial;
        }
    </style>
</head>

<body class="h-screen bg-federal flex items-center justify-center ">

    <div class=" rounded-lg p-8 w-full max-w-md text-center m-2">

        <!-- Error Text -->
        <h1 class="text-6xl font-extrabold text-seasalt mb-4">403</h1>
        <p class="text-xl text-seasalt mb-8">You're not authorized to access this page.</p>

        <!-- Action Button -->
        <button onclick="goBack()" class="px-4 py-2 text-seasalt text-md bg-celestial
        font-semibold rounded-lg transition">
            Go Back
        </button>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>

</html>
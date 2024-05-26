<?php
session_start(); // Ensure the session is started
// Redirect users to login page if they're not authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
    <link rel="stylesheet" href="home.css"/>
    <script src="https://kit.fontawesome.com/c4254e24a8.js"></script>
    <title>Adman Home page</title>
</head>
<style>
    .products {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
    }
    #img {
    width: 149px;  /* Change this value to the desired width */
    height: auto;  /* Keeps the aspect ratio intact */
}

    .product-card {
        width: 300px;
        margin: 10px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        text-align: center;
    }
.product-card img {
    width: 100%; /* تأكيد تمدد الصورة لتملأ المربع بالكامل */
    height: 60%;

}
</style>
<body>
    <header class="header">
        <div class="header-1">
            <a href="AdmanHome.php">
           <img id="img" src="im/new_logo-removebg-preview.png">
           <a>
         <form action="" class="search-form">
            <input type="search" name="" placeholder="Search" id="search-box"/>
            <label for="search-box" class="fas fa-search"></label>
         </form>

         <div class="icons">
            <div id="search-btn" class="fas fa-search"></div>

            <div class="dropdown">
                <a href="#" class="dropbtn">
                    <i class="material-icons icon">person</i>
                </a>
                <div class="dropdown-content">
                    <a href="login.php">Login</a>
                    <a href="singup.php">Register</a>
                </div>
            </div>

                <a href="Compared.php"><i class="material-icons compare icon ">compare_arrows</i>
                    <span class="Czero">0</span></a>
                <a href="connection.php"><i id="cart-items" class="material-icons icon">shopping_cart</i>
                    <span class="Szero">0</span></a> 
              </div>
    </div>
      <div class="header-2">
        <nav class="navbar">
            <a href="AdmanHome.php"><img src="">Home</a>

                <a href="Dadmin.php">Donation Management</a>

               <a href="PPP.php">Product Management</a>
         
        </nav>
      </div> 
        
    </header>
        <div class="content">
            <div class="slider-wrapper">
                <button id="prev-slide" class="slid-btn material-symbols-rounded">chevron_left</button>
                <div class="img-bar">
                <a href="Diabetics & Pressure.php"><img src="im\Medical Supplies.png" class="img-item"></a>
                <a href="Diabetics & Pressure.php"><img src="im\Diabetics & Pressure.png" class="img-item"></a>
                <a href="Diapers and bedding.php"><img src="im\Diapers and bedding.png" class="img-item"></a>
                <a href="Medical Supplies.php"><img src="https://balsamok.com/image/cache/catalog/banners/cotton-EN-940x1140w.jpg" class="img-item"></a>
                <a href="Steam Appliances.php"><img src="im\جهاز بخار.png" class="img-item"></a>

              </div>
              <button id="next-slide" class="slid-btn material-symbols-rounded">chevron_right</button>
              
            </div>
        </div><br><br>
        
        <div class="Top-Categories">
            <h3>Top Categories</h3>
            <!--card 1-->
            <div class="cards">
                <div class="card">
                <div class="img-icon">
                    <a href=""><img src="C:\Users\mshab\Downloads\image-removebg-preview (2).png"></a>
                    <div class="info">Medical cotton and gauze</div>
                </div>
              </div>
<?php
// Database credentials
$host = 'localhost';
$dbname = 'postgres';
$user = 'postgres';
$password = '123456';

// Connect to the database
$dsn = "pgsql:host=$host;dbname=$dbname";
try {
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Function to convert image to base64
function image_to_base64($image_tmp_path) {
    $image_data = file_get_contents($image_tmp_path);
    $encoded_image = base64_encode($image_data);
    return $encoded_image;
}

// Check if form is submitted
if(isset($_POST['add_product'])) {
    // Get form data
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $name_id = $_POST['name_id'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    // File upload handling
    $image_tmp_path = $_FILES['product_image']['tmp_name'];
    $base64_string = image_to_base64($image_tmp_path);

    // Prepare SQL query
    $query = "INSERT INTO product (product_id, product_name, name_id, description, price, product_image) VALUES (?, ?, ?, ?, ?, ?)";

    // Execute query
    $stmt = $conn->prepare($query);
    $stmt->execute([$product_id, $product_name, $name_id, $description, $price, $base64_string]);

    // Close connection
    $conn = null;

    // Redirect or display success message
    // header('Location: success.php');
    // exit();
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
   <title>admin page</title>

</head>
<style>
   :root{

   --green:#27ae60;
   --black:#333;
   --white:#fff;
   --bg-color:#eee;
   --box-shadow:0 .5rem 1rem rgba(0,0,0,.1);
   --border:.1rem solid var(--black);
}

*{
   font-family: 'Poppins', sans-serif;
   margin:0; padding:0;
   box-sizing: border-box;
   outline: none; border:none;
   text-decoration: none;
   text-transform: capitalize;
}

html{
   font-size: 62.5%;
   overflow-x: hidden;
   padding-top: 40px;
}

.btn{
   display: block;
   width: 100%;
   cursor: pointer;
   border-radius: .5rem;
   margin-top: 1rem;
   font-size: 1.7rem;
   padding:1rem 3rem;
   background: var(--green);
   color:var(--white);
   text-align: center;
}

.btn:hover{
   background: var(--black);
}

.add-btn{
   display: block;
   width: 100%;
   cursor: pointer;
   border-radius: .5rem;
   margin-top: 1rem;
   font-size: 1.7rem;
   padding:1rem 3rem;
   background: #3a909f;
   color:var(--white);
   text-align: center;
}

.add-btn:hover{
   background: grey;
}


.message{
   display: block;
   background: var(--bg-color);
   padding:1.5rem 1rem;
   font-size: 2rem;
   color:var(--black);
   margin-bottom: 2rem;
   text-align: center;
}

.container{
   max-width: 1200px;
   padding:2rem;
   margin:0 auto;
}

.admin-product-form-container.centered{
    padding-top: 40px;
   display: flex;
   align-items: center;
   justify-content: center;
   min-height: 100vh;
   
}

.admin-product-form-container form{
    padding-top: 40px;
   max-width: 50rem;
   margin:0 auto;
   padding:2rem;
   border-radius: .5rem;
   background: var(--bg-color);
}

.admin-product-form-container form h3{
   text-transform: uppercase;
   color:var(--black);
   margin-bottom: 1rem;
   text-align: center;
   font-size: 2.5rem;
}

.admin-product-form-container form .box{
   width: 100%;
   border-radius: .5rem;
   padding:1.2rem 1.5rem;
   font-size: 1.7rem;
   margin:1rem 0;
   background: var(--white);
   text-transform: none;
}

.product-display{
   margin:2rem 0;
}

.product-display .product-display-table{
   width: 100%;
   text-align: center;
}

.product-display .product-display-table thead{
   background: var(--bg-color);
}

.product-display .product-display-table th{
   padding:1rem;
   font-size: 2rem;
}


.product-display .product-display-table td{
   padding:1rem;
   font-size: 2rem;
   border-bottom: var(--border);
}

.product-display .product-display-table .btn:first-child{
   margin-top: 0;
}

.product-display .product-display-table .btn:last-child{
   background: crimson;
}

.product-display .product-display-table .btn:last-child:hover{
   background: var(--black);
}









@media (max-width:991px){

   html{
      font-size: 55%;
   }

}

@media (max-width:768px){

   .product-display{
      overflow-y:scroll;
   }

   .product-display .product-display-table{
      width: 80rem;
   }

}

@media (max-width:450px){

   html{
      font-size: 50%;
   }

}
</style>
<body>

   
<div class="container">

   <div class="admin-product-form-container">
   <form action="Padmin.php" method="post" enctype="multipart/form-data">


         <h3>add a new product</h3>
         <input type="text" placeholder="Add Product id" name="product_id" class="box">
         <input type="text" placeholder="Add Product" name="product_name" class="box">
         <input type="text" placeholder="Add Category" name="name_id" class="box"> 
   <input type="text" placeholder=" Add Description" name="description" class="box">
         <input type="text" placeholder=" Add Price" name="price" class="box">
         <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box">
         <input onclick="addProduct()" type="submit" class="add-btn" name="add_product" value="add product">
      </form>

   </div>
</body>
</html>
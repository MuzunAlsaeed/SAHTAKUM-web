<?php
session_start(); // Initialize session handling at the top

// Ensure that the cart items are stored in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$host = 'localhost';
$dbname = 'postgres';
$user = 'postgres';
$password = '123456';
$dsn = "pgsql:host=$host;dbname=$dbname;";

// Connect to the database
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Check if the user is attempting to add or modify items in the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        if (isset($_POST['change_quantity'])) {
            // Handle quantity increase or decrease
            if ($_POST['change_quantity'] === 'increase') {
                $_SESSION['cart'][$product_id]++;
            } elseif ($_POST['change_quantity'] === 'decrease') {
                if ($_SESSION['cart'][$product_id] > 1) {
                    $_SESSION['cart'][$product_id]--;
                } else {
                    unset($_SESSION['cart'][$product_id]); // Remove the item if quantity becomes zero
                }
            }
        } elseif (isset($_POST['delete_product'])) {
            // Handle item deletion
            unset($_SESSION['cart'][$product_id]);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Function to display cart items
function displayCartItems($pdo) {
    $totalAmount = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $productDetailsSql = "SELECT product_image, price, product_name FROM product WHERE product_id = ?";
            $productDetailsStmt = $pdo->prepare($productDetailsSql);
            $productDetailsStmt->execute([$productId]);
            while ($productDetailsRow = $productDetailsStmt->fetch(PDO::FETCH_ASSOC)) {
                $base64Image = $productDetailsRow['product_image'];
                $productPrice = $productDetailsRow['price'];
                $productName = $productDetailsRow['product_name'];
                $totalAmount += $productPrice * $quantity;

                echo "<div class='cart-item'>";
                echo "<div class='item-image'><img src='data:image/jpeg;base64," . htmlspecialchars($base64Image) . "' alt='Product'></div>";
                echo "<div class='item-info'>";
                echo "<div class='item-name'>" . htmlspecialchars($productName) . "</div>";
                echo "<div class='item-price'>price: $" . htmlspecialchars(number_format($productPrice, 2)) . "</div>";
                echo "<div class='quantity-controls'>";
                echo "<form action='' method='POST'>";
                echo "<input type='hidden' name='product_id' value='" . $productId . "'>";
                echo "<button type='submit' name='change_quantity' value='decrease'>-</button>";
                echo "<span class='quantity-input'>" . $quantity . "</span>";
                echo "<button type='submit' name='change_quantity' value='increase'>+</button>";
                echo "<button type='submit' name='delete_product' style='background-color: white; color: white; border: none; padding: 5px 10px; margin-left: 10px;'>🗑️</button>";
                echo "</form>";
                echo "</div>";
                echo "</div></div>";
            }
        }
        echo "<div class='total-amount'>Total: $" . number_format($totalAmount, 2) . "</div>";
    } else {
        echo "<p>No items in the cart.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        .shopping-cart {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            background-color: #f9f9f9;
            border: 1px solid #e1e1e1;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #fff;
            border-bottom: 1px solid #e1e1e1;
        }
        .item-image img {
            width: 150px;
            margin-right: 20px;
            border-radius: 4px;
        }
        .item-info {
            flex-grow: 1;
        }
        .item-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .item-price {
            font-size: 16px;
            color: black;
            margin-bottom: 10px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-controls button {
            padding: 5px 10px;
            background-color: #3a909f;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            margin: 0 5px;
        }
        .quantity-controls .quantity-input {
            background-color: white;
            color: black;
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: inline-block;
            width: 30px;
            text-align: center;
        }
        .total-amount {
            text-align: center;
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }
        .cart-actions {
            display: flex;
            justify-content: space-around;
            width: 100%;
        }
        .cart-actions button {
            padding: 10px 20px;
            background-color: #3a909f;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class='shopping-cart'>
        <h2>Shopping cart</h2>
        <?php displayCartItems($pdo); ?>
        <div class='cart-actions'>
            <button onclick="completeOrder();">Complete the order</button>
            <button onclick="window.location.href='product.php';">Continue shopping</button>
        </div>
    </div>

    <script>
        function completeOrder() {
            // Redirect based on user login status
            <?php
            if (!isset($_SESSION['user_id'])) {
                echo "window.location.href = 'login.php';";
            } else {
                echo "window.location.href = 'checkout.php';";
            }
            ?>
        }
    </script>
</body>
</html>
<?php
session_start(); // Initialize session handling at the top

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$host = 'localhost';
$dbname = 'postgres';
$user = 'postgres';
$password = '123456';
$dsn = "pgsql:host=$host;dbname=$dbname;";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        if (isset($_POST['change_quantity'])) {
            if ($_POST['change_quantity'] === 'increase') {
                $_SESSION['cart'][$product_id]++;
            } elseif ($_POST['change_quantity'] === 'decrease') {
                if ($_SESSION['cart'][$product_id] > 1) {
                    $_SESSION['cart'][$product_id]--;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
        } elseif (isset($_POST['delete_product'])) {
            unset($_SESSION['cart'][$product_id]);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

function getProductPrice($product_id) {
    return 10.00;
}

function saveCartToDatabase($pdo) {
    if (!isset($_SESSION['customerid'])) {
        exit("User is not logged in.");
    }
    $pdo->beginTransaction();
    try {
        if (!empty($_SESSION['cart'])) {
            $customerid = $_SESSION['customerid'];
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $sql = "INSERT INTO orders (customerid, product_id, quantity) VALUES (?, ?, ?) ON CONFLICT (product_id, customerid) DO UPDATE SET quantity = orders.quantity + EXCLUDED.quantity";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$customerid, $product_id, $quantity]);
            }
            $_SESSION['cart'] = array();
            $pdo->commit();
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        exit("Failed to save cart: " . $e->getMessage());
    }
}

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
                echo "<div class='item-price'>Price: $" . htmlspecialchars(number_format($productPrice, 2)) . "</div>";
                echo "<div class='item-quantity'>Quantity: " . htmlspecialchars($quantity) . "</div>";
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
    <title>Checkout Page</title>
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
            max-width: 1200px; /* Adjusted width */
            display: flex; /* New style to arrange child divs side by side */
            justify-content: space-between;
            margin: 20px auto;
            background-color: #f9f9f9;
            border: 1px solid #e1e1e1;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .cart-summary, .payment-info {
            flex: 1; /* Each section takes up half of the container */
            padding: 20px;
            min-height: 300px; /* Ensure both sections are the same height */

           
    border-right: none; /* Remove any existing right border */
    border-left: 1px solid #ccc; /* Add a left border instead, after swapping */

        }
        .cart-summary {
            border-right: 1px solid #ccc; /* Vertical line between sections */
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
            width: 100px;
            margin-right: 20px;
            border-radius: 4px;
        }
        .item-info {
            flex-grow: 1;
        }
        .item-name, .item-price, .item-quantity {
            font-size: 16px;
            color: black;
            margin-bottom: 5px;
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
    justify-content: center; /* Align buttons center horizontally */
    align-items: center; /* Align buttons center vertically */
    padding: 20px; /* Add some padding around the buttons */
   margin-right: 10px;
        }
        .cart-actions button {
            padding: 10px 20px;
            background-color: #3a909f;
            color: white;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        cart-actions button:last-child {
    margin-right: 0; /* Ensures that the last button does not have a margin to the right */
        }
        #try {
            background-color: #fff;
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2, .total-amount {
            color: #333;
            margin-top: 10px;
            margin-bottom: 30px;
        }
        .instructions, p {
            color: #333;
        }
        #buttonContainer {
            justify-content: center;
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #3a909f;
            color: white;
            border: 1px solid #7da2a9;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background-color: #3a909f;
            border-color: #6897a7;
        }
    </style>
</head>
<body>
    <div class='shopping-cart'>
      
        <div class='payment-info'>
            <div id="try">
                <h2>Complete the Order</h2>
                <div id="buttonContainer">
                    <button onclick="showTransferInstructions()">Transfer</button>
                    <button onclick="showCashOption()">Cash</button>
                </div>
                <div id="transferInstructions" style="display:none;" class="instructions">
                    <h3>Payment Instructions</h3>
                    <p>Please send a copy of your transaction to WhatsApp: <strong>0563388771</strong></p>
                    <p>Bank details: <strong>Alrajhi 00222277655511865438</strong></p>
                    <p>Your order will not ship until we receive payment.</p>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" name="productimage" class="box">
                    <br>
                    <button onclick="paymentMade()">Pay</button> <!-- New Pay button for Transfer -->
                </div>
                <div id="cashOption" style="display:none;" class="instructions">
                    <button onclick="paymentMadeForCash()">Pay</button>
                </div>
                <br>
            </div>
        </div>
        <div class='cart-summary'>
            <h2>Cart Summary</h2>
            <?php displayCartItems($pdo); ?>
        </div>
    </div>
    <div class='cart-actions'>
    
        <button onclick="window.location.href='home.php';">Continue Shopping</button>
    </div>
    <script>
       
        function showTransferInstructions() {
            document.getElementById('transferInstructions').style.display = 'block';
            document.getElementById('cashOption').style.display = 'none';
        }
        function showCashOption() {
            document.getElementById('cashOption').style.display = 'block';
            document.getElementById('transferInstructions').style.display = 'none';
        }
        function paymentMade() {
            alert('Thank you for your payment! We will process your order shortly.');
        }
        function paymentMadeForCash() {
            alert('Thank you for ordering from our store! We will process your order shortly.');
        }
    </script>
</body>
</html>
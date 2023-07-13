<?php
require_once 'config.php';
require_once 'Database.php';
require_once 'User.php';
require_once 'Inventory.php';

session_start();
if (isset($_SESSION['output'])) {
    $output = $_SESSION['output'];
    unset($_SESSION['output']);
    echo $output;
}

// Create a database connection
$database = new Database($servername, $username, $password, $dbname);
$conn = $database->getConnection();

// Variable for the specific user ID
// Enter user id in the database to log in
$id = 3; // Replace with the desired user ID

// Create User and Inventory instances
$user = new User($conn, $id);
$inventory = new Inventory($conn);

// Retrieve account information for the specified user
$name = $user->getName();
$wallet = $user->getWallet();

// Initialize the output variable
$output = '';

// Check if the buy form is submitted and redirect
if (isset($_POST['buy']) && empty($output)) {
    // Retrieve form data
    $cardStatus = $_POST['card_status'];
    $cardAmount = $_POST['card_amount'];
    $quantity = $_POST['quantity'];
    $paymentMethod = $_POST['payment'];

    // Check if the card amount exists in the inventory and is available
    if ($inventory->isCardGeneratedByAdminOrHasAvailableStatus($cardAmount)) {
        // Get available cards with the specified amount and status
        $availableCards = $inventory->getAvailableCards($cardAmount);

        // Check if there are available cards
        if (count($availableCards) >= $quantity) {
            // Calculate the total price
            $totalPrice = $cardAmount * $quantity;

            // Check if the user has sufficient balance
            if ($wallet >= $totalPrice) {
                // Deduct the price from the user's wallet balance
                $newBalance = $wallet - $totalPrice;

                // Update the user's balance in the database
                $user->updateBalance($newBalance);

                $output = "Stable cards purchased successfully:<br>";

                for ($i = 0; $i < $quantity; $i++) {
                    // Get the first available card
                    $card = array_shift($availableCards);
                    $cardCode = $card['card_code'];

                    // Update the card status based on user choice (subscribe or purchase)
                    $cardStatus = $_POST['card_status'];
                    $inventory->updateCardStatus($cardCode, $cardStatus, $id);

                    $output .= "Card Code: " . $cardCode . "<br>";
                    $output .= "Card Amount: " . $cardAmount . " <br>";
                    $output .= "Card Status: " . $cardStatus . "<br><br>";
                }
            } else {
                $output = "Insufficient balance. Please recharge your account.";
            }
        } else {
            $output = "No available cards. Please try again later.";
        }
    } else {
        $output = "Card Amount is currently unavailable. Please try again later.";
    }

    // Store the output message in the session variable
    $_SESSION['output'] = $output;

    // Redirect to the same page to display the transaction result
    header("Location: dashboard.php");
    exit();
}

// Get inventory data for the user
$inventoryData = $inventory->getInventoryData($id);

// Close the database connection
$database->closeConnection();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buy Stable Cards</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function calculatePrice() {
            var cardAmount = parseInt(document.getElementById("card_amount").value);
            var quantity = parseInt(document.getElementById("quantity").value);
            var overallPrice = cardAmount * quantity;
            document.getElementById("overall_price").innerHTML = overallPrice.toFixed(2);
        }
    </script>
</head>
<body>
    <h2>User Information</h2>
    <p>Name: <?php echo $name; ?></p>
    <p>Wallet Balance: <?php echo $wallet; ?> </p>

    <?php if (!isset($_POST['buy']) || !empty($output)) : ?>
        <h2>Buy Stable Cards</h2>
        <?php if (!empty($output)) : ?>
            <h3>Transaction Result:</h3>
            <p><?php echo $output; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="card_status">Card Status:</label>
            <select name="card_status" id="card_status">
                <option value="subscribed">Subscribe</option>
                <option value="purchased">Purchase</option>
            </select>
            <br>
            <label for="card_amount">Card Amount:</label>
            <select name="card_amount" id="card_amount" onchange="calculatePrice()">
                <option value="10">10</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="500">500</option>
                <option value="1000">1000</option>
            </select>
            <br>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" min="1" max="10" value="1" onchange="calculatePrice()">
            <br>
            <label for="overall_price">Overall Price:</label>
            <span id="overall_price">0.00</span>
            <br>
            <label for="payment">Payment Method:</label>
            <select name="payment" id="payment">
                <option value="wallet">Wallet</option>
            </select>
            <br>
            <input type="submit" name="buy" value="Buy">
        </form>
    <?php endif; ?>

    <h2>Inventory Data</h2>
    <?php echo $inventoryData; ?>
</body>
</html>

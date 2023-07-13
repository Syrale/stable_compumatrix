<?php
session_start();

require_once 'config.php';
require_once 'Database.php';
require_once 'Inventory.php';

// Create a database connection
$database = new Database($servername, $username, $password, $dbname);
$conn = $database->getConnection();

// Create an instance of the Inventory class
$inventory = new Inventory($conn);

// Retrieve the previously generated cards from the session
$generatedCards = $_SESSION['generated_cards'] ?? array();

// Process the form submission
if (isset($_POST['generate'])) {
    $cardAmount = $_POST['card_amount'];
    $quantity = $_POST['quantity'];

    // Generate stable cards with the status of "available"
    for ($i = 0; $i < $quantity; $i++) {
        $cardCode = $inventory->generateCardCode();
        $cardStatus = 'available';
        $cardPoint = $cardAmount;

        $inventory->insertCard(null, $cardCode, $cardAmount, $cardStatus, $cardPoint);

        // Store the generated card details in the array
        $generatedCards[] = array(
            'cardCode' => $cardCode,
            'cardAmount' => $cardAmount,
            'cardStatus' => $cardStatus,
            'cardPoint' => $cardPoint
        );
    }

    $message = "Stable cards generated successfully!";

    // Store the generated cards in the session for future access
    $_SESSION['generated_cards'] = $generatedCards;

    // Redirect to a different page to avoid form resubmission
    header("Location: admin.php");
    exit();
}

// Clear the generated cards
if (isset($_POST['clear'])) {
    $generatedCards = array();
    $_SESSION['generated_cards'] = $generatedCards;
}

// Close the database connection
$database->closeConnection();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Generate Stable Cards</title>
</head>
<body>
    <h2>Generate Stable Cards</h2>
    <?php if (isset($message)) : ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="card_amount">Card Amount:</label>
        <select name="card_amount" id="card_amount">
            <option value="10">10</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="500">500</option>
            <option value="1000">1000</option>
        </select>
        <br>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" min="1" max="100" value="1">
        <br>
        <input type="submit" name="generate" value="Generate">
        <input type="submit" name="clear" value="Clear">
    </form>

    <?php if (!empty($generatedCards)) : ?>
        <h3>Generated Cards:</h3>
        <table>
            <tr>
                <th>Card Code</th>
                <th>Card Amount</th>
                <th>Card Status</th>
                <th>Card Point</th>
            </tr>
            <?php foreach ($generatedCards as $card) : ?>
                <tr>
                    <td><?php echo $card['cardCode']; ?></td>
                    <td><?php echo $card['cardAmount']; ?></td>
                    <td><?php echo $card['cardStatus']; ?></td>
                    <td><?php echo $card['cardPoint']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>

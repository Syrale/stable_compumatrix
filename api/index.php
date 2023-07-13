<?php
require_once '../config.php';
require_once '../Database.php';
require_once '../User.php';
require_once '../Inventory.php';

// Create a database connection
$database = new Database($servername, $username, $password, $dbname);
$conn = $database->getConnection();

// Check if the API request is made
if (isset($_GET['id'])) {
    $apiUserId = $_GET['id'];

    // Create User and Inventory instances
    $user = new User($conn, $apiUserId);
    $inventory = new Inventory($conn);

    // Retrieve user information based on the provided user ID
    $name = $user->getName();
    $wallet = $user->getWallet();
    $inventoryData = $inventory->getInventoryDataFromDatabase($apiUserId);

    // Return the user information and inventory data as JSON response
    echo json_encode([
        'id' => $apiUserId,
        'name' => $name,
        'wallet' => $wallet,
        'inventory' => $inventoryData
    ]);

    exit();
}

// Close the database connection
$database->closeConnection();
?>

<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "compumatrix";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve all cards from the inventory table
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);

// Display the cards in a table
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Card Code</th><th>Card Amount</th><th>Card Point</th><th>Card Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["card_code"] . "</td>";
        echo "<td>" . $row["card_amount"] . "</td>";
        echo "<td>" . $row["card_point"] . "</td>";
        echo "<td>" . $row["card_status"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No cards found in the inventory.";
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
</head>
<body>
</body>
</html>

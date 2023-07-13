<?php
class User {
    private $conn;
    private $id;
    private $name;
    private $wallet;

    public function __construct($conn, $id) {
        $this->conn = $conn;
        $this->id = $id;
        $this->loadUserData();
    }

    private function loadUserData() {
        $sql = "SELECT id, name, balance FROM user WHERE id = '$this->id'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->name = $row["name"];
            $this->wallet = $row["balance"];
        } else {
            die("No account information found for the specified user.");
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getWallet() {
        return $this->wallet;
    }

    public function updateBalance($newBalance) {
        $sql = "UPDATE user SET balance = '$newBalance' WHERE id = '$this->id'";
        if ($this->conn->query($sql) === TRUE) {
            $this->wallet = $newBalance;
            return true;
        } else {
            return false;
        }
    }
}
?>

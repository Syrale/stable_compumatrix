<?php
class Inventory {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getInventoryData($userId) {
        $sql = "SELECT card_amount, card_status, COUNT(*) AS count FROM inventory WHERE aquired_by = '$userId' GROUP BY card_amount, card_status";
        $result = $this->conn->query($sql);

        $tableHTML = "<table>";
        $tableHTML .= "<tr><th>Stable Card</th><th>Subscribed</th><th>Purchased</th><th>Redeemed</th></tr>";

        $cardAmounts = array(10, 50, 100, 500, 1000);
        $cardStatuses = array("subscribed", "purchased", "redeemed");

        foreach ($cardAmounts as $cardAmount) {
            $tableHTML .= "<tr>";
            $tableHTML .= "<td>$cardAmount</td>";

            foreach ($cardStatuses as $cardStatus) {
                $count = $this->getCardCount($userId, $cardAmount, $cardStatus);
                $tableHTML .= "<td>$count</td>";
            }

            $tableHTML .= "</tr>";
        }

        $tableHTML .= "</table>";

        return $tableHTML;
    }

    public function isCardGeneratedByAdminOrHasAvailableStatus($cardAmount) {
        return $this->isCardGeneratedByAdmin($cardAmount) || $this->hasAvailableStatus($cardAmount);
    }

    private function hasAvailableStatus($cardAmount) {
        $sql = "SELECT COUNT(*) FROM inventory WHERE card_amount = '$cardAmount' AND card_status = 'available'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_row();
        $count = $row[0];
        return $count > 0;
    }
    

    public function getAvailableCards($cardAmount) {
        $sql = "SELECT * FROM inventory WHERE card_amount = '$cardAmount' AND card_status = 'available'";
        $result = $this->conn->query($sql);

        $cards = [];
        while ($row = $result->fetch_assoc()) {
            $cards[] = $row;
        }

        return $cards;
    }

    private function getCardCount($userId, $cardAmount, $cardStatus) {
        $sql = "SELECT COUNT(*) FROM inventory WHERE aquired_by = '$userId' AND card_amount = '$cardAmount' AND (card_status = '$cardStatus' OR card_status IS NULL)";
        $result = $this->conn->query($sql);
        $row = $result->fetch_row();
        $count = $row[0];
        return $count;
    }

    public function updateCardStatus($cardCode, $cardStatus, $acquiredBy) {
        $sql = "UPDATE inventory SET card_status = '$cardStatus', aquired_by = '$acquiredBy' WHERE card_code = '$cardCode'";
        if ($this->conn->query($sql) === TRUE) {
            return true;
        } else {
            echo "Error updating card status: " . $this->conn->error;
            return false;
        }
    }

    public function updateAcquiredBy($cardCode, $acquiredBy) {
        $sql = "UPDATE inventory SET aquired_by = '$acquiredBy' WHERE card_code = '$cardCode'";
        if ($this->conn->query($sql) === TRUE) {
            return true;
        } else {
            echo "Error updating acquired_by: " . $this->conn->error;
            return false;
        }
    }

    public function insertCard($acquiredBy, $cardCode, $cardAmount, $cardStatus, $cardPoint) {
        $sql = "INSERT INTO inventory (aquired_by, card_code, card_amount, card_status, card_point) 
                VALUES ('$acquiredBy', '$cardCode', '$cardAmount', '$cardStatus', '$cardPoint')";

        if ($this->conn->query($sql) === TRUE) {
            return true;
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
            return false;
        }
    }

    public function generateCardCode() {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeLength = 10;

        do {
            $cardCode = '';
            for ($i = 0; $i < $codeLength; $i++) {
                $randomIndex = rand(0, strlen($characters) - 1);
                $cardCode .= $characters[$randomIndex];
            }
        } while ($this->isCardCodeExists($cardCode)); // Check if the card code already exists in the database

        return $cardCode;
    }

    public function isCardCodeExists($cardCode) {
        $sql = "SELECT COUNT(*) FROM inventory WHERE card_code = '$cardCode'";
        $result = $this->conn->query($sql);
        $row = $result->fetch_row();
        $count = $row[0];
        return $count > 0;
    }

    public function isCardGeneratedByAdmin($cardAmount) {
        $sql = "SELECT COUNT(*) FROM inventory WHERE card_amount = '$cardAmount' AND card_status IS NULL";
        $result = $this->conn->query($sql);
        $row = $result->fetch_row();
        $count = $row[0];
        return $count > 0;
    }

    public function getInventoryDataFromDatabase($userId) {
        $sql = "SELECT card_amount, card_status, COUNT(*) AS count FROM inventory WHERE aquired_by = '$userId' GROUP BY card_amount, card_status";
        $result = $this->conn->query($sql);
    
        $inventoryData = [];
    
        while ($row = $result->fetch_assoc()) {
            $inventoryData[] = [
                'card_amount' => $row['card_amount'],
                'card_status' => $row['card_status'],
                'count' => $row['count']
            ];
        }
    
        return $inventoryData;
    }
    
    
}

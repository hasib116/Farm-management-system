<?php
require_once __DIR__ . '/../config/Database.php';

class Inventory
{
    private $conn;
    private $table = "inventory";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function add($item_name, $category, $quantity, $unit, $threshold, $added_by)
    {
        $sql = "INSERT INTO {$this->table} (item_name, category, quantity, unit, low_stock_threshold, added_by) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssdsdi", $item_name, $category, $quantity, $unit, $threshold, $added_by);
        return $stmt->execute();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY item_name ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLowStock()
    {
        $sql = "SELECT * FROM {$this->table} WHERE quantity <= low_stock_threshold";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateQuantity($id, $quantity)
    {
        $sql = "UPDATE {$this->table} SET quantity = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("di", $quantity, $id);
        return $stmt->execute();
    }

    public function consume($id, $amount)
    {
        $sql = "UPDATE {$this->table} SET quantity = quantity - ? WHERE id = ? AND quantity >= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("did", $amount, $id, $amount);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

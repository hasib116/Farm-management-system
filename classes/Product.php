<?php
require_once __DIR__ . '/../config/Database.php';

class Product
{
    private $conn;
    private $table = "products";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($owner_id, $name, $description, $price, $stock_quantity, $unit, $image)
    {
        $sql = "INSERT INTO {$this->table} (owner_id, name, description, price, stock_quantity, unit, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issdiss", $owner_id, $name, $description, $price, $stock_quantity, $unit, $image);
        return $stmt->execute();
    }

    public function getAvailable()
    {
        $sql = "SELECT * FROM {$this->table} WHERE stock_quantity > 0 ORDER BY created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByOwner($owner_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE owner_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updatePriceStock($id, $price, $stock_quantity)
    {
        $sql = "UPDATE {$this->table} SET price = ?, stock_quantity = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("dii", $price, $stock_quantity, $id);
        return $stmt->execute();
    }

    // Reduce stock when an order is placed
    public function reduceStock($id, $qty)
    {
        $sql = "UPDATE {$this->table} SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("dii", $qty, $id, $qty);
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

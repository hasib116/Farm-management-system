<?php
require_once __DIR__ . '/../config/Database.php';

class Delivery
{
    private $conn;
    private $table = "deliveries";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getByOrder($order_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateStatus($order_id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = ? WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }
}

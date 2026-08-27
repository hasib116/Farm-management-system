<?php
require_once __DIR__ . '/../config/Database.php';

class Crop
{
    private $conn;
    private $table = "crops";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($name, $variety, $planted_date, $expected_harvest_date, $created_by)
    {
        $sql = "INSERT INTO {$this->table} (name, variety, planted_date, expected_harvest_date, created_by) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $variety, $planted_date, $expected_harvest_date, $created_by);
        return $stmt->execute();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY planted_date DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}

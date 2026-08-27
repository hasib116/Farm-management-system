<?php
require_once __DIR__ . '/../config/Database.php';

class FieldLog
{
    private $conn;
    private $table = "field_logs";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($worker_id, $crop_id, $entry_date, $notes, $photo)
    {
        $sql = "INSERT INTO {$this->table} (worker_id, crop_id, entry_date, notes, photo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisss", $worker_id, $crop_id, $entry_date, $notes, $photo);
        return $stmt->execute();
    }

    public function getByWorker($worker_id)
    {
        $sql = "SELECT f.*, c.name AS crop_name
                FROM {$this->table} f
                JOIN crops c ON f.crop_id = c.id
                WHERE f.worker_id = ?
                ORDER BY f.entry_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $worker_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll()
    {
        $sql = "SELECT f.*, c.name AS crop_name, u.name AS worker_name
                FROM {$this->table} f
                JOIN crops c ON f.crop_id = c.id
                JOIN users u ON f.worker_id = u.id
                ORDER BY f.entry_date DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

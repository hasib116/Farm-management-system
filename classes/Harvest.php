<?php
require_once __DIR__ . '/../config/Database.php';

class Harvest
{
    private $conn;
    private $table = "harvests";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($crop_id, $worker_id, $quantity, $unit, $harvest_date, $quality_grade)
    {
        $sql = "INSERT INTO {$this->table} (crop_id, worker_id, quantity, unit, harvest_date, quality_grade) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iidsss", $crop_id, $worker_id, $quantity, $unit, $harvest_date, $quality_grade);
        $ok = $stmt->execute();

        if ($ok) {
            // mark the crop as harvested
            $cropStmt = $this->conn->prepare("UPDATE crops SET status = 'harvested' WHERE id = ?");
            $cropStmt->bind_param("i", $crop_id);
            $cropStmt->execute();
        }
        return $ok;
    }

    public function getByWorker($worker_id)
    {
        $sql = "SELECT h.*, c.name AS crop_name
                FROM {$this->table} h
                JOIN crops c ON h.crop_id = c.id
                WHERE h.worker_id = ?
                ORDER BY h.harvest_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $worker_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll()
    {
        $sql = "SELECT h.*, c.name AS crop_name, u.name AS worker_name
                FROM {$this->table} h
                JOIN crops c ON h.crop_id = c.id
                JOIN users u ON h.worker_id = u.id
                ORDER BY h.harvest_date DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // used in Owner reports/analytics
    public function totalYield()
    {
        $sql = "SELECT SUM(quantity) AS total FROM {$this->table}";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total'] ?? 0;
    }
}

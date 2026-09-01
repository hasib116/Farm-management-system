<?php
require_once __DIR__ . '/../config/Database.php';

class Task
{
    private $conn;
    private $table = "tasks";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create($title, $description, $assigned_to, $assigned_by, $crop_id, $due_date)
    {
        $sql = "INSERT INTO {$this->table} (title, description, assigned_to, assigned_by, crop_id, due_date) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssiiis", $title, $description, $assigned_to, $assigned_by, $crop_id, $due_date);
        return $stmt->execute();
    }

    public function getAll()
    {
        $sql = "SELECT t.*, u.name AS worker_name, c.name AS crop_name
                FROM {$this->table} t
                JOIN users u ON t.assigned_to = u.id
                LEFT JOIN crops c ON t.crop_id = c.id
                ORDER BY t.created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByWorker($worker_id)
    {
        $sql = "SELECT t.*, c.name AS crop_name
                FROM {$this->table} t
                LEFT JOIN crops c ON t.crop_id = c.id
                WHERE t.assigned_to = ?
                ORDER BY t.due_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $worker_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Worker updates the status of their own task
    public function updateStatus($task_id, $status, $worker_id)
    {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ? AND assigned_to = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $status, $task_id, $worker_id);
        return $stmt->execute();
    }

    public function delete($task_id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $task_id);
        return $stmt->execute();
    }

    // Simple counts for dashboard widgets
    public function countByStatus($status)
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE status = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }
}

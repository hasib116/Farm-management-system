<?php
require_once __DIR__ . '/../config/Database.php';

class User
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Register a new user
    public function register($name, $email, $password, $role, $phone, $address)
    {
        // check duplicate email
        if ($this->findByEmail($email)) {
            return ["success" => false, "message" => "Email already registered."];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO {$this->table} (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email, $hashed, $role, $phone, $address);

        if ($stmt->execute()) {
            return ["success" => true, "message" => "Registration successful.", "id" => $stmt->insert_id];
        }
        return ["success" => false, "message" => "Registration failed: " . $stmt->error];
    }

    // Login - verifies email/password and returns user row on success
    public function login($email, $password)
    {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return ["success" => true, "user" => $user];
        }
        return ["success" => false, "message" => "Invalid email or password."];
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update profile info (view/edit)
    public function updateProfile($id, $name, $phone, $address)
    {
        $sql = "UPDATE {$this->table} SET name = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $phone, $address, $id);
        return $stmt->execute();
    }

    // Change password
    public function changePassword($id, $newPassword)
    {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $hashed, $id);
        return $stmt->execute();
    }

    // Delete account
    public function deleteAccount($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Get all workers (used by Owner when assigning tasks)
    public function getAllWorkers()
    {
        $sql = "SELECT id, name, email FROM {$this->table} WHERE role = 'worker'";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

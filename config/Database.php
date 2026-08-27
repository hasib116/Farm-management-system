<?php
/**
 * Database.php
 * Handles the MySQLi (Object-Oriented) connection for the whole app.
 * Every class in /classes extends this to get $this->conn (a mysqli object).
 */

class Database
{
    // ---- EDIT THESE TO MATCH YOUR LOCAL XAMPP/WAMP/MYSQL SETTINGS ----
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "smartfarm_db";

    public $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            // mysqli - Object Oriented style
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database Connection Error: " . $e->getMessage());
        }

        return $this->conn;
    }
}

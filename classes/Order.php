<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Product.php';

class Order
{
    private $conn;
    private $table = "orders";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function placeOrder($buyer_id, $items, $payment_method)
    {
        $this->conn->begin_transaction();

        try {
            $total = 0;
            foreach ($items as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            $sql = "INSERT INTO {$this->table} (buyer_id, total_amount, payment_method) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ids", $buyer_id, $total, $payment_method);
            $stmt->execute();
            $order_id = $stmt->insert_id;

            $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $itemStmt = $this->conn->prepare($itemSql);

            $productModel = new Product();

            foreach ($items as $item) {
                $itemStmt->bind_param("iidd", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $itemStmt->execute();

                // reduce stock for the purchased product
                $productModel->reduceStock($item['product_id'], $item['quantity']);
            }

            // create a delivery tracking record automatically
            $deliverySql = "INSERT INTO deliveries (order_id, status, tracking_info) VALUES (?, 'processing', ?)";
            $deliveryStmt = $this->conn->prepare($deliverySql);
            $trackingInfo = "TRK-" . strtoupper(uniqid());
            $deliveryStmt->bind_param("is", $order_id, $trackingInfo);
            $deliveryStmt->execute();

            $this->conn->commit();
            return ["success" => true, "order_id" => $order_id];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getByBuyer($buyer_id)
    {
        $sql = "SELECT o.*, d.status AS delivery_status, d.tracking_info
                FROM {$this->table} o
                LEFT JOIN deliveries d ON o.id = d.order_id
                WHERE o.buyer_id = ?
                ORDER BY o.order_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $buyer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getOrderItems($order_id)
    {
        $sql = "SELECT oi.*, p.name AS product_name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll()
    {
        $sql = "SELECT o.*, u.name AS buyer_name, d.status AS delivery_status
                FROM {$this->table} o
                JOIN users u ON o.buyer_id = u.id
                LEFT JOIN deliveries d ON o.id = d.order_id
                ORDER BY o.order_date DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStatus($order_id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }

    public function totalRevenue()
    {
        $sql = "SELECT SUM(total_amount) AS revenue FROM {$this->table} WHERE status != 'cancelled'";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['revenue'] ?? 0;
    }
}

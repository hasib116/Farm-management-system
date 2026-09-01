<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "smartfarm_db";
// -----------------------------------------------------------

$log = [];
$hasError = false;

try {
    $mysqli = new mysqli($host, $username, $password);
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    $log[] = "Connected to MySQL server successfully.";

    // Step 2: create the database if it doesn't exist
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $log[] = "Database `$database` ready.";

    // Step 3: select the database
    $mysqli->select_db($database);

    // Step 4: create tables (MySQLi OOP query() calls)
    $tables = [];

    $tables['users'] = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('owner','worker','buyer') NOT NULL,
        phone VARCHAR(20) DEFAULT NULL,
        address VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB";

    $tables['crops'] = "CREATE TABLE IF NOT EXISTS crops (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        variety VARCHAR(100) DEFAULT NULL,
        planted_date DATE DEFAULT NULL,
        expected_harvest_date DATE DEFAULT NULL,
        status ENUM('growing','ready','harvested') DEFAULT 'growing',
        created_by INT NOT NULL,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $tables['tasks'] = "CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT,
        assigned_to INT NOT NULL,
        assigned_by INT NOT NULL,
        crop_id INT DEFAULT NULL,
        status ENUM('pending','in_progress','completed') DEFAULT 'pending',
        due_date DATE DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE SET NULL
    ) ENGINE=InnoDB";

    $tables['inventory'] = "CREATE TABLE IF NOT EXISTS inventory (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_name VARCHAR(120) NOT NULL,
        category VARCHAR(80) DEFAULT NULL,
        quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
        unit VARCHAR(30) DEFAULT 'unit',
        low_stock_threshold DECIMAL(10,2) DEFAULT 10,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        added_by INT NOT NULL,
        FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $tables['field_logs'] = "CREATE TABLE IF NOT EXISTS field_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        worker_id INT NOT NULL,
        crop_id INT NOT NULL,
        entry_date DATE NOT NULL,
        notes TEXT,
        photo VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (worker_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $tables['harvests'] = "CREATE TABLE IF NOT EXISTS harvests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crop_id INT NOT NULL,
        worker_id INT NOT NULL,
        quantity DECIMAL(10,2) NOT NULL,
        unit VARCHAR(30) DEFAULT 'kg',
        harvest_date DATE NOT NULL,
        quality_grade ENUM('A','B','C') DEFAULT 'A',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE,
        FOREIGN KEY (worker_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $tables['products'] = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_id INT NOT NULL,
        name VARCHAR(120) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock_quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
        unit VARCHAR(30) DEFAULT 'kg',
        image VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $tables['orders'] = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        buyer_id INT NOT NULL,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
        payment_method ENUM('cash_on_delivery','mobile_banking','card') DEFAULT 'cash_on_delivery',
        status ENUM('pending','confirmed','delivered','cancelled') DEFAULT 'pending',
        FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $tables['order_items'] = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity DECIMAL(10,2) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";

    $tables['deliveries'] = "CREATE TABLE IF NOT EXISTS deliveries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL UNIQUE,
        status ENUM('processing','shipped','out_for_delivery','delivered') DEFAULT 'processing',
        tracking_info VARCHAR(255) DEFAULT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";

    // Create tables in dependency order
    $order = ['users', 'crops', 'tasks', 'inventory', 'field_logs', 'harvests', 'products', 'orders', 'order_items', 'deliveries'];
    foreach ($order as $name) {
        if ($mysqli->query($tables[$name]) === true) {
            $log[] = "Table `$name` created (or already existed).";
        } else {
            throw new Exception("Error creating table `$name`: " . $mysqli->error);
        }
    }

    
    $result = $mysqli->query("SELECT COUNT(*) AS total FROM users");
    $count = $result->fetch_assoc()['total'];

    if ($count == 0) {
        $demoPassword = password_hash("password123", PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");

        $demoUsers = [
            ["Hasibul Haque", "owner@smartfarm.com", $demoPassword, "owner", "01700000001"],
            ["Shiam Shajib", "worker@smartfarm.com", $demoPassword, "worker", "01700000002"],
            ["Nooruzzaman Chowdhury", "buyer@smartfarm.com", $demoPassword, "buyer", "01700000003"],
        ];

        foreach ($demoUsers as $u) {
            $stmt->bind_param("sssss", $u[0], $u[1], $u[2], $u[3], $u[4]);
            $stmt->execute();
        }
        $log[] = "Demo accounts created (owner@smartfarm.com, worker@smartfarm.com, buyer@smartfarm.com — password: password123).";
    } else {
        $log[] = "Users table already has data — demo accounts skipped.";
    }

    $log[] = "Installation complete!";
} catch (Exception $e) {
    $hasError = true;
    $log[] = "ERROR: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SmartFarm Installer</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <main class="container">
        <div class="auth-wrapper">
            <div class="auth-box" style="max-width:600px;">
                <h1> SmartFarm Installation</h1>

                <div class="card" style="border:none;padding:0;">
                    <ul style="line-height:1.9;">
                        <?php foreach ($log as $line): ?>
                            <li style="<?php echo (strpos($line, 'ERROR') === 0) ? 'color:#c62828;font-weight:bold;' : ''; ?>">
                                <?php echo htmlspecialchars($line); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if (!$hasError): ?>
                    <div class="alert">Setup finished successfully. You can now log in.</div>
                    <a href="auth/login.php" class="btn btn-block">Go to Login</a>
                <?php else: ?>
                    <div class="alert alert-error">
                        Installation failed. Check your MySQL username/password/host at the top of <code>install.php</code>, then refresh this page.
                    </div>
                <?php endif; ?>

                <p style="margin-top:16px;font-size:0.85rem;color:#616161;">
                    For security, delete or rename <code>install.php</code> after installation is complete.
                </p>
            </div>
        </div>
    </main>
</body>

</html>
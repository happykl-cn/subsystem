<?php
require_once 'config.php';

class Database {
    private $connection;

    public function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("数据库连接失败: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
public function getRecentAttempts($ip, $minutes) {
    $stmt = $this->connection->prepare(
        "SELECT COUNT(*) FROM login_attempts 
        WHERE ip_address = :ip 
        AND attempt_time > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)"
    );
    $stmt->bindParam(':ip', $ip);
    $stmt->bindParam(':minutes', $minutes, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}
    // 检查邮箱是否已存在
    public function emailExists($email) {
        $stmt = $this->connection->prepare("SELECT id FROM subscribers WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // 添加订阅者
    public function addSubscriber($email) {
        $stmt = $this->connection->prepare("INSERT INTO subscribers (email, verified, created_at) VALUES (:email, 1, NOW())");
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    // 获取所有订阅者
    public function getAllSubscribers() {
        $stmt = $this->connection->query("SELECT id, email, created_at FROM subscribers WHERE verified = 1 ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 创建表（安装时使用）
    public function createTables() {
    // 订阅者表
    $sql = "CREATE TABLE IF NOT EXISTS subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        verification_code VARCHAR(32),
        verified TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $this->connection->exec($sql);
    
    // 登录尝试日志表
    $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        username VARCHAR(255) NOT NULL,
        attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_success TINYINT(1) DEFAULT 0
    )";
    $this->connection->exec($sql);
}
// 记录登录尝试
public function logLoginAttempt($ip, $username, $isSuccess, $reason = '') {
    $stmt = $this->connection->prepare(
        "INSERT INTO login_attempts (ip_address, username, is_success, reason) 
        VALUES (:ip, :username, :is_success, :reason)"
    );
    $stmt->bindParam(':ip', $ip);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':is_success', $isSuccess, PDO::PARAM_BOOL);
    $stmt->bindParam(':reason', $reason);
    return $stmt->execute();
}

// 获取所有登录尝试
public function getAllLoginAttempts() {
    $stmt = $this->connection->query(
        "SELECT * FROM login_attempts ORDER BY attempt_time DESC"
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}

// 创建数据库实例
$db = new Database();
$db->createTables(); // 确保表存在
?>
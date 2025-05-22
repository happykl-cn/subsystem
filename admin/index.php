<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

// 确保 session 已启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // 获取客户端IP地址
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // 获取客户端IP地址
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // 检查登录尝试次数限制
    $attempts = $db->getRecentAttempts($ip, 15); // 15分钟内尝试次数
    if ($attempts > 5) {
        $error = '尝试次数过多，请15分钟后再试';
        
        // 记录安全事件
        $db->logLoginAttempt($ip, $username, false, 'rate_limit');
        
        // 可以在这里添加发送警报的代码
        // sendAlertEmail("暴力破解尝试: IP $ip 在15分钟内尝试了 $attempts 次登录");
    } else {
        // 验证登录凭据
        $isSuccess = ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD);
        
        // 记录登录尝试
        $db->logLoginAttempt($ip, $username, $isSuccess);
        
        if ($isSuccess) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: dashboard.php');
            exit;
        } else {
            $error = '用户名或密码不正确';
        }
    }
}
    
    // 验证登录凭据
    $isSuccess = ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD);
    
    // 记录登录尝试
    $db->logLoginAttempt($ip, $username, $isSuccess);
    
    // 记录到文本日志
    $logEntry = sprintf(
        "[%s] IP: %s | 用户名: %s | 状态: %s\n",
        date('Y-m-d H:i:s'),
        $ip,
        $username,
        $isSuccess ? '成功' : '失败'
    );
    
    // 确保日志目录存在
    if (!file_exists(LOG_DIR)) {
        mkdir(LOG_DIR, 0755, true);
    }
    
    file_put_contents(LOGIN_ATTEMPTS_LOG, $logEntry, FILE_APPEND);
    
    if ($isSuccess) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = '没想到这么一个小破网站还会记录你的IP吧？哈哈！';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>管理员登录</h1>
            <p>万幸得以相识</p>
            <?php if ($error): ?>
                <div class="alert error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="post">
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input type="text" id="username" name="username" placeholder="请输入用户名" required>
                </div>
                <div class="form-group">
                    <label for="password">密码</label>
                    <input type="password" id="password" name="password" placeholder="请输入密码" required>
                </div>
                <button type="submit" class="btn">登录</button>
            </form>
        </div>
    </div>
</body>
</html>
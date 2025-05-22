<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php';

// 检查登录状态
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if (empty($subject) || empty($message)) {
        $error = '邮件主题和内容不能为空';
    } else {
        // 获取所有订阅者
        $subscribers = $db->getAllSubscribers();
        $successCount = 0;
        
        foreach ($subscribers as $subscriber) {
            if (Mailer::sendCustomEmail($subscriber['email'], $subject, $message)) {
                $successCount++;
            }
        }
        
        $success = "邮件已成功发送给 $successCount 个订阅者";
    }
} else {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>邮件发送结果</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>邮件发送结果</h1>
            
            <?php if ($error): ?>
                <div class="alert error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <a href="dashboard.php" class="btn">返回管理面板</a>
        </div>
    </div>
</body>
</html>
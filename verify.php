<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    
    // 验证会话中的验证码
    if (isset($_SESSION['verification_email']) && isset($_SESSION['verification_code'])) {
        if ($_SESSION['verification_email'] === $email && $_SESSION['verification_code'] === $code) {
            // 验证通过，添加到数据库
            if (!$db->emailExists($email)) {
                if ($db->addSubscriber($email)) {
                    $success = '订阅成功！感谢您的订阅。您可以加入毫无生机的QQ群936107760持续关注没有任何消息的进展。也可以逛逛首页happykl.cn(预计5.25恢复)';
                    // 清除会话中的验证信息
                    unset($_SESSION['verification_email']);
                    unset($_SESSION['verification_code']);
                } else {
                    $error = '订阅失败，请稍后再试';
                }
            } else {
                $success = '您已经订阅过我们的服务了！';
            }
        } else {
            $error = '验证码不正确，请重新输入';
        }
    } else {
        $error = '验证会话已过期，请重新获取验证码';
    }
} else {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订阅结果</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>订阅结果</h1>
            
            <?php if ($error): ?>
                <div class="alert error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <a href="index.php" class="btn">返回</a>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <a href="index.php" class="btn">返回首页</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
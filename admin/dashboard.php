<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

// 检查登录状态
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

// 获取所有订阅者
$subscribers = $db->getAllSubscribers();

// 处理批量删除
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    if (!empty($_POST['selected_emails'])) {
        $placeholders = implode(',', array_fill(0, count($_POST['selected_emails']), '?'));
        $stmt = $db->getConnection()->prepare("DELETE FROM subscribers WHERE email IN ($placeholders)");
        $stmt->execute($_POST['selected_emails']);
        $deletedCount = $stmt->rowCount();
        $success = "成功删除 $deletedCount 个订阅者";
    } else {
        $error = "请选择要删除的订阅者";
    }
}
?>
<?php
// 处理退出登录
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订阅管理</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .subscriber-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .subscriber-table th, .subscriber-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .subscriber-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .subscriber-table tr:hover {
            background-color: #f5f5f5;
        }
        .action-buttons {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        .send-email-form {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>订阅管理</h1>
            <p>当前共有 <?php echo count($subscribers); ?> 个订阅者</p>
            
            <?php if (isset($error)): ?>
                <div class="alert error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="post">
                <table class="subscriber-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>ID</th>
                            <th>邮箱</th>
                            <th>订阅时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscribers as $subscriber): ?>
                            <tr>
                                <td><input type="checkbox" name="selected_emails[]" value="<?php echo htmlspecialchars($subscriber['email']); ?>"></td>
                                <td><?php echo htmlspecialchars($subscriber['id']); ?></td>
                                <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                                <td><?php echo htmlspecialchars($subscriber['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="action-buttons">
                    <button type="submit" name="delete_selected" class="btn" onclick="return confirm('确定要删除选中的订阅者吗？')">删除选中</button>
                    <a href="send_email.php" class="btn">发送邮件</a>
                    <a href="?logout" class="btn">退出登录</a>
                </div>
            </form>
            
            <div class="send-email-form">
                <h2>发送邮件</h2>
                <form action="send_email.php" method="post">
                    <div class="form-group">
                        <label for="subject">邮件主题</label>
                        <input type="text" id="subject" name="subject" placeholder="请输入邮件主题" required>
                    </div>
                    <div class="form-group">
                        <label for="message">邮件内容</label>
                        <textarea id="message" name="message" rows="5" placeholder="请输入邮件内容" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px;"></textarea>
                    </div>
                    <button type="submit" class="btn">发送给所有订阅者</button>
                </form>
            </div>
        </div>
    </div>
    <div class="login-attempts" style="margin-top: 30px;">
    <h2>登录尝试记录</h2>
    <table class="subscriber-table">
        <thead>
            <tr>
                <th>时间</th>
                <th>IP地址</th>
                <th>用户名</th>
                <th>状态</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($db->getAllLoginAttempts() as $attempt): ?>
                <tr>
                    <td><?= htmlspecialchars($attempt['attempt_time']) ?></td>
                    <td><?= htmlspecialchars($attempt['ip_address']) ?></td>
                    <td><?= htmlspecialchars($attempt['username']) ?></td>
                    <td><?= $attempt['is_success'] ? '成功' : '失败' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <script>
        // 全选/取消全选
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_emails[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
</body>
</html>


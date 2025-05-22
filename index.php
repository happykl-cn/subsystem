<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/mailer.php';

$error = '';
$success = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // 验证邮箱格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '请输入有效的邮箱地址';
    } else {
        // 生成验证码
        $code = substr(md5(uniqid(rand(), true)), 0, 6);
        
        // 存储验证码到会话
        $_SESSION['verification_email'] = $email;
        $_SESSION['verification_code'] = $code;
        
        // 发送验证邮件
       // 在发送验证码的部分修改为：
if (Mailer::sendVerificationEmail($email, $code)) {
    $success = '验证码已发送到您的邮箱，请查收并输入验证码';
} else {
    $error = '发送验证码失败，请检查您的邮箱地址是否正确或稍后再试';
    // 清除会话中的验证信息
    unset($_SESSION['verification_email']);
    unset($_SESSION['verification_code']);
}
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订阅我们的资讯</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="container">
        <article class="card">
            <!-- 标题和描述 -->
            <header class="card-header">
                <h1 class="card-title">订阅我们的资讯</h1>
                <p class="card-description">订阅后，您将在我们有进展时收到我们的最新内容与通知</p>
            </header>

            <!-- 消息提示区域 -->
            <?php if ($error): ?>
                <div class="alert error" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- 表单区域 -->
            <section class="card-body">
                <?php if ($success): ?>
                    <div class="alert success" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    
                    <form class="verification-form" action="verify.php" method="post">
                        <div class="form-group">
                            <label for="code">验证码</label>
                            <input type="text" id="code" name="code" placeholder="请输入收到的验证码" required>
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                        <button type="submit" class="btn">验证并订阅</button>
                    </form>
                <?php else: ?>
                    <form class="subscription-form" action="" method="post">
                        <div class="form-group">
                            <label for="email">邮箱地址</label>
                            <input type="email" id="email" name="email" placeholder="请输入您的邮箱地址" required value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                        <button type="submit" class="btn">获取验证码</button>
                    </form>
                <?php endif; ?>
            </section>
<br>
            <!-- 隐私声明 -->
            <footer class="card-footer">
                <p class="privacy-notice">我们重视您的隐私，绝不会分享您的邮箱地址</p>
            </footer>

            <!-- 常见问题 -->
            <aside class="faq-section">
                <div class="dropdown-container">
                    <div class="dropdown-header" onclick="toggleDropdown(this)" aria-expanded="false">
                        <h2 class="dropdown-title">常见问题 <span class="dropdown-hint">(点击展开)</span></h2>
                        <span class="dropdown-icon"><i class="fa fa-chevron-down"></i></span>
                    </div>
                    <div class="dropdown-content">
                        <ul>
                        <li><strong>如何取消订阅？</strong> 回复TD即可取消订阅</li>
                        <li><strong>多久会收到一次邮件？</strong> 我们通常会在重大更新时发送一次邮件，包含最新内容和未来规划等。</li>
                        <li><strong>如何确认我的订阅？</strong> 提交邮箱后，您将收到一封包含验证码的邮件，请按照指引完成验证。</li>
                        <li><strong>如果没有收到验证码怎么办？</strong> 请检查您的垃圾邮件文件夹，如果仍未找到，请尝试重新订阅。</li>
                    </ul>
                    </div>
                </div>
            </aside>
            <aside class="faq-section">
                <div class="dropdown-container">
                    <div class="dropdown-header" onclick="toggleDropdown(this)" aria-expanded="false">
                        <h2 class="dropdown-title">更新日志 <span class="dropdown-hint">(点击展开)</span></h2>
                        <span class="dropdown-icon"><i class="fa fa-chevron-down"></i></span>
                    </div>
                    <div class="dropdown-content">
                        <ul>
                        <li><strong>5.18</strong> 后台增加了网络验证。后台增加了登陆IP限制。增加了登陆IP监听。增加了防劫持请求头。启用了xss保护。防止mi me混淆，强制https等策略。更新了数据库表结构。启用了IP黑名单。添加了登陆失败的小彩蛋。</li>
                        
                    </ul>
                    </div>
                </div>
            </aside>
        </article>
    </main>

    <script>
        function toggleDropdown(header) {
            const content = header.nextElementSibling;
            const isExpanded = header.getAttribute('aria-expanded') === 'true';
            
            header.classList.toggle('active');
            content.classList.toggle('active');
            header.setAttribute('aria-expanded', !isExpanded);
            
            // 更新提示文本
            const hint = header.querySelector('.dropdown-hint');
            if (hint) {
                hint.textContent = isExpanded ? '(点击展开)' : '(点击收起)';
            }
        }
    </script>
    <!-- 2025 Copyright 北京恐龙理工大学 版权所有 本源码旨在交流学习 禁止贩卖以及用于商业用途 使用请标注作者@小恐龙太好拉-->
</body>
</html>
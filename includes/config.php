<?php
header("X-Frame-Options: DENY"); // 防止点击劫持
header("X-XSS-Protection: 1; mode=block"); // 启用XSS过滤器
header("X-Content-Type-Options: nosniff"); // 防止MIME类型混淆攻击
header("Referrer-Policy: strict-origin-when-cross-origin"); // 控制referrer信息
header("Permissions-Policy: geolocation=(), microphone=()"); // 限制敏感API访问
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-src 'none'; object-src 'none';");
?>
<?php
// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');

// SMTP配置
define('SMTP_HOST', '');
define('SMTP_PORT', 25);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM', '');
define('SMTP_FROM_NAME', '');

// 安全配置
define('ADMIN_USERNAME', '');
define('ADMIN_PASSWORD', ''); // 请在生产环境中更改

// 会话启动
session_start();

// 日志配置
define('LOG_DIR', __DIR__ . '/../logs');
define('LOGIN_ATTEMPTS_LOG', LOG_DIR . '/login_attempts.log');
?>
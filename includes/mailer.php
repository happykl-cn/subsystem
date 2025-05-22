<?php
require_once 'config.php';

// 引入 PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

class Mailer {
    public static function sendVerificationEmail($to, $code) {
        $subject = "您的验证码";
        $message = "感谢您的订阅！\n\n您的验证码是: $code\n\n请使用此验证码完成订阅过程。";
        
        return self::sendEmail($to, $subject, $message);
    }
    
    public static function sendCustomEmail($to, $subject, $message) {
        return self::sendEmail($to, $subject, $message);
    }
    
    private static function sendEmail($to, $subject, $message) {
        $mail = new PHPMailer(true);
        
        try {
            // 服务器设置
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';
            
            // 收件人
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($to);
            
            // 内容
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            // 记录错误日志
            error_log("邮件发送失败: " . $mail->ErrorInfo);
            return false;
        }
    }
}
?>
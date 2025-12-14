<?php

$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $_mailer;
    private array $_config;

    public function __construct()
    {
        $this->_config = config('mail');
        $this->_mailer = new PHPMailer(true);
        $this->_configureMailer();
    }

    private function _configureMailer(): void
    {
        $this->_mailer->isSMTP();
        $this->_mailer->Host = $this->_config['smtp_host'];
        $this->_mailer->SMTPAuth = true;
        $this->_mailer->Username = $this->_config['smtp_username'];
        $this->_mailer->Password = $this->_config['smtp_password'];
        $this->_mailer->SMTPSecure = $this->_config['smtp_encryption'];
        $this->_mailer->Port = $this->_config['smtp_port'];
        $this->_mailer->CharSet = 'UTF-8';
        $this->_mailer->SMTPDebug = 0; // 0 = off, 1 = client, 2 = client and server
        $this->_mailer->Debugoutput = function($str, $level) {
            error_log("SMTP Debug: $str");
        };
        $this->_mailer->setFrom($this->_config['from_email'], $this->_config['from_name']);
    }

    public function sendVerificationCode(string $toEmail, string $code): bool
    {
        try {
            $this->_mailer->clearAddresses();
            $this->_mailer->addAddress($toEmail);
            $this->_mailer->isHTML(true);
            $this->_mailer->Subject = 'Подтверждение email';
            $this->_mailer->Body = $this->_getVerificationEmailBody($code);
            $this->_mailer->AltBody = "Ваш код подтверждения: {$code}";
            
            $result = $this->_mailer->send();
            if (!$result) {
                error_log("Email sending failed: " . $this->_mailer->ErrorInfo);
            }
            return $result;
        } catch (Exception $e) {
            error_log("Email sending exception: " . $e->getMessage());
            error_log("PHPMailer ErrorInfo: " . $this->_mailer->ErrorInfo);
            return false;
        }
    }

    public function sendPasswordResetCode(string $toEmail, string $code): bool
    {
        try {
            $this->_mailer->clearAddresses();
            $this->_mailer->addAddress($toEmail);
            $this->_mailer->isHTML(true);
            $this->_mailer->Subject = 'Восстановление пароля';
            $this->_mailer->Body = $this->_getPasswordResetEmailBody($code);
            $this->_mailer->AltBody = "Ваш код для восстановления пароля: {$code}";
            
            return $this->_mailer->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $this->_mailer->ErrorInfo);
            return false;
        }
    }

    private function _getVerificationEmailBody(string $code): string
    {
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #4CAF50;'>Подтверждение email</h2>
                <p>Спасибо за регистрацию! Для завершения регистрации введите следующий код:</p>
                <div style='background-color: #f4f4f4; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px;'>
                    <h1 style='color: #4CAF50; font-size: 32px; margin: 0; letter-spacing: 5px;'>{$code}</h1>
                </div>
                <p style='color: #666; font-size: 14px;'>Код действителен в течение 1 часа.</p>
                <p style='color: #666; font-size: 14px;'>Если вы не регистрировались, просто проигнорируйте это письмо.</p>
            </div>
        </body>
        </html>
        ";
    }

    private function _getPasswordResetEmailBody(string $code): string
    {
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #2196F3;'>Восстановление пароля</h2>
                <p>Вы запросили восстановление пароля. Используйте следующий код:</p>
                <div style='background-color: #f4f4f4; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px;'>
                    <h1 style='color: #2196F3; font-size: 32px; margin: 0; letter-spacing: 5px;'>{$code}</h1>
                </div>
                <p style='color: #666; font-size: 14px;'>Код действителен в течение 1 часа.</p>
                <p style='color: #666; font-size: 14px;'>Если вы не запрашивали восстановление пароля, просто проигнорируйте это письмо.</p>
            </div>
        </body>
        </html>
        ";
    }
}


<?php

/**
 * Email Helper - Handles email sending for OTP and other notifications
 * Supports both native mail() and SMTP
 */
class EmailHelper
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/config.php';
    }

    /**
     * Send OTP email to student
     *
     * @param string $email Student email address
     * @param string $otpCode 6-digit OTP code
     * @param string $studentName Student's full name (optional)
     * @return bool True if sent successfully, false otherwise
     */
    public function sendOTPEmail(string $email, string $otpCode, string $studentName = 'Student'): bool
    {
        $subject = 'ReidHub - Verify Your Email';
        
        $body = $this->buildOTPEmailTemplate($otpCode, $studentName);

        return $this->send($email, $subject, $body);
    }

    /**
     * Send password reset email
     *
     * @param string $email Email address
     * @param string $resetLink Reset link
     * @param string $studentName Student's name
     * @return bool
     */
    public function sendPasswordResetEmail(string $email, string $resetLink, string $studentName = 'Student'): bool
    {
        $subject = 'ReidHub - Reset Your Password';
        
        $body = $this->buildPasswordResetTemplate($resetLink, $studentName);

        return $this->send($email, $subject, $body);
    }

    /**
     * Generic email sending method
     * Supports both mail() and SMTP
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body HTML email body
     * @param array $headers Optional additional headers
     * @return bool True if sent, false otherwise
     */
    private function send(string $to, string $subject, string $body, array $headers = []): bool
    {
        if ($this->config['MAIL_DRIVER'] === 'mail') {
            return $this->sendViaMail($to, $subject, $body, $headers);
        } elseif ($this->config['MAIL_DRIVER'] === 'smtp') {
            return $this->sendViaSMTP($to, $subject, $body, $headers);
        }

        Logger::error("Invalid mail driver: {$this->config['MAIL_DRIVER']}");
        return false;
    }

    /**
     * Send email using PHP's native mail() function
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $body HTML body
     * @param array $headers Additional headers
     * @return bool
     */
    private function sendViaMail(string $to, string $subject, string $body, array $headers = []): bool
    {
        try {
            $emailHeaders = array_merge([
                'MIME-Version' => '1.0',
                'Content-type' => 'text/html; charset=UTF-8',
                'From' => "{$this->config['MAIL_FROM_NAME']} <{$this->config['MAIL_FROM_EMAIL']}>",
                'Reply-To' => $this->config['MAIL_FROM_EMAIL'],
            ], $headers);

            $headerString = '';
            foreach ($emailHeaders as $key => $value) {
                $headerString .= "$key: $value\r\n";
            }

            $result = mail($to, $subject, $body, $headerString);

            if ($result) {
                Logger::info("Email sent successfully to: $to");
            } else {
                Logger::error("Failed to send email to: $to");
            }

            return $result;
        } catch (Exception $e) {
            Logger::error("Email sending error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email via SMTP (future implementation for production)
     * Placeholder for SMTP implementation (e.g., PHPMailer, SwiftMailer)
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $body HTML body
     * @param array $headers Additional headers
     * @return bool
     */
    private function sendViaSMTP(string $to, string $subject, string $body, array $headers = []): bool
    {
        // TODO: Implement SMTP sending using PHPMailer or similar library
        // For now, fallback to mail()
        Logger::warning("SMTP not yet implemented, using native mail() instead");
        return $this->sendViaMail($to, $subject, $body, $headers);
    }

    /**
     * Build HTML email template for OTP verification
     *
     * @param string $otpCode 6-digit OTP
     * @param string $studentName Student name
     * @return string HTML email body
     */
    private function buildOTPEmailTemplate(string $otpCode, string $studentName): string
    {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body {
                    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background-color: #f5f5f5;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 500px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 10px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    padding: 40px 30px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .logo {
                    font-size: 24px;
                    font-weight: 700;
                    color: #0466C8;
                    margin-bottom: 10px;
                }
                .title {
                    font-size: 20px;
                    font-weight: 600;
                    color: #333;
                    margin-bottom: 5px;
                }
                .subtitle {
                    font-size: 14px;
                    color: #666;
                }
                .content {
                    margin-bottom: 30px;
                }
                .greeting {
                    font-size: 16px;
                    color: #333;
                    margin-bottom: 15px;
                }
                .otp-box {
                    background: linear-gradient(135deg, #0466C8 0%, #0456b8 100%);
                    border-radius: 8px;
                    padding: 25px;
                    text-align: center;
                    margin-bottom: 20px;
                }
                .otp-label {
                    font-size: 12px;
                    color: rgba(255, 255, 255, 0.8);
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    margin-bottom: 10px;
                }
                .otp-code {
                    font-size: 36px;
                    font-weight: 700;
                    color: #ffffff;
                    letter-spacing: 4px;
                    font-family: 'Courier New', monospace;
                }
                .info-text {
                    font-size: 14px;
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 10px;
                }
                .warning {
                    background-color: #FFF3CD;
                    border-left: 4px solid #FFC107;
                    padding: 12px 15px;
                    border-radius: 4px;
                    font-size: 13px;
                    color: #856404;
                    margin-top: 15px;
                }
                .footer {
                    text-align: center;
                    border-top: 1px solid #e0e0e0;
                    padding-top: 20px;
                    font-size: 12px;
                    color: #999;
                }
                .footer-link {
                    color: #0466C8;
                    text-decoration: none;
                }
                .cta-button {
                    display: inline-block;
                    background-color: #0466C8;
                    color: #ffffff;
                    padding: 12px 30px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-weight: 600;
                    margin-top: 15px;
                    transition: background-color 0.3s;
                }
                .cta-button:hover {
                    background-color: #0456b8;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🎓 ReidHub</div>
                    <div class='title'>Email Verification</div>
                    <div class='subtitle'>Complete your signup</div>
                </div>

                <div class='content'>
                    <div class='greeting'>Hi {$studentName},</div>
                    
                    <p class='info-text'>
                        Thank you for signing up at ReidHub! To complete your account registration, 
                        please verify your email address using the code below:
                    </p>

                    <div class='otp-box'>
                        <div class='otp-label'>Your Verification Code</div>
                        <div class='otp-code'>{$otpCode}</div>
                    </div>

                    <p class='info-text'>
                        This code is valid for 10 minutes. If you didn't request this email, 
                        you can safely ignore it.
                    </p>

                    <div class='warning'>
                        ⚠️ Never share this code with anyone. ReidHub staff will never ask for it.
                    </div>
                </div>

                <div class='footer'>
                    <p>
                        If you're having trouble, contact us at 
                        <a href='mailto:support@reidhub.com' class='footer-link'>support@reidhub.com</a>
                    </p>
                    <p>© 2026 ReidHub. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Build HTML email template for password reset
     *
     * @param string $resetLink Password reset link
     * @param string $studentName Student name
     * @return string HTML email body
     */
    private function buildPasswordResetTemplate(string $resetLink, string $studentName): string
    {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body {
                    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background-color: #f5f5f5;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 500px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 10px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    padding: 40px 30px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .logo {
                    font-size: 24px;
                    font-weight: 700;
                    color: #0466C8;
                    margin-bottom: 10px;
                }
                .title {
                    font-size: 20px;
                    font-weight: 600;
                    color: #333;
                }
                .content {
                    margin-bottom: 30px;
                }
                .info-text {
                    font-size: 14px;
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 15px;
                }
                .cta-button {
                    display: inline-block;
                    background-color: #0466C8;
                    color: #ffffff;
                    padding: 12px 30px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-weight: 600;
                    margin: 20px 0;
                    transition: background-color 0.3s;
                }
                .cta-button:hover {
                    background-color: #0456b8;
                }
                .footer {
                    text-align: center;
                    border-top: 1px solid #e0e0e0;
                    padding-top: 20px;
                    font-size: 12px;
                    color: #999;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🎓 ReidHub</div>
                    <div class='title'>Password Reset</div>
                </div>

                <div class='content'>
                    <p class='info-text'>Hi {$studentName},</p>
                    
                    <p class='info-text'>
                        We received a request to reset your password. Click the button below to proceed:
                    </p>

                    <center>
                        <a href='{$resetLink}' class='cta-button'>Reset Password</a>
                    </center>

                    <p class='info-text'>
                        This link is valid for 30 minutes. If you didn't request this, ignore this email.
                    </p>
                </div>

                <div class='footer'>
                    <p>© 2026 ReidHub. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}

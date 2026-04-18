<?php
require_once __DIR__ . '/../core/Logger.php';

/**
 * EmailService - Handles sending email notifications
 * 
 * Note: This is a basic implementation. For production, consider using:
 * - PHPMailer library
 * - SendGrid API
 * - Amazon SES
 * - Other email service providers
 */
class EmailService
{
    private static $nocEmail = 'noc@ucsc.cmb.ac.lk';
    private static $unionEmail = 'studentsunion@ucsc.cmb.ac.lk';
    private static $fromEmail = 'reidhub@ucsc.cmb.ac.lk';
    private static $fromName = 'ReidHub - UCSC';

    /**
     * Send email to NOC for Critical lost items
     */
    public static function sendNOCAlert($itemId, $itemName, $category, $severity, $location, $reporter)
    {
        $subject = "[CRITICAL] Lost Item Alert - $itemName";
        
        $message = "
        <html>
        <head><title>Critical Lost Item Alert</title></head>
        <body style='font-family: Arial, sans-serif;'>
            <h2 style='color: #dc2626;'>🚨 Critical Lost Item Alert</h2>
            <p>A critical lost item has been reported on ReidHub and requires immediate attention.</p>
            
            <table style='border-collapse: collapse; width: 100%; max-width: 600px; margin: 20px 0;'>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Item ID:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$itemId</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Item Name:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$itemName</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Category:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$category</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Severity:</td>
                    <td style='padding: 10px; border: 1px solid #ddd; color: #dc2626; font-weight: bold;'>$severity</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Last Known Location:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$location</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Reported By:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$reporter</td>
                </tr>
            </table>
            
            <p style='margin-top: 20px;'>
                <a href='http://localhost:8000/dashboard/lost-and-found/admin' 
                   style='background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>
                    View in Admin Dashboard
                </a>
            </p>
            
            <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>
                This is an automated email from ReidHub Lost & Found System.<br>
                For support, contact: support@reidhub.com
            </p>
        </body>
        </html>
        ";

        return self::sendEmail(self::$nocEmail, $subject, $message);
    }

    /**
     * Send email to Students' Union for found items
     */
    public static function sendUnionAlert($itemId, $itemName, $category, $condition, $location, $reporter)
    {
        $subject = "Found Item Report - $itemName";
        
        $message = "
        <html>
        <head><title>Found Item Report</title></head>
        <body style='font-family: Arial, sans-serif;'>
            <h2 style='color: #059669;'>✅ Found Item Reported</h2>
            <p>A found item has been reported on ReidHub and is awaiting your review.</p>
            
            <table style='border-collapse: collapse; width: 100%; max-width: 600px; margin: 20px 0;'>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Item ID:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$itemId</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Item Name:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$itemName</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Category:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$category</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Condition:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$condition</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Found Location:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$location</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd; background: #f9fafb; font-weight: bold;'>Reported By:</td>
                    <td style='padding: 10px; border: 1px solid #ddd;'>$reporter</td>
                </tr>
            </table>
            
            <p style='margin-top: 20px;'>
                <a href='http://localhost:8000/dashboard/lost-and-found/admin' 
                   style='background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>
                    View in Admin Dashboard
                </a>
            </p>
            
            <p style='color: #6b7280; font-size: 14px; margin-top: 30px;'>
                This is an automated email from ReidHub Lost & Found System.<br>
                For support, contact: support@reidhub.com
            </p>
        </body>
        </html>
        ";

        return self::sendEmail(self::$unionEmail, $subject, $message);
    }

    /**
     * Core email sending function using PHP's mail()
     * For production, replace with PHPMailer or SendGrid
     */
    private static function sendEmail($to, $subject, $htmlMessage)
    {
        try {
            // Email headers
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
            $headers .= "Reply-To: " . self::$fromEmail . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

            // Send email
            $sent = mail($to, $subject, $htmlMessage, $headers);

            if ($sent) {
                Logger::info("Email sent successfully to: $to | Subject: $subject");
                return true;
            } else {
                Logger::warning("Email failed to send to: $to | Subject: $subject");
                return false;
            }
        } catch (Exception $e) {
            Logger::error("Email sending error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk notification email to all users (future implementation)
     */
    public static function sendBulkUserNotification($itemType, $itemName, $userEmails)
    {
        // This would be implemented when user email notifications are required
        // For now, we're using in-app notifications only
        Logger::info("Bulk notification queued: $itemType - $itemName to " . count($userEmails) . " users");
        return true;
    }
}

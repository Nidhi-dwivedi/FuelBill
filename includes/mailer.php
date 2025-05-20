<?php
require_once 'config.php';
require_once 'vendor/autoload.php'; // Composer autoload for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    public static function sendReceipt($to_email, $to_name, $transaction_id, $amount, $fuel_type, $liters, $transaction_date) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($to_email, $to_name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Petrol Pump Transaction Receipt #' . $transaction_id;
            
            $mail->Body = self::getReceiptTemplate(
                $to_name,
                $transaction_id,
                $amount,
                $fuel_type,
                $liters,
                $transaction_date
            );

            $mail->AltBody = "Thank you for your transaction. Transaction ID: $transaction_id, Amount: $amount";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    private static function getReceiptTemplate($name, $transaction_id, $amount, $fuel_type, $liters, $date) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .header { text-align: center; margin-bottom: 20px; }
                .logo { font-size: 24px; font-weight: bold; color: #2c3e50; }
                .receipt-title { text-align: center; font-size: 20px; margin: 20px 0; }
                .receipt-details { margin-bottom: 20px; }
                .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
                .detail-label { font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>Petrofy</div>
                    <div>India</div>
                </div>
                
                <div class='receipt-title'>TRANSACTION RECEIPT</div>
                
                <div class='receipt-details'>
                    <div class='detail-row'>
                        <span class='detail-label'>Customer Name:</span>
                        <span>$name</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Transaction ID:</span>
                        <span>#$transaction_id</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Date & Time:</span>
                        <span>$date</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Fuel Type:</span>
                        <span>$fuel_type</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Liters:</span>
                        <span>$liters L</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Amount:</span>
                        <span>₹$amount</span>
                    </div>
                </div>
                
                <div class='footer'>
                    Thank you for your business!<br>
                    For any queries, please contact petrofy8@gmail.com
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
?>
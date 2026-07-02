<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php'; // Adjust path as needed

class CompanyEmailSender {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com'; // Your SMTP server
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'ahmedikram567@gmail.com'; // Your email
        $this->mail->Password   = 'kuljrntfpqgomvwj'; // Your app password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        
        // Sender
        $this->mail->setFrom('ahmedikram567@gmail.com', 'career_finder');
        $this->mail->isHTML(true);
    }
    
    public function sendOTP($toEmail, $companyName, $otp) {
        try {
            // Recipient
            $this->mail->addAddress($toEmail, $companyName);
            
            // Content
            $this->mail->Subject = 'Company Account OTP Verification - CareerFinder';
            
            $emailTemplate = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
                    .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .header { text-align: center; margin-bottom: 30px; }
                    .otp-code { font-size: 32px; font-weight: bold; text-align: center; color: #059669; margin: 20px 0; padding: 15px; background: #f0fdf4; border-radius: 8px; letter-spacing: 5px; }
                    .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2 style='color: #059669; margin: 0;'>CareerFinder</h2>
                        <p style='color: #666; margin: 5px 0;'>Company Email Verification</p>
                    </div>
                    
                    <p>Hello <strong>$companyName</strong>,</p>
                    <p>Thank you for registering your company with CareerFinder. Use the OTP code below to verify your email address:</p>
                    
                    <div class='otp-code'>$otp</div>
                    
                    <p>This OTP will not expire. Enter this code in the verification form to complete your company registration.</p>
                    <p>If you didn't request this, please ignore this email.</p>
                    
                    <div class='footer'>
                        <p>&copy; 2024 CareerFinder. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            $this->mail->Body = $emailTemplate;
            
            // Plain text version
            $this->mail->AltBody = "Your company OTP verification code is: $otp\n\nThis OTP will not expire.";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}
?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';; // Adjust path as needed

function sendOTP($email, $otp, $name, $type = 'user') {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'muneeb.amed0@gmail.com'; // Your email
        $mail->Password   = 'cbfgzztxvhdtinxr'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('muneeb.amed0@gmail.com', 'CareerFinder');
        $mail->addAddress($email, $name);
        
        // Content
        $mail->isHTML(true);
        
        if ($type === 'user') {
            $mail->Subject = 'Verify Your Email - CareerFinder';
            $mail->Body    = "
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
                        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        .header { text-align: center; color: #3b82f6; margin-bottom: 30px; }
                        .otp-code { font-size: 32px; font-weight: bold; text-align: center; color: #1f2937; background: #f3f4f6; padding: 15px; border-radius: 8px; letter-spacing: 5px; margin: 20px 0; }
                        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>CareerFinder</h1>
                            <h2>Email Verification</h2>
                        </div>
                        <p>Hello $name,</p>
                        <p>Thank you for registering with CareerFinder. Use the OTP below to verify your email address:</p>
                        <div class='otp-code'>$otp</div>
                        <p>This OTP will not expire. Enter it on the verification page to complete your registration.</p>
                        <div class='footer'>
                            <p>If you didn't create an account, please ignore this email.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
        } else {
            $mail->Subject = 'Verify Your Company Email - CareerFinder';
            $mail->Body    = "
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
                        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        .header { text-align: center; color: #10b981; margin-bottom: 30px; }
                        .otp-code { font-size: 32px; font-weight: bold; text-align: center; color: #1f2937; background: #f3f4f6; padding: 15px; border-radius: 8px; letter-spacing: 5px; margin: 20px 0; }
                        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>CareerFinder</h1>
                            <h2>Company Email Verification</h2>
                        </div>
                        <p>Hello $name,</p>
                        <p>Thank you for registering your company with CareerFinder. Use the OTP below to verify your email address:</p>
                        <div class='otp-code'>$otp</div>
                        <p>This OTP will not expire. Enter it on the verification page to complete your registration.</p>
                        <div class='footer'>
                            <p>If you didn't create a company account, please ignore this email.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
        }
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
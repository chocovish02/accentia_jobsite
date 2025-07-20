<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Database connection
$conn = new mysqli("localhost", "root", "", "accentia_jobs");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '********'; // Using the same email as sendThankYouEmail
        $mail->Password   = '********'; // Using the same password as sendThankYouEmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('careers@accentia.com', 'Accentia Careers');
        $mail->addAddress($to);
        $mail->addReplyTo('careers@accentia.com', 'Accentia Careers');

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendThankYouEmail($to, $name, $university, $graduation) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth   = true;
        $mail->Username   = '************'; // Replace with your email
        $mail->Password   = '************'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('careers@accentia.com', 'Accentia Careers');
        $mail->addAddress($to, $name);
        $mail->addReplyTo('careers@accentia.com', 'Accentia Careers');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Thank you for applying to Accentia';
        
        // HTML Email Body
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #2C3E50;'>Dear " . htmlspecialchars($name) . ",</h2>
                
                <p>Thank you for applying for the Software Engineer Intern position at Accentia. We appreciate your interest in joining our team.</p>
                
                <p>We have received your application and our hiring team will review it carefully. If your qualifications match our requirements, we will contact you for the next steps.</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h3 style='color: #2C3E50; margin-top: 0;'>Application Details:</h3>
                    <p><strong>Position:</strong> Software Engineer Intern</p>
                    <p><strong>University:</strong> " . htmlspecialchars($university) . "</p>
                    <p><strong>Expected Graduation:</strong> " . htmlspecialchars($graduation) . "</p>
                </div>
                
                <p>Please note that due to the high volume of applications, it may take us some time to review all submissions.</p>
                
                <p>Best regards,<br>Accentia Hiring Team</p>
            </div>";
        
        // Plain text version for non-HTML mail clients
        $mail->AltBody = "Dear " . $name . ",

Thank you for applying for the Software Engineer Intern position at Accentia. We appreciate your interest in joining our team.

We have received your application and our hiring team will review it carefully. If your qualifications match our requirements, we will contact you for the next steps.

Application Details:
Position: Software Engineer Intern
University: " . $university . "
Expected Graduation: " . $graduation . "

Please note that due to the high volume of applications, it may take us some time to review all submissions.

Best regards,
Accentia Hiring Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

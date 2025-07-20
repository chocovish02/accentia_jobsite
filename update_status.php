<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

require_once 'includes/db_connect.php';
require_once 'includes/mail_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = (int)$_POST['id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    $valid_statuses = ['pending', 'reviewed', 'accepted', 'rejected'];
    if (!in_array($status, $valid_statuses)) {
        die(json_encode(['success' => false, 'error' => 'Invalid status']));
    }

    // Fetch applicant data
    $query = "SELECT email, fullname FROM applications WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();

    if (!$applicant) {
        die(json_encode(['success' => false, 'error' => 'Applicant not found']));
    }

    // Update application status
    $query = "UPDATE applications SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $id);

    if ($stmt->execute()) {
        // Prepare email
        $subject = "";
        $message = "Dear " . htmlspecialchars($applicant['fullname']) . ",\n\n";

        switch ($status) {
            case 'accepted':
                $subject = "🎉 Congratulations! Interview Scheduled at Accentia";
                $message .= "Congratulations! We are excited to inform you that your profile has been shortlisted for the position you applied for at Accentia.\n\n";
                $message .= "Your interview is scheduled for tomorrow at 4:00 PM IST and will be conducted via Zoom Meeting.\n\n";
                $message .= "Please be available and ready at least 10 minutes prior to the scheduled time. The Zoom meeting link and additional details will be shared with you shortly.\n\n";
                $message .= "We look forward to connecting with you.\n\n";
                break;

            case 'rejected':
                $subject = "Update on Your Job Application at Accentia";
                $message .= "Thank you for your interest in joining Accentia. After careful review, we regret to inform you that your application has not been selected for further consideration at this time.\n\n";
                $message .= "We appreciate the time and effort you put into your application and encourage you to apply again in the future if suitable roles become available.\n\n";
                break;

            case 'reviewed':
                $subject = "Your Application Has Been Reviewed – Accentia Careers";
                $message .= "We wanted to inform you that your application for the position at Accentia has been reviewed by our recruitment team.\n\n";
                $message .= "We will be in touch soon with the next steps.\n\n";
                break;

            case 'pending':
                $subject = "Accentia Job Application – Status: Pending";
                $message .= "Your job application is currently marked as 'Pending'. Our team is reviewing your profile and documents.\n\n";
                $message .= "We appreciate your patience and will get back to you shortly.\n\n";
                break;
        }

        $message .= "Best regards,\nAccentia HR Team";

        $emailSent = sendEmail($applicant['email'], $subject, $message);

        if ($emailSent) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => true,
                'warning' => 'Status updated but email notification failed to send'
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

require_once 'includes/db_connect.php';

if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM applications WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

if (!$application) {
    header('Location: admin_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application - Accentia</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .application-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .application-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .application-field {
            margin-bottom: 15px;
        }
        .application-field label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #666;
        }
        .application-field .value {
            font-size: 16px;
            line-height: 1.6;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-reviewed { background: #d1ecf1; color: #0c5460; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .action-bar {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .back-btn {
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Accentia Admin</div>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </nav>
    </header>

    <main class="application-container">
        <div class="application-header">
            <h1>Application Details</h1>
            <span class="status-badge status-<?php echo $application['status'] ?? 'pending'; ?>">
                <?php echo ucfirst($application['status'] ?? 'pending'); ?>
            </span>
        </div>

        <div class="application-field">
            <label>Full Name</label>
            <div class="value"><?php echo htmlspecialchars($application['fullname']); ?></div>
        </div>

        <div class="application-field">
            <label>Email</label>
            <div class="value"><?php echo htmlspecialchars($application['email']); ?></div>
        </div>

        <div class="application-field">
            <label>Phone</label>
            <div class="value"><?php echo htmlspecialchars($application['phone']); ?></div>
        </div>

        <div class="application-field">
            <label>University</label>
            <div class="value"><?php echo htmlspecialchars($application['university']); ?></div>
        </div>

        <div class="application-field">
            <label>CGPA</label>
            <div class="value"><?php echo htmlspecialchars($application['cgpa']); ?></div>
        </div>

        <div class="application-field">
            <label>Position Applied For</label>
            <div class="value"><?php echo htmlspecialchars($application['position']); ?></div>
        </div>

        <div class="application-field">
            <label>Experience</label>
            <div class="value"><?php echo nl2br(htmlspecialchars($application['experience'])); ?></div>
        </div>

        <div class="application-field">
            <label>Skills</label>
            <div class="value"><?php echo nl2br(htmlspecialchars($application['skills'])); ?></div>
        </div>

        <div class="application-field">
            <label>Cover Letter</label>
            <div class="value"><?php echo nl2br(htmlspecialchars($application['cover_letter'])); ?></div>
        </div>

        <div class="application-field">
            <label>Resume</label>
            <div class="value">
                <a href="<?php echo htmlspecialchars($application['resume_path']); ?>" class="btn btn-download" download>Download Resume</a>
            </div>
        </div>

        <div class="action-bar">
            <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Accentia. All rights reserved.</p>
    </footer>
</body>
</html>

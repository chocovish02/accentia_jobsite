<?php
require_once 'includes/db_connect.php';

// Fetch available positions
$query = "SELECT * FROM job_positions WHERE is_active = 1 ORDER BY posted_date DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers - Accentia</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .careers-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .job-listing {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .job-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .job-details {
            color: #666;
            margin-bottom: 15px;
        }
        .job-description {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .apply-btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .apply-btn:hover {
            background: #2980b9;
        }
        .no-jobs {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Accentia</div>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="careers.php" class="active">Careers</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main class="careers-container">
        <h1>Career Opportunities at Accentia</h1>
        <p>Join our team and be part of something extraordinary. We're always looking for talented individuals who share our passion for innovation and excellence.</p>
        
        <div class="job-listings">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($job = $result->fetch_assoc()): ?>
                    <div class="job-listing">
                        <h2 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h2>
                        <div class="job-details">
                            <span><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></span> |
                            <span><strong>Type:</strong> <?php echo htmlspecialchars($job['employment_type']); ?></span>
                        </div>
                        <div class="job-description">
                            <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                        </div>
                        <a href="apply.php?position=<?php echo urlencode($job['id']); ?>" class="apply-btn">Apply Now</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-jobs">
                    <h2>No positions currently available</h2>
                    <p>Please check back later for new opportunities.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Accentia. All rights reserved.</p>
    </footer>
</body>
</html>

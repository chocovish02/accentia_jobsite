<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "accentia_jobs");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadDir = "uploads/";
    $uploadFile = $uploadDir . basename($_FILES["resume"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($uploadFile,PATHINFO_EXTENSION));
    
    // Check file size
    if ($_FILES["resume"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        echo "Sorry, only PDF, DOC & DOCX files are allowed.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $uploadFile)) {
            // Insert the application data into database
            $sql = "INSERT INTO applications (fullname, email, phone, university, graduation_date, cgpa, skills, experience, resume_path, cover_letter) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssss", 
                $_POST['fullname'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['university'],
                $_POST['graduation'],
                $_POST['cgpa'],
                $_POST['skills'],
                $_POST['experience'],
                $uploadFile,
                $_POST['cover']
            );
            
            if ($stmt->execute()) {
                // Include mail functions
                require_once 'includes/mail_functions.php';
                
                // Send thank you email
                $emailSent = sendThankYouEmail(
                    $_POST['email'],
                    $_POST['fullname'],
                    $_POST['university'],
                    $_POST['graduation']
                );
                
                header("Location: success.php");
                exit();
            } else {
                echo "Error saving application data: " . $stmt->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply - Software Engineer Intern | Accentia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Accentia</div>
            <ul>
                <li><a href="SDEintern.html" class="active">Careers</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h1>Apply for Software Engineer Intern Position</h1>
            <form action="apply.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="fullname">Full Name <span class="required">*</span></label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="university">University <span class="required">*</span></label>
                    <input type="text" id="university" name="university" required>
                </div>

                <div class="form-group">
                    <label for="graduation">Expected Graduation Date <span class="required">*</span></label>
                    <input type="text" id="graduation" name="graduation" placeholder="MM/YYYY" required>
                </div>

                <div class="form-group">
                    <label for="cgpa">Current CGPA <span class="required">*</span></label>
                    <input type="text" id="cgpa" name="cgpa" required>
                </div>

                <div class="form-group">
                    <label for="skills">Technical Skills <span class="required">*</span></label>
                    <textarea id="skills" name="skills" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="experience">Previous Experience (if any)</label>
                    <textarea id="experience" name="experience" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="resume">Upload Resume (PDF/DOC/DOCX) <span class="required">*</span></label>
                    <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                </div>

                <div class="form-group">
                    <label for="cover">Cover Letter</label>
                    <textarea id="cover" name="cover" rows="6"></textarea>
                </div>

                <div class="apply-section">
                    <button type="submit" class="apply-button">Submit Application</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Accentia. All rights reserved.</p>
    </footer>
</body>
</html>

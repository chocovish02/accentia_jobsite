<?php
require_once 'db_connect.php';

$sample_jobs = [
    [
        'title' => 'Senior Software Engineer',
        'description' => "We are looking for a Senior Software Engineer to join our dynamic team.

Requirements:
- 1+ years of experience in software development
- Strong knowledge of PHP, JavaScript, and MySQL
- Experience with modern frameworks
- Excellent problem-solving skills
- Team leadership experience

Benefits:
- Competitive salary
- Health insurance
- Flexible working hours
- Professional development opportunities",
        'location' => 'Bangalore, India',
        'employment_type' => 'Full-time'
    ],
    [
        'title' => 'Full Stack Developer',
        'description' => "Join our team as a Full Stack Developer and help build cutting-edge web applications.

Requirements:
- 3+ years of full stack development experience
- Proficiency in MERN/MEAN stack
- Experience with RESTful APIs
- Knowledge of cloud services (AWS/Azure)
- Good communication skills

Benefits:
- Remote work options
- Competitive compensation
- Learning allowance
- Health benefits",
        'location' => 'Hyderabad, India (Hybrid)',
        'employment_type' => 'Full-time'
    ],
    [
        'title' => 'UI/UX Designer',
        'description' => "Looking for a creative UI/UX Designer to craft beautiful and functional interfaces.

Requirements:
- 2+ years of UI/UX design experience
- Proficiency in Figma/Adobe XD
- Understanding of user-centered design principles
- Portfolio showcasing web/mobile projects
- Knowledge of HTML/CSS is a plus

Benefits:
- Creative work environment
- Latest design tools
- Health insurance
- Flexible hours",
        'location' => 'Mumbai, India',
        'employment_type' => 'Full-time'
    ],
    [
        'title' => 'DevOps Engineer',
        'description' => "Seeking a DevOps Engineer to streamline our development and deployment processes.

Requirements:
- 3+ years of DevOps experience
- Strong knowledge of AWS/Azure
- Experience with Docker and Kubernetes
- Expertise in CI/CD pipelines
- Scripting skills (Python/Bash)

Benefits:
- Competitive salary
- Remote work options
- Learning budget
- Health coverage",
        'location' => 'Pune, India (Remote)',
        'employment_type' => 'Full-time'
    ]
];

// Clear existing jobs
$conn->query("TRUNCATE TABLE job_positions");

// Insert sample jobs
$stmt = $conn->prepare("INSERT INTO job_positions (title, description, location, employment_type) VALUES (?, ?, ?, ?)");

foreach ($sample_jobs as $job) {
    $stmt->bind_param("ssss", $job['title'], $job['description'], $job['location'], $job['employment_type']);
    $stmt->execute();
}

echo "Sample jobs added successfully!\n";

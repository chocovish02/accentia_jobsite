<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit();
}

// Database connection
require_once 'includes/db_connect.php';

// Get filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'application_date';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Build query
$query = "SELECT * FROM applications WHERE 1=1";
if ($status_filter) {
    $query .= " AND status = '" . $conn->real_escape_string($status_filter) . "'";
}
if ($search) {
    $query .= " AND (fullname LIKE '%" . $conn->real_escape_string($search) . "%' 
                OR email LIKE '%" . $conn->real_escape_string($search) . "%' 
                OR university LIKE '%" . $conn->real_escape_string($search) . "%')";
}
$query .= " ORDER BY $sort $order";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Accentia</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            align-items: center;
        }
        .search-box {
            flex: 1;
        }
        .search-box input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .applications-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .applications-table th, .applications-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .applications-table th {
            background: #f8f9fa;
            cursor: pointer;
        }
        .applications-table th:hover {
            background: #e9ecef;
        }
        .status-select {
            padding: 8px;
            border-radius: 4px;
            border: 2px solid #ddd;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
        }
        .status-select:hover {
            transform: scale(1.02);
        }
        .status-select.status-pending { 
            color: #856404; 
            border-color: #ffc107; 
            background-color: #fff3cd;
        }
        .status-select.status-reviewed { 
            color: #0c5460; 
            border-color: #17a2b8; 
            background-color: #d1ecf1;
        }
        .status-select.status-accepted { 
            color: #155724; 
            border-color: #28a745; 
            background-color: #d4edda;
        }
        .status-select.status-rejected { 
            color: #721c24; 
            border-color: #dc3545; 
            background-color: #f8d7da;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn {
            padding: 5px 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-view { background: #007bff; color: white; }
        .btn-download { background: #28a745; color: white; }
        .btn-status { background: #17a2b8; color: white; }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            float: right;
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

    <main class="dashboard-container">
        <h1>Applications Dashboard</h1>
        
        <div class="filters">
            <div class="search-box">
                <input type="text" placeholder="Search by name, email, or university" 
                       value="<?php echo htmlspecialchars($search); ?>"
                       onchange="updateFilters(this.value)">
            </div>
            <select class="filter-select" onchange="updateFilters(null, this.value)">
                <option value="">All Status</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="reviewed" <?php echo $status_filter === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                <option value="accepted" <?php echo $status_filter === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>
        </div>

        <table class="applications-table">
            <thead>
                <tr>
                    <th onclick="sortBy('fullname')">Name</th>
                    <th onclick="sortBy('email')">Email</th>
                    <th onclick="sortBy('university')">University</th>
                    <th onclick="sortBy('cgpa')">CGPA</th>
                    <th onclick="sortBy('application_date')">Date Applied</th>
                    <th onclick="sortBy('status')">Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['university']); ?></td>
                    <td><?php echo htmlspecialchars($row['cgpa']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($row['application_date'])); ?></td>
                    <td>
                        <select 
                            onchange="updateStatus(<?php echo $row['id']; ?>, this)" 
                            class="status-select status-<?php echo $row['status'] ?? 'pending'; ?>"
                            data-original-value="<?php echo $row['status'] ?? 'pending'; ?>"
                        >
                            <option value="pending" <?php echo ($row['status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="reviewed" <?php echo ($row['status'] ?? 'pending') === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                            <option value="accepted" <?php echo ($row['status'] ?? 'pending') === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                            <option value="rejected" <?php echo ($row['status'] ?? 'pending') === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </td>
                    <td class="action-buttons">
                        <a href="view_application.php?id=<?php echo $row['id']; ?>" class="btn btn-view">View</a>
                        <a href="<?php echo htmlspecialchars($row['resume_path']); ?>" class="btn btn-download" download>Resume</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <script>
    async function updateStatus(id, element) {
        try {
            const response = await fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&status=${element.value}`
            });

            const data = await response.json();
            
            if (data.success) {
                // Update the select element's class
                element.className = `status-select status-${element.value}`;
                element.setAttribute('data-original-value', element.value);
            } else {
                alert('Error updating status: ' + (data.error || 'Unknown error'));
                element.value = element.getAttribute('data-original-value');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error updating status. Please try again.');
            element.value = element.getAttribute('data-original-value');
        }
    }

    function updateFilters(search, status) {
        let url = new URL(window.location.href);
        if (search !== null) url.searchParams.set('search', search);
        if (status !== null) url.searchParams.set('status', status);
        window.location.href = url.toString();
    }

    function sortBy(column) {
        let url = new URL(window.location.href);
        let currentSort = url.searchParams.get('sort');
        let currentOrder = url.searchParams.get('order');
        
        if (currentSort === column) {
            url.searchParams.set('order', currentOrder === 'ASC' ? 'DESC' : 'ASC');
        } else {
            url.searchParams.set('sort', column);
            url.searchParams.set('order', 'ASC');
        }
        
        window.location.href = url.toString();
    }

    function updateStatus(id, element) {
        const newStatus = element.value;
        if (newStatus && ['pending', 'reviewed', 'accepted', 'rejected'].includes(newStatus.toLowerCase())) {
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&status=${newStatus.toLowerCase()}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error updating status');
                    element.value = element.getAttribute('data-original-value');
                }
            })
            .catch(error => {
                alert('Error updating status');
                element.value = element.getAttribute('data-original-value');
            });
        }
    }
    </script>

    <footer>
        <p>&copy; 2025 Accentia. All rights reserved.</p>
    </footer>
</body>
</html>

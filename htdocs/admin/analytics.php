<?php
session_start();
require_once '../includes/config.php';

// Authenticate: Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// --- Fetch Analytics Data ---

// 1. Key Metrics
$total_views = $conn->query("SELECT COUNT(id) AS total FROM analytics")->fetch_assoc()['total'] ?? 0;
$unique_visitors = $conn->query("SELECT COUNT(DISTINCT ip_address) AS total FROM analytics")->fetch_assoc()['total'] ?? 0;
$total_downloads = $conn->query("SELECT SUM(download_count) AS total FROM downloads")->fetch_assoc()['total'] ?? 0;
$contact_submissions = $conn->query("SELECT COUNT(id) AS total FROM contact_submissions")->fetch_assoc()['total'] ?? 0;

// 2. Most Visited Pages
$popular_pages = [];
$result = $conn->query("SELECT page_visited, COUNT(id) AS views FROM analytics GROUP BY page_visited ORDER BY views DESC LIMIT 10");
if ($result) {
    $popular_pages = $result->fetch_all(MYSQLI_ASSOC);
}

// 3. Recent Activity
$recent_activity = [];
$result = $conn->query("SELECT ip_address, page_visited, created_at FROM analytics ORDER BY created_at DESC LIMIT 20");
if ($result) {
    $recent_activity = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Analytics</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f4f4; margin: 0; }
        .header { background-color: #333; color: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .header a { color: #fff; text-decoration: none; }
        .container { max-width: 1200px; margin: 30px auto; padding: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .stat-card .value { font-size: 2.5rem; font-weight: bold; color: #007bff; }
        .stat-card .label { font-size: 1rem; color: #6c757d; margin-top: 5px; }
        .section { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h2 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Site Analytics</h1>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <div class="container">
        <!-- Key Metrics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="value"><?php echo $total_views; ?></div>
                <div class="label">Total Page Views</div>
            </div>
            <div class="stat-card">
                <div class="value"><?php echo $unique_visitors; ?></div>
                <div class="label">Unique Visitors</div>
            </div>
            <div class="stat-card">
                <div class="value"><?php echo $total_downloads; ?></div>
                <div class="label">Total Downloads</div>
            </div>
            <div class="stat-card">
                <div class="value"><?php echo $contact_submissions; ?></div>
                <div class="label">Contact Submissions</div>
            </div>
        </div>

        <!-- Data Tables -->
        <div class="section">
            <h2>Most Popular Pages</h2>
            <table>
                <thead><tr><th>Page URL</th><th>Views</th></tr></thead>
                <tbody>
                    <?php foreach ($popular_pages as $page): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($page['page_visited']); ?></td>
                        <td><?php echo htmlspecialchars($page['views']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Recent Visitor Activity</h2>
            <table>
                <thead><tr><th>IP Address</th><th>Page Visited</th><th>Timestamp</th></tr></thead>
                <tbody>
                    <?php foreach ($recent_activity as $activity): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($activity['ip_address']); ?></td>
                        <td><?php echo htmlspecialchars($activity['page_visited']); ?></td>
                        <td><?php echo htmlspecialchars($activity['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
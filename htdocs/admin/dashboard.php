<?php
session_start();

// If the user is not logged in, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// A simple welcome message
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f4f4; margin: 0; }
        .header { background-color: #333; color: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .header a { color: #fff; text-decoration: none; background-color: #007bff; padding: 8px 15px; border-radius: 4px; }
        .container { padding: 30px; }
        .welcome { font-size: 1.2rem; margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .card h2 { margin-top: 0; }
        .card a { text-decoration: none; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <p class="welcome">Welcome, <?php echo $username; ?>!</p>
        <p>From here you can manage all the content on your portfolio website.</p>

        <div class="grid">
            <div class="card">
                <h2>About Me</h2>
                <p>Edit your biography and philosophy.</p>
                <a href="manage_about.php">Manage About</a>
            </div>
            <div class="card">
                <h2>Education</h2>
                <p>Update your educational background.</p>
                <a href="manage_education.php">Manage Education</a>
            </div>
            <div class="card">
                <h2>Experience</h2>
                <p>Add or remove work experiences.</p>
                <a href="manage_experience.php">Manage Experience</a>
            </div>
            <div class="card">
                <h2>Skills</h2>
                <p>Showcase your soft and hard skills.</p>
                <a href="manage_skills.php">Manage Skills</a>
            </div>
            <div class="card">
                <h2>Projects</h2>
                <p>Manage your project portfolio.</p>
                <a href="manage_projects.php">Manage Projects</a>
            </div>
            <div class="card">
                <h2>Testimonials</h2>
                <p>Add or update client testimonials.</p>
                <a href="manage_testimonials.php">Manage Testimonials</a>
            </div>
             <div class="card">
                <h2>Downloads</h2>
                <p>Manage downloadable files.</p>
                <a href="manage_downloads.php">Manage Downloads</a>
            </div>
             <div class="card">
                <h2>Site Analytics</h2>
                <p>View visitor and download logs.</p>
                <a href="analytics.php">View Analytics</a>
            </div>
        </div>
    </div>
</body>
</html>
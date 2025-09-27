<?php
// Check if the config file exists. If not, redirect to the installer.
if (!file_exists('php/db_connect.php')) {
    header('Location: install.php');
    exit;
}

require_once "php/db_connect.php";

// Fetch all portfolio data in one go
$portfolio_data = [];

// Home
$result = mysqli_query($link, "SELECT * FROM home LIMIT 1");
$portfolio_data['home'] = mysqli_fetch_assoc($result) ?: [];

// About
$result = mysqli_query($link, "SELECT * FROM about LIMIT 1");
$portfolio_data['about'] = mysqli_fetch_assoc($result) ?: [];

// Education
$result = mysqli_query($link, "SELECT * FROM education ORDER BY display_order ASC, year DESC");
$portfolio_data['education'] = mysqli_fetch_all($result, MYSQLI_ASSOC) ?: [];

// Experience
$result = mysqli_query($link, "SELECT * FROM experience ORDER BY display_order ASC, year_range DESC");
$portfolio_data['experience'] = mysqli_fetch_all($result, MYSQLI_ASSOC) ?: [];

// Skills
$result = mysqli_query($link, "SELECT * FROM skills WHERE type = 'Soft Skill' ORDER BY display_order ASC");
$portfolio_data['skills_soft'] = mysqli_fetch_all($result, MYSQLI_ASSOC) ?: [];
$result = mysqli_query($link, "SELECT * FROM skills WHERE type = 'Hard Skill' ORDER BY display_order ASC");
$portfolio_data['skills_hard'] = mysqli_fetch_all($result, MYSQLI_ASSOC) ?: [];

// Projects
$result = mysqli_query($link, "SELECT * FROM projects ORDER BY display_order ASC");
$portfolio_data['projects'] = mysqli_fetch_all($result, MYSQLI_ASSOC) ?: [];

// Testimonials
$result = mysqli_query($link, "SELECT * FROM testimonials ORDER BY display_order ASC");
$portfolio_data['testimonials'] = mysqli_fetch_all($result, MYSQLI_ASSOC) ?: [];

// Downloads
$result = mysqli_query($link, "SELECT id, filename, is_protected FROM downloads ORDER BY filename ASC");
$portfolio_data['downloads'] = mysqli_fetch_all($result, MYSQLI_ASSOC) ?: [];

// Contact Info
$result = mysqli_query($link, "SELECT * FROM contact_info LIMIT 1");
$portfolio_data['contact_info'] = mysqli_fetch_assoc($result) ?: [];

// Handle Contact Form Submission
$contact_form_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);
            if (mysqli_stmt_execute($stmt)) {
                $contact_form_msg = "Message sent successfully! Thank you.";
            } else {
                $contact_form_msg = "Error: Could not send message.";
            }
        }
    } else {
        $contact_form_msg = "Error: Please fill all fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($portfolio_data['home']['name'] ?? 'Portfolio'); ?> - Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Mobile Top Bar -->
    <header class="top-bar">
        <button id="menu-toggle" class="menu-toggle">☰</button>
        <h1 class="portfolio-title"><?php echo htmlspecialchars($portfolio_data['home']['name'] ?? 'Portfolio'); ?></h1>
        <button id="theme-toggle" class="theme-toggle">🌙</button>
    </header>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="<?php echo htmlspecialchars($portfolio_data['home']['profile_pic_url'] ?? 'assets/profile-pic.jpg'); ?>" alt="Profile Picture" class="profile-pic">
            <h2><?php echo htmlspecialchars($portfolio_data['home']['name'] ?? 'Jane Doe'); ?></h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#home" class="nav-link active"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#about" class="nav-link"><i class="fas fa-user"></i> About Me</a></li>
                <li><a href="#education" class="nav-link"><i class="fas fa-graduation-cap"></i> Education</a></li>
                <li><a href="#experience" class="nav-link"><i class="fas fa-briefcase"></i> Experience</a></li>
                <li><a href="#skills" class="nav-link"><i class="fas fa-cogs"></i> Skills</a></li>
                <li><a href="#projects" class="nav-link"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="#testimonials" class="nav-link"><i class="fas fa-comments"></i> Testimonials</a></li>
                <li><a href="#downloads" class="nav-link"><i class="fas fa-download"></i> Downloads</a></li>
                <li><a href="#contact" class="nav-link"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="admin/" class="admin-login"><i class="fas fa-cog"></i> Admin Login</a>
        </div>
    </aside>

    <!-- Main Content Panel -->
    <main class="main-panel">
        <section id="home" class="panel hero">
            <div class="hero-content">
                <h1><?php echo htmlspecialchars($portfolio_data['home']['name'] ?? 'Jane Doe'); ?></h1>
                <p>"<?php echo htmlspecialchars($portfolio_data['home']['tagline'] ?? 'Inspiring young minds through patience'); ?>"</p>
                <div class="hero-buttons">
                    <a href="#downloads" class="btn-primary">View Resume</a>
                    <a href="#contact" class="btn-secondary">Get in Touch</a>
                </div>
            </div>
        </section>

        <section id="about" class="panel">
            <h2>About Me</h2>
            <div class="about-content">
                <p><?php echo nl2br(htmlspecialchars($portfolio_data['about']['bio'] ?? 'Bio not available.')); ?></p>
                <h3>Education Philosophy</h3>
                <p><?php echo nl2br(htmlspecialchars($portfolio_data['about']['philosophy'] ?? 'Philosophy not available.')); ?></p>
                <?php if (!empty($portfolio_data['about']['video_embed_url'])): ?>
                <div class="video-container">
                    <iframe src="<?php echo htmlspecialchars($portfolio_data['about']['video_embed_url']); ?>" frameborder="0" allowfullscreen></iframe>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <section id="education" class="panel">
            <h2>Education</h2>
            <div class="timeline">
                <?php foreach ($portfolio_data['education'] as $edu): ?>
                <div class="timeline-item">
                    <div class="timeline-year"><?php echo htmlspecialchars($edu['year']); ?></div>
                    <div class="timeline-content">
                        <h3><?php echo htmlspecialchars($edu['degree']); ?></h3>
                        <h4><?php echo htmlspecialchars($edu['institution']); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="experience" class="panel">
            <h2>Experience</h2>
            <div class="timeline">
                <?php foreach ($portfolio_data['experience'] as $exp): ?>
                <div class="timeline-item">
                    <div class="timeline-year"><?php echo htmlspecialchars($exp['year_range']); ?></div>
                    <div class="timeline-content">
                        <h3><?php echo htmlspecialchars($exp['position']); ?></h3>
                        <h4><?php echo htmlspecialchars($exp['institution']); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="skills" class="panel">
            <h2>Skills</h2>
            <div class="skills-container">
                <div class="skills-column">
                    <h3>Hard Skills</h3>
                    <?php foreach ($portfolio_data['skills_hard'] as $skill): ?>
                    <div class="skill-bar">
                        <div class="skill-info">
                            <span><?php echo htmlspecialchars($skill['name']); ?></span>
                            <span><?php echo $skill['proficiency']; ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo $skill['proficiency']; ?>%;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="skills-column">
                    <h3>Soft Skills</h3>
                    <?php foreach ($portfolio_data['skills_soft'] as $skill): ?>
                    <div class="skill-bar">
                        <div class="skill-info">
                            <span><?php echo htmlspecialchars($skill['name']); ?></span>
                        </div>
                        <div class="progress-bar">
                             <div class="progress" style="width: <?php echo $skill['proficiency']; ?>%;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="projects" class="panel">
            <h2>Projects</h2>
            <div class="projects-grid">
                <?php foreach ($portfolio_data['projects'] as $project): ?>
                <div class="project-card">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                    <!-- Image gallery would be implemented here -->
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="testimonials" class="panel">
            <h2>Testimonials</h2>
            <div class="testimonial-slider">
                <?php foreach ($portfolio_data['testimonials'] as $testimonial): ?>
                <div class="testimonial-item">
                    <blockquote>"<?php echo nl2br(htmlspecialchars($testimonial['quote'])); ?>"</blockquote>
                    <cite>- <?php echo htmlspecialchars($testimonial['author']); ?>, <?php echo htmlspecialchars($testimonial['position']); ?></cite>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="downloads" class="panel">
            <h2>Download Center</h2>
            <div class="download-list">
                <?php foreach ($portfolio_data['downloads'] as $download): ?>
                <div class="download-item">
                    <span><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($download['filename']); ?></span>
                    <a href="php/download_handler.php?id=<?php echo $download['id']; ?>" class="btn-primary">Download</a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="contact" class="panel">
            <h2>Contact Me</h2>
            <div class="contact-container">
                <div class="contact-info">
                    <?php $info = $portfolio_data['contact_info']; ?>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($info['address'] ?? ''); ?></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($info['email'] ?? ''); ?>"><?php echo htmlspecialchars($info['email'] ?? ''); ?></a></p>
                    <div class="social-links">
                        <?php if(!empty($info['facebook'])) echo '<a href="'.htmlspecialchars($info['facebook']).'"><i class="fab fa-facebook"></i></a>'; ?>
                        <?php if(!empty($info['tiktok'])) echo '<a href="'.htmlspecialchars($info['tiktok']).'"><i class="fab fa-tiktok"></i></a>'; ?>
                        <?php if(!empty($info['youtube'])) echo '<a href="'.htmlspecialchars($info['youtube']).'"><i class="fab fa-youtube"></i></a>'; ?>
                        <?php if(!empty($info['instagram'])) echo '<a href="'.htmlspecialchars($info['instagram']).'"><i class="fab fa-instagram"></i></a>'; ?>
                    </div>
                </div>
                <div class="contact-form">
                    <?php if ($contact_form_msg): ?>
                        <div class="form-message"><?php echo $contact_form_msg; ?></div>
                    <?php endif; ?>
                    <form action="#contact" method="post">
                        <input type="text" name="name" placeholder="Your Name" required>
                        <input type="email" name="email" placeholder="Your Email" required>
                        <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
                        <button type="submit" name="send_message" class="btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script src="js/script.js"></script>
</body>
</html>
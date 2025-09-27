<?php
// --- DATABASE CONNECTION & DATA FETCHING ---
// Note: This will show errors if the database isn't set up yet.
// This is expected until the .sql file is imported into your database.
@require_once 'php/db_connect.php';

// --- Initialize variables with default values ---
$site_settings = ['site_title' => 'Portfolio', 'hero_name' => 'Your Name', 'hero_tagline' => 'Your Professional Tagline', 'hero_photo_url' => 'images/profile.jpg'];
$about = ['bio' => 'Bio not available.', 'philosophy' => 'Philosophy not available.', 'video_embed_url' => ''];
$education = [];
$experience = [];
$skills = ['Soft Skill' => [], 'Hard Skill' => []];
$projects = [];
$testimonials = [];
$downloads = [];
$contact_info = ['address' => 'Address not available', 'email' => 'email@not.available', 'facebook_url' => '#', 'tiktok_url' => '#', 'youtube_url' => '#', 'instagram_url' => '#'];

// Check if the database connection exists before querying
if (isset($mysqli) && $mysqli->ping()) {
    // Fetch Site Settings
    $result = $mysqli->query("SELECT * FROM site_settings WHERE id = 1");
    if ($result && $result->num_rows > 0) { $site_settings = $result->fetch_assoc(); }

    // Fetch About section
    $result = $mysqli->query("SELECT * FROM about WHERE id = 1");
    if ($result && $result->num_rows > 0) { $about = $result->fetch_assoc(); }

    // Fetch Education
    $result = $mysqli->query("SELECT * FROM education ORDER BY display_order ASC, year DESC");
    if($result) { while ($row = $result->fetch_assoc()) { $education[] = $row; } }

    // Fetch Experience
    $result = $mysqli->query("SELECT * FROM experience ORDER BY display_order ASC, year_range DESC");
    if($result) { while ($row = $result->fetch_assoc()) { $experience[] = $row; } }

    // Fetch Skills
    $result = $mysqli->query("SELECT * FROM skills ORDER BY type, name");
    if($result) { while ($row = $result->fetch_assoc()) { $skills[$row['type']][] = $row; } }

    // Fetch Projects
    $result = $mysqli->query("SELECT * FROM projects ORDER BY display_order ASC");
    if($result) { while ($row = $result->fetch_assoc()) { $projects[] = $row; } }

    // Fetch Testimonials
    $result = $mysqli->query("SELECT * FROM testimonials ORDER BY display_order ASC");
    if($result) { while ($row = $result->fetch_assoc()) { $testimonials[] = $row; } }

    // Fetch Downloads
    $result = $mysqli->query("SELECT * FROM downloads ORDER BY file_name ASC");
    if($result) { while ($row = $result->fetch_assoc()) { $downloads[] = $row; } }

    // Fetch Contact Info
    $result = $mysqli->query("SELECT * FROM contact_info WHERE id = 1");
    if ($result && $result->num_rows > 0) { $contact_info = $result->fetch_assoc(); }

    // Close the connection
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_settings['site_title']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Mobile Top Bar -->
    <div class="mobile-top-bar">
        <button class="menu-toggle" id="menu-toggle" aria-label="Open navigation menu"><i class="fas fa-bars"></i></button>
        <h1 class="portfolio-title-mobile">J.D.</h1>
        <button class="theme-toggle" id="theme-toggle-mobile" aria-label="Toggle light/dark mode"><i class="fas fa-sun"></i></button>
    </div>

    <!-- Sidebar / Drawer Navigation -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="<?php echo htmlspecialchars($site_settings['hero_photo_url']); ?>" alt="Profile Picture" class="profile-pic">
            <h1 class="portfolio-title-desktop"><?php echo htmlspecialchars($site_settings['hero_name']); ?></h1>
        </div>
        <ul class="nav-links">
            <li><a href="#home"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#about"><i class="fas fa-user"></i> About Me</a></li>
            <li><a href="#education"><i class="fas fa-graduation-cap"></i> Education</a></li>
            <li><a href="#experience"><i class="fas fa-briefcase"></i> Experience</a></li>
            <li><a href="#skills"><i class="fas fa-cogs"></i> Skills</a></li>
            <li><a href="#projects"><i class="fas fa-project-diagram"></i> Projects</a></li>
            <li><a href="#testimonials"><i class="fas fa-comments"></i> Testimonials</a></li>
            <li><a href="#downloads"><i class="fas fa-download"></i> Downloads</a></li>
            <li><a href="#contact"><i class="fas fa-envelope"></i> Contact</a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="admin/" class="admin-login-link"><i class="fas fa-cog"></i> Admin Login</a>
            <button class="theme-toggle" id="theme-toggle-desktop" aria-label="Toggle light/dark mode"><i class="fas fa-sun"></i></button>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-panel">
        <section id="home" class="panel hero-section">
            <div class="hero-content">
                <img src="<?php echo htmlspecialchars($site_settings['hero_photo_url']); ?>" alt="Profile Photo" class="hero-photo">
                <h1 class="hero-name"><?php echo htmlspecialchars($site_settings['hero_name']); ?></h1>
                <p class="hero-tagline"><?php echo htmlspecialchars($site_settings['hero_tagline']); ?></p>
                <div class="hero-buttons"><a href="#downloads" class="btn btn-primary">View Resume</a><a href="#contact" class="btn btn-secondary">Contact Me</a></div>
            </div>
        </section>

        <section id="about" class="panel">
            <h2>About Me</h2>
            <div class="content-card">
                <p><?php echo nl2br(htmlspecialchars($about['bio'])); ?></p>
                <h3>My Philosophy</h3>
                <p><?php echo nl2br(htmlspecialchars($about['philosophy'])); ?></p>
                <?php if (!empty($about['video_embed_url'])): ?>
                <div class="video-container">
                    <iframe src="<?php echo htmlspecialchars($about['video_embed_url']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <section id="education" class="panel">
            <h2>Education</h2>
            <div class="content-card table-container">
                <table>
                    <thead><tr><th>Year</th><th>Degree</th><th>Institution</th><th>Description</th></tr></thead>
                    <tbody>
                        <?php if (empty($education)): ?>
                            <tr><td colspan="4">No education details available. Please add some in the admin panel.</td></tr>
                        <?php else: ?>
                            <?php foreach ($education as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['year']); ?></td>
                                <td><?php echo htmlspecialchars($item['degree']); ?></td>
                                <td><?php echo htmlspecialchars($item['institution']); ?></td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="experience" class="panel">
            <h2>Experience</h2>
            <div class="timeline">
                <?php if (empty($experience)): ?>
                    <div class="content-card"><p>No experience details available. Please add some in the admin panel.</p></div>
                <?php else: ?>
                    <?php foreach ($experience as $item): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content content-card">
                            <h3><?php echo htmlspecialchars($item['position']); ?></h3>
                            <span class="timeline-date"><?php echo htmlspecialchars($item['year_range']); ?> | <?php echo htmlspecialchars($item['institution']); ?></span>
                            <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section id="skills" class="panel">
            <h2>Skills</h2>
            <div class="skills-container">
                <div class="skill-category content-card">
                    <h3>Hard Skills</h3>
                    <ul class="skill-list">
                        <?php if (empty($skills['Hard Skill'])): ?><li>No hard skills listed.</li><?php else: ?>
                            <?php foreach ($skills['Hard Skill'] as $skill): ?><li><?php echo htmlspecialchars($skill['name']); ?></li><?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="skill-category content-card">
                    <h3>Soft Skills</h3>
                    <ul class="skill-list">
                         <?php if (empty($skills['Soft Skill'])): ?><li>No soft skills listed.</li><?php else: ?>
                            <?php foreach ($skills['Soft Skill'] as $skill): ?><li><?php echo htmlspecialchars($skill['name']); ?></li><?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </section>

        <section id="projects" class="panel">
            <h2>Projects</h2>
            <div class="projects-grid">
                <?php if (empty($projects)): ?>
                    <div class="content-card"><p>No projects available. Please add some in the admin panel.</p></div>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                    <div class="project-card content-card">
                        <img src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <div class="project-info">
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <p><?php echo htmlspecialchars($project['description']); ?></p>
                            <?php if (!empty($project['project_link'])): ?>
                                <a href="<?php echo htmlspecialchars($project['project_link']); ?>" target="_blank" class="btn">View Project</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section id="testimonials" class="panel">
            <h2>Testimonials</h2>
            <div class="testimonial-slider">
                <?php if (empty($testimonials)): ?>
                    <div class="content-card"><p>No testimonials available.</p></div>
                <?php else: ?>
                    <?php foreach ($testimonials as $testimonial): ?>
                    <div class="testimonial-item content-card">
                        <blockquote>“<?php echo htmlspecialchars($testimonial['quote']); ?>”</blockquote>
                        <cite>— <?php echo htmlspecialchars($testimonial['author']); ?>, <?php echo htmlspecialchars($testimonial['author_position']); ?></cite>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section id="downloads" class="panel">
            <h2>Download Center</h2>
            <div class="content-card">
                <div class="download-list">
                    <?php if (empty($downloads)): ?>
                        <p>No downloadable files available.</p>
                    <?php else: ?>
                        <?php foreach ($downloads as $download): ?>
                        <div class="download-item">
                            <span><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($download['file_name']); ?></span>
                            <form action="php/download_handler.php" method="POST" class="download-form">
                                <input type="hidden" name="file_id" value="<?php echo $download['id']; ?>">
                                <input type="password" name="password" placeholder="Password" required>
                                <button type="submit" class="btn">Download</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section id="contact" class="panel">
            <h2>Contact</h2>
            <div class="content-card">
                <div class="contact-container">
                    <div class="contact-info">
                        <h3>Get in Touch</h3>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($contact_info['address']); ?></p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($contact_info['email']); ?>"><?php echo htmlspecialchars($contact_info['email']); ?></a></p>
                        <div class="social-links">
                            <a href="<?php echo htmlspecialchars($contact_info['facebook_url']); ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                            <a href="<?php echo htmlspecialchars($contact_info['tiktok_url']); ?>" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                            <a href="<?php echo htmlspecialchars($contact_info['youtube_url']); ?>" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                            <a href="<?php echo htmlspecialchars($contact_info['instagram_url']); ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <div class="contact-form">
                        <h3>Send a Message</h3>
                        <form action="php/contact_handler.php" method="POST">
                            <input type="text" name="name" placeholder="Your Name" required>
                            <input type="email" name="email" placeholder="Your Email" required>
                            <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="js/main.js"></script>
</body>
</html>
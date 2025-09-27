<?php
// Fetch about content from the database
// Placeholder data for now
$bio = "A passionate and dedicated educator with over ten years of experience in fostering inclusive and engaging learning environments. My goal is to empower students to become critical thinkers and lifelong learners.";
$philosophy = "I believe in a student-centered approach where curiosity is encouraged, and learning is tailored to individual needs. Every student has the potential to succeed, and my role is to provide the guidance and resources to help them achieve it.";
$video_url = "https://www.youtube.com/embed/your_video_id"; // Example YouTube embed URL
?>

<section id="about">
    <h2>About Me</h2>
    <div class="about-content">
        <div class="about-text">
            <h3>Biography</h3>
            <p><?php echo htmlspecialchars($bio); ?></p>
            <h3>My Philosophy</h3>
            <p><?php echo htmlspecialchars($philosophy); ?></p>
        </div>
        <?php if (!empty($video_url)): ?>
        <div class="about-video">
            <h3>Introduction Video</h3>
            <div class="video-container">
                <iframe src="<?php echo htmlspecialchars($video_url); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
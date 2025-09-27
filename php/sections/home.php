<?php
// Fetch home content from the database
// For now, we'll use placeholder data
$owner_name = "Ms. Jane Doe";
$tagline = "Inspiring young minds through patience";
$profile_pic = "images/profile.jpg";
?>

<section id="home" class="hero-section">
    <div class="hero-content">
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Photo" class="hero-photo">
        <h1 class="hero-name"><?php echo htmlspecialchars($owner_name); ?></h1>
        <p class="hero-tagline">“<?php echo htmlspecialchars($tagline); ?>”</p>
        <div class="hero-buttons">
            <a href="#downloads" class="btn btn-primary">View Resume</a>
            <a href="#contact" class="btn btn-secondary">Contact Me</a>
        </div>
    </div>
    <div class="animated-background"></div>
</section>
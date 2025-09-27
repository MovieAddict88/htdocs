<?php
// We will need to require the database connection at the top of the main index.php
// For now, assume $pdo is available.
// In the final version, index.php will handle the connection and this file will just use the $pdo variable.
if (!isset($pdo)) {
    // This is a fallback for direct access, but shouldn't happen in production
    require_once 'includes/database.php';
    $pdo = get_db_connection();
}

try {
    $stmt = $pdo->query("SELECT bio, philosophy, video_url FROM about LIMIT 1");
    $about_data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error gracefully on the frontend
    $about_data = [
        'bio' => 'Could not load biography at this time.',
        'philosophy' => 'Could not load philosophy at this time.',
        'video_url' => ''
    ];
}

// Function to convert YouTube watch URL to embed URL
function get_youtube_embed_url($url) {
    if (preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[2];
    }
    return ''; // Return empty if not a valid YouTube URL
}

$embed_url = !empty($about_data['video_url']) ? get_youtube_embed_url($about_data['video_url']) : '';
?>

<section id="about">
    <div class="section-container">
        <h2 class="section-title">About Me</h2>
        <div class="about-content">
            <div class="about-text">
                <h3>My Biography</h3>
                <p><?php echo nl2br(htmlspecialchars($about_data['bio'] ?? 'No biography provided.')); ?></p>

                <h3>My Philosophy</h3>
                <p><?php echo nl2br(htmlspecialchars($about_data['philosophy'] ?? 'No philosophy provided.')); ?></p>
            </div>

            <?php if ($embed_url): ?>
            <div class="about-video">
                <h3>Introductory Video</h3>
                <div class="video-wrapper">
                    <iframe
                        width="560"
                        height="315"
                        src="<?php echo htmlspecialchars($embed_url); ?>"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
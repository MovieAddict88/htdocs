<?php
// Fetch downloadable files from the database
// Placeholder data
$downloads = [
    [
        'id' => 1,
        'display_name' => 'Resume.pdf',
        'filename' => 'resume_protected.pdf',
        'password_protected' => true
    ],
    [
        'id' => 2,
        'display_name' => 'Clearance.pdf',
        'filename' => 'clearance_protected.pdf',
        'password_protected' => true
    ]
];
?>

<section id="downloads">
    <h2>Download Center</h2>
    <div class="downloads-container">
        <?php foreach ($downloads as $download): ?>
        <div class="download-item">
            <span class="download-name"><?php echo htmlspecialchars($download['display_name']); ?></span>
            <form class="download-form" action="php/download_handler.php" method="POST">
                <input type="hidden" name="file_id" value="<?php echo $download['id']; ?>">
                <?php if ($download['password_protected']): ?>
                    <input type="password" name="password" placeholder="Enter Password" required>
                <?php endif; ?>
                <button type="submit" class="btn">Download</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php
// This logic assumes $pdo is available from the main index.php file.
if (!isset($pdo)) {
    require_once 'includes/database.php';
    $pdo = get_db_connection();
}

try {
    $stmt = $pdo->query("SELECT year, degree, institution, description FROM education ORDER BY year DESC");
    $education_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gracefully handle the error on the frontend
    $education_entries = [];
    echo '<p>Could not load education history at this time.</p>';
}
?>

<section id="education">
    <div class="section-container">
        <h2 class="section-title">Education</h2>

        <?php if (empty($education_entries)): ?>
            <p style="text-align:center;">No educational history has been added yet.</p>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($education_entries as $entry): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-year"><?php echo htmlspecialchars($entry['year']); ?></div>
                            <h3 class="timeline-title"><?php echo htmlspecialchars($entry['degree']); ?></h3>
                            <h4 class="timeline-subtitle"><?php echo htmlspecialchars($entry['institution']); ?></h4>
                            <?php if (!empty($entry['description'])): ?>
                                <p class="timeline-description">
                                    <?php echo nl2br(htmlspecialchars($entry['description'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
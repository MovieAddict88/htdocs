<?php
// Fetch education data from the database
// Placeholder data for now
$education = [
    ['year' => '2012', 'degree' => 'Master of Education', 'institution' => 'University of Learning', 'description' => 'Focused on curriculum development and educational technology.'],
    ['year' => '2010', 'degree' => 'Bachelor of Arts in English', 'institution' => 'State College', 'description' => 'Graduated with honors.'],
];
?>

<section id="education">
    <h2>Education</h2>
    <div class="education-container">
        <?php foreach ($education as $edu): ?>
        <div class="education-item">
            <div class="education-header">
                <span class="education-year"><?php echo htmlspecialchars($edu['year']); ?></span>
                <h3 class="education-degree"><?php echo htmlspecialchars($edu['degree']); ?></h3>
                <p class="education-institution"><?php echo htmlspecialchars($edu['institution']); ?></p>
            </div>
            <p class="education-description"><?php echo htmlspecialchars($edu['description']); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>
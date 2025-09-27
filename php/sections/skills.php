<?php
// Fetch skills from the database, categorized
// Placeholder data
$skills = [
    'soft' => ['Communication', 'Creativity', 'Patience', 'Teamwork', 'Problem Solving'],
    'hard' => ['Lesson Planning', 'Technology Integration', 'Classroom Management', 'Curriculum Design', 'Student Assessment']
];
?>

<section id="skills">
    <h2>Skills</h2>
    <div class="skills-container">
        <div class="skills-category">
            <h3>Soft Skills</h3>
            <ul class="skills-list">
                <?php foreach ($skills['soft'] as $skill): ?>
                    <li><?php echo htmlspecialchars($skill); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="skills-category">
            <h3>Hard Skills</h3>
            <ul class="skills-list">
                <?php foreach ($skills['hard'] as $skill): ?>
                    <li><?php echo htmlspecialchars($skill); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
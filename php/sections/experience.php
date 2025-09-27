<?php
// Fetch experience data from the database
// Placeholder data for now
$experience = [
    [
        'year_range' => '2015 - Present',
        'position' => 'Lead Teacher',
        'institution' => 'Maplewood Elementary School',
        'description' => 'Developed and implemented innovative lesson plans for 3rd-grade students. Mentored new teachers and led professional development workshops.'
    ],
    [
        'year_range' => '2012 - 2015',
        'position' => 'English Teacher',
        'institution' => 'Oakwood Middle School',
        'description' => 'Taught English literature and composition to students in grades 6-8. Organized the annual school-wide spelling bee.'
    ],
];
?>

<section id="experience">
    <h2>Experience</h2>
    <div class="experience-timeline">
        <?php foreach ($experience as $exp): ?>
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <span class="timeline-date"><?php echo htmlspecialchars($exp['year_range']); ?></span>
                <h3 class="timeline-title"><?php echo htmlspecialchars($exp['position']); ?></h3>
                <p class="timeline-institution"><?php echo htmlspecialchars($exp['institution']); ?></p>
                <p class="timeline-description"><?php echo htmlspecialchars($exp['description']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php
// Fetch projects from the database
// Placeholder data
$projects = [
    [
        'title' => 'Interactive Science Fair Guide',
        'description' => 'A web-based application to help students organize and present their science fair projects. Built with HTML, CSS, and JavaScript.',
        'image_album' => ['images/project1.jpg', 'images/project2.jpg'],
        'external_link' => '#'
    ],
    [
        'title' => 'Classroom Reading Challenge',
        'description' => 'A platform to track and reward students for their reading progress throughout the school year.',
        'image_album' => ['images/project3.jpg'],
        'external_link' => '#'
    ]
];
?>

<section id="projects">
    <h2>Projects</h2>
    <div class="projects-container">
        <?php foreach ($projects as $project): ?>
        <div class="project-card">
            <div class="project-images">
                <!-- In a real implementation, this would be a swipeable album -->
                <img src="<?php echo htmlspecialchars($project['image_album'][0]); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
            </div>
            <div class="project-info">
                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                <p><?php echo htmlspecialchars($project['description']); ?></p>
                <?php if (!empty($project['external_link'])): ?>
                    <a href="<?php echo htmlspecialchars($project['external_link']); ?>" class="btn" target="_blank">View Project</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
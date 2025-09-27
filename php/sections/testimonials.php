<?php
// Fetch testimonials from the database
// Placeholder data
$testimonials = [
    [
        'quote' => 'Jane has a remarkable ability to connect with students and make learning exciting. Her classroom is a place of joy and discovery.',
        'author' => 'Prof. Smith',
        'author_role' => 'Mentor'
    ],
    [
        'quote' => 'Ms. Doe was my favorite teacher. She was always patient and willing to help me when I was struggling with a concept.',
        'author' => 'John A.',
        'author_role' => 'Former Student'
    ]
];
?>

<section id="testimonials">
    <h2>Testimonials</h2>
    <div class="testimonial-slider">
        <!-- In a real implementation, this would be a JS-powered carousel -->
        <?php foreach ($testimonials as $testimonial): ?>
        <div class="testimonial-item">
            <blockquote class="testimonial-quote">
                “<?php echo htmlspecialchars($testimonial['quote']); ?>”
            </blockquote>
            <cite class="testimonial-author">
                - <?php echo htmlspecialchars($testimonial['author']); ?>, <?php echo htmlspecialchars($testimonial['author_role']); ?>
            </cite>
        </div>
        <?php endforeach; ?>
    </div>
</section>
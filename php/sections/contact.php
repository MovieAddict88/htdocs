<?php
// Placeholder data for contact info
$contact_info = [
    'address' => '123 Example St, City',
    'email' => 'janedoe@email.com',
    'facebook' => 'fb.com/janedoe',
    'tiktok' => 'tiktok.com/@janedoe',
    'youtube' => 'youtube.com/janedoe',
    'instagram' => 'instagram.com/janedoe'
];
?>

<section id="contact">
    <h2>Contact Me</h2>
    <div class="contact-container">
        <div class="contact-info">
            <h3>Get in Touch</h3>
            <p>[📍] Address: <?php echo htmlspecialchars($contact_info['address']); ?></p>
            <p>[✉️] Email: <a href="mailto:<?php echo htmlspecialchars($contact_info['email']); ?>"><?php echo htmlspecialchars($contact_info['email']); ?></a></p>
            <div class="social-links">
                <a href="https://<?php echo htmlspecialchars($contact_info['facebook']); ?>" target="_blank">[📘] Facebook</a>
                <a href="https://<?php echo htmlspecialchars($contact_info['tiktok']); ?>" target="_blank">[🎵] TikTok</a>
                <a href="https://<?php echo htmlspecialchars($contact_info['youtube']); ?>" target="_blank">[▶️] YouTube</a>
                <a href="https://<?php echo htmlspecialchars($contact_info['instagram']); ?>" target="_blank">[📸] Instagram</a>
            </div>
        </div>
        <div class="contact-form">
            <h3>Send a Message</h3>
            <form action="php/message_handler.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn">Send Message</button>
            </form>
        </div>
    </div>
</section>
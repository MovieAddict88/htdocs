<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "../php/db_connect.php";

$all_messages = [];
$sql = "SELECT id, name, email, message, sent_at, is_read FROM contact_messages ORDER BY sent_at DESC";
$result = mysqli_query($link, $sql);
if ($result) {
    $all_messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle marking as read/unread
if (isset($_GET['toggle_read'])) {
    $message_id = $_GET['toggle_read'];
    $current_status = $_GET['status'];
    $new_status = $current_status == 1 ? 0 : 1;

    $sql = "UPDATE contact_messages SET is_read = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $new_status, $message_id);
        mysqli_stmt_execute($stmt);
    }
    header("location: view_messages.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Contact Messages</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="crud_style.css">
</head>
<body>
    <div class="dashboard-header">
        <h1>View Contact Messages</h1>
        <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
    </div>

    <div class="crud-container">
        <div class="crud-table">
            <h3>Received Messages</h3>
            <table>
                <thead>
                    <tr>
                        <th>Received At</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_messages as $message): ?>
                    <tr style="<?php echo $message['is_read'] ? 'background-color: #f0f0f0;' : 'font-weight: bold;'; ?>">
                        <td><?php echo htmlspecialchars($message['sent_at']); ?></td>
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><a href="mailto:<?php echo htmlspecialchars($message['email']); ?>"><?php echo htmlspecialchars($message['email']); ?></a></td>
                        <td><p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p></td>
                        <td><?php echo $message['is_read'] ? 'Read' : 'Unread'; ?></td>
                        <td>
                            <a href="view_messages.php?toggle_read=<?php echo $message['id']; ?>&status=<?php echo $message['is_read']; ?>" class="btn-edit">
                                Mark as <?php echo $message['is_read'] ? 'Unread' : 'Read'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($all_messages)): ?>
                    <tr><td colspan="6">No messages found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<style>
p {
    white-space: pre-wrap;
    word-break: break-word;
}
</style>
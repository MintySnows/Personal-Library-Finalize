<?php
session_start();
include 'bookwebsite.php'; // adjust as needed

$bookId = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
$chapter = isset($_GET['chapter']) ? intval($_GET['chapter']) : 1;

// If "continue" param is set, fetch last read chapter for user
if (isset($_GET['continue']) && isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $res = $conn->query("SELECT last_chapter FROM continue_reading WHERE user_id=$userId AND book_id=$bookId");
    if ($row = $res->fetch_assoc()) {
        $chapter = intval($row['last_chapter']);
    }
}

// Fetch chapter content (text or images)
$res = $conn->query("SELECT * FROM chapters WHERE book_id=$bookId AND chapter_number=$chapter");
$chapterData = $res->fetch_assoc();

if (!$chapterData) {
    echo "Chapter not found.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Read Chapter <?php echo $chapter; ?></title>
    <link rel="stylesheet" href="hehe.css">
</head>
<body>
    <div class="chapter-content">
        <h2><?php echo htmlspecialchars($chapterData['title']); ?></h2>
        <div>
            <?php
            // If content is text
            echo nl2br(htmlspecialchars($chapterData['content']));
            // If content is image, you can use:
            // echo '<img src="'.htmlspecialchars($chapterData['image_url']).'" alt="Chapter Image">';
            ?>
        </div>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <a href="admin_pame.php?edit_chapter=<?php echo $chapterData['id']; ?>" style="margin-top:20px; display:inline-block; background:#9c6b3e; color:#fff; padding:8px 18px; border-radius:8px;">Edit Chapter</a>
        <?php endif; ?>
    </div>
</body>
</html>
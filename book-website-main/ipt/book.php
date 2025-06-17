<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Get book id from URL
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Log as recently read if user is logged in
if (isset($_SESSION['user_id']) && $book_id > 0) {
    $user_id = intval($_SESSION['user_id']);
    $conn->query("DELETE FROM recently_read WHERE user_id=$user_id AND book_id=$book_id");
    $conn->query("INSERT INTO recently_read (user_id, book_id, read_at) VALUES ($user_id, $book_id, NOW())");
}

// Handle favorite actions
if (isset($_POST['favorite_book_id']) && isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $favBookId = intval($_POST['favorite_book_id']);
    $conn->query("INSERT IGNORE INTO favorites (user_id, book_id) VALUES ($userId, $favBookId)");
}

if (isset($_POST['unfavorite_book_id']) && isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $unfavBookId = intval($_POST['unfavorite_book_id']);
    $conn->query("DELETE FROM favorites WHERE user_id=$userId AND book_id=$unfavBookId");
}

// Get book details
$result = $conn->query("SELECT * FROM books WHERE id=$book_id");
$book = $result->fetch_assoc();
if (!$book) {
    echo "Book not found.";
    exit;
}

// Get chapters for this book
$chapters = [];
$chapterResult = $conn->query("SELECT * FROM chapters WHERE book_id=$book_id ORDER BY chapter_number ASC");
while ($row = $chapterResult->fetch_assoc()) {
    $chapters[] = $row;
}

// Check if book is favorite
$isFavorite = false;
if (isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $favCheck = $conn->query("SELECT 1 FROM favorites WHERE user_id=$userId AND book_id=$book_id");
    $isFavorite = $favCheck->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($book['title']); ?> - Book Details</title>
    <link rel="stylesheet" href="hehe.css">
    <style>
    /* Base Styles */
/* Base Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
    color: #333;
    line-height: 1.6;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Header Styles */
.header {
    padding: 15px;
    background: #f8f8f8;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: background-color 0.3s ease;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Book Content Layout */
.content-wrapper {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.book-info {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

/* Fixed-size Image Container */
.image-container {
    width: 100%;
    margin: 0 auto;
    text-align: center;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

/* Fixed-size Chapter Images */
.chapter-image {
    max-width: 800px;
    width: 100%;
    height: auto;
    margin: 0 auto;
    display: block;
    object-fit: contain;
    cursor: zoom-in;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 4px;
}

/* Zoomed State */
.chapter-image.zoomed {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 100%;
    margin: auto;
    z-index: 1000;
    cursor: zoom-out;
    background: rgba(255,255,255,0.95);
    padding: 20px;
    box-sizing: border-box;
    object-fit: contain;
}

/* Navigation */
.navigation {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.nav-button {
    padding: 10px 20px;
    background: #9c6b3e;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    transition: background 0.3s ease;
}

.nav-button:hover {
    background: #8a5d34;
}

/* Dark Mode Styles */
body.dark-mode {
    background-color: #121212;
    color: #e0e0e0;
}

.dark-mode .header {
    background-color: #1f1f1f;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}

.dark-mode .book-info,
.dark-mode .image-container {
    background-color: #1f1f1f;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}

.dark-mode .chapter-image:not(.zoomed) {
    box-shadow: 0 2px 10px rgba(0,0,0,0.5);
}

.dark-mode .nav-button {
    background-color: #7a5b38;
}

.dark-mode .chapter-image.zoomed {
    background-color: rgba(30,30,30,0.95);
}

/* Dark Mode Toggle Button */
.dark-mode-toggle {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.dark-mode-toggle button {
    background: #333;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.dark-mode .dark-mode-toggle button {
    background: #555;
}

/* Responsive Design */
@media (max-width: 900px) {
    .chapter-image {
        max-width: 100%;
    }
}

@media (max-width: 600px) {
    .container {
        padding: 10px;
    }
    
    .image-container {
        padding: 10px;
    }
    
    .dark-mode-toggle {
        bottom: 10px;
        right: 10px;
    }
    
    .dark-mode-toggle button {
        padding: 8px 12px;
        font-size: 12px;
    }
}

/* Text Elements */
h1, h2, h3, h4, h5, h6 {
    transition: color 0.3s ease;
}

.dark-mode h1,
.dark-mode h2,
.dark-mode h3,
.dark-mode h4,
.dark-mode h5,
.dark-mode h6,
.dark-mode p,
.dark-mode a:not(.nav-button) {
    color: #e0e0e0;
}

/* Links */
a {
    color: #9c6b3e;
    transition: color 0.3s ease;
}

.dark-mode a {
    color: #c88d5a;
}

    </style>
</head>
<body>
    <header class="header">
        <div style="text-align:right; max-width:1200px; margin:0 auto;">
            <form action="1.php" method="get" style="display:inline;">
                <button type="submit" style="margin:12px 0 0 0; padding:8px 22px; border-radius:12px; border:none; background:#9c6b3e; color:#fff; font-weight:bold; font-size:1em; cursor:pointer;">
                    Back to Home
                </button>
            </form>
        </div>
    </header>
    <div class="container">
        <main class="main-content">
            <div class="content-wrapper">
                <div class="book-description">
                    <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                    <h3><?php echo htmlspecialchars($book['author']); ?></h3>
                    <br>
                    <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                    
                    <div class="action-buttons">
                        <button onclick="toggleFullBook()" class="read-full-btn" id="readFullBtn">
                            📚 Read Full Book
                        </button>
                        
                        <?php if (isset($_SESSION['id'])): ?>
                            <form method="post" action="book.php?id=<?php echo $book['id']; ?>" style="display:inline;">
                                <?php if ($isFavorite): ?>
                                    <input type="hidden" name="unfavorite_book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="favorite-btn" title="Remove from Favorites">
                                        ❤️ Remove from Favorites
                                    </button>
                                <?php else: ?>
                                    <input type="hidden" name="favorite_book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="favorite-btn" title="Add to Favorites">
                                        🤍 Add to Favorites
                                    </button>
                                <?php endif; ?>
                            </form>
                        <?php else: ?>
                            <a href="login.php" style="color: #9c6b3e; text-decoration: none; font-weight: bold;">
                                Login to add to favorites
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="book-image">
                    <img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover" />
                </div>
            </div>
            
            <!-- Full Book Reader -->
            <div id="fullBookContent" class="full-book-reader" style="display: none;">
                <div class="book-reader-header">
                    <h2>📖 <?php echo htmlspecialchars($book['title']); ?></h2>
                    <div class="reader-controls">
                        <label class="dark-mode-toggle">
                            <input type="checkbox" id="darkModeToggle" onchange="toggleDarkMode()"> Dark Mode
                        </label>
                        <button onclick="toggleFullBook()" class="close-reader-btn">✕ Close</button>
                    </div>
                </div>
                <div class="book-content" id="bookContent">
                    <?php if (!empty($chapters)): ?>
                        <?php foreach ($chapters as $index => $chapter): ?>
                            <div class="chapter-container" id="chapter-<?php echo $chapter['chapter_number']; ?>">
                                <div class="chapter-title"><?php echo htmlspecialchars($chapter['title']); ?></div>
                                <div class="chapter-text">
                                    <?php if ($chapter['chapter_type'] === 'image'): ?>
                                        <div class="image-viewer-container">
                                            <?php 
                                            $imageUrls = explode("\n", $chapter['content']);
                                            foreach ($imageUrls as $imgIndex => $imageUrl): 
                                                if (!empty(trim($imageUrl))): ?>
                                                    <div class="image-slide">
                                                        <img src="<?php echo htmlspecialchars(trim($imageUrl)); ?>" 
                                                             alt="Chapter Image" 
                                                             class="chapter-image"
                                                             onclick="toggleZoom(this)">
                                                    </div>
                                                <?php endif;
                                            endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <?php echo nl2br(htmlspecialchars($chapter['content'])); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-chapters">
                            <p>This book doesn't have chapters yet or is still being added to our library.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <div class="dark-mode-toggle">
    <button id="darkModeToggle">🌙 Dark Mode</button>
</div>
    <script>
        // Book reader functionality
        function toggleFullBook() {
            const fullBookContent = document.getElementById('fullBookContent');
            const readFullBtn = document.getElementById('readFullBtn');
            
            if (fullBookContent.style.display === 'none' || fullBookContent.style.display === '') {
                fullBookContent.style.display = 'block';
                document.body.style.overflow = 'hidden';
                readFullBtn.textContent = '📚 Close Book';
                
                <?php if (isset($_SESSION['id'])): ?>
                fetch('track_reading.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        book_id: <?php echo $book['id']; ?>,
                        action: 'start_reading'
                    })
                });
                <?php endif; ?>
            } else {
                fullBookContent.style.display = 'none';
                document.body.style.overflow = 'auto';
                readFullBtn.textContent = '📚 Read Full Book';
            }
        }
        
        // Image zoom functionality
        function toggleZoom(img) {
            if (img.classList.contains('zoomed')) {
                img.classList.remove('zoomed');
            } else {
                // First remove zoom from any other images
                document.querySelectorAll('.chapter-image.zoomed').forEach(zoomedImg => {
                    zoomedImg.classList.remove('zoomed');
                });
                img.classList.add('zoomed');
            }
        }
        
        // Close zoom when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('zoomed')) {
                return; // Don't close if clicking on the zoomed image itself
            }
            
            const zoomedImages = document.querySelectorAll('.chapter-image.zoomed');
            if (zoomedImages.length > 0 && !e.target.closest('.chapter-image')) {
                zoomedImages.forEach(img => {
                    img.classList.remove('zoomed');
                });
            }
        });
        
        // Dark mode toggle
        function toggleDarkMode() {
            const fullBookReader = document.getElementById('fullBookContent');
            const isChecked = document.getElementById('darkModeToggle').checked;
            
            if (isChecked) {
                fullBookReader.classList.add('dark-mode');
            } else {
                fullBookReader.classList.remove('dark-mode');
            }
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const fullBookContent = document.getElementById('fullBookContent');
            
            if (e.key === 'Escape') {
                // Close zoomed images first
                const zoomedImages = document.querySelectorAll('.chapter-image.zoomed');
                if (zoomedImages.length > 0) {
                    zoomedImages.forEach(img => {
                        img.classList.remove('zoomed');
                    });
                } 
                // Then close reader if still open
                else if (fullBookContent.style.display === 'block') {
                    toggleFullBook();
                }
            }
        });

        function toggleZoom(img) {
    img.classList.toggle('zoomed');
    document.body.style.overflow = img.classList.contains('zoomed') ? 'hidden' : 'auto';
}

// Close zoom when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('zoomed')) {
        return;
    }
    
    const zoomedImages = document.querySelectorAll('.chapter-image.zoomed');
    if (zoomedImages.length > 0 && !e.target.closest('.chapter-image')) {
        zoomedImages.forEach(img => {
            img.classList.remove('zoomed');
            document.body.style.overflow = 'auto';
        });
    }
});
        
        // Page transition for links
        document.querySelectorAll('a').forEach(function(link) {
            if (link.hostname === window.location.hostname && 
                link.target !== "_blank" && 
                !link.href.startsWith('javascript:')) {
                link.addEventListener('click', function(e) {
                    if (link.hash && link.pathname === window.location.pathname) return;
                    e.preventDefault();
                    document.body.classList.add('fade-out');
                    setTimeout(function() {
                        window.location = link.href;
                    }, 500);
                });
            }
        });

        // Dark Mode Toggle
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;

// Check for saved preference
if (localStorage.getItem('darkMode') === 'enabled') {
    enableDarkMode();
}

darkModeToggle.addEventListener('click', toggleDarkMode);

function toggleDarkMode() {
    if (body.classList.contains('dark-mode')) {
        disableDarkMode();
    } else {
        enableDarkMode();
    }
}

function enableDarkMode() {
    body.classList.add('dark-mode');
    darkModeToggle.innerHTML = '☀️ Light Mode';
    localStorage.setItem('darkMode', 'enabled');
}

function disableDarkMode() {
    body.classList.remove('dark-mode');
    darkModeToggle.innerHTML = '🌙 Dark Mode';
    localStorage.setItem('darkMode', 'disabled');
}
    </script>
</body>
</html>
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Get book id from URL
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Log as recently read if user is logged in
if (isset($_SESSION['user_id']) && $book_id > 0) {
    $user_id = intval($_SESSION['user_id']);
    // Optional: Remove previous entry for this book/user to avoid duplicates
    $conn->query("DELETE FROM recently_read WHERE user_id=$user_id AND book_id=$book_id");
    // Insert new entry
    $conn->query("INSERT INTO recently_read (user_id, book_id, read_at) VALUES ($user_id, $book_id, NOW())");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle favorite action
if (isset($_POST['favorite_book_id']) && isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $favBookId = intval($_POST['favorite_book_id']);
    $conn->query("INSERT IGNORE INTO favorites (user_id, book_id) VALUES ($userId, $favBookId)");
}

// Handle unfavorite action
if (isset($_POST['unfavorite_book_id']) && isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $unfavBookId = intval($_POST['unfavorite_book_id']);
    $conn->query("DELETE FROM favorites WHERE user_id=$userId AND book_id=$unfavBookId");
}

// Handle continue reading action
if (isset($_POST['continue_book_id']) && isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $contBookId = intval($_POST['continue_book_id']);
    $conn->query("REPLACE INTO continue_reading (user_id, book_id, last_accessed) VALUES ($userId, $contBookId, NOW())");
}

// Handle most read action
if (isset($_POST['read_book_id']) && isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $readBookId = intval($_POST['read_book_id']);
    $conn->query("INSERT INTO most_read (user_id, book_id, read_at) VALUES ($userId, $readBookId, NOW())");
}

$result = $conn->query("SELECT * FROM books WHERE id=$id");
$book = $result->fetch_assoc();
if (!$book) {
    echo "Book not found.";
    exit;
}

// Check if book is already in favorites
$isFavorite = false;
if (isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $favCheck = $conn->query("SELECT 1 FROM favorites WHERE user_id=$userId AND book_id=$id");
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
      body {
        transition: opacity 0.5s;
      }
      body.fade-out {
        opacity: 0;
      }
      .fav-btn, .cont-btn, .read-btn {
        border: none;
        background: none;
        font-size: 1.3em;
        cursor: pointer;
        margin-right: 10px;
        transition: transform 0.1s;
      }
      .fav-btn:hover { color: #e25555; transform: scale(1.2);}
      .cont-btn:hover, .read-btn:hover { color: #9c6b3e; transform: scale(1.1);}
      
      .action-buttons {
        margin-top: 20px;
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
      }
      
      .read-full-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 25px;
        font-size: 1.1em;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
      }
      
      .read-full-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
      }
      
      .favorite-btn {
        background: <?php echo $isFavorite ? '#e25555' : 'transparent'; ?>;
        color: <?php echo $isFavorite ? 'white' : '#e25555'; ?>;
        border: 2px solid #e25555;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 1em;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      
      .favorite-btn:hover {
        background: #e25555;
        color: white;
        transform: scale(1.05);
      }
      
      .quick-actions {
        display: flex;
        gap: 10px;
        align-items: center;
      }
      
      /* Full Book Reader Styles */
      .full-book-reader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: white;
        z-index: 1000;
        overflow-y: auto;
        animation: slideIn 0.3s ease-out;
      }
      
      @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      .book-reader-header {
        position: sticky;
        top: 0;
        background: #9c6b3e;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1001;
      }
      
      .book-reader-header h2 {
        margin: 0;
        font-size: 1.5em;
      }
      
      .reader-controls {
        display: flex;
        gap: 15px;
        align-items: center;
      }
      
      .font-btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9em;
        transition: all 0.3s ease;
      }
      
      .font-btn:hover {
        background: rgba(255,255,255,0.3);
      }
      
      .dark-mode-toggle {
        color: white;
        font-size: 0.9em;
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
      }
      
      .close-reader-btn {
        background: #e25555;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 0.9em;
        font-weight: bold;
        transition: all 0.3s ease;
      }
      
      .close-reader-btn:hover {
        background: #d04444;
        transform: scale(1.05);
      }
      
      .book-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 30px;
        font-family: 'Georgia', serif;
        line-height: 1.8;
        font-size: 1.1em;
        color: #333;
      }
      
      .chapter-title {
        font-size: 2.5em;
        color: #9c6b3e;
        text-align: center;
        margin-bottom: 10px;
        font-weight: bold;
      }
      
      .chapter-author {
        font-size: 1.3em;
        color: #666;
        text-align: center;
        margin-bottom: 40px;
        font-style: italic;
      }
      
      .chapter-text {
        text-align: justify;
        text-indent: 2em;
      }
      
      .chapter-text p {
        margin-bottom: 1.5em;
      }
      
      .chapter-text strong {
        color: #9c6b3e;
        font-size: 1.2em;
        display: block;
        margin: 30px 0 15px 0;
        text-align: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
      }
      
      /* Dark mode styles */
      .full-book-reader.dark-mode {
        background: #1a1a1a;
        color: #e0e0e0;
      }
      
      .full-book-reader.dark-mode .book-content {
        color: #e0e0e0;
      }
      
      .full-book-reader.dark-mode .chapter-title {
        color: #d4af8c;
      }
      
      .full-book-reader.dark-mode .chapter-author {
        color: #aaa;
      }
      
      .full-book-reader.dark-mode .chapter-text strong {
        color: #d4af8c;
        border-color: #444;
      }
      
      /* Mobile responsive */
      @media (max-width: 768px) {
        .book-reader-header {
          flex-direction: column;
          gap: 10px;
          text-align: center;
        }
        
        .reader-controls {
          flex-wrap: wrap;
          justify-content: center;
        }
        
        .book-content {
          padding: 20px 15px;
          font-size: 1em;
        }
        
        .chapter-title {
          font-size: 2em;
        }
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
        <aside class="sidebar">
            <!-- ...your sidebar code... -->
        </aside>
        <main class="main-content">
            <div class="content-wrapper">
                <div class="book-description">
                    <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                    <h3><?php echo htmlspecialchars($book['author']); ?></h3>
                    <br>
                    <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                    
                    <div class="action-buttons">
                        <!-- Read Full Book Button -->
                        <button onclick="toggleFullBook()" class="read-full-btn" id="readFullBtn">
                            📚 Read Full Book
                        </button>
                        
                        <?php if (isset($_SESSION['id'])): ?>
                            <!-- Enhanced Favorites Button -->
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
                            
                            <!-- Quick Action Buttons -->
                            <div class="quick-actions">
                                <form method="post" action="book.php?id=<?php echo $book['id']; ?>" style="display:inline;">
                                    <input type="hidden" name="continue_book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="cont-btn" title="Add to Continue Reading">📖</button>
                                </form>
                                <form method="post" action="book.php?id=<?php echo $book['id']; ?>" style="display:inline;">
                                    <input type="hidden" name="read_book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="read-btn" title="Mark as Most Read">🔥</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <!-- For non-logged in users -->
                            <a href="login.php" style="color: #9c6b3e; text-decoration: none; font-weight: bold;">
                                Login to add to favorites and track reading
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="book-image">
                    <img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover" />
                </div>
            </div>
            
            <!-- Full Book Content (Hidden by default) -->
            <div id="fullBookContent" class="full-book-reader" style="display: none;">
                <div class="book-reader-header">
                    <h2>📖 <?php echo htmlspecialchars($book['title']); ?></h2>
                    <div class="reader-controls">
                        <button onclick="changeFontSize(-1)" class="font-btn">A-</button>
                        <button onclick="changeFontSize(1)" class="font-btn">A+</button>
                        <label class="dark-mode-toggle">
                            <input type="checkbox" id="darkModeToggle" onchange="toggleDarkMode()"> Dark Mode
                        </label>
                        <button onclick="toggleFullBook()" class="close-reader-btn">✕ Close</button>
                    </div>
                </div>
                <div class="book-content" id="bookContent">
                    <div class="chapter-title"><?php echo htmlspecialchars($book['title']); ?></div>
                    <div class="chapter-author">by <?php echo htmlspecialchars($book['author']); ?></div>
                    <div class="chapter-text">
                        <?php 
                        // Display book content - you can modify this to show actual book content
                        // For now, using description as placeholder content
                        echo nl2br(htmlspecialchars($book['description'])); 
                        
                        // If you have a 'content' field in your books table, use this instead:
                        // echo nl2br(htmlspecialchars($book['content'])); 
                        ?>
                        
                        <!-- Sample additional content for demonstration -->
                        <br><br>
                        <p><strong>Chapter 1: The Beginning</strong></p>
                        <p>This is where the full book content would appear. You can replace this with actual book text from your database or file system. The reader provides a comfortable reading experience with adjustable font sizes and dark mode support.</p>
                        
                        <p><strong>Chapter 2: The Journey Continues</strong></p>
                        <p>More content would follow here. You can structure this however you like - with chapters, sections, or as one continuous text. The reader will scroll smoothly through all the content.</p>
                        
                        <p><strong>Chapter 3: The Conclusion</strong></p>
                        <p>Final content of the book would appear here. Users can adjust the reading experience to their preferences using the controls at the top.</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
let currentFontSize = 1.1;

function toggleFullBook() {
    const fullBookContent = document.getElementById('fullBookContent');
    const readFullBtn = document.getElementById('readFullBtn');
    
    if (fullBookContent.style.display === 'none' || fullBookContent.style.display === '') {
        fullBookContent.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        readFullBtn.textContent = '📚 Close Book';
        
        // Track reading if user is logged in
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
        document.body.style.overflow = 'auto'; // Restore scrolling
        readFullBtn.textContent = '📚 Read Full Book';
    }
}

function changeFontSize(delta) {
    currentFontSize += delta * 0.2;
    if (currentFontSize < 0.8) currentFontSize = 0.8;
    if (currentFontSize > 2.5) currentFontSize = 2.5;
    
    document.getElementById('bookContent').style.fontSize = currentFontSize + 'em';
}

function toggleDarkMode() {
    const fullBookReader = document.getElementById('fullBookContent');
    const isChecked = document.getElementById('darkModeToggle').checked;
    
    if (isChecked) {
        fullBookReader.classList.add('dark-mode');
    } else {
        fullBookReader.classList.remove('dark-mode');
    }
}

// Close reader with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const fullBookContent = document.getElementById('fullBookContent');
        if (fullBookContent.style.display === 'block') {
            toggleFullBook();
        }
    }
});

// Original fade transition script
document.querySelectorAll('a').forEach(function(link) {
  // Only apply to internal links
  if (link.hostname === window.location.hostname && link.target !== "_blank" && !link.href.startsWith('javascript:')) {
    link.addEventListener('click', function(e) {
      // Ignore anchor links
      if (link.hash && link.pathname === window.location.pathname) return;
      e.preventDefault();
      document.body.classList.add('fade-out');
      setTimeout(function() {
        window.location = link.href;
      }, 500); // Match the CSS transition duration
    });
  }
});
</script>
</body>
</html>
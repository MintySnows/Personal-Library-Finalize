<?php
session_start();

// Database connection with error handling
$conn = new mysqli("localhost", "root", "", "bookwebsite");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$books = [];
$username = '';
$user_role = 'user'; // Default role

// Fetch books from database
$result = $conn->query("SELECT * FROM books");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

// Fetch username and role from users table
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $username = $row['username'];
        $user_role = $row['role'] ?? 'user';
        $_SESSION['role'] = $user_role; // Store role in session
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Personal Book Website</title>
  <link rel="stylesheet" href="haha.css" />
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      transition: opacity 0.5s;
    }
    
    body.fade-out {
      opacity: 0;
    }
    
    .container {
      display: flex;
      min-height: 100vh;
    }
    
    .header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      background: #fff;
      border-bottom: 1px solid #ddd;
      padding: 10px 20px;
    }
    
    .logo-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .logo-left {
      display: flex;
      align-items: center;
    }
    
    .logo-img {
      width: 40px;
      height: 40px;
      margin-right: 10px;
    }
    
    .topuser {
      display: flex;
      align-items: center;
    }
    
    .user-small-img {
      width: 30px;
      height: 30px;
      margin-left: 10px;
    }
    
    .sidebar {
      background: #fdf1d6;
      width: 220px;
      min-height: 100vh;
      padding-top: 100px; /* Account for fixed header */
      display: flex;
      flex-direction: column;
      align-items: center;
      position: fixed;
      left: 0;
      top: 0;
    }

    .profile-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 18px;
    }

    .user-img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 8px;
      object-fit: cover;
      background: #fff;
    }

    .profile-section p {
      margin: 0;
      font-size: 1.08rem;
      color: #222;
      font-weight: 500;
      text-align: center;
    }

    .nav-buttons {
      background: #fff7e6;
      border-radius: 22px;
      padding: 24px 10px 32px 10px;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .main-buttons {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .main-buttons form {
      width: 100%;
    }

    .main-buttons form button {
      width: 90%;
      height: 48px;
      margin: 0 auto 18px auto;
      background: #ffe49c;
      color: #222;
      border: none;
      border-radius: 22px;
      font-size: 1.08rem;
      font-family: inherit;
      font-weight: 700;
      cursor: pointer;
      box-shadow: none;
      text-align: center;
      letter-spacing: 0.2px;
      transition: background 0.2s, color 0.2s;
      outline: none;
      display: block;
    }

    .main-buttons form button:hover,
    .main-buttons form button:focus {
      background: #f5c748;
      color: #7b522e;
    }

    .admin-btn {
      width: 90%;
      height: 48px;
      margin: 0 auto 18px auto;
      background: #9c6b3e;
      color: #fff;
      border: none;
      border-radius: 22px;
      font-size: 1.08rem;
      font-family: inherit;
      font-weight: 700;
      cursor: pointer;
      text-align: center;
      letter-spacing: 0.2px;
      transition: background 0.2s, color 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }

    .admin-btn:hover,
    .admin-btn:focus {
      background: #7b522e;
      color: #fff;
    }

    .logout {
      width: 90%;
      height: 40px;
      margin: 18px auto 0 auto;
      background: #f5c748;
      color: #333;
      border: none;
      border-radius: 22px;
      font-size: 1.05rem;
      font-family: inherit;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
      text-align: center;
      display: block;
    }

    .logout:hover {
      background: #e6b800;
      color: #fff;
    }
    
    .main-content {
      margin-left: 220px;
      padding: 100px 20px 20px 20px; /* Account for fixed header and sidebar */
      flex: 1;
    }
    
    .search-section {
      margin-bottom: 30px;
    }
    
    .search-bar {
      flex: 1;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      margin-right: 10px;
    }
    
    .search-btn {
      padding: 10px 20px;
      background: #ffe49c;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    
    .book-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
    }
    
    .book-card {
      background: #fff;
      padding: 15px;
      border-radius: 12px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }
    
    .book-card img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
    }
    
    .book-card p {
      margin: 5px 0;
    }
    
    .author {
      color: #666;
      font-style: italic;
    }
    
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 220px;
      right: 0;
      background: #fff;
      border-top: 1px solid #ddd;
      padding: 10px 20px;
      display: flex;
      gap: 20px;
    }
    
    .bottom-nav a {
      text-decoration: none;
      color: #333;
      padding: 8px 16px;
      border-radius: 5px;
      transition: background 0.2s;
    }
    
    .bottom-nav a:hover {
      background: #f0f0f0;
    }
  </style>
</head>
<body>
    
  <header class="header">
    <div class="logo-bar">
      <div class="logo-left">
        <img src="book.png" alt="Logo" class="logo-img" />
        <h1>Personal Book Website</h1>
      </div>
      <div class="topuser">
        <span>
          <strong>
            <?php echo htmlspecialchars($username); ?>
          </strong>
        </span>
        <img src="github_logo.png" alt="User Icon" class="user-small-img" />
      </div>
    </div>
  </header>

  <div class="container">
    <aside class="sidebar">
      <div class="profile-section">
        <img src="profile.jpg" alt="User Icon" class="user-img" />
        <p>
          <strong><?php echo htmlspecialchars($username); ?></strong>
          <br>
          <span style="font-size:0.95em;color:#9c6b3e;">
            <?php echo ucfirst(htmlspecialchars($user_role)); ?>
          </span>
        </p>
      </div>

      <div class="nav-buttons">
        <div class="main-buttons">
          <form method="get" action="user_profile.php">
            <button type="submit">User</button>
          </form>
          <form method="post" action="library.php">
            <button type="submit">Library</button>
          </form>
          <form method="post" action="settings.php">
            <button type="submit">Settings</button>
          </form>
        </div>
        <?php if ($user_role === 'admin'): ?>
          <a href="admin_panel.php" class="admin-btn">Admin Panel</a>
        <?php endif; ?>
        <form method="post" action="logout.php">
          <button type="submit" class="logout" name="logoutBtn">Log Out</button>
        </form>
      </div>
    </aside>

    <main class="main-content">
      <section class="search-section">
        <form method="post" action="search.php" style="display:flex;">
          <input type="text" name="search" placeholder="Search..." class="search-bar" />
          <button type="submit" class="search-btn" name="searchBtn">Search</button>
        </form>
      </section>

      <section class="top-picks">
        <h2>Top Picks:</h2>
        <div class="book-cards">
          <?php if (empty($books)): ?>
            <p>No books available at the moment.</p>
          <?php else: ?>
            <?php foreach ($books as $book): ?>
              <div class="book-card">
                <a href="book.php?id=<?php echo intval($book['id']); ?>" style="display:block;">
                  <img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover" style="opacity:<?php echo isset($book['available']) && $book['available'] ? '1' : '0.5'; ?>;">
                </a>
                <p><strong><?php echo htmlspecialchars($book['title']); ?></strong></p>
                <p class="author"><?php echo htmlspecialchars($book['author']); ?></p>
                <?php if (isset($book['available']) && !$book['available']): ?>
                  <div style="color:#b00;font-weight:bold;">Not Available</div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>

  <nav class="bottom-nav">
    <a href="home.php">Home</a>
    <a href="stories.php">Stories</a>
    <?php if ($user_role === 'admin'): ?>
      <a href="admin_panel.php">Admin</a>
    <?php endif; ?>
  </nav>

<script>
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

// Add fade-out on form submit for sidebar buttons
document.querySelectorAll('.nav-buttons form').forEach(function(form) {
  form.addEventListener('submit', function(e) {
    document.body.classList.add('fade-out');
  });
});
</script>
</body>
</html>
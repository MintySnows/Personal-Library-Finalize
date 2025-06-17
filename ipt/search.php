<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bookwebsite");

$search = '';
$results = [];

// Fetch username from users table
$username = '';
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $res = $conn->query("SELECT username FROM users WHERE id=$uid");
    if ($row = $res->fetch_assoc()) {
        $username = $row['username'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $sql = "SELECT * FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
    $query = $conn->query($sql);
    while ($row = $query->fetch_assoc()) {
        $results[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Search Results</title>
    <link rel="stylesheet" href="haha.css" />
    <style>
        body { font-family: Arial, sans-serif; background: #fff6e3; margin: 0; transition: opacity 0.5s; }
        body.fade-out { opacity: 0; }
        .header {
            background: #ffe7b3;
            box-shadow: 0 2px 12px #e0c9a6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
            padding: 0;
        }
        .logo-bar {
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: space-between;
            padding: 0 40px;
        }
        .logo-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-img { height: 48px; }
        .logo-bar h1 {
            font-size: 1.6rem;
            color: #444;
            font-family: inherit;
            font-weight: 700;
            margin: 0;
        }
        .topuser {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
            color: #9c6b3e;
        }
        .topuser strong {
            font-size: 1.08rem;
            color: #222;
        }
        .user-small-img {
            height: 32px;
            border-radius: 50%;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            background: #fdf1d6;
            width: 220px;
            min-height: 100vh;
            padding-top: 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
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
            height: 48px;
            margin: 0 auto 0 auto;
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
            display: block;
            line-height: 48px;
            text-decoration: none;
        }
        .logout:hover,
        .logout:focus {
            background: #7b522e;
            color: #fff;
        }
        .main-content {
            flex: 1;
            padding: 40px 60px;
        }
        .search-bar {
            padding: 12px;
            border-radius: 20px;
            border: none;
            background: #fff3d1;
            font-size: 1em;
            width: 250px;
            margin-right: 12px;
        }
        .search-btn {
            padding: 12px 32px;
            border: none;
            border-radius: 20px;
            background: #ffd36b;
            color: #9c6b3e;
            font-weight: 700;
            font-size: 1.1em;
            cursor: pointer;
            min-width: 100px;
        }
        .search-btn:hover {
            background: #ffe7b3;
        }
        .book-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            margin-top: 32px;
        }
        .book-card {
            background: #fffbe9;
            border-radius: 16px;
            box-shadow: 0 2px 8px #e0c9a6;
            padding: 18px;
            width: 180px;
            text-align: center;
            transition: box-shadow 0.2s;
        }
        .book-card img {
            width: 100px;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .book-card strong {
            color: #9c6b3e;
        }
        .author {
            color: #7b522e;
            font-size: 0.95em;
        }
        .back-link {
            display: inline-block;
            margin: 16px 0;
            color: #9c6b3e;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
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
            <?php echo $username ? htmlspecialchars($username) : 'Set your username'; ?>
          </strong>
          <?php if (isset($_SESSION['role'])): ?>
            <span style="font-size:0.95em;color:#9c6b3e;">
              (<?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?>)
            </span>
          <?php endif; ?>
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
          <strong>
            <?php echo $username ? htmlspecialchars($username) : 'Set your username'; ?>
          </strong>
          <br>
          <span style="font-size:0.95em;color:#9c6b3e;">
            <?php echo isset($_SESSION['role']) ? ucfirst(htmlspecialchars($_SESSION['role'])) : 'User'; ?>
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
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <a href="admin_panel.php" class="admin-btn">Admin Panel</a>
        <?php endif; ?>
        <form method="post" action="logout.php">
          <button type="submit" class="logout" name="logoutBtn">Log Out</button>
        </form>
      </div>
    </aside>
    <main class="main-content">
      <a href="1.php" class="back-link">&larr; Back to Home</a>
      <h1>Search Results for "<?php echo htmlspecialchars($search); ?>"</h1>
      <form method="post" action="search.php" style="display:flex; margin-bottom: 24px;">
        <input type="text" name="search" placeholder="Search..." class="search-bar" value="<?php echo htmlspecialchars($search); ?>" />
        <button type="submit" class="search-btn" name="searchBtn">Search</button>
      </form>
      <div class="book-cards">
        <?php if (count($results) > 0): ?>
          <?php foreach ($results as $book): ?>
            <div class="book-card">
              <a href="book.php?id=<?php echo $book['id']; ?>">
                <img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="Book Cover">
              </a>
              <p><strong><?php echo htmlspecialchars($book['title']); ?></strong></p>
              <p class="author"><?php echo htmlspecialchars($book['author']); ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No books found.</p>
        <?php endif; ?>
      </div>
    </main>
  </div>
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
</script>
</body>
</html>
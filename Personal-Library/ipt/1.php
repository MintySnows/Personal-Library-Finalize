<?php
session_start();

// Database configuration - should be in a separate config file
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'bookwebsite'
];

// Create database connection with error handling
try {
    $conn = new mysqli($db_config['host'], $db_config['username'], $db_config['password'], $db_config['database']);
    $conn->set_charset("utf8mb4");
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Sorry, we're experiencing technical difficulties. Please try again later.");
}

// Initialize variables
$books = [];
$username = '';
$profile_pic = 'profile.jpg'; // default image
$user_role = 'user';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all books using prepared statement
try {
    $stmt = $conn->prepare("SELECT id, title, author, cover FROM books ORDER BY title ASC");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching books: " . $e->getMessage());
    $books = []; // Fallback to empty array
}

// Fetch user data using prepared statement
try {
    $uid = intval($_SESSION['user_id']);
    $stmt = $conn->prepare("SELECT username, profile_pic, role FROM users WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $username = $row['username'];
        $profile_pic = !empty($row['profile_pic']) ? $row['profile_pic'] : 'profile.jpg';
        $user_role = $row['role'] ?? 'user';
        $_SESSION['role'] = $user_role; // Update session with current role
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    // Keep default values
}

$conn->close();

// Security function to sanitize output
function sanitize_output($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library - Personal Book Website</title>
    <meta name="description" content="Browse our collection of books in the personal library">
    <link rel="stylesheet" href="haha.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #fff8ef 0%, #fdf4e7 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            min-height: 100vh;
            transition: opacity 0.3s ease;
        }

        body.fade-out {
            opacity: 0;
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #ffe7b3 0%, #ffd89b 100%);
            box-shadow: 0 4px 20px rgba(156, 107, 62, 0.15);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(156, 107, 62, 0.1);
        }

        .logo-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-img {
            height: 3rem;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .logo-bar h1 {
            font-size: 1.5rem;
            color: #2c1810;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .topuser {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .topuser strong {
            font-size: 1rem;
            color: #2c1810;
            font-weight: 600;
        }

        .user-small-img {
            height: 2rem;
            width: 2rem;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .back-home-btn {
            background: #9c6b3e;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-left: 1rem;
        }

        .back-home-btn:hover {
            background: #7b522e;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(156, 107, 62, 0.3);
        }

        /* Container and Layout */
        .container {
            display: flex;
            max-width: 1400px;
            margin: 2rem auto;
            gap: 2rem;
            padding: 0 1rem;
        }

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(145deg, #fdf5e6 0%, #f9f0e1 100%);
            border-radius: 1.5rem;
            padding: 2rem;
            width: 280px;
            height: fit-content;
            box-shadow: 0 8px 32px rgba(156, 107, 62, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 120px;
        }

        .profile-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid rgba(156, 107, 62, 0.1);
        }

        .user-img {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #9c6b3e;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .profile-section p {
            text-align: center;
            font-size: 1.1rem;
            color: #2c1810;
            font-weight: 600;
        }

        .profile-section span {
            font-size: 0.9rem;
            color: #9c6b3e;
            font-weight: 500;
            margin-top: 0.25rem;
            display: block;
        }

        .nav-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .nav-btn {
            width: 100%;
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 2rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: block;
            background: #ffe49c;
            color: #2c1810;
        }

        .nav-btn:hover {
            background: #f5c748;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 199, 72, 0.4);
        }

        .nav-btn.active {
            background: #9c6b3e;
            color: white;
        }

        .admin-btn {
            background: linear-gradient(135deg, #9c6b3e 0%, #b8824a 100%);
            color: white;
        }

        .admin-btn:hover {
            background: linear-gradient(135deg, #7b522e 0%, #9c6b3e 100%);
        }

        .logout-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            margin-top: 1rem;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(156, 107, 62, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .library-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 1rem;
            text-align: center;
            background: linear-gradient(135deg, #9c6b3e 0%, #b8824a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title {
            font-size: 1.5rem;
            color: #2c1810;
            font-weight: 600;
            margin: 2rem 0 1.5rem 0;
            position: relative;
            padding-left: 1rem;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 1.5rem;
            background: linear-gradient(135deg, #9c6b3e 0%, #b8824a 100%);
            border-radius: 2px;
        }

        /* Book Cards */
        .book-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .book-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(156, 107, 62, 0.1);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .book-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #9c6b3e 0%, #b8824a 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(156, 107, 62, 0.2);
        }

        .book-card:hover::before {
            transform: scaleX(1);
        }

        .book-card a {
            text-decoration: none;
            display: block;
        }

        .book-card img {
            width: 100%;
            max-width: 140px;
            height: 200px;
            object-fit: cover;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }

        .book-card:hover img {
            transform: scale(1.05);
        }

        .book-card strong {
            display: block;
            font-size: 1.1rem;
            color: #2c1810;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .book-card p {
            color: #666;
            font-size: 0.95rem;
            margin: 0;
        }

        .empty-msg {
            text-align: center;
            color: #999;
            font-style: italic;
            font-size: 1.1rem;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 1rem;
            border: 2px dashed #ddd;
        }

        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #f5c6cb;
            margin-bottom: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
                gap: 1.5rem;
            }
            
            .sidebar {
                width: 100%;
                position: static;
            }
            
            .logo-bar {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .logo-bar h1 {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 768px) {
            .logo-bar {
                padding: 1rem;
            }
            
            .container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .main-content, .sidebar {
                padding: 1.5rem;
            }
            
            .library-title {
                font-size: 2rem;
            }
            
            .book-cards {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .book-cards {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
            }
            
            .book-card {
                padding: 1rem;
            }
            
            .book-card img {
                max-width: 120px;
                height: 170px;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #9c6b3e;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Accessibility improvements */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Focus styles for keyboard navigation */
        .nav-btn:focus,
        .back-home-btn:focus,
        .book-card a:focus {
            outline: 2px solid #9c6b3e;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo-bar">
            <div class="logo-left">
                <img src="book.png" alt="Personal Book Website Logo" class="logo-img">
                <h1>Personal Book Website</h1>
            </div>
            <div style="display: flex; align-items: center;">
                <div class="topuser">
                    <strong>
                        <?php echo $username ? sanitize_output($username) : 'Set your username'; ?>
                    </strong>
                    <img src="<?php echo sanitize_output($profile_pic); ?>" alt="User Profile Picture" class="user-small-img">
                </div>
                <a href="1.php" class="back-home-btn">Back to Home</a>
            </div>
        </div>
    </header>

    <div class="container">
        <aside class="sidebar">
            <div class="profile-section">
                <img src="<?php echo sanitize_output($profile_pic); ?>" alt="User Profile Picture" class="user-img">
                <p>
                    <strong><?php echo sanitize_output($username); ?></strong>
                    <span>
                        <?php echo sanitize_output(ucfirst($user_role)); ?>
                    </span>
                </p>
            </div>
            
            <nav class="nav-buttons" aria-label="User navigation">
                <a href="user_profile.php" class="nav-btn">User Profile</a>
                <a href="library.php" class="nav-btn active" aria-current="page">Library</a>
                <a href="settings.php" class="nav-btn">Settings</a>
                
                <?php if ($user_role === 'admin'): ?>
                    <a href="admin_panel.php" class="nav-btn admin-btn">Admin Panel</a>
                <?php endif; ?>
                
                <form method="post" action="logout.php" style="margin-top: 1rem;">
                    <button type="submit" class="nav-btn logout-btn" name="logoutBtn">
                        Log Out
                        <span class="sr-only">from Personal Book Website</span>
                    </button>
                </form>
            </nav>
        </aside>

        <main class="main-content">
            <h1 class="library-title">Library</h1>
            
            <section>
                <h2 class="section-title">All Books</h2>
                
                <?php if (empty($books)): ?>
                    <div class="empty-msg">
                        <p>No books available in the library yet.</p>
                        <?php if ($user_role === 'admin'): ?>
                            <p><a href="admin_panel.php" style="color: #9c6b3e; text-decoration: none; font-weight: 600;">Add some books to get started!</a></p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="book-cards">
                        <?php foreach ($books as $book): ?>
                            <article class="book-card">
                                <a href="book.php?id=<?php echo intval($book['id']); ?>" aria-label="View details for <?php echo sanitize_output($book['title']); ?>">
                                    <img src="<?php echo sanitize_output($book['cover']); ?>" 
                                         alt="Cover of <?php echo sanitize_output($book['title']); ?>"
                                         onerror="this.src='placeholder-book.jpg'; this.alt='Book cover not available';">
                                    <strong><?php echo sanitize_output($book['title']); ?></strong>
                                    <p>by <?php echo sanitize_output($book['author']); ?></p>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        // Smooth page transitions
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in effect when page loads
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);

            // Handle link clicks for smooth transitions
            document.querySelectorAll('a').forEach(function(link) {
                // Only apply to internal links
                if (link.hostname === window.location.hostname && 
                    link.target !== "_blank" && 
                    !link.href.startsWith('javascript:') &&
                    !link.classList.contains('no-transition')) {
                    
                    link.addEventListener('click', function(e) {
                        // Ignore anchor links
                        if (link.hash && link.pathname === window.location.pathname) return;
                        
                        e.preventDefault();
                        document.body.classList.add('fade-out');
                        
                        setTimeout(function() {
                            window.location.href = link.href;
                        }, 300);
                    });
                }
            });

            // Handle form submissions for smooth transitions (except logout)
            document.querySelectorAll('form').forEach(function(form) {
                // Skip logout form to prevent issues
                if (form.querySelector('[name="logoutBtn"]')) return;
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    document.body.classList.add('fade-out');
                    
                    setTimeout(function() {
                        form.submit();
                    }, 300);
                });
            });
        });

        // Add loading state to buttons
        document.querySelectorAll('button[type="submit"]').forEach(button => {
            button.addEventListener('click', function(e) {
                // Prevent double submission
                if (this.disabled) {
                    e.preventDefault();
                    return;
                }
                
                if (!this.querySelector('.loading')) {
                    this.innerHTML += ' <span class="loading"></span>';
                    this.disabled = true;
                    
                    // Re-enable button after 5 seconds as fallback
                    setTimeout(() => {
                        this.disabled = false;
                        const loading = this.querySelector('.loading');
                        if (loading) loading.remove();
                    }, 5000);
                }
            });
        });

        // Error handling for images
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('error', function() {
                console.warn('Failed to load image:', this.src);
            });
        });
    </script>
</body>
</html>
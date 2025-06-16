<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: 1.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Handle book deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM books WHERE id=$id");
    header("Location: admin_panel.php");
    exit();
}

// Handle book addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addBook'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $cover = $conn->real_escape_string($_POST['cover']);
    $description = $conn->real_escape_string($_POST['description']);
    $conn->query("INSERT INTO books (title, author, cover, description, available) VALUES ('$title', '$author', '$cover', '$description', 1)");
    header("Location: admin_panel.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: admin_panel.php");
    exit();
}

// Handle user update form display and submission
$edit_user = null;
if (isset($_GET['edit_user'])) {
    $edit_id = intval($_GET['edit_user']);
    $edit_user = $conn->query("SELECT * FROM users WHERE id=$edit_id")->fetch_assoc();
}
if (isset($_POST['update_user'])) {
    $id = intval($_POST['id']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $conn->query("UPDATE users SET username='$username', email='$email' WHERE id=$id");
    header("Location: admin_panel.php");
    exit();
}

// Handle book update form display and submission
$edit_book = null;
if (isset($_GET['edit_book'])) {
    $edit_id = intval($_GET['edit_book']);
    $edit_book = $conn->query("SELECT * FROM books WHERE id=$edit_id")->fetch_assoc();
}
if (isset($_POST['update_book'])) {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $cover = $conn->real_escape_string($_POST['cover']);
    $description = $conn->real_escape_string($_POST['description']);
    $conn->query("UPDATE books SET title='$title', author='$author', cover='$cover', description='$description' WHERE id=$id");
    header("Location: admin_panel.php");
    exit();
}

// Fetch all books
$books = [];
$result = $conn->query("SELECT * FROM books");
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Fetch users from the database
$users = [];
$result = $conn->query("SELECT id, username, email FROM users");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Book Library</title>
    <link rel="stylesheet" href="haha.css">
    <style>
        body {
  transition: opacity 0.5s;
}
body.fade-out {
  opacity: 0;
}
        body { background: #fff6e3; font-family: 'Segoe UI', Arial, sans-serif; }
        .admin-container { max-width: 900px; margin: 40px auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(42,93,255,0.10); padding: 32px; }
        h1 { color: #9c6b3e; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { padding: 12px; border-bottom: 1px solid #e0c9a6; text-align: left; }
        th { background: #fff3d1; color: #7b522e; }
        tr:last-child td { border-bottom: none; }
        .admin-btn, .edit-btn, .delete-btn {
            padding: 8px 18px;
            border: none;
            border-radius: 20px;
            background: #9c6b3e;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            margin-right: 6px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .admin-btn:hover, .edit-btn:hover, .delete-btn:hover { background: #7b522e; }
        .delete-btn { background: #b00; }
        .delete-btn:hover { background: #900; }
        .add-form { margin-top: 32px; background: #fff6e3; padding: 18px; border-radius: 12px; }
        .add-form input, .add-form textarea {
            width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 7px; border: 1px solid #9c6b3e; background: #fff; color: #3a2a13;
        }
        .add-form label { font-weight: 500; color: #7b522e; }
        .admin-btn {
    margin-top: 16px;
    margin-bottom: 20px;
    display: inline-block;
}
.modal {
  display: none; 
  position: fixed; 
  z-index: 1000; 
  left: 0; top: 0; width: 100%; height: 100%;
  overflow: auto; background: rgba(0,0,0,0.3);
}
.modal-content {
  background: #fff; margin: 8% auto; padding: 24px 32px; border-radius: 14px;
  width: 100%; max-width: 400px; position: relative;
}
.close {
  color: #aaa; position: absolute; right: 18px; top: 12px; font-size: 28px; font-weight: bold; cursor: pointer;
}
.close:hover { color: #b00; }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Admin Panel</h1>
        <a href="1.php" class="admin-btn" style="margin-top:16px; display:inline-block;">Back to Library</a>
        <h2>All Books</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Cover</th>
                <th>Title</th>
                <th>Author</th>
                <th>Available</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($books as $book): ?>
            <tr>
                <td><?php echo $book['id']; ?></td>
                <td><img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="cover" style="height:60px;border-radius:6px;"></td>
                <td><?php echo htmlspecialchars($book['title']); ?></td>
                <td><?php echo htmlspecialchars($book['author']); ?></td>
                <td><?php echo $book['available'] ? 'Yes' : 'No'; ?></td>
                <td>
                    <!-- Edit and Delete actions -->
                    <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="edit-btn">Edit</a>
                    <a href="admin_panel.php?delete=<?php echo $book['id']; ?>" class="delete-btn" onclick="return confirm('Delete this book?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <form class="add-form" method="post">
            <h2>Add New Book</h2>
            <label>Title:</label>
            <input type="text" name="title" required>
            <label>Author:</label>
            <input type="text" name="author" required>
            <label>Cover Image Filename:</label>
            <input type="text" name="cover" placeholder="e.g. cover.jpg" required>
            <label>Description:</label>
            <textarea name="description" rows="3"></textarea>
            <button type="submit" name="addBook" class="admin-btn">Add Book</button>
        </form>

        <h2>Manage Users</h2>
<table border="1" cellpadding="8" style="border-collapse:collapse; width:100%;">
  <tr>
    <th>ID</th>
    <th>Username</th>
    <th>Email</th>
    <th>Actions</th>
</tr>
  <?php foreach ($users as $user): ?>
    <tr>
      <td><?php echo isset($user['id']) ? htmlspecialchars($user['id']) : ''; ?></td>
      <td><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?></td>
      <td><?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?></td>
      <td>
    <a href="#" class="admin-btn" style="padding:4px 12px; font-size:0.95em;"
       onclick="openUserModal('<?php echo isset($user['id']) ? $user['id'] : ''; ?>', '<?php echo isset($user['username']) ? htmlspecialchars($user['username'], ENT_QUOTES) : ''; ?>', '<?php echo isset($user['email']) ? htmlspecialchars($user['email'], ENT_QUOTES) : ''; ?>'); return false;">
       Update
    </a>
    <a href="admin_panel.php?delete_user=<?php echo isset($user['id']) ? $user['id'] : ''; ?>" class="delete-btn" style="padding:4px 12px; font-size:0.95em;" onclick="return confirm('Delete this user?');">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<!-- User Update Modal -->
<div id="userModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close" onclick="closeUserModal()">&times;</span>
    <h2>Update User</h2>
    <form method="post" id="userUpdateForm">
      <input type="hidden" name="id" id="user_id">
      <label>Username: <input type="text" name="username" id="user_username"></label><br><br>
      <label>Email: <input type="email" name="email" id="user_email"></label><br><br>
      <button type="submit" name="update_user" class="admin-btn">Update</button>
    </form>
  </div>
</div>

    </div>

    <script>
function openUserModal(id, username, email) {
  document.getElementById('user_id').value = id;
  document.getElementById('user_username').value = username;
  document.getElementById('user_email').value = email;
  document.getElementById('userModal').style.display = 'block';
}
function closeUserModal() {
  document.getElementById('userModal').style.display = 'none';
}
window.onclick = function(event) {
  var modal = document.getElementById('userModal');
  if (event.target == modal) modal.style.display = "none";
}
document.querySelectorAll('a').forEach(function(link) {
  // Only apply to internal links
  if (
    link.hostname === window.location.hostname &&
    link.target !== "_blank" &&
    !link.href.startsWith('javascript:') &&
    // Prevent fade-out for Update User button (which opens modal)
    !link.closest('.modal-content') &&
    !(link.classList.contains('admin-btn') && link.getAttribute('onclick') && link.getAttribute('onclick').includes('openUserModal'))
  ) {
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
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: 1.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "bookwebsite");

// File upload configuration
$uploadDir = 'uploads/chapters/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

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

// Handle user update
$edit_user = null;
if (isset($_GET['edit_user'])) {
    $edit_id = intval($_GET['edit_user']);
    $edit_user = $conn->query("SELECT * FROM users WHERE id=$edit_id")->fetch_assoc();
}

// Handle book update
$edit_book = null;
if (isset($_GET['edit_book'])) {
    $edit_id = intval($_GET['edit_book']);
    $edit_book = $conn->query("SELECT * FROM books WHERE id=$edit_id")->fetch_assoc();
}

// Chapter Management
$current_book_id = null;
$current_book = null;
$chapters = [];
if (isset($_GET['book_id']) && is_numeric($_GET['book_id'])) {
    $current_book_id = intval($_GET['book_id']);
    $current_book = $conn->query("SELECT * FROM books WHERE id=$current_book_id")->fetch_assoc();
    
    // Fetch chapters
    $result = $conn->query("SELECT * FROM chapters WHERE book_id=$current_book_id ORDER BY chapter_number");
    while ($row = $result->fetch_assoc()) {
        $chapters[] = $row;
    }
    
    // Handle chapter deletion
    if (isset($_GET['delete_chapter'])) {
        $id = intval($_GET['delete_chapter']);
        $conn->query("DELETE FROM chapters WHERE id=$id");
        header("Location: admin_panel.php?book_id=$current_book_id");
        exit();
    }
    
    // Handle chapter addition
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addChapter'])) {
        $chapter_number = intval($_POST['chapter_number']);
        $title = $conn->real_escape_string($_POST['title']);
        $content = '';
        $chapter_type = $conn->real_escape_string($_POST['chapter_type']);
        
        // Handle file upload
        $imagePaths = [];
        if (!empty($_FILES['chapter_images']['name'][0])) {
            foreach ($_FILES['chapter_images']['name'] as $key => $name) {
                if ($_FILES['chapter_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['chapter_images']['tmp_name'][$key];
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $newName = uniqid() . '.' . $ext;
                    $destination = $uploadDir . $newName;
                    
                    if (move_uploaded_file($tmpName, $destination)) {
                        $imagePaths[] = $destination;
                    }
                }
            }
        }
        
        // If image chapter, store image paths as content
        if ($chapter_type === 'image' && !empty($imagePaths)) {
            $content = implode("\n", $imagePaths);
        } else {
            $content = $conn->real_escape_string($_POST['content']);
        }
        
        // Handle cover image upload
        $coverImage = '';
        if (!empty($_FILES['cover_image']['name'])) {
            $tmpName = $_FILES['cover_image']['tmp_name'];
            $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
            $newName = uniqid() . '.' . $ext;
            $destination = $uploadDir . $newName;
            
            if (move_uploaded_file($tmpName, $destination)) {
                $coverImage = $destination;
            }
        }
        
        $conn->query("INSERT INTO chapters (book_id, chapter_number, title, content, image, chapter_type) 
                     VALUES ($current_book_id, $chapter_number, '$title', '$content', '$coverImage', '$chapter_type')");
        header("Location: admin_panel.php?book_id=$current_book_id");
        exit();
    }
    
    // Handle chapter update
    if (isset($_POST['update_chapter'])) {
        $id = intval($_POST['id']);
        $chapter_number = intval($_POST['chapter_number']);
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $chapter_type = $conn->real_escape_string($_POST['chapter_type']);
        
        // Handle file upload for update
        $imagePaths = [];
        if (!empty($_FILES['chapter_images']['name'][0])) {
            foreach ($_FILES['chapter_images']['name'] as $key => $name) {
                if ($_FILES['chapter_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['chapter_images']['tmp_name'][$key];
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $newName = uniqid() . '.' . $ext;
                    $destination = $uploadDir . $newName;
                    
                    if (move_uploaded_file($tmpName, $destination)) {
                        $imagePaths[] = $destination;
                    }
                }
            }
        }
        
        // If image chapter, store image paths as content
        if ($chapter_type === 'image' && !empty($imagePaths)) {
            $content = implode("\n", $imagePaths);
        }
        
        // Handle cover image update
        $coverImage = '';
        if (!empty($_FILES['cover_image']['name'])) {
            $tmpName = $_FILES['cover_image']['tmp_name'];
            $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
            $newName = uniqid() . '.' . $ext;
            $destination = $uploadDir . $newName;
            
            if (move_uploaded_file($tmpName, $destination)) {
                $coverImage = $destination;
            }
        } else {
            // Keep existing cover image if not updated
            $existing = $conn->query("SELECT image FROM chapters WHERE id=$id")->fetch_assoc();
            $coverImage = $existing['image'];
        }
        
        $conn->query("UPDATE chapters SET 
                     chapter_number=$chapter_number, 
                     title='$title', 
                     content='$content', 
                     image='$coverImage',
                     chapter_type='$chapter_type'
                     WHERE id=$id");
        header("Location: admin_panel.php?book_id=$current_book_id");
        exit();
    }
}

// Fetch all books
$books = [];
$result = $conn->query("SELECT * FROM books");
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Fetch users
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
        /* Admin panel styles */
        body { 
            background: #fff6e3; 
            font-family: 'Segoe UI', Arial, sans-serif; 
            margin: 0;
            padding: 0;
        }
        
        .admin-container { 
            max-width: 1200px; 
            margin: 40px auto; 
            background: #fff; 
            border-radius: 18px; 
            box-shadow: 0 4px 24px rgba(42,93,255,0.10); 
            padding: 32px; 
        }
        
        /* Chapter upload form styles */
        .add-form { 
            margin-top: 32px; 
            background: #fff6e3; 
            padding: 18px; 
            border-radius: 12px; 
        }
        
        .chapter-type-selector { 
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        
        .chapter-type-selector label {
            display: inline-block;
            margin-right: 15px;
            cursor: pointer;
        }
        
        .upload-preview {
            border: 2px dashed #9c6b3e;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .file-list {
            list-style-type: none;
            padding: 0;
            margin: 10px 0;
        }
        
        .file-item {
            display: flex;
            align-items: center;
            padding: 8px;
            margin: 5px 0;
            background: #f9f9f9;
            border-radius: 4px;
        }
        
        .file-order {
            font-weight: bold;
            margin-right: 10px;
            min-width: 25px;
            text-align: center;
        }
        
        .file-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 4px;
        }
        
        .file-name {
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .admin-container {
                margin: 20px;
                padding: 15px;
            }
        }

        /* Button Styles */
.admin-btn, .chapter-btn, .edit-btn, .delete-btn {
    display: inline-block;
    padding: 10px 20px;
    margin: 5px;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    font-size: 14px;
}

/* Main Admin Button */
.admin-btn {
    background-color: #9c6b3e;
    color: white;
    border: 2px solid #9c6b3e;
}

.admin-btn:hover {
    background-color: #8a5d34;
    border-color: #8a5d34;
}

/* Chapter Management Button */
.chapter-btn {
    background-color: #4a6fa5;
    color: white;
    border: 2px solid #4a6fa5;
}

.chapter-btn:hover {
    background-color: #3d5d8a;
    border-color: #3d5d8a;
}

/* Edit Button */
.edit-btn {
    background-color: #4caf50;
    color: white;
    border: 2px solid #4caf50;
}

.edit-btn:hover {
    background-color: #3e8e41;
    border-color: #3e8e41;
}

/* Delete Button */
.delete-btn {
    background-color: #f44336;
    color: white;
    border: 2px solid #f44336;
}

.delete-btn:hover {
    background-color: #d32f2f;
    border-color: #d32f2f;
}

/* Action Buttons Container */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* Form Submit Buttons */
button[type="submit"] {
    background-color: #2e7d32;
    color: white;
    padding: 12px 24px;
    margin-top: 15px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #1b5e20;
}

/* Modal Buttons (if you add modals later) */
.modal-btn {
    padding: 8px 16px;
    margin: 0 10px;
    border-radius: 4px;
    font-weight: 500;
}

.modal-btn-primary {
    background-color: #2196F3;
    color: white;
}

.modal-btn-secondary {
    background-color: #f1f1f1;
    color: #333;
}

/* Small buttons for compact spaces */
.small-btn {
    padding: 5px 10px;
    font-size: 12px;
}

/* Disabled state */
button:disabled, .admin-btn:disabled {
    background-color: #cccccc;
    border-color: #cccccc;
    cursor: not-allowed;
}

/* Button icons */
.btn-with-icon {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

/* Responsive buttons */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
        gap: 5px;
    }
    
    .admin-btn, .chapter-btn, .edit-btn, .delete-btn {
        padding: 8px 12px;
        font-size: 12px;
    }
    
    button[type="submit"] {
        padding: 10px 18px;
        font-size: 14px;
    }
}
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Admin Panel</h1>
        <a href="1.php" class="admin-btn" style="margin-top:16px; display:inline-block;">Back to Library</a>
        
        <?php if ($current_book_id): ?>
            <a href="admin_panel.php" class="admin-btn">Back to All Books</a>
            <h2>Managing Chapters for: <?php echo htmlspecialchars($current_book['title']); ?></h2>
            
            <!-- Chapters List -->
            <table>
                <tr>
                    <th>Chapter #</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Cover</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($chapters as $chapter): ?>
                <tr>
                    <td><?php echo $chapter['chapter_number']; ?></td>
                    <td><?php echo htmlspecialchars($chapter['title']); ?></td>
                    <td><?php echo $chapter['chapter_type'] === 'image' ? 'Image Story' : 'Text Story'; ?></td>
                    <td>
                        <?php if ($chapter['image']): ?>
                            <img src="<?php echo htmlspecialchars($chapter['image']); ?>" alt="Chapter cover" style="height:60px;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="edit-btn" onclick="openChapterModal(
                                <?php echo $chapter['id']; ?>,
                                <?php echo $chapter['chapter_number']; ?>,
                                '<?php echo htmlspecialchars($chapter['title'], ENT_QUOTES); ?>',
                                `<?php echo htmlspecialchars(str_replace(array("\r", "\n"), '', $chapter['content']), ENT_QUOTES); ?>`,
                                '<?php echo htmlspecialchars($chapter['image'], ENT_QUOTES); ?>',
                                '<?php echo $chapter['chapter_type']; ?>'
                            )">Edit</button>
                            <a href="admin_panel.php?book_id=<?php echo $current_book_id; ?>&delete_chapter=<?php echo $chapter['id']; ?>" class="delete-btn" onclick="return confirm('Delete this chapter?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <!-- Add Chapter Form -->
            <form class="add-form" method="post" id="chapterForm" enctype="multipart/form-data">
                <h2>Add New Chapter</h2>
                <input type="hidden" name="id" id="chapter_id">
                
                <div class="chapter-type-selector">
                    <label>
                        <input type="radio" name="chapter_type" value="text" id="type-text" onchange="toggleChapterType()"> Text Story
                    </label>
                    <label>
                        <input type="radio" name="chapter_type" value="image" id="type-image" onchange="toggleChapterType()" checked> Image Story
                    </label>
                </div>
                
                <label>Chapter Number:</label>
                <input type="number" name="chapter_number" id="chapter_number" required min="1">
                
                <label>Title:</label>
                <input type="text" name="title" id="chapter_title" required>
                
                <!-- Text Content Field -->
                <div id="text-content-field" class="chapter-content-field">
                    <label>Story Content:</label>
                    <textarea name="content" id="chapter_content" rows="5"></textarea>
                </div>
                
                <!-- Image Content Field -->
                <div id="image-content-field" class="chapter-content-field active">
                    <label>Upload Images (Multiple):</label>
                    <input type="file" name="chapter_images[]" id="chapter_images" multiple accept="image/*" required>
                    <div id="image-preview" class="upload-preview">
                        <p>No images selected</p>
                        <ul class="file-list" id="file-list"></ul>
                    </div>
                    <small>Images will be displayed in the order you select them</small>
                </div>
                
                <label>Cover Image (optional):</label>
                <input type="file" name="cover_image" id="cover_image" accept="image/*">
                <img src="" id="cover_image_preview" class="chapter-image-preview" style="display:none;">
                
                <button type="submit" name="addChapter" class="admin-btn">Add Chapter</button>
            </form>
            
        <?php else: ?>
            <!-- Regular Admin Panel View -->
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
                    <td><img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="cover" style="height:60px;"></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo $book['available'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="admin_panel.php?book_id=<?php echo $book['id']; ?>" class="chapter-btn">Chapters</a>
                            <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="edit-btn">Edit</a>
                            <a href="admin_panel.php?delete=<?php echo $book['id']; ?>" class="delete-btn" onclick="return confirm('Delete this book?');">Delete</a>
                        </div>
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
                        <div class="action-buttons">
                            <a href="#" class="edit-btn" onclick="openUserModal('<?php echo isset($user['id']) ? $user['id'] : ''; ?>', '<?php echo isset($user['username']) ? htmlspecialchars($user['username'], ENT_QUOTES) : ''; ?>', '<?php echo isset($user['email']) ? htmlspecialchars($user['email'], ENT_QUOTES) : ''; ?>'); return false;">
                                Update
                            </a>
                            <a href="admin_panel.php?delete_user=<?php echo isset($user['id']) ? $user['id'] : ''; ?>" class="delete-btn" onclick="return confirm('Delete this user?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <script>
        // Enhanced image preview for admin panel
        document.getElementById('chapter_images').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('image-preview');
            const fileList = document.getElementById('file-list');
            fileList.innerHTML = '';
            
            if (this.files && this.files.length > 0) {
                previewContainer.querySelector('p').style.display = 'none';
                
                Array.from(this.files).forEach((file, index) => {
                    if (file.type.match('image.*')) {
                        const listItem = document.createElement('li');
                        listItem.className = 'file-item';
                        
                        const orderBadge = document.createElement('div');
                        orderBadge.className = 'file-order';
                        orderBadge.textContent = `${index + 1}`;
                        
                        const preview = document.createElement('img');
                        preview.className = 'file-thumb';
                        preview.src = URL.createObjectURL(file);
                        
                        const fileName = document.createElement('div');
                        fileName.className = 'file-name';
                        fileName.textContent = file.name;
                        
                        listItem.appendChild(orderBadge);
                        listItem.appendChild(preview);
                        listItem.appendChild(fileName);
                        fileList.appendChild(listItem);
                    }
                });
            } else {
                previewContainer.querySelector('p').style.display = 'block';
            }
        });
        
        // Toggle between text and image fields
        function toggleChapterType() {
            const isImage = document.getElementById('type-image').checked;
            document.getElementById('text-content-field').style.display = isImage ? 'none' : 'block';
            document.getElementById('image-content-field').style.display = isImage ? 'block' : 'none';
            document.getElementById('chapter_content').required = !isImage;
            document.getElementById('chapter_images').required = isImage;
        }
        
        // Initialize
        toggleChapterType();
    </script>
</body>
</html>
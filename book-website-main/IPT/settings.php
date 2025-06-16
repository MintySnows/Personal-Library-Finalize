<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$conn = new mysqli("localhost", "root", "", "bookwebsite");

// Fetch user info
$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

$show_otp_form = false;
$pending_email = '';
$pending_username = '';
$otp_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // When Save is clicked (profile form)
    if (isset($_POST['start_verification'])) {
        // Step 1: User submits new info, send OTP
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $otp = rand(100000, 999999);

        // Store pending info and OTP in session
        $_SESSION['pending_profile'] = [
            'username' => $username,
            'email' => $email,
            'otp' => $otp
        ];

        // Send OTP to email (will fail on localhost, so show on screen)
        $to = $email;
        $subject = "Your OTP Verification Code";
        $message = "Your OTP code is: $otp";
        $headers = "From: noreply@yourdomain.com";
        @mail($to, $subject, $message, $headers); // Suppress warning on localhost

        $show_otp_form = true;
        $pending_email = $email;

        // For local development, show OTP on screen
        $dev_otp_message = "<div style='color:#b00;text-align:center;'>[DEV ONLY] Your OTP is: <b>$otp</b></div>";
    }
    // When Verify OTP is clicked (OTP form)
    elseif (isset($_POST['verify_otp'])) {
        // Step 2: User submits OTP
        $entered_otp = $_POST['otp'];
        $pending = $_SESSION['pending_profile'] ?? null;
        if ($pending && $entered_otp == $pending['otp']) {
            // OTP correct, update user
            $username = $pending['username'];
            $email = $pending['email'];
            $conn->query("UPDATE users SET username='$username', email='$email' WHERE id=$user_id");
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            unset($_SESSION['pending_profile']);
            header("Location: user_profile.php?success=1");
            exit();
        } else {
            $show_otp_form = true;
            $otp_error = "Invalid OTP. Please try again.";
            $pending_email = $pending['email'] ?? '';
            $pending_username = $pending['username'] ?? '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Profile</title>
  <style>
    body { font-family: Arial, sans-serif; background: #fff6e3; }
    body {
  transition: opacity 0.5s;
}
body.fade-out {
  opacity: 0;
}
    .profile-container { max-width: 400px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px #e0c9a6; padding: 32px; }
    h1 { color: #9c6b3e; text-align: center; }
    label { display: block; margin-top: 22px; color: #9c6b3e; font-size: 1.2em; font-weight: bold; text-align: center; }
    input { width: 100%; padding: 12px; border-radius: 20px; border: none; background: #fff3d1; margin-top: 8px; font-size: 1em; text-align: center; }
    .buttons { margin-top: 32px; display: flex; justify-content: space-between; }
    button { padding: 16px 32px; border: none; border-radius: 20px; background: #9c6b3e; color: #fff; font-weight: 700; font-size: 1.1em; cursor: pointer; min-width: 120px; }
    button:hover { background: #7b522e; }
    .success { color: green; margin-bottom: 16px; text-align: center; }
    .error { color: #b00; margin-bottom: 16px; text-align: center; }
  </style>
</head>
<body>
  <div class="profile-container">
    <h1>User Profile</h1>
    <?php if (isset($_GET['success'])): ?>
      <div class="success">Profile updated!</div>
    <?php endif; ?>

    <!-- Add Log Out Button Here -->
    <form method="post" action="logout.php" style="text-align:center; margin-bottom:20px;">
      <button type="submit" class="logout" name="logoutBtn" style="background:#f5c748;color:#7b522e;padding:10px 32px;border-radius:20px;font-size:1.1em;font-weight:700;cursor:pointer;border:none;margin-bottom:16px;">
        Log Out
      </button>
    </form>
    <!-- End Log Out Button -->

    <?php if ($show_otp_form): ?>
      <?php if (!empty($dev_otp_message)) echo $dev_otp_message; ?>
      <?php if ($otp_error): ?>
        <div class="error"><?php echo $otp_error; ?></div>
      <?php endif; ?>
      <form method="post" autocomplete="off">
        <label>Enter the OTP sent to <br><b><?php echo htmlspecialchars($pending_email); ?></b>:</label>
        <input
          type="text"
          name="otp"
          placeholder="Enter 6-digit OTP"
          required
          maxlength="6"
          pattern="\d{6}"
        >
        <div class="buttons">
          <button type="submit" name="verify_otp">Verify OTP</button>
          <button type="button" onclick="window.location.href='user.php'">Cancel</button>
        </div>
      </form>
    <?php else: ?>
      <!-- Profile form (shows Save button) -->
      <form method="post" autocomplete="off">
        <label>Username:
          <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
        </label>
        <label>Email:
          <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
        </label>
        <div class="buttons">
          <button type="submit" name="start_verification">Save</button>
          <button type="button" onclick="window.location.href='1.php'">Cancel</button>
        </div>
      </form>
    <?php endif; ?>
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
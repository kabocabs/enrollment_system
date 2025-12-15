<?php
require_once 'config.php';
require_once 'functions.php';

if (is_logged_in()) {
    $user = current_user();
    if ($user['role'] === 'admin') header('Location: dashboard_admin.php');
    if ($user['role'] === 'faculty') header('Location: dashboard_faculty.php');
    header('Location: dashboard_student.php');
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($email && $pass) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($u && password_verify($pass, $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            if ($u['role'] === 'admin') header('Location: dashboard_admin.php');
            elseif ($u['role'] === 'faculty') header('Location: dashboard_faculty.php');
            else header('Location: dashboard_student.php');
            exit;
        } else $err = "Invalid email or password.";
    } else $err = "Fill required fields.";
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login — Enrollment System</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="centered">
  <div class="card form-card">
    <h2>Login</h2>
    <?php if($err): ?><div class="alert"><?=$err?></div><?php endif; ?>
    <form method="post">
      <label>Email</label>
      <input name="email" type="email" required>
      <label>Password</label>
      <input name="password" type="password" required>
      <button type="submit">Login</button>
    </form>
    <p>New user? <a href="register.php">Register</a></p>
  </div>
</body>
</html>

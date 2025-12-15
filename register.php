<?php
require_once 'config.php';
require_once 'functions.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student';
    if (!$fullname || !$email || !$password || !in_array($role, ['student','faculty','admin'])) {
        $err = "Please fill all required fields.";
    } else {
        $c = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $c->execute([$email]);
        if ($c->fetchColumn() > 0) $err = "Email already registered.";
        else {
            $profile_name = upload_image('profile_pic','prof_');
            $sign_name = upload_image('signature_pic','sign_');
            if (!$profile_name || !$sign_name) {
                $err = "Profile picture and signature are required and must be images (jpg/png/webp).";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $pdo->prepare("INSERT INTO users (fullname,email,password,role,profile_pic,signature_pic) VALUES (?,?,?,?,?,?)");
                $ins->execute([$fullname,$email,$hash,$role,$profile_name,$sign_name]);
                header('Location: index.php');
                exit;
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register — Enrollment System</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="centered">
  <div class="card form-card">
    <h2>Register</h2>
    <?php if($err): ?><div class="alert"><?=$err?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <label>Full Name</label><input name="fullname" required>
      <label>Email</label><input name="email" type="email" required>
      <label>Password</label><input name="password" type="password" required>
      <label>Role</label>
      <select name="role">
        <option value="student">Student</option>
        <option value="faculty">Faculty</option>
        <option value="admin">Administrator</option>
      </select>
      <label>Profile Picture</label><input name="profile_pic" type="file" accept="image/*" required>
      <label>Signature Image</label><input name="signature_pic" type="file" accept="image/*" required>
      <button type="submit">Create account</button>
    </form>
    <p><a href="index.php">Back to login</a></p>
  </div>
</body>
</html>

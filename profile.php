<?php
require_once 'functions.php';
if (!is_logged_in()) header('Location: index.php');
$user = current_user();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Profile</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
  <header class="topbar"><div class="container"><h3>Profile</h3><nav><a href="<?= $user['role']=='admin' ? 'dashboard_admin.php' : ($user['role']=='faculty'? 'dashboard_faculty.php':'dashboard_student.php') ?>">Back</a> | <a href="logout.php">Logout</a></nav></div></header>
  <main class="container">
    <div class="card profile-card">
      <img src="<?=UPLOAD_PATH . htmlspecialchars($user['profile_pic'])?>" class="profile-photo">
      <h3><?=htmlspecialchars($user['fullname'])?></h3>
      <p><strong>Email:</strong> <?=htmlspecialchars($user['email'])?></p>
      <p><strong>Role:</strong> <?=htmlspecialchars($user['role'])?></p>
      <div><strong>Signature:</strong><br>
        <img src="<?=UPLOAD_PATH . htmlspecialchars($user['signature_pic'])?>" class="signature">
      </div>
    </div>
  </main>
</body>
</html>

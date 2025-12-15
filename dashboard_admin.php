<?php
require_once 'functions.php';
require_role('admin');
$user = current_user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar"><div class="container">
    <h3>Admin Dashboard — <?=$user['fullname']?></h3>
    <nav><a href="profile.php">Profile</a> | <a href="users_admin.php">Manage Users</a> | <a href="subjects_admin.php">Subjects</a> | <a href="prerequisites_admin.php">Prerequisites</a> | <a href="logout.php">Logout</a></nav>
  </div></header>

  <main class="container">
    <div class="card">
      <h4>Quick Overview</h4>
      <?php
        $tot = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stu = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
        $fac = $pdo->query("SELECT COUNT(*) FROM users WHERE role='faculty'")->fetchColumn();
      ?>
      <p>Total users: <?=$tot?> — Students: <?=$stu?> — Faculty: <?=$fac?></p>
    </div>
  </main>
</body>
</html>


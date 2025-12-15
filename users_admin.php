<?php
require_once 'functions.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $role = $_POST['role'];
        $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$role,$id]);
    }
    header('Location: users_admin.php');
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Users — Admin</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
  <header class="topbar"><div class="container"><h3>User Management</h3><nav><a href="dashboard_admin.php">Back</a></nav></div></header>
  <main class="container">
    <section class="card">
      <table class="table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Profile</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($users as $u): ?>
          <tr>
            <td><?=$u['fullname']?></td>
            <td><?=$u['email']?></td>
            <td>
              <form method="post">
                <input type="hidden" name="id" value="<?=$u['id']?>">
                <select name="role"><option value="student" <?= $u['role']=='student'?'selected':''?>>Student</option><option value="faculty" <?= $u['role']=='faculty'?'selected':''?>>Faculty</option><option value="admin" <?= $u['role']=='admin'?'selected':''?>>Admin</option></select>
                <button name="update">Save</button>
              </form>
            </td>
            <td>
              <?php if($u['profile_pic']): ?><img src="<?=UPLOAD_PATH . htmlspecialchars($u['profile_pic'])?>" class="thumb"><?php endif; ?>
              <?php if($u['signature_pic']): ?><img src="<?=UPLOAD_PATH . htmlspecialchars($u['signature_pic'])?>" class="thumb smallsig"><?php endif; ?>
            </td>
            <td>
              <form method="post" onsubmit="return confirm('Delete user?');">
                <input type="hidden" name="id" value="<?=$u['id']?>"><button name="delete">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>

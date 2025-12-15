<?php
require_once 'functions.php';
require_role('admin');

$action = $_GET['action'] ?? null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['create'])) {
        $code = trim($_POST['code']);
        $name = trim($_POST['name']);
        $units = (int)$_POST['units'];
        $ins = $pdo->prepare("INSERT INTO subjects (code,name,units) VALUES (?,?,?)");
        $ins->execute([$code,$name,$units]);
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $code = trim($_POST['code']);
        $name = trim($_POST['name']);
        $units = (int)$_POST['units'];
        $pdo->prepare("UPDATE subjects SET code=?,name=?,units=? WHERE id=?")->execute([$code,$name,$units,$id]);
    } elseif (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM subjects WHERE id=?")->execute([$id]);
    }
    header('Location: subjects_admin.php');
    exit;
}
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Subjects — Admin</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
  <header class="topbar"><div class="container"><h3>Subjects Management</h3><nav><a href="dashboard_admin.php">Back</a></nav></div></header>
  <main class="container">
    <section class="card">
      <h4>Create Subject</h4>
      <form method="post">
        <label>Code</label><input name="code" required>
        <label>Name</label><input name="name" required>
        <label>Units</label><input name="units" type="number" value="3" required>
        <button name="create" type="submit">Create</button>
      </form>
    </section>

    <section class="card">
      <h4>Existing Subjects</h4>
      <table class="table">
        <thead><tr><th>Code</th><th>Name</th><th>Units</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($subjects as $s): ?>
          <tr>
            <td><?=$s['code']?></td>
            <td><?=$s['name']?></td>
            <td><?=$s['units']?></td>
            <td>
              <form method="post" style="display:inline">
                <input type="hidden" name="id" value="<?=$s['id']?>">
                <input name="code" value="<?=$s['code']?>">
                <input name="name" value="<?=$s['name']?>">
                <input name="units" value="<?=$s['units']?>" style="width:60px">
                <button name="update">Save</button>
              </form>
              <form method="post" style="display:inline" onsubmit="return confirm('Delete subject?');">
                <input type="hidden" name="id" value="<?=$s['id']?>"><button name="delete">Delete</button>
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

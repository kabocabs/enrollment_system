<?php
require_once 'functions.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $subject = (int)$_POST['subject_id'];
        $pr = (int)$_POST['prereq_id'];
        if ($subject && $pr && $subject !== $pr) {
            $ins = $pdo->prepare("INSERT IGNORE INTO prerequisites (subject_id, prereq_subject_id) VALUES (?, ?)");
            $ins->execute([$subject, $pr]);
        }
    } elseif (isset($_POST['remove'])) {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM prerequisites WHERE id=?")->execute([$id]);
    }
    header('Location: prerequisites_admin.php');
    exit;
}

$subjects = $pdo->query("SELECT * FROM subjects ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

$pr = $pdo->query("
    SELECT 
        p.*, 
        s.code AS subject_code, s.name AS subject_name, 
        sp.code AS prereq_code, sp.name AS prereq_name 
    FROM prerequisites p
    JOIN subjects s ON p.subject_id = s.id
    JOIN subjects sp ON p.prereq_subject_id = sp.id
    ORDER BY s.code
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Prerequisites — Admin</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="container">
        <h3>Prerequisites Management</h3>
        <nav><a href="dashboard_admin.php">Back</a></nav>
    </div>
</header>

<main class="container">
    <section class="card">
        <h4>Add Prerequisite</h4>
        <form method="post">
            <label>Subject</label>
            <select name="subject_id" required>
                <?php foreach($subjects as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['code'] ?> — <?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Prerequisite</label>
            <select name="prereq_id" required>
                <?php foreach($subjects as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['code'] ?> — <?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>

            <button name="add" type="submit">Add</button>
        </form>
    </section>

    <section class="card">
        <h4>Existing Prerequisites</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Prerequisite</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pr as $p): ?>
                    <tr>
                        <td><?= $p['subject_code'] ?> — <?= $p['subject_name'] ?></td>
                        <td><?= $p['prereq_code'] ?> — <?= $p['prereq_name'] ?></td>
                        <td>
                            <form method="post" style="display:inline">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button name="remove">Remove</button>
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

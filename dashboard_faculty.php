<?php
require_once 'functions.php';
require_role('faculty');

$user = current_user();
$stmt = $pdo->prepare("
    SELECT 
        fa.id AS fa_id,
        fa.subject_id,
        fa.school_year,
        fa.semester,
        s.code,
        s.name
    FROM faculty_assignments fa
    JOIN subjects s ON fa.subject_id = s.id
    WHERE fa.faculty_id = ?
");
$stmt->execute([$user['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Faculty Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="topbar">
  <div class="container">
    <h3>Faculty Dashboard — <?=htmlspecialchars($user['fullname'])?></h3>
    <nav>
      <a href="profile.php">Profile</a> |
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="container">

<section class="card">
<h4>Your Classes</h4>

<?php if (empty($assignments)): ?>
    <p>No assigned subjects yet.</p>
<?php else: ?>

<?php foreach ($assignments as $a): ?>

<div class="card small">
  <h5><?=$a['code']?> — <?=$a['name']?></h5>
  <p><strong>School Year:</strong> <?=$a['school_year']?> | 
     <strong>Semester:</strong> <?=$a['semester']?></p>

<?php
/* Get enrolled students for this subject */
$st = $pdo->prepare("
    SELECT 
        e.id AS enrollment_id,
        e.status,
        e.grade,
        u.fullname,
        u.profile_pic,
        u.signature_pic
    FROM enrollments e
    JOIN users u ON e.student_id = u.id
    WHERE e.subject_id = ?
    ORDER BY u.fullname
");
$st->execute([$a['subject_id']]);
$students = $st->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (empty($students)): ?>
    <p>No students enrolled.</p>
<?php else: ?>

<table class="table">
<thead>
<tr>
  <th>Student</th>
  <th>Profile</th>
  <th>Status</th>
  <th>Grade</th>
  <th>Action</th>
</tr>
</thead>
<tbody>

<?php foreach ($students as $st): ?>
<tr>
  <td><?=htmlspecialchars($st['fullname'])?></td>
  <td>
    <img src="<?=UPLOAD_PATH . $st['profile_pic']?>" class="thumb" alt="Profile">
    <img src="<?=UPLOAD_PATH . $st['signature_pic']?>" class="smallsig" alt="Signature">
  </td>
  <td><?=htmlspecialchars($st['status'])?></td>
  <td><?= $st['grade'] ?: '—' ?></td>
  <td>
    <form method="post" action="submit_grade.php">
      <input type="hidden" name="enrollment_id" value="<?=$st['enrollment_id']?>">

      <input 
        name="grade" 
        value="<?=htmlspecialchars($st['grade'])?>" 
        placeholder="A / B / C / D / F"
        required
      >

      <select name="status">
        <option value="enrolled" <?=$st['status']=='enrolled'?'selected':''?>>Enrolled</option>
        <option value="completed" <?=$st['status']=='completed'?'selected':''?>>Completed</option>
        <option value="dropped" <?=$st['status']=='dropped'?'selected':''?>>Dropped</option>
      </select>

      <button type="submit">Save</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

<?php endif; ?>

</div>
<?php endforeach; ?>

<?php endif; ?>
</section>

</main>
</body>
</html>

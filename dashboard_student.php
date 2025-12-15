<?php
require_once 'functions.php';
require_role('student');
$user = current_user();

$stmt = $pdo->query("SELECT s.* FROM subjects s ORDER BY s.code");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// student's enrollment statuses
$enStmt = $pdo->prepare("SELECT e.*, s.code, s.name FROM enrollments e JOIN subjects s ON e.subject_id=s.id WHERE e.student_id=?");
$enStmt->execute([$user['id']]);
$enrolled = $enStmt->fetchAll(PDO::FETCH_ASSOC);

function can_enroll($student_id, $subject_id, $pdo) {
    
    $ps = $pdo->prepare("SELECT prereq_subject_id FROM prerequisites WHERE subject_id = ?");
    $ps->execute([$subject_id]);
    $pr = $ps->fetchAll(PDO::FETCH_COLUMN);
    if (empty($pr)) return true;
    $q = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id=? AND subject_id=? AND status='completed'");
    foreach ($pr as $pid) {
        $q->execute([$student_id, $pid]);
        if ($q->fetchColumn() == 0) return false;
    }
    return true;
}

$enroll_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_id'])) {
    $sid = (int)$_POST['subject_id'];

    $c = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id=? AND subject_id=?");
    $c->execute([$user['id'],$sid]);
    if ($c->fetchColumn()>0) $enroll_msg = "Already enrolled in this subject.";
    else {
        if (can_enroll($user['id'],$sid,$pdo)) {
            $ins = $pdo->prepare("INSERT INTO enrollments (student_id,subject_id,status) VALUES (?,?, 'enrolled')");
            $ins->execute([$user['id'],$sid]);
            $enroll_msg = "Enrolled successfully.";
            header('Location: dashboard_student.php'); exit;
        } else $enroll_msg = "Cannot enroll: missing prerequisite(s).";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar">
    <div class="container">
      <h3>Student Dashboard — Welcome <?=$user['fullname']?></h3>
      <nav><a href="profile.php">Profile</a> | <a href="logout.php">Logout</a></nav>
    </div>
  </header>

  <main class="container">
    <section class="card">
      <h4>Available Subjects</h4>
      <?php if($enroll_msg): ?><div class="alert"><?=$enroll_msg?></div><?php endif; ?>
      <table class="table">
        <thead><tr><th>Code</th><th>Name</th><th>Units</th><th>Prereqs</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($subjects as $s): 
            // list prereqs
            $pstmt = $pdo->prepare("SELECT pr.prereq_subject_id, sub.code FROM prerequisites pr JOIN subjects sub ON pr.prereq_subject_id=sub.id WHERE pr.subject_id=?");
            $pstmt->execute([$s['id']]);
            $prlist = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
          <tr>
            <td><?=$s['code']?></td>
            <td><?=$s['name']?></td>
            <td><?=$s['units']?></td>
            <td><?php
                if(empty($prlist)) echo '—';
                else {
                    $out = [];
                    foreach($prlist as $p) $out[] = $p['code'];
                    echo implode(', ', $out);
                }
            ?></td>
            <td>
              <form method="post" style="display:inline">
                <input type="hidden" name="subject_id" value="<?=$s['id']?>">
                <button type="submit">Enroll</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section class="card">
      <h4>Your Enrollments</h4>
      <table class="table">
        <thead><tr><th>Code</th><th>Name</th><th>Status</th><th>Grade</th></tr></thead>
        <tbody>
        <?php foreach($enrolled as $e): ?>
          <tr>
            <td><?=$e['code']?></td>
            <td><?=$e['name']?></td>
            <td><?=$e['status']?></td>
            <td><?=$e['grade'] ?? '—'?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>

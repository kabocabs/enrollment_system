<?php
require_once 'functions.php';
require_role('faculty');

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $enr = (int)($_POST['enrollment_id'] ?? 0);
    $grade = trim($_POST['grade'] ?? null);
    $status = $_POST['status'] ?? 'enrolled';

    $q = $pdo->prepare("SELECT e.*, fa.id as fa_id, fa.faculty_id FROM enrollments e JOIN faculty_assignments fa ON e.subject_id = fa.subject_id WHERE e.id=?");
    $q->execute([$enr]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if (!$row) die("Invalid enrollment.");
    if ($row['faculty_id'] != $_SESSION['user_id']) die("You are not assigned to this class.");
    // update
    $u = $pdo->prepare("UPDATE enrollments SET grade=?, status=?, faculty_id=? WHERE id=?");
    $u->execute([$grade, $status, $_SESSION['user_id'], $enr]);
    header('Location: dashboard_faculty.php');
    exit;
}

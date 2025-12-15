<?php
require_once 'config.php';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}
function current_user() {
    global $pdo;
    if (!is_logged_in()) return null;
    static $user = null;
    if ($user === null) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return $user;
}
function require_role($roles = []) {
    if (!is_logged_in()) header('Location: index.php');
    $user = current_user();
    if (!in_array($user['role'], (array)$roles)) {
        die("Access denied.");
    }
}

function upload_image($file_field, $prefix = '') {
    if (!isset($_FILES[$file_field]) || $_FILES[$file_field]['error'] !== UPLOAD_ERR_OK) return null;
    $f = $_FILES[$file_field];
    $allowed = ['image/png','image/jpeg','image/jpg','image/webp'];
    if (!in_array($f['type'], $allowed)) return null;
    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
    $name = $prefix . time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
    $target = UPLOAD_DIR . $name;
    if (move_uploaded_file($f['tmp_name'], $target)) {
        return $name;
    }
    return null;
}
?>

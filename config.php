<?php
session_start();

$DB_HOST = '127.0.0.1';
$DB_NAME = 'enroll_system';
$DB_USER = 'root';
$DB_PASS = ''; 

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_PATH', 'uploads/'); // relative path for HTML
// helper: ensure upload dir exists
if (!file_exists(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
?>

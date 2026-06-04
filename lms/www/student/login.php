<?php
session_start();

if (!isset($_POST['submit'])) {
    header('Location: index.php');
    exit;
}

require_once 'connect/db.php';
require_once 'connect/fun.php';

$db = new connect();
$con = $db->dbconnect();
if (!$con) {
    header('Location: index.php?msg=' . urlencode('Database connection failed. Start MySQL in XAMPP.'));
    exit;
}

$dconection = new fun($con);

$error = false;
$username = $_POST['sid'] ?? '';
$id = substr($username, 3);
$password = $_POST['password'] ?? '';

if (empty($id) || empty($password)) {
    header('Location: index.php?msg=' . urlencode('Please enter student ID and password.'));
    exit;
}

$output = $dconection->login($id, $password);
if ($output[1]) {
    if ($output[0] == 'Not Verified') {
        $msg = $output[0] . '! Contact Your Administrator';
        header('Location: index.php?msg=' . urlencode($msg));
        exit;
    }

    unset($_SESSION['username'], $_SESSION['is_valid'], $_SESSION['email']);
    $_SESSION['sid'] = $username;
    $_SESSION['is_start'] = true;
    header('Location: dashboard.php');
    exit;
}

header('Location: index.php?msg=' . urlencode($output[0]));
exit;

<?php
session_start();
require_once 'models/UserModel.php';
$userModel = new UserModel();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id']) && !empty($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $id = $_POST['id'];

        if ($_SESSION['id'] == $id) {
            $userModel->deleteUserById($id);
            header('location: list_users.php');
            exit;
        } else {
            $_SESSION['error_message'] = "Bạn không có quyền xóa người dùng này.";
            header('location: list_users.php');
            exit;
        }
    }
}

header('location: list_users.php');
?>
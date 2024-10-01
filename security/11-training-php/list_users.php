<?php
// Start the session
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Kiểm tra đăng nhập
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit;
}

// Hàm mã hóa ID
function encodeId($id)
{
    $encoded = strrev($id) . '*&BUYG';
    return base64_encode($encoded);  // Mã hóa bằng base64
}

$params = [];
if (!empty($_GET['keyword'])) {
    $params['keyword'] = $_GET['keyword'];
}

$users = $userModel->getUsers($params);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <?php include 'views/meta.php' ?>
</head>

<body class="bg-info">
    <?php if (isset($errorMessage)) { ?>

        <div class="alert alert-danger" role="alert">
            <strong>Error!</strong> <?php echo $errorMessage; ?>
            <button type="button" class="btn btn-danger btn-sm pull-right" data-dismiss="alert" aria-label="Close">
                OK
            </button>
        </div>
    <?php } ?>

    <?php include 'views/header.php' ?>
    <div class="container">
        <?php if (!empty($users)) { ?>
            <div class="alert alert-success" role="alert">
                List of users! <br>
            </div>
            <?php if (isset($_SESSION['error_message'])) {
                ?>
                <div class="alert alert-danger container" role="alert">
                    <?php echo $_SESSION['error_message']; ?>
                    <?php unset($_SESSION['error_message']); // Clear ?>
                </div>
            <?php } ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Fullname</th>
                        <th scope="col">Email</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <?php $encodedId = encodeId($user['id']); ?>
                            <th scope="row"><?php echo $user['id'] ?></th>
                            <td>
                                <?php echo $user['name'] ?>
                            </td>
                            <td> <?php
                            if ($user['fullname']) {
                                echo $user['fullname'];
                            } else
                                echo "Không khả dụng"
                                    ?>
                                </td>
                                <td>
                                    <?php
                            if ($user['email']) {
                                echo $user['email'];
                            } else
                                echo "Không khả dụng"
                                    ?>
                                </td>
                                <td>
                                    <a href="form_user.php?id=<?php echo urlencode($encodedId); ?>">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true" title="Update"></i>
                                </a>
                                <a href="view_user.php?id=<?php echo $user['id'] ?>">
                                    <i class="fa fa-eye" aria-hidden="true" title="View"></i>
                                </a>
                                <form action="delete_user.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">
                                        <i class="fa fa-eraser" aria-hidden="true" title="Xóa"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert alert-dark" role="alert">
                This is a dark alert—check it out!
            </div>
            <button type="button" class="btn btn-primary" onclick="window.history.back()">Cancel</button>
        <?php } ?>
    </div>
</body>

</html>
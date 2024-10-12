<?php
// Start the session
session_start();
require_once 'models/UserModel.php';
$userModel = new UserModel();

$user = NULL; // Add new user
$_id = NULL;

// Hàm giải mã ID
function decodeId($encodedId)
{
    list($encryptedData, $iv) = explode('::', base64_decode($encodedId), 2);
    return openssl_decrypt($encryptedData, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, base64_decode($iv));
}

if (!empty($_GET['id'])) {
    try {
        // Giải mã ID trước khi sử dụng
        $_id = decodeId($_GET['id']);
        $user = $userModel->findUserById($_id); // Update existing user
    } catch (Exception $e) {
        die("ID người dùng không hợp lệ");
    }
}

if (!empty($_POST['submit'])) {
    if (!empty($_id)) {
        $updateSuccess = $userModel->updateUser($_POST);
        if (!$updateSuccess) {
            $errorMessage = "Người dùng đã được chỉnh sửa bởi người khác. Vui lòng làm mới trang.";
        } else {
            header('location: list_users.php');
            exit; // Đảm bảo dừng thực hiện mã sau khi chuyển hướng
        }
    } else {
        $insertSuccess = $userModel->insertUser($_POST);
        if (!$insertSuccess) {
            $errorMessage = "Email đã tồn tại. Vui lòng chọn email khác.";
        } else {
            header('location: list_users.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>

<body>

    <?php include 'views/header.php' ?>
    <div class="container">
        <?php if (isset($errorMessage)) { ?>
            <div class="alert alert-danger" role="alert">
                <strong>Error!</strong> <?php echo $errorMessage; ?>
                <button type="button" class="btn btn-danger btn-sm pull-right" data-dismiss="alert" aria-label="Close">
                    OK
                </button>
            </div>
        <?php } ?>

        <?php if ($user || !isset($_id)) { ?>
            <div class="alert alert-warning" role="alert">
                User form
            </div>
            <form method="POST" name="userForm" onsubmit="return validateForm()">
                <div id="errorMessages" style="color: red;"></div>
                <?php if ($user) { ?>
                    <input type="hidden" name="last_updated" value="<?php echo $user[0]['last_updated'] ?>">
                <?php } ?>
                <input type="hidden" name="id" value="<?php echo $_id ?>">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input class="form-control" name="name" placeholder="Name" value='<?php if (!empty($user[0]['name']))
                        echo $user[0]['name'] ?>'>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" value='<?php if (!empty($user[0]['password']))
                        echo $user[0]['password'] ?>'>
                    </div>
                    <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-primary" onclick="window.history.back()">Cancel</button>
                </form>
        <?php } else { ?>
            <div class="alert alert-success" role="alert">
                User not found!
            </div>
        <?php } ?>
    </div>
    <script>
        function validateForm() {
            var name = document.forms["userForm"]["name"].value;
            var password = document.forms["userForm"]["password"].value;
            var errorMessages = "";

            var namePattern = /^[A-Za-z0-9]{5,15}$/;
            if (name == "") {
                errorMessages += "Tên là bắt buộc.<br>";
            } else if (!namePattern.test(name)) {
                errorMessages += "Tên không được có ký tự đặc biệc và phải chứa từ 5 đến 15 ký tự và chỉ được chứa chữ cái hoặc số và không có khoảng trắng.<br>";
            }

            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[~!@#$%^&*()])[A-Za-z\d~!@#$%^&*()]{5,10}$/;
            if (password == "") {
                errorMessages += "Mật khẩu là bắt buộc.<br>";
            } else if (!passwordPattern.test(password)) {
                errorMessages += "Mật khẩu phải chứa từ 5 đến 10 ký tự, bao gồm ít nhất một chữ thường, một chữ hoa, một số và một ký tự đặc biệt (~!@#$%^&*()).<br>";
            }

            if (errorMessages != "") {
                document.getElementById("errorMessages").innerHTML = errorMessages;
                return false;
            }

            // Nếu không có lỗi, cho phép form được submit
            return true;
        }
    </script>


</body>

</html>
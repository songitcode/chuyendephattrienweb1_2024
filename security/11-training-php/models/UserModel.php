<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel
{

    public function findUserById($id)
    {
        $sql = 'SELECT * FROM users WHERE id = ' . $id;
        $user = $this->select($sql);

        return $user;
    }

    public function findUser($keyword)
    {
        $sql = 'SELECT * FROM users WHERE user_name LIKE %' . $keyword . '%' . ' OR user_email LIKE %' . $keyword . '%';
        $user = $this->select($sql);

        return $user;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
    public function auth($userName, $password)
    {
        $md5Password = md5($password);
        $sql = 'SELECT * FROM users WHERE name = "' . $userName . '" AND password = "' . $md5Password . '"';

        $user = $this->select($sql);
        return $user;
    }

    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id)
    {
        $sql = 'DELETE FROM users WHERE id = ' . $id;
        return $this->delete($sql);

    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input)
    {
        // Lấy giá trị last_updated hiện tại
        $sql = 'SELECT last_updated FROM users WHERE id = ' . (int) $input['id'];
        $result = mysqli_query(self::$_connection, $sql);
        $currentUser = mysqli_fetch_assoc($result);

        if (!$currentUser) {
            throw new Exception("Người dùng không tồn tại.");
        }

        // So sánh thời gian
        if ($currentUser['last_updated'] !== $input['last_updated']) {
            return false; // Bản ghi đã bị chỉnh sửa bởi người khác
        }

        // Tiến hành cập nhật nếu thời gian khớp
        $sql = 'UPDATE users SET 
             name = "' . mysqli_real_escape_string(self::$_connection, $input['name']) . '", 
             fullname="' . mysqli_real_escape_string(self::$_connection, $input['fullname']) . '", 
             password="' . md5($input['password']) . '",
             email="' . mysqli_real_escape_string(self::$_connection, $input['email']) . '"
            WHERE id = ' . (int) $input['id'];

        return $this->update($sql);
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
    public function insertUser($input)
    {
        $sql = "INSERT INTO `app_web1`.`users` (`name`, `password`, `email`, `fullname`) VALUES (" .
            "'" . $input['name'] . "', '" . md5($input['password']) . "', '" . $input['email'] . "', '" . $input['fullname'] . "')";

        $user = $this->insert($sql);

        return $user;
    }

    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = [])
    {
        // Initialize the SQL string
        $sql = 'SELECT * FROM users';

        // Keyword filtering
        if (!empty($params['keyword'])) {
            // Append the keyword filter
            $sql .= ' WHERE name LIKE "%' . $params['keyword'] . '%"';

            // Use multi_query to demonstrate multiple statements
            if (self::$_connection->multi_query($sql)) {
                do {
                    // Store the first result set
                    if ($result = self::$_connection->store_result()) {
                        // Fetch the result set
                        $users = $result->fetch_all(MYSQLI_ASSOC);
                        $result->free();
                    }
                } while (self::$_connection->next_result());
            }
        } else {
            // No keyword, retrieve all users with the regular query method
            $users = $this->select($sql);
        }

        return $users;
    }

}
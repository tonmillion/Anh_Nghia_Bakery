<?php
/**
 * User Class
 * File: includes/classes/User.php
 * Mô tả: Xử lý các nghiệp vụ liên quan đến người dùng
 */

class User {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Đăng ký user mới
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'user_id' => int]
     */
    public function register($data) {
        try {
            // Validate dữ liệu
            $error = $this -> validateRegistration($data);
            if (!empty($error)) {
                return ['success' => false, 'message' => implode('<br>', $error)];
            }

            // Kiểm tra username đã tồn tại
            if ($this->usernameExists($data['username'])) {
                return ['success' => false, 'message' => 'Tên đăng nhập đã tồn tại'];
            }

            // Kiểm tra email đã tồn tại
            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'message' => 'Email đã được sử dụng'];
            }

            // Hash pasword
            $hashed_password = hash_password($data['password']);

            // Insert vào DB
            $sql = "INSERT INTO users (username, password, full_name, email, phone, address, role)
                    VALUES (?, ?, ?, ?, ?, ?, 'customer')";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['username'],
                $hashed_password,
                $data['full_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['address'] ?? null
            ]);

            if ($result) {
                $user_id = $this->db->lastInsertId();
                return [
                    'success' => true,
                    'message' => 'Đăng ký thành công',
                    'user_id' => $user_id
                ];
            }

            return ['success' => false, 'message' => 'Đã có lỗi xảy ra'];

        } catch (PDOException $e) {
            error_log("Register error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    /**
     * Đăng nhập
     * @param string $username
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user' => array]
     */
    public function login($username, $password) {
        try {
            // Lấy thông tin user
            $sql = "SELECT * FROM users WHERE username = ? AND is_active = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Tên đăng nhập không tồn tại'];
            }

            // Verify password
            if (!verify_password($password, $user['password'])) {
                return ['success' => false, 'message' => 'Mật khẩu không chính xác'];
            }

            // Kiểm tra tài khoản có bị khóa không
            if ($user['is_active'] == 0) {
                return ['success' => false, 'message' => 'Tài khoản của bạn đã bị khóa'];
            }

            return [
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'user' => $user
            ];

        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    /**
     * Lấy thông tin user theo ID
     * @param int $user_id
     * @return array|null
     */
    public function getUserById($user_id)  {
        try {
            $sql = "SELECT user_id, username, full_name, email, phone, address, role, created_at
            FROM users WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật thông tin user
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    public function updateProfile($user_id, $data) {
        try {
            $sql = "UPDATE users
                    SET full_name = ?, email = ?, phone = ?, address = ?
                    WHERE user_id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['full_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $user_id
            ]);
        } catch (PDOException $e) {
            error_log("Update profile error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đổi mật khẩu
     * @param int $user_id
     * @param string $old_password
     * @param string $new_password
     * @return array
     */
    public function changePassword($user_id, $old_password, $new_password) {
        try {
            // Lấy password hiện tại
            $stmt = $this->db->prepare("SELECT password FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Người dùng không tồn tại'];
            }

            // Verify old password
            if (!verify_password($old_password, $user['password'])) {
                return ['success' => false, 'message' => 'Mật khẩu cũ không đúng'];
            }

            // Validate new password
            $error = validate_password($new_password);
            if ($error) {
                return ['success' => false, 'message' => $error];
            }

            // Update password
            $hashed_password = hash_password($new_password);
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $result = $stmt->execute([$hashed_password, $user_id]);

            if ($result) {
                return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];
            }

            return ['success' => false, 'message' => 'Đã có lỗi xảy ra'];

        } catch (PDOException $e) {
            error_log("Change password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    /**
     * Lấy danh sách tất cả users (cho admin)
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllUsers($limit = 20, $offset = 0) {
        try {
            $sql = "SELECT user_id, username, full_name, email, phone, role, is_active, created_at
                    FROM users
                    ORDER BY created_at DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm tổng số users
     * @return int
     */
    public function countUsers() {
        try {
            $stmt = $this -> db->query("SELECT COUNT(*) FROM users");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count users error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Khóa/Mở khóa tài  khoản
     * @param int $user_id
     * @param int $status (0: khóa, 1: mở khóa)
     * @return bool
     */
    public function toggleUserStatus ($user_id, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
            return $stmt->execute([$status, $user_id]);
        } catch (PDOException $e) {
            error_log("Toggle user status error: " . $e->getMessage());
            return false;
        }
    }

    // =================================================================
    // PRIVATE METHODS
    // =================================================================

    /**
     * Validate dữ liệu đăng ký
     * @param array $data
     * @return array 
     */
    private function validateRegistration($data) {
        $error = [];

        // Username
        if (empty($data['username'])) {
            $error[] = 'Tên đăng nhập không được để trống';
        } elseif (strlen($data['username']) < 4)  {
            $error[] = 'Tên đăng nhập phải có ít nhất 4 ký tự';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $error[] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới';
        }

        // Password
        if (empty($data['password'])) {
            $error[] = 'Mật khẩu không được để trống';
        } else {
            $password_error = validate_password($data['password']);
            if ($password_error) {
                $error[] = $password_error;
            }
        }

        // Full name
        if (empty($data['full_name'])) {
            $error[] = 'Họ và tên không được để trống';
        }

        // Email
        if (empty($data['email'])) {
            $error[] = 'Email không được để trống';
        } elseif (!is_valid_email($data['email'])) {
            $error[] = 'Email không hợp lệ';
        }

        // Phone
        if (!empty($data['phone']) && !is_valid_phone($data['phone'])) {
            $error[] = 'Số điện thoại không hợp lệ';
        }

        return $error;
    }

    /**
     * Kiểm tra username đã tồn tại
     * @param string $username
     * @return bool
     */
    private function usernameExists($username) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Check username error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra email đã tồn tại
     * @param string $email
     * @return bool
     */
    private function emailExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Check email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật thông tin User (admin)
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    public function updateUser($user_id, $data) {
        try {
            $sql = "UPDATE users
                    SET full_name = ?, email = ?, phone = ?, address = ?, role = ?
                    WHERE user_id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['full_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['role'] ?? 'customer',
                $user_id
            ]);
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reset mật khẩu user (admin)
     * @param int $user_id
     * @param string $new_password
     * @return bool
     */
    public function resetPassword($user_id, $new_password) {
        try {
            $hashed_password = hash_password($new_password);
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            return $stmt->execute([$hashed_password, $user_id]);
        } catch (PDOException $e) {
            error_log("Reset password error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa user (soft delete - set is_active = 0)
     * @param int $user_id
     * @return bool
     */
    public function deleteUser($user_id) {
        try {
            // Soft delete - chỉ set is_active = 0
            $stmt = $this->db->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
            return $stmt->execute([$user_id]);
            
            // Nếu muốn hard delete (xóa hẳn khỏi database):
            // $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            // return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }
}
?>
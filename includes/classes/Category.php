<?php
/**
 * Category class
 * File: includes/classes/Category.php
 * Mô tả: Xử lý các nghiệp vụ liên quan đến danh mục
 */

class Category {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Lấy tất cả dnah mục active
     * @return array
     */
    public function getAllCategories() {
        try {
            $sql = "SELECT * FROM categories
                    WHERE is_active = 1
                    ORDER BY display_order ASC, category_name ASC";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get category error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh mục kèm theo số lượng sản phẩm
     * @return array
     */
    public function getCategoriesWithCount() {
        try {
            $sql = "SELECT c.*, COUNT(p.product_id) as product_count
                    FROM categories c
                    LEFT JOIN products p ON c.category_id = p.category_id AND p.is_active = 1
                    WHERE c.is_active = 1
                    GROUP BY c.category_id
                    ORDER BY c.display_order ASC";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Get categories with count error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin danh mục theo ID
     * @param int $category_id
     * @return array|null
     */
    public function getCategoryById($category_id) {
        try {
            $sql = $this->db->prepare("SELECT * FROM categories WHERE category_id = ?");
            $sql->execute([$category_id]);
            return $sql->fetch();
        } catch (PDOException $e) {
            error_log("Get category by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Thêm danh mục mới
     * @param array $data
     * @return array
     */
    public function addCategory($data) {
        try {
            $sql = "INSERT INTO categories (category_name, description, display_order)
                    VALUES (?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['category_name'],
                $data['description'] ?? null,
                $data['display_order'] ?? 0
            ]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Thêm danh mục thành công',
                    'category_id' => $this->db->lastInsertId()
                ];
            }

            return ['success' => false, 'message' => 'Đã có lỗi xảy ra'];

        } catch (PDOException $e) {
            error_log("Add category error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    /**
     * Cập nhật danh mục
     * @param int $category_id
     * @param array $data
     * @return bool
     */
    public function updateCategory($category_id, $data) {
        try {
            $sql = "UPDATE categories
                    SET category_name = ?, description = ?, display_order = ?
                    WHERE category_id = ?";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['category_name'],
                $data['description'] ?? null,
                $data['display_order'] ?? 0,
                $category_id
            ]);
        } catch (PDOException $e) {
            error_log("Update category error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xoá danh mục (soft delete)
     * @param int $category_id
     * @return bool
     */
    public function deleteCategory($category_id) {
        try {
            $stmt = $this->db->prepare("UPDATE categories SET is_active = 0 WHERE category_id = ?");
            return $stmt->execute([$category_id]);
        } catch (PDOException $e) {
            error_log("Delete category error: " . $e->getMessage());
            return false;
        }
    }
}
?>














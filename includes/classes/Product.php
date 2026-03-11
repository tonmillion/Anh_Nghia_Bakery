<?php
/**
 * Product class
 * File: includes/classes/Product.php
 * Mô tả: Xử lý các nghiệp vụ liên quan đến sản phẩm
 */

class Product {
    private $db;

    // constructor no longer requires parameter; uses global helper
    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Lấy tất cả sản phẩm có phân trang
     * @param int $limit
     * @param int $offset
     * @param array $filters ['category_id', 'min_price', 'max_price', 'search']
     * @param string $order_by
     * @return array
     */
    public function getProducts($limit = 12, $offset = 0, $filters = [], $order_by = 'created_at DESC') {
        try {
            $sql = "SELECT p.*, c.category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    WHERE 1";

            $params = [];

            // Filter theo category
            if (!empty($filters['category_id'])) {
                $sql .= " AND p.category_id = ?";
                $params[] = $filters['category_id'];
            }

            // Filter theo khoảng giá
            if (!empty($filters['min_price'])) {
                $sql .= " AND p.price >= ?";
                $params[] = $filters['min_price'];
            }

            if (!empty($filters['max_price'])) {
                $sql .= " AND p.price <= ?";
                $params[] = $filters['max_price'];
            }

            // Filter sản phẩm đang bán
            if (isset($filters['is_active'])) {
                $sql .= " AND p.is_active = ?";
                $params[] = $filters['is_active'];
            }

            // Tìm kiếm theo tên
            if (!empty($filters['search'])) {
                $sql .= " AND (p.product_name LIKE ? or p.description LIKE ?)";
                $search_term = '%' . $filters['search'] . '%';
                $params[] = $search_term;
                $params[] = $search_term;
            }

            $sql .= " ORDER BY p.$order_by LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get product error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm tổng số sản phẩm (có filter)
     * @param array $filters
     * @return int
     */
    public function countProducts($filters = []) {
        try {
            $sql = "SELECT COUNT(*) FROM products WHERE is_active = 1";
            $params = [];

            if (!empty($filters['category_id'])) {
                $sql .= " AND category_id = ?";
                $params[] = $filters['category_id'];
            }

            if (!empty($filters['min_price'])) {
                $sql .= " AND price >= ?";
                $params[] = $filters['min_price'];
            }

            if (!empty($filters['max_price'])) {
                $sql .= " AND price <= ?";
                $params[] = $filters['max_price'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (product_name LIKE ? or description LIKE ?)";
                $search_term = '%' . $filters['search'] . '%';
                $params[] = $search_term;
                $params[] = $search_term;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();

        } catch (PDOException $e) {
            error_log("Count product error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy chi tiết sản phẩm theo ID
     * @param int $product_id
     * @return array|null
     */
    public function getProductById($product_id) {
        try {
            $sql = "SELECT p.*, c.category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    WHERE p.product_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            // Tăng view count
            if ($product) {
                $this -> incrementViewCount($product_id);
            }

            return $product;

        } catch (PDOException $e) {
            error_log("Get product by ID error: " . $e->getMessage());
            return null;
        }
    }

    /** 
     * Lấy sản phẩm liên quan cùng danh mục
     * @param int $category_id
     * @param int $exclude_id
     * @param int $limit
     * @return array
     */
    public function getRelatedProducts($category_id, $exclude_id, $limit = 4)  {
        try {
            $sql = "SELECT * FROM products 
                    WHERE category_id = ? AND product_id != ? AND is_active = 1
                    ORDER BY RAND()
                    LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$category_id, $exclude_id, $limit]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get related products error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy sản phẩm mới nhất
     * @param int $limit
     * @return array
     */
    public function getLatestProducts($limit = 8) {
        try {
            $sql = "SELECT p.*, c.category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    WHERE p.is_active = 1
                    ORDER BY p.created_at DESC
                    LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get latest products error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy sản phẩm bán chạy
     * @param int $limit
     * @return array
     */
    public function getBestSellingProducts($limit = 8) {
        try {
            $sql = "SELECT p.*, c.category_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    WHERE p.is_active = 1
                    ORDER BY p.sold_count DESC
                    LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get best-selling products error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm sản phẩm (fulltext search)
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function searchProducts($keyword, $limit = 20) {
        try {
            $sql = "SELECT p.*, c.category_name,
                    MATCH(p.product_name, p.description) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    WHERE MATCH (p.product_name, p.description) AGAINST(? IN NATURAL LANGUAGE MODE)
                    AND p.is_active = 1
                    ORDER BY relevance DESC
                    LIMIT ?";
                
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$keyword, $keyword, $limit]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            // Fallback to LIKE search nếu fulltext không hoạt động
            return $this->getProducts($limit, 0, ['search' => $keyword]);
        }
    }

    /**
     * Thêm sản phẩm mới (admin)
     * @param array $data
     * @return array
     */
    public function addProduct($data) {
        try {
            $sql = "INSERT INTO products
                    (product_name, category_id, description, price, stock_quantity, image_url)
                    VALUES (?, ?, ?, ?, ?, ?)";
                    
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['product_name'],
                $data['category_id'],
                $data['description'],
                $data['price'],
                $data['stock_quantity'],
                $data['image_url'] ?? null
            ]);

            if($result) {
                return [
                    'success' => true,
                    'message' => 'Thêm sản phẩm thành công',
                    'product_id' => $this->db->lastInsertId()
                ];
            }

            return ['success' => false, 'message' => 'Đã có lỗi xảy ra'];

        } catch (PDOException $e) {
            error_log("Add product error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    /**
     * Cập nhật sản phẩm (admin)
     * @param int $product_id
     * @param array $data
     * @return bool
     */
    public function updateProduct($product_id, $data) {
        try {
            $sql = "UPDATE products
                    SET product_name = ?, category_id = ?, description = ?,
                        price = ?, stock_quantity = ?, is_active = ?";

            $param = [
                $data['product_name'],
                $data['category_id'],
                $data['description'],
                $data['price'],
                $data['stock_quantity'],
                $data['is_active']
            ];

            // Cập nhật hình ảnh nếu có
            if (!empty($data['image_url'])) {
                $sql .= ", image_url = ?";
                $param[] = $data['image_url'];
            }

            $sql .= " WHERE product_id = ?";
            $param[] = $product_id;

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($param);

        } catch (PDOException $e) {
            error_log("Update product error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa sản phẩm (soft delete - chỉ ẩn)
     * @param int $product_id
     * @return bool
     */
    public function deleteProduct($product_id) {
        try {
            $sql = $this -> db->prepare("UPDATE products SET is_active = 0 WHERE product_id = ?");
            return $sql->execute([$product_id]);
        } catch (PDOException $e) {
            error_log("Delete product error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra tồn kho
     * @param int $product_id
     * @param int $quantity
     * return bool
     */
    public function checkStock($product_id, $quantity) {
        try {
            $stmt = $this->db->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            return $product && $product['stock_quantity'] >= $quantity;

        } catch (PDOException $e) {
            error_log("Check stock error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Giảm tồn kho sau khi bán
     * @param int $product_id
     * @param int $quantity
     * return bool
     */
    public function decreaseStock($product_id, $quantity) {
        try {
            $sql = "UPDATE products
                    SET stock_quantity = stock_quantity - ?,
                        sold_count = sold_count + ?
                    WHERE product_id = ? AND stock_quantity >= ?";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$quantity, $quantity, $product_id, $quantity]);

        } catch (PDOException $e) {
            error_log("Decrease stock error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tăng view count
     * @param int $product_id
     */
    private function incrementViewCount($product_id) {
        try {
            $stnt = $this->db->prepare("UPDATE products SET view_count = view_count + 1 WHERE product_id = ?");
            $stnt->execute([$product_id]);
        } catch (PDOException $e) {
            error_log("Increment view count error: " . $e->getMessage());
        }
    }
}
?>
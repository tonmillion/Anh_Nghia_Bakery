<?php
/**
 * Cart Class 
 * File: includes/classes/Cart.php
 * Mô tả: Xử lý các nghiệp vụ liên quan đến giỏ hàng
 */

class Cart {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Lấy giỏ hàng đầy đủ thông tin
     * @param int $user_id (optional - nếu null thì lấy từ session)
     * @return array
     */
    public function getCart($user_id = null) {
        //Nếu user đã đăng nhập, lấy từ database
        if ($user_id || is_logged_in()) {
            $uid = $user_id ?? get_user_id();
            return $this->getCartFromDB($uid);
        }

        // Nếu user chưa đăng nhập, lấy từ session
        return $this->getCartFromSession();
    }

    /**
     * Lấy giỏ hàng từ session với thông tin sản phẩm đầy đủ
     * @return array
     */
    private function getCartFromSession() {
        $cart = get_cart(); // Từ session.php

        if (empty($cart)) {
            return [];
        }

        try {
            // Lấy thông tin sản phẩm từ database
            $product_ids = array_keys($cart);
            $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

            $sql = "SELECT product_id, product_name, price, image_url, stock_quantity
                    FROM products
                    WHERE product_id IN ($placeholders) AND is_active = 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($product_ids);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Kết hợp thông tin
            $cart_items = [];
            foreach ($products as $product) {
                $product_id = $product['product_id'];
                $quantity = $cart[$product_id]['quantity'];

                $cart_items[] = [
                    'product_id' => $product_id,
                    'product_name' => $product['product_name'],
                    'price' => $product['price'],
                    'image_url' => $product['image_url'],
                    'stock_quantity' => $product['stock_quantity'],
                    'quantity' => $quantity,
                    'subtotal' => $product['price'] * $quantity
                ];
            }

            return $cart_items;

        } catch (PDOException $e) {
            error_log("Get cart from session error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy giở hàng từ database
     * @param int $user_id
     * @return array
     */
    private function getCartFromDB($user_id) {
        try {
            $sql = "SELECT c.cart_id, c.product_id, c.quantity, c.added_at,
                            p.product_name, p.price, p.image_url, p.stock_quantity,
                            (p.price * c.quantity) as subtotal
                    FROM cart c
                    INNER JOIN products p ON c.product_id = p.product_id
                    WHERE c.user_id = ? AND p.is_active = 1
                    ORDER BY c.added_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get cart from DB error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     * @param int $product_id
     * @param int $quantity
     * @return array
     */
    public function addToCart($product_id, $quantity = 1) {
        // Kiểm tra sản phẩm có tồn tại không
        if (!$this->productExists($product_id)) {
            return ['success'=> false, 'message' => 'Sản phẩm không tồn tại'];
        }

        //Kiểm tra tồn kho
        if (!$this->checkStock($product_id, $quantity)) {
            return ['success' => false, 'message' => 'Sản phẩm không đủ số lượng'];
        }

        // Nếu user đã đăng nhập, lưu vào database
        if (is_logged_in()) {
            return $this->addToCartDB(get_user_id(), $product_id, $quantity);
        }

        // Nếu chưa đăng nhập, lưu vào session
        return $this->addToCartSession($product_id, $quantity);
    }

    /**
     * Thêm vào giở hàng trong DB
     * @param int $user_id
     * @param int $product_id
     * @param int $quantity
     * @return array
     */
    private function addToCartDB($user_id, $product_id, $quantity) {
        try {
            // Kiểm tra đã có trong giỏ chưa
            $sql = "SELECT cart_id, quantity FROM cart
                    WHERE user_id = ? AND product_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $product_id]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Cập nhật số lượng
                $new_quantity = $existing['quantity'] + $quantity;

                // Kiểm tra tồn kho trước khi update
                if (!$this->checkStock($product_id, $new_quantity)) {
                    return ['success' => false, 'message' => 'Vượt quá số lượng tồn kho'];
                }

                $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$new_quantity, $existing['cart_id']]);
            } else {
                // Thêm mới
                $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$user_id, $product_id, $quantity]);
            }

            return ['success' => true, 'message' => 'Đã thêm vào giỏ hàng'];

        } catch (PDOException $e) {
            error_log("Add to cart DB error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    /**
     * Thêm vào giỏ trong session
     * @param int $product_id
     * @param int $quantity
     * @return array
     */
    private function addToCartSession($product_id, $quantity) {
        $cart = get_cart();

        if (isset($cart[$product_id])) {
            // Cộng thêm số lượng
            $new_quantity = $cart[$product_id]['quantity'] + $quantity;

            // Kiểm tra tồn kho
            if (!$this->checkStock($product_id, $new_quantity)) {
                return ['success' => false, 'message' => 'Vượt quá số lượng tồn kho'];
            }

            $cart[$product_id]['quantity'] = $new_quantity;
        } else {
            // Thêm mới
            $cart[$product_id] = [
                'product_id' => $product_id,
                'quantity' => $quantity
            ];
        }

        $_SESSION['cart'] = $cart;
        return ['success' => true, 'message' => 'ĐÃ thêm vào giỏ hàng'];
    }

    /**
     * Cập nhật số lượng trong giỏ
     * @param int $product_id
     * @param int @quantity
     * @return array
     */
    public function updateQuantity ($product_id, $quantity) {
        // Nếu quantity = 0 thì xóa
        if ($quantity <= 0) {
            return $this->removeFromCart($product_id);
        }

        // Kiểm tra tồn kho
        if (!$this->checkStock($product_id, $quantity)) {
            return ['success' => false, 'message' => 'Vượt quá số lượng tồn kho'];
        }

        // Nếu user đã đăng nhập
        if (is_logged_in()) {
            return $this->updateQuantityDB(get_user_id(), $product_id, $quantity);
        }

        // Session
        return $this->updateQuantitySession($product_id, $quantity);
    }

    /**
     * Cập nhật số lượng trong database
     * @param int $user_id
     * @param int $product_id
     * @param int $quantity
     * @return array
     */
    private function updateQuantityDB($user_id, $product_id, $quantity) {
        try {
            $sql = "UPDATE cart SET quantity =?
                    WHERE user_id = ? AND product_id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$quantity, $user_id, $product_id]);

            if ($result) {
                return ['success' => true, 'message' => 'Đã cập nhật'];
            }

            return ['success'=> false, 'message' => 'Không tìm thấy sản phẩm trong giỏ'];

        } catch (PDOException $e) {
            error_log("Update quantity DB error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    /**
     * Cập nhật số lượng trong session
     * @param int $product_id
     * @param int $quantity
     * @return array
     */
    private function updateQuantitySession($product_id, $quantity) {
        $cart = get_cart();

        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] = $quantity;
            $_SESSION['cart'] = $cart;
            return ['success' => true, 'message' => 'Đã cập nhật'];
        }

        return ['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ'];
    }

    /**
     * Xóa sản phẩm khỏi giở hàng
     * @param int $product_id
     * @return array
     */
    public function removeFromCart($product_id) {
        if (is_logged_in()) {
            return $this->removeFromCartDB(get_user_id(), $product_id);
        }

        return $this->removeFromCartSession($product_id);
    }

    /**
     * Xóa khỏi database
     * @param int $user_id
     * @param int $Product_id
     * @return array
     */
    private function removeFromCartDB($user_id, $product_id) {
        try {
            $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $product_id]);

            return ['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng'];

        } catch (PDOException $e) {
            error_log("Remove from cart DB error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    /**
     * Xóa khỏi session
     * @param int $product_id
     * @return array
     */
    private function removeFromCartSession($product_id) {
        $cart = get_cart();
        unset($cart[$product_id]);
        $_SESSION['cart'] = $cart;

        return ['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng'];
    }

    /**
     * Xóa toàn bộ giỏ hàng
     * @return bool
     */
    public function clearCart() {
        if (is_logged_in()) {
            return $this->clearCartDB(get_user_id());
        }

        clear_cart(); // từ session.php
        return true;
    }

    /**
     * Xóa giỏ hàng trong database
     * @param int $user_id
     * @return bool
     */
    private function clearCartDB($user_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = ?");
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Clear cart from DB error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm số items trong giỏ hàng
     * @return int
     */
    public function getCartCount() {
        if (is_logged_in()) {
            return $this->getCartCountDB(get_user_id());
        }

        return get_cart_count(); // Từ session.php
    }

    /**
     * Đếm từ database
     * @param int @user_id
     * @return int
     */
    private function getCartCountDB($user_id) {
        try {
            $sql = "SELECT SUM(quantity) FROM cart WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Get cart count DB error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tính tổng tiền giỏ hàng
     * @return float
     */
    public function getCartTotal() {
        if (is_logged_in()) {
            return $this->getCartTotalDB(get_user_id());
        }

        return get_cart_total(); // Từ session.php
    }

    /**
     * Tính tổng từ database
     * @param $user_id
     * @return float
     */
    private function getCartTotalDB($user_id) {
        try {
            $sql = "SELECT SUM(p.price * c.quantity) as total
                    FROM cart c
                    INNER JOIN products p ON c.product_id = p.product_id
                    WHERE c.user_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();

            return (float)($result['total'] ?? 0);

        } catch (PDOException $e) {
            error_log("Get cart total DB error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Đồng bộ giỏ hàng từ session sang database khi user đăng nhập
     * @param int $user_id
     * @return bool
     */
    public function syncCartToDatabase($user_id) {
        try {
            $session_cart = get_cart();

            if (empty($session_cart)) {
                return true;
            }

            $this->db->beginTransaction();

            foreach ($session_cart as $product_id => $item) {
                // Kiểm tra đã có trong DB chưa
                $sql = "SELECT cart_id, quantity FROM cart
                        WHERE user_id = ? AND product_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$user_id, $product_id]);
                $existing = $stmt->fetch();

                if ($existing) {
                    // Cộng thêm số lượng
                    $new_quantity = $existing['quantity'] + $item['quantity'];
                    $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$new_quantity, $existing['cart_id']]);
                } else {
                    // Thêm mới
                    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$user_id, $product_id, $item['quantity']]);
                }
            }

            $this->db->commit();

            // Xóa giỏ hàng session
            clear_cart();

            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Sync cart error: " . $e->getMessage());
            return false;
        }
    }

    // =============================================================
    // PRIVATE HELPER METHODS
    // =============================================================

    /**
     * Kiểm tra sản phẩm có tồn tại không
     * @param int $product_id
     * @return bool
     */
    private function productExists($product_id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE product_id = ? AND is_active = 1");
            $stmt->execute([$product_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Check product exists error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra tồn kho
     * @param int $product_id
     * @param int $quantity
     * @return bool
     */
    private function checkStock($product_id, $quantity) {
        try{ 
            $stmt = $this->db->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            return $product && $product['stock_quantity'] >= $quantity;

        } catch (PDOException $e) {
            error_log("Check stock error: " . $e->getMessage());
            return false;
        }
    }
}
?>

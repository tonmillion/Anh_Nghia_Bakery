<?php
/**
 * Order Class
 * File: includes/classes/Order.php
 * Mô tả: Xử lý các nghiệp vụ liên quan dến đơn hàng
 */

class Order {
    private $db;

    // make constructor public so instances can be created externally
    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Tạo đơn hàng mới
     * @param array $order_data
     * @param array $cart_items
     * @return array
     */
    public function createOrder ($order_data, $cart_items) {
        try {
            // Kiểm tra tồn kho trước khi tạo đơn
            foreach ($cart_items as $item) {
                $stmt = $this->db->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch();

                if (!$product || $product['stock_quantity'] < $item['quantity']) {
                    return [
                        'success' => false,
                        'message' => 'Sản phẩm ' . $item['product_name'] . ' không đủ hàng trong kho'
                    ];
                }
            }

            $this->db->beginTransaction();

            // Generate order code
            $order_code = $this->generateOrderCode();

            // Insert order
            $sql = "INSERT INTO orders
                    (user_id, order_code, total_amount, payment_method,
                    shipping_name, shipping_phone, shipping_address, customer_note)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $order_data['user_id'],
                $order_code,
                $order_data['total_amount'],
                $order_data['payment_method'],
                $order_data['shipping_name'],
                $order_data['shipping_phone'],
                $order_data['shipping_address'],
                $order_data['customer_note'] ?? null
            ]);

            $order_id = $this->db->lastInsertId();

            // Insert order details
            $sql_detail = "INSERT INTO order_details
                            (order_id, product_id, product_name, quantity, unit_price)
                            VALUES (?, ?, ?, ?, ?)";

            $stmt_detail = $this->db->prepare($sql_detail);

            // Chuẩn bị câu lệnh trừ tồn kho
            $sql_update_stock = "UPDATE products
                                SET stock_quantity = stock_quantity - ?,
                                    sold_count = sold_count + ?
                                WHERE product_id = ?";
            $stmt_update_stock = $this->db->prepare($sql_update_stock);

            foreach ($cart_items as $item) {
                // Insert order detail
                $stmt_detail -> execute([
                    $order_id,
                    $item['product_id'],
                    $item['product_name'],
                    $item['quantity'],
                    $item['price']
                ]);

                // Trừ tồn kho và cộng sold_count
                $stmt_update_stock->execute([
                    $item['quantity'],
                    $item['quantity'],
                    $item['product_id']
                ]);
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'order_id' => $order_id,
                'order_code' => $order_code
            ];

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Create order error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi khi tạo đơn hàng'];
        }
    }

    /**
     * Lấy đơn hàng theo ID
     * @param int $order_id
     * @return array|null
     */
    public function getOrderById($order_id) {
        try {
            $sql = "SELECT o.*, u.username, u.email
                    FROM orders o
                    LEFT JOIN users u ON o.user_id = u.user_id
                    WHERE o.order_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$order_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get order error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy chi tiết đơn hàng
     * @param int $order_id
     * @return array
     */
    public function getOrderDetails($order_id) {
        try {
            $sql = "SELECT od.*, p.image_url
                    FROM order_details od
                    LEFT JOIN products p ON od.product_id = p.product_id
                    WHERE od.order_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$order_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get order details error: " . $e->getMessage());
            return [];
        }
    }

    /** 
     * Lấy đơn hàng của User
     * @param int $user_id
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getOrdersByUser($user_id, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT * FROM orders
                    WHERE user_id = ?
                    ORDER BY order_date DESC
                    LIMIT ? OFFSET ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id, $limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get user orders error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả đơn hàng (admin)
     * @param array $filters
     * @param int $limit
     * @param $offset
     * @return array
     */
    public function getAllOrders($filters = [], $limit = 20, $offset = 0) {
        try {
            $sql = "SELECT o.*, u.username, u.full_name
                    FROM orders o
                    LEFT JOIN users u ON o.user_id = u.user_id
                    WHERE 1=1";

            $params = [];

            // Filter by status
            if (!empty($filters['status'])) {
                $sql .= " AND o.order_status = ?";
                $params[] = $filters['status'];
            }

            // Filter by payment status
            if (!empty($filters['payment_status'])) {
                $sql .= " AND o.payment_status = ?";
                $params[] = $filters['payment_status'];
            }

            // FIlter by date range
            if (!empty($filters['from_date'])) {
                $sql .= " AND DATE(o.order_date) >= ?";
                $params[] = $filters['from_date'];
            }

            if (!empty($filters['to_date'])) {
                $sql .= " AND DATE (o.order_date) <= ?";
                $params[] = $filters['to_date'];
            }

            $sql .= " ORDER BY o.order_date DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Get all order error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm đơn hàng
     * @param $array $filters
     * @return int
     */
    public function countOrders($filters = []) {
        try {
            $sql = "SELECT COUNT(*) FROM orders WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND order_status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['user_id'])) {
                $sql .= " AND user_id = ?";
                $params[] = $filters['user_id'];
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count orders error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * @param int $order_id
     * @param string $status
     * @return bool
     */
    public function updateOrderStatus($order_id, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
            return $stmt->execute([$status, $order_id]);
        } catch (PDOException $e) {
            error_log("Update order status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật trạng thái thanh toán
     * @param int $order_id
     * @param string $payment_status
     * @param array $payment_data (optimal VNPay data)
     * @return bool
     */
    public function updatePaymentStatus($order_id, $payment_status, $payment_data = []) {
        try {
            $sql = "UPDATE orders SET payment_status = ?, payment_date = NOW()";
            $params = [$payment_status];

            if (!empty($payment_data['transaction_id'])) {
                $sql .= ", vnpay_transaction_id = ?";
                $params[] = json_encode($payment_data['response']);
            }

            if (!empty($payment_data['response'])) {
                $sql .= ", vnpay_response = ?";
                $params[] = json_encode($payment_data['response']);
            }

            $sql .= " WHERE order_id = ?";
            $params[] = $order_id;

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Update payment status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hủy đơn hàng
     * @param int $order)id
     * @param string $reason
     * @return bool
     */
    public function cancelOrder($order_id, $reason = '') {
        try {
            $this ->db->beginTransaction();

            // Lấy chi tiết đơn hàng để kiểm tra tồn kho
            $details = $this->getOrderDetails($order_id);

            // Hoàn trả tồn kho
            $sql_restore = "UPDATE products
                            SET stock_quantity = stock_quantity + ?,
                                sold_count = sold_count - ?
                            WHERE product_id = ?";
            $stmt_restore = $this->db->prepare($sql_restore);

            foreach ($details as $item) {
                $stmt_restore->execute([
                    $item['quantity'],
                    $item['quantity'],
                    $item['product_id']
                ]);
            }

            // Cập nhật trạng thái đơn hàng
            $sql = "UPDATE orders
                    SET order_status = 'cancelled', admin_note = ?
                    WHERE order_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$reason, $order_id]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Cancel order error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Thống kê doanh thu
     * @param string $from_date
     * @param string $to_date
     * @return array
     */
    public function getRenenueStats($from_date = null, $to_date = null) {
        try {
            $sql = "SELECT
                        COUNT(*) as total_orders,
                        SUM(total_amount) as total_revenue,
                        AVG(total_amount) as avg_order_value
                    FROM orders
                    WHERE order_status = 'completed'";

            $params = [];

            if ($from_date) {
                $sql .= " AND DATE(order_date) >= ?";
                $params[] = $from_date;
            }

            if ($to_date) {
                $sql .= " AND DATE(order_date) <= ?";
                $params[] = $to_date;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();

        } catch (PDOException $e) {
            error_log("Get revenue stats error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate mã đơn hàng tự động
     * @return string
     */
    private function generateOrderCode() {
        try {
            // Sử dụng function đã tạo trong database
            $stmt = $this->db->query("SELECT fn_generate_order_code() as code");
            $result = $stmt->fetch();
            return $result['code'];
        } catch (PDOException $e) {
            // Fallback nếu function không hoạt động
            return 'DH' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }
    }
}
?>

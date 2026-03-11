<?php
/**
 * VNPay payment gateway class
 * File: includes/classes/VNPay.php
 */

class VNPay {
    private $vnp_TmnCode;
    private $vnp_HashSecret;
    private $vnp_Url;
    private $vnp_Returnurl;

    public function __construct() {
        // Load config từ constants
        $this->vnp_TmnCode = VNPAY_TMN_CODE;
        $this->vnp_HashSecret = VNPAY_HASH_SECRET;
        $this->vnp_Url = VNPAY_URL;
        $this->vnp_Returnurl = VNPAY_RETURN_URL;
    }

    /**
     * Tạo URL thanh toán VNPay
     * @param array $order_data
     * @return string Payment URL
     */
    public function createPaymentUrl($order_data) {
        $vnp_TxnRef = $order_data['order_code']; // Mã đơn hàng
        $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $order_data['order_code'];
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order_data['amount'] * 100; // VNPay yêu cầu số tiền phải nhân 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = $order_data['bank_code'] ?? ''; // Mã ngân hàng (optional)
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        // Tạo mảng dữ liệu để gửi đến VNPay
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        // Sắp xếp dữ liệu theo thứ tự alphabet
        ksort($inputData);

        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $this->vnp_Url . "?" . $query;

        if (!empty($this->vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    /**
     * Xác thực callback từ VNPay
     * @param array $vnp_data Dữ liệu từ $_GET
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function verifyCallback($vnp_data) {
        $vnp_SecureHash = $vnp_data['vnp_SecureHash'] ?? '';
        // Loại bỏ SecureHash ra khỏi data
        $inputData = array();
        foreach ($vnp_data as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                if ($key != 'vnp_SecureHash') {
                    $inputData[$key] = $value;
                }
            }
        }

        // Sắp xếp dữ liệu
        ksort($inputData);

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);

        // Kiểm tra chữ ký
        if ($secureHash != $vnp_SecureHash) {
            return [
                'success' => false, 
                'message' => 'Chữ ký không hợp lệ', 
                'data' => null
            ];
        }

        // Kiểm tra response code
        $vnp_ResponseCode = $vnp_data['vnp_ResponseCode'];

        if ($vnp_ResponseCode == '00') {
            // Thanh toán thành công
            return [
                'success' => true, 
                'message' => 'Thanh toán thành công', 
                'data' => [
                    'order_code' => $vnp_data['vnp_TxnRef'],
                    'amount' => $vnp_data['vnp_Amount'] / 100, // Chia lại cho 100 để lấy số tiền gốc
                    'bank_code' => $vnp_data['vnp_BankCode'] ?? '',
                    'bank_tran_no' => $vnp_data['vnp_BankTranNo'] ?? '',
                    'card_type' => $vnp_data['vnp_CardType'] ?? '',
                    'pay_date' => $vnp_data['vnp_PayDate'] ?? '',
                    'transaction_no' => $vnp_data['vnp_TransactionNo'] ?? '',
                ]
            ];
        } else {
            // Thanh toán thất bại
            $error_message = $this->getErrorMessage($vnp_ResponseCode);
            return [
                'success' => false, 
                'message' => $error_message, 
                'data' => [
                    'order_code' => $vnp_data['vnp_TxnRef'],
                    'response_code' => $vnp_ResponseCode
                ]
            ];
        }
    }

    /**
     * Lấy thông báo lỗi từ response code
     * @param string $code
     * @return string
     */
    private function getErrorMessage($code) {
        $message = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)'
        ];

        return $message[$code] ?? 'Lỗi không xác định';
    }

    /**
     * Lấy danh sách ngân hàng hỗ trợ
     * @return array
     */
    public function getBankList() {
        return [
            '' => 'Cổng thanh toán VNPAYQR',
            'VNPAYQR' => 'Thanh toán qua ứng dụng hỗ trợ VNPAYQR',
            'VNBANK' => 'Thanh toán qua ATM-Tài khoản ngân hàng nội địa',
            'INTCARD' => 'Thanh toán qua thẻ quốc tế',
            'VIETCOMBANK' => 'Ngân hàng TMCP Ngoại Thương Việt Nam',
            'VIETINBANK' => 'Ngân hàng TMCP Công Thương Việt Nam',
            'BIDV' => 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam',
            'AGRIBANK' => 'Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam',
            'SACOMBANK' => 'Ngân hàng TMCP Sài Gòn Thương Tín',
            'TECHCOMBANK' => 'Ngân hàng TMCP Kỹ Thương Việt Nam',
            'ACB' => 'Ngân hàng TMCP Á Châu',
            'VPBANK' => 'Ngân hàng TMCP Việt Nam Thịnh Vượng',
            'TPBANK' => 'Ngân hàng TMCP Tiên Phong',
            'MBBANK' => 'Ngân hàng TMCP Quân Đội',
            'VIB' => 'Ngân hàng TMCP Quốc Tế',
            'SHB' => 'Ngân hàng TMCP Sài Gòn - Hà Nội',
        ];
    }
}
?>
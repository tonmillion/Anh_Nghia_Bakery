# HƯỚNG DẪN SỬ DỤNG GIT CHO DỰ ÁN

## KHỞI TẠO GIT REPOSITORY

### 1. Di chuyển đến thư mục dự án
```bash
cd C:\xampp\htdocs\bakery-shop   # Windows
# cd /Applications/XAMPP/htdocs/bakery-shop   # Mac
# cd /opt/lampp/htdocs/bakery-shop   # Linux
```

### 2. Khởi tạo Git repository
```bash
git init
```

### 3. Thêm file vào staging area
```bash
# Thêm tất cả file
git add .

# Hoặc thêm từng file cụ thể
git add README.md
git add index.php
```

### 4. Commit lần đầu
```bash
git commit -m "Initial commit: Tạo cấu trúc dự án Bakery Shop"
```

---

## CÁC LỆNH GIT CƠ BẢN

### Kiểm tra trạng thái
```bash
git status
```

### Xem lịch sử commit
```bash
git log
git log --oneline   # Hiển thị gọn hơn
```

### Thêm file mới/thay đổi
```bash
git add ten_file.php
git add .   # Thêm tất cả
```

### Commit thay đổi
```bash
git commit -m "Mô tả ngắn gọn về thay đổi"
```

### Xem sự khác biệt
```bash
git diff   # So sánh với commit trước
```

---

## WORKFLOW HÀNG NGÀY

### Scenario 1: Hoàn thành một tính năng mới
```bash
# 1. Kiểm tra trạng thái hiện tại
git status

# 2. Thêm file đã thay đổi
git add user/pages/products.php
git add includes/classes/Product.php

# 3. Commit với message rõ ràng
git commit -m "Tạo trang danh sách sản phẩm với phân trang"

# 4. Kiểm tra log
git log --oneline
```

### Scenario 2: Sửa lỗi
```bash
git add file_da_sua.php
git commit -m "Fix: Sửa lỗi hiển thị giá sản phẩm"
```

### Scenario 3: Thêm tính năng lớn (dùng branch)
```bash
# Tạo branch mới
git checkout -b feature/vnpay-integration

# Làm việc trên branch này
git add config/vnpay_config.php
git commit -m "Thêm cấu hình VNPay"

# Merge về branch main khi hoàn thành
git checkout main
git merge feature/vnpay-integration
```

---

## BRANCHES (NỀN TẢNG)

### Tại sao cần branch?
- Làm việc độc lập trên các tính năng khác nhau
- Không ảnh hưởng đến code chính (main/master)
- Dễ dàng quay lại nếu có lỗi

### Các lệnh branch
```bash
# Xem danh sách branch
git branch

# Tạo branch mới
git branch ten-branch

# Chuyển sang branch khác
git checkout ten-branch

# Tạo và chuyển sang branch mới (1 lệnh)
git checkout -b ten-branch

# Xóa branch (sau khi merge)
git branch -d ten-branch
```

---

## REMOTE REPOSITORY (GitHub/GitLab)

### 1. Tạo repository trên GitHub
- Truy cập: https://github.com
- Click "New repository"
- Đặt tên: `bakery-shop`
- Chọn: Private (để không public code)
- Không chọn README, .gitignore (đã có rồi)

### 2. Kết nối repository local với GitHub
```bash
git remote add origin https://github.com/username/bakery-shop.git
```

### 3. Push code lên GitHub lần đầu
```bash
git branch -M main   # Đổi tên branch thành main
git push -u origin main
```

### 4. Push các lần sau
```bash
git push
```

### 5. Pull code từ GitHub (làm việc nhóm)
```bash
git pull origin main
```

---

## QUẢN LÝ FILE NHẠY CẢM

### ⚠️ QUAN TRỌNG: Không commit file có thông tin nhạy cảm!

File `.gitignore` đã được cấu hình để bỏ qua:
- `config/database.php` - Chứa password DB
- `config/vnpay_config.php` - Chứa API key
- Thư mục `uploads/` - File do user upload

### Tạo file example cho team
```bash
# Tạo file config.example.php
cp config/database.php config/database.example.php

# Mở file và thay thông tin thật bằng placeholder
# Ví dụ: 
# DB_HOST = "localhost"
# DB_USER = "your_username_here"
# DB_PASS = "your_password_here"

# Commit file example
git add config/database.example.php
git commit -m "Thêm file cấu hình mẫu"
```

---

## XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi 1: Quên commit rồi thay đổi tiếp
```bash
# Xem những file chưa commit
git status

# Stash (cất giữ) thay đổi hiện tại
git stash

# Commit những thay đổi trước đó
git add .
git commit -m "Commit trước đó"

# Lấy lại thay đổi đã stash
git stash pop
```

### Lỗi 2: Commit nhầm file
```bash
# Uncommit file (giữ thay đổi)
git reset HEAD~1

# Xóa file khỏi staging
git reset HEAD ten_file.php

# Commit lại đúng
git add file_dung.php
git commit -m "Commit đúng"
```

### Lỗi 3: Muốn hủy toàn bộ thay đổi chưa commit
```bash
# CẢNH BÁO: Lệnh này sẽ XÓA tất cả thay đổi!
git checkout .
```

### Lỗi 4: Đã commit nhưng muốn sửa message
```bash
git commit --amend -m "Message mới đúng hơn"
```

---

## COMMIT MESSAGE TỐT

### ❌ Không tốt:
```
git commit -m "update"
git commit -m "fix bug"
git commit -m "code mới"
```

### ✅ Tốt:
```
git commit -m "Thêm tính năng tìm kiếm sản phẩm theo tên"
git commit -m "Fix: Sửa lỗi tính toán tổng tiền giỏ hàng"
git commit -m "Refactor: Tối ưu hóa query lấy danh sách sản phẩm"
```

### Quy ước commit message (Convention)
```
feat: Thêm tính năng mới
fix: Sửa lỗi
refactor: Tái cấu trúc code
docs: Cập nhật tài liệu
style: Format code, không thay đổi logic
test: Thêm test
chore: Update dependencies, config

Ví dụ:
feat: Tích hợp thanh toán VNPay
fix: Sửa lỗi session timeout
docs: Cập nhật README với hướng dẫn cài đặt
```

---

## WORKFLOW TEAM (Làm việc nhóm)

### Quy trình cơ bản:
1. **Pull code mới nhất** trước khi bắt đầu làm việc
   ```bash
   git pull origin main
   ```

2. **Tạo branch** cho tính năng của bạn
   ```bash
   git checkout -b feature/ten-tinh-nang
   ```

3. **Commit thường xuyên** với message rõ ràng
   ```bash
   git add .
   git commit -m "feat: Hoàn thành module đăng nhập"
   ```

4. **Push branch** lên GitHub
   ```bash
   git push origin feature/ten-tinh-nang
   ```

5. **Tạo Pull Request** trên GitHub
   - Mô tả chi tiết thay đổi
   - Request review từ teammates

6. **Merge** sau khi được approve

7. **Xóa branch** đã merge
   ```bash
   git branch -d feature/ten-tinh-nang
   ```

---

## .gitignore QUAN TRỌNG

File `.gitignore` đã được tạo với nội dung:
- File config nhạy cảm
- Thư mục uploads
- File log
- File hệ thống (.DS_Store, Thumbs.db)
- IDE settings (.vscode, .idea)

**Luôn kiểm tra `.gitignore` trước khi commit lần đầu!**

---

## BACKUP VÀ PHỤC HỒI

### Backup toàn bộ dự án
```bash
# GitHub tự động là backup
# Nhưng có thể clone về nhiều nơi
git clone https://github.com/username/bakery-shop.git bakery-shop-backup
```

### Phục hồi về commit trước đó
```bash
# Xem lịch sử
git log --oneline

# Phục hồi về commit cụ thể (CẢNH BÁO: Mất thay đổi sau đó)
git reset --hard commit_id

# Hoặc tạo branch mới từ commit cũ (An toàn hơn)
git checkout -b recovery commit_id
```

---

## CHECKLIST TRƯỚC KHI COMMIT

- [ ] Code đã test chạy được
- [ ] Không có lỗi syntax
- [ ] Đã xóa console.log, var_dump debug
- [ ] Không commit file config nhạy cảm
- [ ] Commit message rõ ràng
- [ ] File đã được format đẹp

---

## TÀI NGUYÊN HỌC GIT

**Documentation:**
- https://git-scm.com/doc

**Tutorials:**
- Git Handbook (GitHub): https://guides.github.com/introduction/git-handbook/
- Learn Git Branching: https://learngitbranching.js.org/

**Video:**
- "Git and GitHub for Beginners" - freeCodeCamp (YouTube)
- "Git Tutorial for Beginners" - Programming with Mosh

**Cheat Sheet:**
- https://education.github.com/git-cheat-sheet-education.pdf

---

## GHI NHỚ

> "Commit sớm, commit thường xuyên" - Mỗi tính năng nhỏ xong là commit ngay, đừng chờ đến cuối ngày!

> "Branch cho mọi tính năng mới" - Tránh làm trực tiếp trên main branch.

> "Pull trước khi push" - Tránh conflict code khi làm việc nhóm.

🚀 **Happy Coding!**

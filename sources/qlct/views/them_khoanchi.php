<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Thêm khoản chi</title>
    <link rel="stylesheet" href="../public/css/khoanchi.css" />
    <link rel="stylesheet" href="../public/css/themkhoanchi.css" />
</head>

<body>
    <div class="app">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <div class="burger" aria-hidden="true"></div>
                <strong style="color:#222">Menu</strong>
            </div>

            <nav class="menu" aria-label="Main menu">
                <a href="index.php">Trang chủ</a>
                <a href="khoanthu.php">Khoản thu</a>
                <a href="khoanchi.php" class="active">Khoản chi</a>
                <a href="danhmuc.php">Danh mục</a>
                <a href="ngansach.php">Ngân sách</a>
                <a href="baocao.php">Báo cáo</a>
                <a href="caidat.php">Cài đặt</a>
            </nav>
        </aside>

        <!-- Main -->
        <div class="main">
            <header class="header">
                <h1>Thêm khoản chi tiêu</h1>
            </header>

            <main class="content">
                <div class="form-container">
                    <?php
                    // Xử lý form submit
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        require_once __DIR__ . '/../controllers/KhoanchiController.php';
                        $controller = new KhoanchiController();
                        $result = $controller->add();
                        
                        if ($result['success']) {
                            echo '<div class="alert alert-success">' . $result['message'] . '</div>';
                            // Redirect về trang danh sách sau 2 giây
                            echo '<script>setTimeout(() => window.location.href = "khoanchi.php", 2000);</script>';
                        } else {
                            echo '<div class="alert alert-error">' . $result['message'] . '</div>';
                        }
                    }
                    ?>

                    <form method="POST" action="">
                        <div class="form-row">
                            <label for="noidung">Tên khoản chi tiêu: <span class="required">*</span></label>
                            <input type="text" id="noidung" name="noidung" required 
                                   value="<?php echo htmlspecialchars($_POST['noidung'] ?? ''); ?>"
                                   placeholder="Nhập tên khoản chi tiêu">
                        </div>

                        <div class="form-row">
                            <label for="sotien">Số tiền: <span class="required">*</span></label>
                            <div style="flex: 1; display: flex; flex-direction: column;">
                                <input type="text" id="sotien" name="sotien" required
                                       value="<?php echo htmlspecialchars($_POST['sotien'] ?? ''); ?>"
                                       placeholder="Nhập số tiền">
                                <span id="sotien-error" class="error-message" style="display: none;"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <label for="ngaychitieu">Ngày: <span class="required">*</span></label>
                            <input type="date" id="ngaychitieu" name="ngaychitieu" required
                                   value="<?php echo htmlspecialchars($_POST['ngaychitieu'] ?? date('Y-m-d')); ?>">
                        </div>

                        <div class="form-row">
                            <label for="madmchitieu">Danh mục: <span class="required">*</span></label>
                            <select id="madmchitieu" name="madmchitieu" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php
                                // Lấy danh sách danh mục chi tiêu
                                try {
                                    require_once __DIR__ . '/../models/KhoanchiModel.php';
                                    $khoanchiModel = new KhoanchiModel();
                                    $categories = $khoanchiModel->getExpenseCategories(1); // Tạm thời hardcode makh = 1
                                    
                                    foreach ($categories as $category) {
                                        $selected = (isset($_POST['madmchitieu']) && $_POST['madmchitieu'] == $category['madmchitieu']) ? 'selected' : '';
                                        echo "<option value='{$category['madmchitieu']}' {$selected}>{$category['tendanhmuc']}</option>";
                                    }
                                } catch (Exception $e) {
                                    echo '';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="btn-group">
                            <a href="khoanchi.php" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        const sotienInput = document.getElementById('sotien');
        const sotienError = document.getElementById('sotien-error');

        // Validate số tiền khi người dùng nhập
        sotienInput.addEventListener('input', function(e) {
            validateSotien();
        });

        // Validate số tiền khi người dùng rời khỏi trường
        sotienInput.addEventListener('blur', function(e) {
            validateSotien();
        });

        function validateSotien() {
            const value = sotienInput.value.trim();
            sotienError.style.display = 'none';
            sotienError.textContent = '';

            if (!value) {
                sotienError.textContent = 'Lỗi: Vui lòng nhập số tiền';
                sotienError.style.display = 'block';
                sotienInput.style.borderColor = '#e74c3c';
                sotienInput.classList.add('error');
                return false;
            }

            // Kiểm tra nếu giá trị không phải là số
            if (isNaN(value)) {
                sotienError.textContent = 'Lỗi: Giá trị nhập vào không phải là số. Vui lòng chỉ nhập số (ví dụ: 100000)';
                sotienError.style.display = 'block';
                sotienInput.style.borderColor = '#e74c3c';
                sotienInput.classList.add('error');
                return false;
            }

            // Kiểm tra nếu giá trị là số âm hoặc bằng 0
            const numValue = parseFloat(value);
            if (numValue <= 0) {
                sotienError.textContent = 'Lỗi: Số tiền phải lớn hơn 0. Vui lòng nhập số dương';
                sotienError.style.display = 'block';
                sotienInput.style.borderColor = '#e74c3c';
                sotienInput.classList.add('error');
                return false;
            }

            // Kiểm tra nếu giá trị không phải là số nguyên dương
            // So sánh giá trị float với giá trị integer của nó
            if (parseInt(numValue) != numValue) {
                sotienError.textContent = 'Lỗi: Số tiền phải là số nguyên. Vui lòng không nhập số thập phân';
                sotienError.style.display = 'block';
                sotienInput.style.borderColor = '#e74c3c';
                sotienInput.classList.add('error');
                return false;
            }

            // Hợp lệ
            sotienInput.style.borderColor = '';
            sotienInput.classList.remove('error');
            return true;
        }

        // Validate form khi submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const noidung = document.getElementById('noidung').value.trim();
            const madmchitieu = document.getElementById('madmchitieu').value;
            const sotien = document.getElementById('sotien').value.trim();
            const ngaychitieu = document.getElementById('ngaychitieu').value;
            let hasError = false;

            if (!noidung) {
                alert('Vui lòng nhập tên khoản chi tiêu');
                e.preventDefault();
                hasError = true;
                return;
            }

            if (!madmchitieu) {
                alert('Vui lòng chọn danh mục');
                e.preventDefault();
                hasError = true;
                return;
            }

            // Validate số tiền với thông báo cụ thể
            if (!sotien) {
                sotienError.textContent = 'Lỗi: Vui lòng nhập số tiền';
                sotienError.style.display = 'block';
                sotienInput.style.borderColor = '#e74c3c';
                sotienInput.classList.add('error');
                e.preventDefault();
                hasError = true;
                return;
            }

            if (isNaN(sotien)) {
                sotienError.textContent = 'Lỗi: Giá trị nhập vào không phải là số. Vui lòng chỉ nhập số (ví dụ: 100000)';
                sotienError.style.display = 'block';
                sotienInput.style.borderColor = '#e74c3c';
                sotienInput.classList.add('error');
                e.preventDefault();
                hasError = true;
                return;
            }

            const numValue = parseFloat(sotien);
            if (numValue <= 0) {
                sotienError.textContent = 'Lỗi: Số tiền phải lớn hơn 0. Vui lòng nhập số dương';
                sotienError.style.display = 'block';
                sotienInput.style.borderColor = '#e74c3c';
                sotienInput.classList.add('error');
                e.preventDefault();
                hasError = true;
                return;
            }

            if (parseInt(numValue) != numValue) {
                sotienError.textContent = 'Lỗi: Số tiền phải là số nguyên. Vui lòng không nhập số thập phân';
                sotienError.style.display = 'block';
                sotienInput.style.borderColor = '#e74c3c';
                sotienInput.classList.add('error');
                e.preventDefault();
                hasError = true;
                return;
            }

            if (!ngaychitieu) {
                alert('Vui lòng chọn ngày');
                e.preventDefault();
                hasError = true;
                return;
            }

            if (hasError) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>

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
                            <input type="number" id="sotien" name="sotien" required min="1" step="1"
                                   value="<?php echo htmlspecialchars($_POST['sotien'] ?? ''); ?>"
                                   placeholder="Nhập số tiền">
                        </div>

                        <div class="form-row">
                            <label for="ngaygiaodich">Ngày: <span class="required">*</span></label>
                            <input type="date" id="ngaygiaodich" name="ngaygiaodich" required
                                   value="<?php echo htmlspecialchars($_POST['ngaygiaodich'] ?? date('Y-m-d')); ?>">
                        </div>

                        <div class="form-row">
                            <label for="machitieu">Danh mục: <span class="required">*</span></label>
                            <select id="machitieu" name="machitieu" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php
                                // Lấy danh sách danh mục chi tiêu
                                try {
                                    require_once __DIR__ . '/../models/KhoanchiModel.php';
                                    $khoanchiModel = new KhoanchiModel();
                                    $categories = $khoanchiModel->getExpenseCategories(1); // Tạm thời hardcode makh = 1
                                    
                                    foreach ($categories as $category) {
                                        $selected = (isset($_POST['machitieu']) && $_POST['machitieu'] == $category['machitieu']) ? 'selected' : '';
                                        echo "<option value='{$category['machitieu']}' {$selected}>{$category['tendanhmuc']}</option>";
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
        // Format số tiền khi nhập
        document.getElementById('sotien').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                e.target.value = parseInt(value);
            }
        });

        // Validate form
        document.querySelector('form').addEventListener('submit', function(e) {
            const noidung = document.getElementById('noidung').value.trim();
            const machitieu = document.getElementById('machitieu').value;
            const sotien = document.getElementById('sotien').value;
            const ngaygiaodich = document.getElementById('ngaygiaodich').value;

            if (!noidung) {
                alert('Vui lòng nhập tên khoản chi tiêu');
                e.preventDefault();
                return;
            }

            if (!machitieu) {
                alert('Vui lòng chọn danh mục');
                e.preventDefault();
                return;
            }

            if (!sotien || parseInt(sotien) <= 0) {
                alert('Vui lòng nhập số tiền hợp lệ');
                e.preventDefault();
                return;
            }

            if (!ngaygiaodich) {
                alert('Vui lòng chọn ngày');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>

</html>

<?php
    session_start();
    require_once __DIR__ . '/../models/BaseModel.php';
    require_once __DIR__ . '/../models/KhoanchiModel.php';

 
    if (!isset($_SESSION['makh'])) {
        header('Location: login.php');
        exit();
    }

    $makh = (int)$_SESSION['makh'];
    $khoanchiModel = new KhoanchiModel();
    $message = '';
    $messageType = '';


    $categories = $khoanchiModel->getExpenseCategories($makh);

 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $noidung = trim($_POST['noidung'] ?? '');
        $sotien = isset($_POST['sotien']) ? (float)$_POST['sotien'] : 0;
        $ngay = $_POST['ngaygiaodich'] ?? '';
        $machitieu = isset($_POST['machitieu']) ? (int)$_POST['machitieu'] : null;
        $lapphieu = $_POST['lapphieu'] ?? 'Không lặp lại';

        
        if ($noidung === '' || $sotien <= 0 || !$ngay || !$machitieu) {
            $message = 'Vui lòng điền đầy đủ thông tin hợp lệ.';
            $messageType = 'error';
        } else {
            try {
                $ok = $khoanchiModel->addExpense($noidung, $sotien, $machitieu, $ngay, $lapphieu);
                if ($ok) {
                    $message = 'Lưu khoản chi thành công.';
                    $messageType = 'success';
                    
                    header('Refresh:2; url=khoanchi.php');
                } else {
                    $message = 'Lỗi khi lưu khoản chi.';
                    $messageType = 'error';
                }
            } catch (Exception $e) {
                $message = 'Lỗi hệ thống: ' . htmlspecialchars($e->getMessage());
                $messageType = 'error';
            }
        }
    }
    ?>
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
                    <?php if ($message): ?>
                        <div class="alert <?= $messageType === 'success' ? 'alert-success' : 'alert-error' ?>">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-row">
                            <label for="noidung">Tên khoản chi tiêu: <span class="required">*</span></label>
                            <input type="text" id="noidung" name="noidung" required
                                   value="<?= htmlspecialchars($_POST['noidung'] ?? '') ?>"
                                   placeholder="Nhập tên khoản chi tiêu">
                        </div>

                        <div class="form-row">
                            <label for="sotien">Số tiền: <span class="required">*</span></label>
                            <input type="number" id="sotien" name="sotien" required min="1" step="0.01"
                                   value="<?= htmlspecialchars($_POST['sotien'] ?? '') ?>"
                                   placeholder="Nhập số tiền">
                        </div>

                        <div class="form-row">
                            <label for="ngaygiaodich">Ngày: <span class="required">*</span></label>
                            <input type="date" id="ngaygiaodich" name="ngaygiaodich" required
                                   value="<?= htmlspecialchars($_POST['ngaygiaodich'] ?? date('Y-m-d')) ?>">
                        </div>

                        <div class="form-row">
                            <label for="machitieu">Danh mục: <span class="required">*</span></label>
                            <select id="machitieu" name="machitieu" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $category):
                                    $selected = (isset($_POST['machitieu']) && $_POST['machitieu'] == $category['machitieu']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $category['machitieu'] ?>" <?= $selected ?>><?= htmlspecialchars($category['tendanhmuc']) ?></option>
                                <?php endforeach; ?>
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
       
        const sotienEl = document.getElementById('sotien');
        if (sotienEl) {
            sotienEl.addEventListener('input', function (e) {
                let value = e.target.value.replace(/[^(\d|\.)]/g, '');
                if (value) {
                    e.target.value = value;
                }
            });
        }

     
        document.querySelector('form').addEventListener('submit', function (e) {
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

            if (!sotien || parseFloat(sotien) <= 0) {
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

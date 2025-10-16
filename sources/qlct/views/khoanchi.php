<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Khoản chi</title>
    <link rel="stylesheet" href="../public/css/khoanchi.css" />
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
                <h1>Khoản chi</h1>

                <div class="search" role="search">
                    <form method="get" action="khoanchi.php">
                        <input id="q" name="q" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" />
                        <button id="searchBtn" type="submit">Tìm kiếm</button>
                    </form>
                </div>

                <div class="header-right">
                    <div class="avatar" title="Văn A">
                        <div class="circle">VA</div>
                        <div style="font-weight:600;color:#0b6b3f">Văn A</div>
                    </div>
                    <div class="bell" title="Thông báo">🔔</div>
                </div>
            </header>

            <main class="content">
                <div class="controls">
                    <button class="btn primary" id="addBtn" onclick="window.location.href='them_khoanchi.php'">Thêm khoản chi tiêu</button>
                    <button class="btn danger" id="deleteBtn">Xóa</button>
                </div>

                <section class="card" aria-labelledby="tableTitle">
                    <table id="expenseTable" aria-describedby="tableTitle">
                        <thead>
                            <tr>
                                <th class="col-select"><input id="selectAll" type="checkbox" /></th>
                                <th class="col-date">Ngày</th>
                                <th class="col-content">Nội dung</th>
                                <th class="col-type">Loại</th>
                                <th class="col-money">Số tiền</th>
                            </tr>
                        </thead>

                        <tbody id="tbody">
                            <?php
                            // --- Dữ liệu demo, bạn có thể thay bằng dữ liệu lấy từ DB ---
                            $data = [
                                ["10/10/2025", "Mua đồ ăn", "Ăn uống", "100.000"],
                                ["09/10/2025", "Thanh toán điện nước", "Hóa đơn", "500.000"],
                                ["08/10/2025", "Đi taxi", "Đi lại", "150.000"],
                                ["07/10/2025", "Mua sắm", "Mua sắm", "800.000"],
                                ["06/10/2025", "Tiền nhà", "Hóa đơn", "2.000.000"],
                            ];

                            foreach ($data as $row) {
                                echo "<tr>
                                    <td><input type='checkbox' /></td>
                                    <td>{$row[0]}</td>
                                    <td>{$row[1]}</td>
                                    <td>{$row[2]}</td>
                                    <td>{$row[3]}</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <div class="pagination" style="margin-top:12px;">
                        <div class="circle" id="prevBtn">&lt;</div>
                        <div class="page-num" id="pageInfo">1/2</div>
                        <div class="circle" id="nextBtn">&gt;</div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="../public/js/khoanchi.js"></script>
</body>

</html>

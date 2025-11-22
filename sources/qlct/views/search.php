<?php
session_start();
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ExpenseModel.php';
require_once __DIR__ . '/../models/SpendingModel.php';


if (!isset($_SESSION['makh']) || empty($_SESSION['makh'])) {
    header('Location: /');
    exit;
}

$makh = (int) $_SESSION['makh'];
$expenseModel = new ExpenseModel();
$spendingModel = new SpendingModel();

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$searchType = isset($_GET['type']) ? $_GET['type'] : 'all'; // all, chi, khoanni

$results = [];
$totalResults = 0;
$message = '';


if (!empty($keyword) && strlen($keyword) >= 2) {
    if ($searchType === 'chi') {
       
        $results = $expenseModel->searchExpenses($makh, $keyword, 100, 0);
        $totalResults = $expenseModel->countSearchExpenses($makh, $keyword);
    } elseif ($searchType === 'khoanni') {
       
        $results = $SpendingModel->searchExpenses($keyword, 100, 0);
        $totalResults = $SpendingModel->countSearchExpenses($keyword);
    } else {
        
        $allResults = $expenseModel->globalSearch($makh, $keyword, 50);
        $results = array_merge(
            $allResults['chitieu'] ?? [],
            $allResults['khoanni'] ?? [],
            $allResults['danhmuc'] ?? []
        );
        $totalResults = count($results);
    }

    if (empty($results)) {
        $message = 'Không tìm thấy kết quả phù hợp với từ khóa "' . htmlspecialchars($keyword) . '".';
    }
} elseif (!empty($keyword) && strlen($keyword) < 2) {
    $message = 'Vui lòng nhập ít nhất 2 ký tự để tìm kiếm.';
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tìm Kiếm Giao Dịch</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #a5f5bb;
            padding: 20px 0;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 12px;
        }
        h1 {
            margin: 0 0 15px 0;
            color: #333;
        }
        .search-form {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        input[type="text"] {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: white;
        }
        button {
            background-color: #82e59a;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #6ad88b;
        }
        .content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 12px;
        }
        .info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            color: #666;
        }
        .no-data {
            text-align: center;
            color: #e74c3c;
            margin-top: 20px;
            font-weight: bold;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #a5f5bb;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        .loai-chi {
            color: #e74c3c;
            font-weight: 600;
        }
        .loai-thu {
            color: #27ae60;
            font-weight: 600;
        }
        .loai-khoanni {
            color: #3498db;
            font-weight: 600;
        }
        .loai-danhmuc {
            color: #f39c12;
            font-weight: 600;
        }
        @media (max-width: 600px) {
            .search-form {
                flex-direction: column;
            }
            input[type="text"], select {
                width: 100%;
            }
            table {
                font-size: 12px;
            }
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="header-content">
        <h1>Tìm Kiếm Giao Dịch / Khoản Chi</h1>
        <form method="GET" class="search-form">
            <input type="text" name="keyword" placeholder="Nhập từ khóa tìm kiếm (tên, danh mục, mô tả)..." 
                   value="<?= htmlspecialchars($keyword) ?>" required minlength="2">
            <select name="type">
                <option value="all" <?= ($searchType === 'all') ? 'selected' : '' ?>>Tất cả</option>
                <option value="chi" <?= ($searchType === 'chi') ? 'selected' : '' ?>>Chi Tiêu</option>
                <option value="khoanni" <?= ($searchType === 'khoanni') ? 'selected' : '' ?>>Khoản Chi</option>
            </select>
            <button type="submit">Tìm Kiếm</button>
        </form>
    </div>
</div>

<div class="content">
    <?php if (!empty($keyword)): ?>
        <div class="info">
            <?php if (!empty($results)): ?>
                Tìm thấy <strong><?= $totalResults ?></strong> kết quả phù hợp với từ khóa "<?= htmlspecialchars($keyword) ?>"
            <?php else: ?>
                <span style="color: #e74c3c;"><?= htmlspecialchars($message) ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Loại</th>
                    <th>Mô Tả / Tên</th>
                    <th>Danh Mục</th>
                    <th style="text-align: right; width: 120px;">Số Tiền (VND)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $item): ?>
                    <tr>
                        <td>
                            <?php 
                            
                            if (isset($item['ngaychitieu'])) {
                                echo htmlspecialchars(date('d/m/Y', strtotime($item['ngaychitieu'])));
                            } elseif (isset($item['ngaybatdau'])) {
                                echo htmlspecialchars(date('d/m/Y', strtotime($item['ngaybatdau'])));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if (isset($item['loai']) && $item['loai'] === 'khoanni') {
                                echo '<span class="loai-khoanni">Khoản Chi</span>';
                            } elseif (isset($item['loai']) && $item['loai'] === 'danhmuc') {
                                echo '<span class="loai-danhmuc">Danh Mục</span>';
                            } else {
                                echo '<span class="loai-chi">Chi Tiêu</span>';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($item['noidung'] ?? $item['tenkhoanchi'] ?? $item['tendanhmuc'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($item['tendanhmuc'] ?? 'N/A') ?></td>
                        <td style="text-align: right;">
                            <?php
                            $sotien = isset($item['sotien']) ? (float)$item['sotien'] : 0;
                            echo number_format($sotien, 0, ',', '.');
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (!empty($keyword)): ?>
        <div class="no-data">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php else: ?>
        <div style="padding: 20px; color: #666; text-align: center;">
            Nhập từ khóa để tìm kiếm giao dịch hoặc khoản chi.
        </div>
    <?php endif; ?>
</div>

</body>
</html>

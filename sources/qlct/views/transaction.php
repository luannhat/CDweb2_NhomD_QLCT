<style>
/* Background toàn màn hình */
body {
    background-color: #eef2f7; /* nền nhẹ */
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
}

/* Căn giữa bảng */
.table-container {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

/* Bảng */
table {
    border-collapse: collapse; /* giữ đường dọc và ngang */
    min-width: 800px;
    text-align: center;
    background-color: #ffffff; 
    box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
    border-radius: 10px;
    overflow: hidden;
}

/* Header */
th {
    background-color: #4a90e2; 
    color: white;
    font-weight: bold;
    padding: 12px 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid #ccc; /* đường kẻ dọc ngang */
}

/* Dòng dữ liệu */
td {
    padding: 12px 15px;
    border: 1px solid #ccc; /* đường kẻ dọc ngang */
}

/* Dòng chẵn xen kẽ */
tr:nth-child(even) td {
    background-color: #f9f9f9;
}

/* Hover trên cả dòng */
tr:hover td {
    background-color: #e6f7ff;
    transition: 0.2s;
}

/* Input trong form */
input[type="text"] {
    padding: 6px;
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 5px;
}

/* Nút lưu */
button {
    padding: 6px 12px;
    cursor: pointer;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    transition: 0.2s;
}

button:hover {
    background-color: #218838;
}

/* Tiêu đề */
h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

/* Thông báo */
.message {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
    color: green;
}
</style>


<div>
    <h1>Giao dịch của <?= htmlspecialchars($tenkh ?? 'Khách hàng') ?></h1>
</div>
<div class="table-container">
<?php if (!empty($giaodichs) && is_array($giaodichs)): ?>
    <table>
        <tr>
            <th>Mã giao dịch</th>
            <th>Mã khách hàng</th>
            <th>Nội dung</th>
            <th>Số tiền</th>
            <th>Ghi chú</th>
            <th>Thay đổi ghi chú</th>
        </tr>
        <?php foreach ($giaodichs as $gd): ?>
            <tr>
                <td><?= $gd['magd'] ?></td>
                <td><?= $gd['makh'] ?></td>
                <td><?= $gd['noidung'] ?></td>
                <td><?= $gd['sotien'] ?></td>
                <td><?= $gd['ghichu'] ?></td>
                <td>
                    <form method="POST" action="index.php?controller=transaction&action=updateNote">
                        <input type="hidden" name="magd" value="<?= $gd['magd'] ?>">
                        <input type="text" name="ghichu" value="" placeholder="Nhập ghi chú">
                        <button type="submit">Lưu</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Chưa có giao dịch nào.</p>
<?php endif; ?>
</div>

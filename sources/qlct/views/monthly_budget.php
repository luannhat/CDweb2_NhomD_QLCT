<?php
session_start();
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ExpenseModel.php';


if (!isset($_SESSION['makh'])) {
    header("Location: login.php");
    exit();
}

$makh = $_SESSION['makh'];
$model = new ExpenseModel();
$message = '';
$messageType = '';

// ============ XỬ LÝ LƯU NGÂN SÁCH ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $thang = (int)($_POST['thang'] ?? date('n'));
    $nam = (int)($_POST['nam'] ?? date('Y'));
    $an_uong = (int)($_POST['an_uong'] ?? 0);
    $di_lai = (int)($_POST['di_lai'] ?? 0);
    $mua_sam = (int)($_POST['mua_sam'] ?? 0);
    
    
    $ngay = sprintf("%04d-%02d-01", $nam, $thang);

    try {
        $success = $model->saveNganSachChiTiet($makh, $ngay, 0, $an_uong, $di_lai, $mua_sam);
        if ($success) {
            $message = '✓ Lưu/cập nhật ngân sách thành công!';
            $messageType = 'success';
        } else {
            $message = '✗ Lỗi khi lưu ngân sách!';
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = '✗ Lỗi hệ thống: ' . htmlspecialchars($e->getMessage());
        $messageType = 'error';
    }
}


$show_thang = (int)($_GET['thang'] ?? $_POST['thang'] ?? date('n'));
$show_nam = (int)($_GET['nam'] ?? $_POST['nam'] ?? date('Y'));

// ============ LẤY DỮ LIỆU NGÂN SÁCH ============
$ns = $model->getBudgetBreakdown($makh, $show_thang, $show_nam);
$ngansach_val = 0;
$an_uong_v = 0;
$di_lai_v = 0;
$mua_sam_v = 0;

if ($ns) {
    $ngansach_val = (int)($ns['tong_ngan_sach'] ?? 0);
    $an_uong_v = (int)($ns['anuong'] ?? 0);
    $di_lai_v = (int)($ns['dilai'] ?? 0);
    $mua_sam_v = (int)($ns['muasam'] ?? 0);
}

// ============ LẤY CHI TIÊU THỰC TẾ ============
$chitieuData = $model->getChiTieuByMonth($makh, $show_thang, $show_nam);
$data = [];
$total_expense = 0;
$hasData = false;

if (!empty($chitieuData)) {
    $hasData = true;
    foreach ($chitieuData as $item) {
        $data[] = $item;
        $total_expense += (int)$item['tongtien'];
    }
}

$chi_thuc_te = $total_expense;
$con_lai = $ngansach_val - $chi_thuc_te;
$tyle_chi = $ngansach_val > 0 ? ($chi_thuc_te / $ngansach_val) * 100 : 0;

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lập Ngân Sách Theo Tháng</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
* {margin:0; padding:0; box-sizing:border-box;}
body {font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height:100vh; background:#ffffff; padding:20px;}
.container {width:100%; margin:0 auto; background:white; min-height:100vh; padding-bottom:40px;}
header {background:#a5f5bb; color:#000; padding:40px 20px; text-align:center; border-bottom:2px solid #7cd89b;}
header h1 {font-size:36px; margin-bottom:0; font-weight:700;}
.content {padding:30px;}
.message {padding:15px 20px; margin-bottom:20px; border-radius:8px; display:flex; align-items:center; gap:10px; font-weight:600; animation:slideIn 0.3s ease-in-out;}
@keyframes slideIn {from {opacity:0; transform:translateY(-10px);} to {opacity:1; transform:translateY(0);}}
.message.success {background:#d4edda; color:#155724; border:1px solid #c3e6cb;}
.message.error {background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;}
.tabs {display:flex; gap:10px; margin-bottom:30px; border-bottom:2px solid #eee; flex-wrap:wrap;}
.tab-btn {padding:12px 24px; background:none; border:none; cursor:pointer; font-size:16px; font-weight:600; color:#666; border-bottom:3px solid transparent; transition:all 0.3s; position:relative; top:2px;}
.tab-btn.active {color:#667eea; border-bottom-color:#667eea;}
.tab-btn:hover {color:#667eea;}
.tab-content {display:none; animation:fadeIn 0.3s ease-in-out;}
@keyframes fadeIn {from {opacity:0;} to {opacity:1;}}
.tab-content.active {display:block;}
.month-selector {display:flex; gap:15px; align-items:center; margin-bottom:30px; background:#f8f9fa; padding:20px; border-radius:10px; flex-wrap:wrap;}
.month-selector label {font-weight:600; color:#333;}
.month-selector select {padding:10px 15px; border:2px solid #ddd; border-radius:8px; font-size:14px; cursor:pointer; transition:border-color 0.3s;}
.month-selector select:focus {outline:none; border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,0.1);}
.form-group {margin-bottom:20px;}
label {display:block; margin-bottom:8px; font-weight:600; color:#333;}
input[type="number"], select {width:100%; padding:12px 15px; border:2px solid #ddd; border-radius:8px; font-size:14px; transition:border-color 0.3s;}
input[type="number"]:focus, select:focus {outline:none; border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,0.1);}
.form-row {display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:20px; margin-bottom:30px;}
.btn-group {display:flex; gap:10px; justify-content:center; margin-top:30px; flex-wrap:wrap;}
button {padding:12px 30px; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.3s;}
button[type="submit"] {background:linear-gradient(135deg, #7cd89b 100%); color:white;}
button[type="submit"]:hover {transform:translateY(-2px); box-shadow:0 10px 20px rgba(102,126,234,0.3);}
button[type="reset"] {background:#f0f0f0; color:#333;}
button[type="reset"]:hover {background:#e0e0e0;}
.info-box {background:#f8f9fa; padding:25px; border-radius:10px; margin-bottom:30px; border-left:5px solid #7cd89b;}
.info-row {display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px;}
.info-item {background:white; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
.info-item label {font-size:12px; color:#999; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;}
.info-item .value {font-size:24px; font-weight:700; color:#667eea;}
.info-item.warning .value {color:#ff6b6b;}
.info-item.success .value {color:#51cf66;}
.chart-container {background:#f8f9fa; padding:25px; border-radius:10px; margin-bottom:30px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
.chart-container h3 {margin-bottom:20px; color:#333; font-size:18px;}
canvas {max-width:100%;}
.no-data {text-align:center; padding:60px 20px; color:#999;}
.no-data p {font-size:16px; margin-bottom:15px;}
.no-data a {display:inline-block; margin-top:10px; padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:8px; transition:all 0.3s;}
.no-data a:hover {background:#764ba2;}
@media (max-width:768px){
    .form-row {grid-template-columns:1fr;}
    .info-row {grid-template-columns:1fr;}
    header h1 {font-size:24px;}
    .content {padding:20px;}
    .tabs {flex-direction:column;}
    .month-selector {flex-direction:column; align-items:flex-start;}
    .month-selector select {width:100%;}
}
</style>
</head>
<body>

<div class="container">
    <header>
        <h1>Lập Ngân Sách Theo Tháng</h1>
    </header>

    <div class="content">
        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <!-- Month Selector -->
        <div class="month-selector">
            <label for="thangnam-select">Chọn tháng/năm:</label>
            <select id="thangnam-select" onchange="changeMonth()">
                <?php
                    for ($y=2020; $y<=2030; $y++) {
                        for ($m=1; $m<=12; $m++) {
                            $selected = ($m==$show_thang && $y==$show_nam)?'selected':'';
                            $displayMonth = str_pad($m,2,'0',STR_PAD_LEFT);
                            echo "<option value=\"$m,$y\" $selected>Tháng $displayMonth/$y</option>";
                        }
                    }
                ?>
            </select>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('form-tab', event)">
                Lập/Cập Nhật Ngân Sách
            </button>
            <button class="tab-btn" onclick="switchTab('view-tab', event)">
                Xem Thống Kê
            </button>
        </div>

        <!-- TAB 1: FORM -->
        <div id="form-tab" class="tab-content active">
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <div class="form-row">
                    <div class="form-group">
                        <label for="thang">Chọn tháng:</label>
                        <select name="thang" id="thang" required>
                            <?php for($i=1;$i<=12;$i++): ?>
                                <option value="<?= $i ?>" <?= $i==$show_thang?'selected':'' ?>>Tháng <?= str_pad($i,2,'0',STR_PAD_LEFT)?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nam">Chọn năm:</label>
                        <select name="nam" id="nam" required>
                            <?php for($i=2020;$i<=2030;$i++): ?>
                                <option value="<?= $i ?>" <?= $i==$show_nam?'selected':'' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="an_uong">Ăn uống (VND):</label>
                        <input type="number" id="an_uong" name="an_uong" min="0" value="<?= $an_uong_v ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="di_lai">Đi lại (VND):</label>
                        <input type="number" id="di_lai" name="di_lai" min="0" value="<?= $di_lai_v ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mua_sam">Mua sắm (VND):</label>
                        <input type="number" id="mua_sam" name="mua_sam" min="0" value="<?= $mua_sam_v ?>" required>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit">Lưu Ngân Sách</button>
                    <button type="reset">Hủy Bỏ</button>
                </div>
            </form>
        </div>

        <!-- TAB 2: STATISTICS -->
        <div id="view-tab" class="tab-content">
            <?php if ($ngansach_val==0 && !$hasData): ?>
                <div class="no-data">
                    <p>Chưa có dữ liệu cho tháng này!</p>
                    <p>Vui lòng bắt đầu bằng cách lập ngân sách</p>
                </div>
            <?php else: ?>
                <div class="info-box">
                    <div class="info-row">
                        <div class="info-item">
                            <label>Ngân Sách Dự Kiến</label>
                            <div class="value"><?= number_format($ngansach_val,0,',','.') ?></div>
                            <small style="color:#999;">VND</small>
                        </div>
                        <div class="info-item <?= $chi_thuc_te>$ngansach_val?'warning':'' ?>">
                            <label>Chi Tiêu Thực Tế</label>
                            <div class="value"><?= number_format($chi_thuc_te,0,',','.') ?></div>
                            <small style="color:#999;">VND</small>
                        </div>
                        <div class="info-item <?= $con_lai<0?'warning':'success' ?>">
                            <label>Còn Lại</label>
                            <div class="value" style="color:<?= $con_lai>=0?'#51cf66':'#ff6b6b' ?>;"><?= number_format($con_lai,0,',','.') ?></div>
                            <small style="color:#999;">VND</small>
                        </div>
                        <div class="info-item">
                            <label>Tỷ Lệ Chi Tiêu</label>
                            <div class="value"><?= number_format($tyle_chi,1,',','.') ?>%</div>
                            <small style="color:#999;">của ngân sách</small>
                        </div>
                    </div>
                </div>

                <div class="chart-container">
                    <h3>So Sánh Ngân Sách vs Chi Tiêu</h3>
                    <canvas id="budgetChart"></canvas>
                </div>

                <div class="chart-container">
                    <h3>Chi Tiêu Theo Danh Mục</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function switchTab(tabId,event){
    event.preventDefault();
    document.querySelectorAll('.tab-content').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    event.target.classList.add('active');
}
function changeMonth(){
    const [thang,nam]=document.getElementById('thangnam-select').value.split(',');
    window.location.href=`?thang=${thang}&nam=${nam}`;
}

// Budget Chart
<?php if($ngansach_val>0 || $hasData): ?>
const ctxBudget=document.getElementById('budgetChart');
if(ctxBudget){
    new Chart(ctxBudget,{
        type:'bar',
        data:{
            labels:['Ngân Sách','Chi Tiêu','Còn Lại'],
            datasets:[{
                label:'Số Tiền (VND)',
                data:[<?= $ngansach_val ?>,<?= $chi_thuc_te ?>,<?= max($con_lai,0) ?>],
                backgroundColor:['rgba(102,126,234,0.8)','rgba(255,107,107,0.8)','rgba(81,207,102,0.8)'],
                borderColor:['rgba(102,126,234,1)','rgba(255,107,107,1)','rgba(81,207,102,1)'],
                borderWidth:2,
                borderRadius:8
            }]
        },
        options:{
            responsive:true,
            maintainAspectRatio:true,
            plugins:{legend:{display:true,position:'bottom',labels:{font:{size:14,weight:'bold'},padding:15}}},
            scales:{y:{beginAtZero:true,ticks:{callback:function(value){return value.toLocaleString('vi-VN')+' đ';}}}}
        }
    });
}
<?php endif; ?>

// Category Chart
<?php if($hasData): ?>
const ctxCategory=document.getElementById('categoryChart');
if(ctxCategory){
    new Chart(ctxCategory,{
        type:'doughnut',
        data:{
            labels:<?= json_encode(array_column($data,'danhmuc')) ?>,
            datasets:[{
                label:'Chi Tiêu (VND)',
                data:<?= json_encode(array_column($data,'tongtien')) ?>,
                backgroundColor:['rgba(102,126,234,0.8)','rgba(255,193,7,0.8)','rgba(76,175,80,0.8)','rgba(244,67,54,0.8)','rgba(156,39,176,0.8)','rgba(0,188,212,0.8)'],
                borderColor:['rgba(102,126,234,1)','rgba(255,193,7,1)','rgba(76,175,80,1)','rgba(244,67,54,1)','rgba(156,39,176,1)','rgba(0,188,212,1)'],
                borderWidth:2
            }]
        },
        options:{
            responsive:true,
            maintainAspectRatio:true,
            plugins:{
                legend:{position:'bottom',labels:{font:{size:14,weight:'bold'},padding:15}},
                tooltip:{callbacks:{label:function(context){return context.label+': '+context.parsed.toLocaleString('vi-VN')+' đ';}}}
            }
        }
    });
}
<?php endif; ?>
</script>

</body>
</html>

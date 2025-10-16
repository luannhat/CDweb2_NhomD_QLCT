// === Dữ liệu mẫu ===
const dataAll = [
    { date: '10/10/2025', content: 'Mua đồ ăn', type: 'Ăn uống', money: 100000 },
    { date: '09/10/2025', content: 'Thanh toán điện nước', type: 'Hóa đơn', money: 500000 },
    { date: '08/10/2025', content: 'Đi taxi', type: 'Đi lại', money: 150000 },
    { date: '07/10/2025', content: 'Mua sắm', type: 'Mua sắm', money: 800000 },
    { date: '06/10/2025', content: 'Tiền nhà', type: 'Hóa đơn', money: 2000000 },
    { date: '05/10/2025', content: 'Cà phê bạn bè', type: 'Ăn uống', money: 90000 },
    { date: '04/10/2025', content: 'Xăng xe', type: 'Đi lại', money: 120000 },
    { date: '03/10/2025', content: 'Mua sách', type: 'Giáo dục', money: 250000 },
];

const rowsPerPage = 5; // 🟢 hiển thị tối đa 5 hàng
let currentPage = 0;

const tbody = document.getElementById('tbody');
const pageInfo = document.getElementById('pageInfo');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const selectAll = document.getElementById('selectAll');

function formatMoney(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function render() {
    tbody.innerHTML = '';
    const start = currentPage * rowsPerPage;
    const end = start + rowsPerPage;
    const pageData = dataAll.slice(start, end);

    pageData.forEach((row, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
        <td class="col-select"><input type="checkbox" class="row-check" data-index="${start + idx}" /></td>
        <td class="col-date">${row.date}</td>
        <td class="col-content">${row.content}</td>
        <td class="col-type">${row.type}</td>
        <td class="col-money">${formatMoney(row.money)}</td>
      `;
        tbody.appendChild(tr);
    });

    const totalPages = Math.ceil(dataAll.length / rowsPerPage);
    pageInfo.textContent = `${currentPage + 1}/${totalPages}`;
    selectAll.checked = false;
}

// pagination
prevBtn.addEventListener('click', () => {
    if (currentPage > 0) {
        currentPage--;
        render();
    }
});

nextBtn.addEventListener('click', () => {
    const totalPages = Math.ceil(dataAll.length / rowsPerPage);
    if (currentPage < totalPages - 1) {
        currentPage++;
        render();
    }
});

// thêm khoản chi
document.getElementById('addBtn').addEventListener('click', () => {
    const date = prompt('Ngày (vd: 12/10/2025):');
    if (!date) return;
    const content = prompt('Nội dung:', 'Ví dụ: Mua đồ');
    if (!content) return;
    const type = prompt('Loại:', 'Ăn uống');
    if (!type) return;
    const moneyStr = prompt('Số tiền (chỉ số, không dùng dấu):', '100000');
    const money = parseInt(moneyStr || '0', 10);
    if (isNaN(money) || money <= 0) {
        alert('Số tiền không hợp lệ.');
        return;
    }

    dataAll.unshift({ date, content, type, money });
    currentPage = 0; // về trang đầu để thấy mục mới
    render();
});

// xóa mục được chọn
document.getElementById('deleteBtn').addEventListener('click', () => {
    const checks = Array.from(document.querySelectorAll('.row-check'));
    const toRemoveIdx = checks.filter(ch => ch.checked).map(ch => parseInt(ch.dataset.index));
    if (toRemoveIdx.length === 0) {
        alert('Chưa chọn hàng để xóa.');
        return;
    }
    if (!confirm(`Xóa ${toRemoveIdx.length} mục?`)) return;

    // xóa theo index giảm dần
    toRemoveIdx.sort((a, b) => b - a).forEach(i => dataAll.splice(i, 1));
    const totalPages = Math.ceil(dataAll.length / rowsPerPage);
    if (currentPage >= totalPages) currentPage = totalPages - 1;
    if (currentPage < 0) currentPage = 0;
    render();
});

// chọn tất cả
selectAll.addEventListener('change', (e) => {
    const checks = document.querySelectorAll('.row-check');
    checks.forEach(c => c.checked = e.target.checked);
});

// tìm kiếm
document.getElementById('searchBtn').addEventListener('click', () => {
    const q = document.getElementById('q').value.trim().toLowerCase();
    if (!q) {
        render();
        return;
    }

    const filtered = dataAll.filter(r =>
        r.content.toLowerCase().includes(q) ||
        r.type.toLowerCase().includes(q) ||
        r.date.includes(q)
    );

    tbody.innerHTML = '';
    filtered.slice(0, rowsPerPage).forEach((row, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
        <td class="col-select"><input type="checkbox" /></td>
        <td class="col-date">${row.date}</td>
        <td class="col-content">${row.content}</td>
        <td class="col-type">${row.type}</td>
        <td class="col-money">${formatMoney(row.money)}</td>
      `;
        tbody.appendChild(tr);
    });
    pageInfo.textContent = 'Tìm kiếm';
    selectAll.checked = false;
});

render();
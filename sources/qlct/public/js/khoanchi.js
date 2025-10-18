// === Khởi tạo ===
const tbody = document.getElementById('tbody');
const pageInfo = document.getElementById('pageInfo');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const selectAll = document.getElementById('selectAll');
const deleteBtn = document.getElementById('deleteBtn');

// === Utility functions ===
function formatMoney(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function showMessage(message, type = 'info') {
    // Tạo thông báo tạm thời
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1'};
        color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460'};
        border: 1px solid ${type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb'};
        border-radius: 4px;
        z-index: 9999;
        max-width: 300px;
    `;
    alertDiv.textContent = message;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// === AJAX functions ===
async function makeAjaxRequest(action, data = {}) {
    try {
        const formData = new FormData();
        formData.append('action', action);
        
        Object.keys(data).forEach(key => {
            if (Array.isArray(data[key])) {
                data[key].forEach(item => {
                    formData.append(`${key}[]`, item);
                });
            } else {
                formData.append(key, data[key]);
            }
        });

        const response = await fetch('?ajax=1', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        return result;
    } catch (error) {
        console.error('AJAX Error:', error);
        return { success: false, message: 'Có lỗi xảy ra khi kết nối server' };
    }
}

// === Event handlers ===
deleteBtn.addEventListener('click', async () => {
    const checkedBoxes = document.querySelectorAll('.expense-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        showMessage('Vui lòng chọn khoản chi cần xóa', 'error');
        return;
    }

    if (!confirm(`Bạn có chắc muốn xóa ${checkedBoxes.length} khoản chi đã chọn?`)) {
        return;
    }

    const magdList = Array.from(checkedBoxes).map(cb => cb.value);
    
    const result = await makeAjaxRequest('delete_multiple', { magd_list: magdList });
    
    if (result.success) {
        showMessage(result.message, 'success');
        // Reload trang để cập nhật dữ liệu
        window.location.reload();
    } else {
        showMessage(result.message, 'error');
    }
});

// Chọn tất cả
selectAll.addEventListener('change', (e) => {
    const checkboxes = document.querySelectorAll('.expense-checkbox');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
});

// Cập nhật trạng thái nút xóa
function updateDeleteButtonState() {
    const checkedBoxes = document.querySelectorAll('.expense-checkbox:checked');
    deleteBtn.disabled = checkedBoxes.length === 0;
    deleteBtn.style.opacity = checkedBoxes.length === 0 ? '0.5' : '1';
}

// Lắng nghe sự kiện thay đổi checkbox
document.addEventListener('change', (e) => {
    if (e.target.classList.contains('expense-checkbox')) {
        updateDeleteButtonState();
    }
});

// Khởi tạo trạng thái nút xóa
updateDeleteButtonState();
<!-- includes/theme_modal.php hoặc resources/views/partials/theme-modal.blade.php -->
<div id="themeModal">
  <div class="theme-box">
    <h3>TÙY CHỈNH GIAO DIỆN</h3>
    <div class="theme-item">
      <span>Chế độ sáng (Light mode)</span>
      <label class="switch"><input type="radio" name="t" value="light"><span></span></label>
    </div>
    <div class="theme-item">
      <span>Chế độ tối (Dark mode)</span>
      <label class="switch"><input type="radio" name="t" value="dark"><span></span></label>
    </div>
    <div class="theme-item">
      <span>Theo hệ thống (Auto)</span>
      <label class="switch"><input type="radio" name="t" value="auto"><span></span></label>
    </div>
    <div class="theme-footer">
      <button onclick="document.getElementById('themeModal').classList.remove('show')">Quay lại</button>
      <button onclick="document.getElementById('themeModal').classList.remove('show')">Đóng</button>
    </div>
  </div>
</div>

<?php
// app/Http/Controllers/ThemeController.php
// Bạn không cần tạo controller thật, chỉ cần 1 file JS+HTML này thôi

// Tạo file: public/js/theme.js
// Nội dung file này:
?>
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">

<!-- Nút trong header -->
<li><a href="javascript:;" onclick="document.getElementById('themeModal').classList.add('show')" class="theme-btn" title="Giao diện">Palette</a></li>

<!-- Include popup -->
@include('partials.theme-modal')  <!-- nếu dùng Blade -->
<!-- hoặc: <?php include '../includes/theme_modal.php'; ?> nếu dùng PHP thuần -->

<script>
// Siêu ngắn gọn – chỉ 15 dòng
const b = document.body;
function apply(t){
  if(t==='dark'){ b.classList.add('dark'); localStorage.theme='dark' }
  else if(t==='light'){ b.classList.remove('dark'); localStorage.theme='light' }
  else{ b.classList.remove('dark'); localStorage.removeItem('theme') }
  document.getElementById('themeModal').classList.remove('show');
}

// Load theme khi mở trang
if(localStorage.theme==='dark' || (!localStorage.theme && window.matchMedia('(prefers-color-scheme: dark)').matches)){
  b.classList.add('dark');
}
document.querySelector(`input[value="${localStorage.theme||'auto'}"]`)?.checked=true;

// Click radio → tự đổi luôn (không cần nút Áp dụng)
document.querySelectorAll('input[name="t"]').forEach(r=>r.onchange=e=>apply(e.target.value));
</script>
<?php

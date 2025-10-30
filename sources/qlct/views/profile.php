<form action="/controllers/UploadAvatarController.php" method="POST" enctype="multipart/form-data">
  <input type="file" name="avatar" accept="image/*" required>
  <button type="submit">Cập nhật</button>
</form>
<img src="/public/images/<?php echo htmlspecialchars($user['hinhanh'] ?? 'default.png'); ?>" width="120" height="120" style="border-radius:50%;">

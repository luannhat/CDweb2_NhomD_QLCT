<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Ảnh đại diện</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            text-align: center;
            padding: 40px;
        }
        .avatar-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            display: inline-block;
            box-shadow: 0 0 10px #ccc;
        }
        .avatar-container img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #ddd;
        }
        .upload-form {
            margin-top: 15px;
        }
        input[type="file"] {
            margin: 10px 0;
        }
        button {
            background: #007bff;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .note {
            color: #777;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="avatar-container">
    <h2>Ảnh đại diện</h2>
    <img src="<?= htmlspecialchars($hinhanh) ?>" alt="Avatar">

    <?php if (isset($_SESSION['makh'])): ?>
        <form class="upload-form" action="index.php?action=upload_avatar_submit" method="POST" enctype="multipart/form-data">
            <input type="file" name="avatar" accept="image/*" required>
            <button type="submit">Lưu Avatar</button>
        </form>
    <?php else: ?>
        <p class="note">🔒 Vui lòng <a href="index.php?action=login">đăng nhập</a> để thay đổi ảnh đại diện.</p>
    <?php endif; ?>
</div>

</body>
</html>

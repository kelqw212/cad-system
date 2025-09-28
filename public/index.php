<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>CAD - Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Вход в CAD</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <input type="text" name="username" placeholder="Логин" required><br>
            <input type="password" name="password" placeholder="Пароль" required><br>
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>

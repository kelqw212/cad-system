<?php
session_start();
require_once '../db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
$stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>CAD - Панель</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Добро пожаловать, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <a href="logout.php">Выйти</a>
        <hr>
        <h3>Информация CAD (пример)</h3>
        <h3>Вызовы (Calls)</h3>
        <table id="calls-table" border="1" style="width:100%;margin-bottom:30px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Заголовок</th>
                    <th>Описание</th>
                    <th>Статус</th>
                    <th>Создано</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        function loadBolos() {
            fetch('../../bolos.php')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.querySelector('#bolos-table tbody');
                    tbody.innerHTML = '';
                    data.forEach(bolo => {
                        tbody.innerHTML += `<tr><td>${bolo.id}</td><td>${bolo.title}</td><td>${bolo.description}</td><td>${bolo.status}</td><td>${bolo.created_at}</td></tr>`;
                    });
                });
        }
        loadBolos();
        <h3>Юниты (Units)</h3>
        <table id="units-table" border="1" style="width:100%;margin-bottom:30px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Статус</th>
                    <th>Тип</th>
                    <th>Создано</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <h3>BOLO</h3>
        <table id="bolos-table" border="1" style="width:100%;margin-bottom:30px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Заголовок</th>
                    <th>Описание</th>
                    <th>Статус</th>
                    <th>Создано</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <script>
        function loadCalls() {
            fetch('../../calls.php')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.querySelector('#calls-table tbody');
                    tbody.innerHTML = '';
                    data.forEach(call => {
                        tbody.innerHTML += `<tr><td>${call.id}</td><td>${call.title}</td><td>${call.description}</td><td>${call.status}</td><td>${call.created_at}</td></tr>`;
                    });
                });
        }
        loadCalls();
        function loadUnits() {
            fetch('../../units.php')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.querySelector('#units-table tbody');
                    tbody.innerHTML = '';
                    data.forEach(unit => {
                        tbody.innerHTML += `<tr><td>${unit.id}</td><td>${unit.name}</td><td>${unit.status}</td><td>${unit.type}</td><td>${unit.created_at}</td></tr>`;
                    });
                });
        }
        loadUnits();
        </script>
    </div>
</body>
</html>

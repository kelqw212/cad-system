<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query('SELECT * FROM calls ORDER BY id DESC');
    echo json_encode($stmt->fetchAll());
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare('INSERT INTO calls (title, description, status) VALUES (?, ?, ?)');
    $stmt->execute([
        $data['title'] ?? '',
        $data['description'] ?? '',
        $data['status'] ?? 'open'
    ]);
    echo json_encode(['success' => true]);
    exit();
}

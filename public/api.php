<?php
// Пример REST API для интеграции сайта с WebSocket сервером CAD
// Здесь можно реализовать проксирование или прямую работу с базой
header('Content-Type: application/json');
require_once '../db.php';


$action = $_GET['action'] ?? '';
$entity = $_GET['entity'] ?? '';

function fetchAll($pdo, $table) {
    $stmt = $pdo->query("SELECT * FROM `$table` ORDER BY id DESC");
    return $stmt->fetchAll();
}

function fetchOne($pdo, $table, $id) {
    $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function insert($pdo, $table, $fields) {
    $keys = array_keys($fields);
    $sql = "INSERT INTO `$table` (".implode(",", $keys).") VALUES (".implode(",", array_fill(0, count($keys), '?')).")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($fields));
    return $pdo->lastInsertId();
}

function update($pdo, $table, $id, $fields) {
    $sets = [];
    foreach ($fields as $k => $v) $sets[] = "$k=?";
    $sql = "UPDATE `$table` SET ".implode(",", $sets)." WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge(array_values($fields), [$id]));
    return $stmt->rowCount();
}

function delete($pdo, $table, $id) {
    $stmt = $pdo->prepare("DELETE FROM `$table` WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->rowCount();
}

switch ($entity) {
    case 'users':
        if ($action === 'list') {
            echo json_encode(fetchAll($pdo, 'users'));
        }
        break;
    case 'calls':
        if ($action === 'list') {
            echo json_encode(fetchAll($pdo, 'calls'));
        } elseif ($action === 'get' && isset($_GET['id'])) {
            echo json_encode(fetchOne($pdo, 'calls', $_GET['id']));
        } elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = insert($pdo, 'calls', $data);
            echo json_encode(['success'=>true,'id'=>$id]);
        } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $cnt = update($pdo, 'calls', $_GET['id'], $data);
            echo json_encode(['success'=>true,'updated'=>$cnt]);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $cnt = delete($pdo, 'calls', $_GET['id']);
            echo json_encode(['success'=>true,'deleted'=>$cnt]);
        }
        break;
    case 'units':
        if ($action === 'list') {
            echo json_encode(fetchAll($pdo, 'units'));
        } elseif ($action === 'get' && isset($_GET['id'])) {
            echo json_encode(fetchOne($pdo, 'units', $_GET['id']));
        } elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = insert($pdo, 'units', $data);
            echo json_encode(['success'=>true,'id'=>$id]);
        } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $cnt = update($pdo, 'units', $_GET['id'], $data);
            echo json_encode(['success'=>true,'updated'=>$cnt]);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $cnt = delete($pdo, 'units', $_GET['id']);
            echo json_encode(['success'=>true,'deleted'=>$cnt]);
        }
        break;
    case 'bolos':
        if ($action === 'list') {
            echo json_encode(fetchAll($pdo, 'bolos'));
        } elseif ($action === 'get' && isset($_GET['id'])) {
            echo json_encode(fetchOne($pdo, 'bolos', $_GET['id']));
        } elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = insert($pdo, 'bolos', $data);
            echo json_encode(['success'=>true,'id'=>$id]);
        } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $cnt = update($pdo, 'bolos', $_GET['id'], $data);
            echo json_encode(['success'=>true,'updated'=>$cnt]);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $cnt = delete($pdo, 'bolos', $_GET['id']);
            echo json_encode(['success'=>true,'deleted'=>$cnt]);
        }
        break;
    case 'incidents':
        if ($action === 'list') {
            echo json_encode(fetchAll($pdo, 'incidents'));
        } elseif ($action === 'get' && isset($_GET['id'])) {
            echo json_encode(fetchOne($pdo, 'incidents', $_GET['id']));
        } elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = insert($pdo, 'incidents', $data);
            echo json_encode(['success'=>true,'id'=>$id]);
        } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $cnt = update($pdo, 'incidents', $_GET['id'], $data);
            echo json_encode(['success'=>true,'updated'=>$cnt]);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $cnt = delete($pdo, 'incidents', $_GET['id']);
            echo json_encode(['success'=>true,'deleted'=>$cnt]);
        }
        break;
    case 'rms':
        if ($action === 'list') {
            echo json_encode(fetchAll($pdo, 'rms'));
        } elseif ($action === 'get' && isset($_GET['id'])) {
            echo json_encode(fetchOne($pdo, 'rms', $_GET['id']));
        } elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = insert($pdo, 'rms', $data);
            echo json_encode(['success'=>true,'id'=>$id]);
        } elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $cnt = update($pdo, 'rms', $_GET['id'], $data);
            echo json_encode(['success'=>true,'updated'=>$cnt]);
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $cnt = delete($pdo, 'rms', $_GET['id']);
            echo json_encode(['success'=>true,'deleted'=>$cnt]);
        }
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown entity or action']);
}

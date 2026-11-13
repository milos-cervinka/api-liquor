<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../Model/logsModel.php';

$file = $_GET['file'] ?? '';
$type = $_GET['type'] ?? 'ALL';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

if ($page < 1)
{
    $page = 1;
}

$model = new LogsModel();
$data = $model->getLogs($file, $type, $search, $page, $limit);

echo json_encode($data);
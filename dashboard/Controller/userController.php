<?php
header('Content-Type: application/json; charset=utf-8');

// Source - https://stackoverflow.com/a/18542272
// Posted by lovelyramos, modified by community. See post 'Timeline' for change history
// Retrieved 2026-11-06, License - CC BY-SA 4.0


if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

$currentUserId   = (int)($_SESSION['user']['id'] ?? 0);
$currentUserRole = $_SESSION['user']['role'] ?? 'guest';

session_write_close();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../Model/userModel.php';

$model  = new UserModel();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') 
{
    $users = $model->getAll();
    echo json_encode([
        'currentUserId'   => $currentUserId,
        'currentUserRole' => $currentUserRole,
        'users'           => $users
    ]);
    exit;
}

if ($method === 'POST') 
{
    if ($currentUserRole !== 'admin') 
    {
        http_response_code(403);
        echo json_encode(['error' => 'not powerful enough']);
        exit;
    }
    // Source - https://stackoverflow.com/a/58927577
    // Posted by Anthony Rutledge, modified by community. See post 'Timeline' for change history
    // Retrieved 2026-11-06, License - CC BY-SA 4.0

    $input    = json_decode(file_get_contents('php://input'), true);
    $targetId = (int)($input['id'] ?? 0);
    $newRole  = $input['role'] ?? '';

    if ($targetId <= 0) 
    {
        http_response_code(400);
        echo json_encode(['error' => 'Wrong user id']);
        exit;
    }

    if ($targetId === $currentUserId) 
    {
        http_response_code(400);
        echo json_encode(['error' => 'Cant change own status']);
        exit;
    }

    if ($model->updateRole($targetId, $newRole)) 
    {
        echo json_encode(['success' => true]);
    } 
    else 
    {
        http_response_code(400);
        echo json_encode(['error' => 'Error while changing user role']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
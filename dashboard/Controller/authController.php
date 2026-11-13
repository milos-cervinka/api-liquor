<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../db.php';

if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

if (isset($_GET['action']) && $_GET['action'] === 'me') 
{
    header('Content-Type: application/json');
    if (!empty($_SESSION['user'])) 
    {
        echo json_encode(['logged' => true, 'user' => $_SESSION['user']]);
    } 
    else 
    {
        echo json_encode(['logged' => false]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') 
{
    unset($_SESSION['user']);
    session_destroy();
    header('Location: /Web/API/dashboard/View/Sites/dashboard/index.html');
    exit;
}

$idToken = $_POST['credential'] ?? null;

if (!$idToken) 
{
    die('Google token not send');
}

$clientId = '620105565533-c1jec3k2ihhad5g266fl6k546o3d3jnk.apps.googleusercontent.com';
$client = new Google_Client(['client_id' => $clientId]);

try 
{
    $payload = $client->verifyIdToken($idToken);
} 
catch (Exception $e) 
{
    die('Error while veryfiyng token:' . $e->getMessage());
}

if ($payload) 
{
    $googleId = $payload['sub'];
    $email    = $payload['email'];
    $name     = $payload['name'] ?? $email;
    $picture  = $payload['picture'] ?? '';

    $comm = $pdo->prepare('SELECT id, role FROM users WHERE google_id = ?');
    $comm->execute([$googleId]);
    $user = $comm->fetch(PDO::FETCH_ASSOC);

    if (!$user) 
    {
        $countUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $role = ($countUsers == 0) ? 'admin' : 'user';

        $insert = $pdo->prepare('INSERT INTO users (google_id, email, name, picture, role) VALUES (?, ?, ?, ?, ?)');
        $insert->execute([$googleId, $email, $name, $picture, $role]);
        $userId = $pdo->lastInsertId();
    } 
    else 
    {
        $userId = $user['id'];
        $role = $user['role'];
    }

    $_SESSION['user'] = [
        'id'       => $userId,
        'google_id'=> $googleId,
        'email'    => $email,
        'name'     => $name,
        'picture'  => $picture,
        'role'     => $role
    ];

    header('Location: /Web/API/dashboard/View/Sites/dashboard/index.html');
    exit;
} 
else 
{
    die('Wrong google token');
}
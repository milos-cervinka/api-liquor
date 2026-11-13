<?php
require_once __DIR__ . '/../../db.php';

class UserModel 
{
    private PDO $db;

    public function __construct() 
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function getAll(): array 
    {
        $comm = $this->db->query('SELECT id, name, email, picture, role, created_at FROM users ORDER BY id DESC');
        return $comm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRole(int $id, string $role): bool 
    {
        if (!in_array($role, ['user', 'admin'])) 
        {
            return false;
        }

        $comm = $this->db->prepare('UPDATE users SET role = ? WHERE id = ?');
        return $comm->execute([$role, $id]);
    }
}
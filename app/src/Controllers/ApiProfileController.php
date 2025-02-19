<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use PDO;

class ApiProfileController extends AbstractController
{
    public function __construct()
    {
        session_start();
        $_SESSION['user_id'] = 1;
    }

    private function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    private function getDbConnexion(): PDO {
        $host = 'php-oop-exercice-db';
        $db = 'blog';
        $user = 'root';
        $password = 'password';

        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        return new PDO($dsn, $user, $password);
    }

    private function getUser(): array {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->getDbConnexion()->prepare($sql);
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user;
    }

    private function getPosts(): array {
        $sql = "SELECT posts.id, posts.title, posts.created_at
                FROM posts 
                INNER JOIN users ON posts.user_id = users.id
                WHERE posts.user_id = :id
                ORDER BY posts.created_at DESC;";

        $stmt = $this->getDbConnexion()->prepare($sql);
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $posts;
    }

    public function process(Request $request): Response {
        if ($request->getMethod() === 'GET') {
            return $this->getProfile();
        } elseif ($request->getMethod() === 'POST') {
            return $this->updateProfile($request);
        }

        return new Response('Method not allowed', 405);
    }

    private function updateProfile(Request $request): Response {
        if (!$this->isLoggedIn()) {
            return new Response(json_encode(['error' => 'Unauthorized']), 401, ['Content-Type' => 'application/json']);
        }

        $data = json_decode($request->getBody(), true);
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $this->updateProfileData($name, $email, $password);

        return new Response(json_encode(['message' => 'Profile updated']), 200, ['Content-Type' => 'application/json']);
    }

    private function getProfile(): Response {
        if (!$this->isLoggedIn()) {
            return new Response(json_encode(['error' => 'Unauthorized']), 401, ['Content-Type' => 'application/json']);
        }

        $user = $this->getUser();
        $posts = $this->getPosts();

        return new Response(json_encode(['user' => $user, 'posts' => $posts]), 200, ['Content-Type' => 'application/json']);
    }
}
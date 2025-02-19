<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use PDO;

class ApiCreatePostController extends AbstractController
{
    public function __construct()
    {
        session_start();
        $_SESSION['user_id'] = 1; // Set this to a valid user ID for testing
    }

    public function process(Request $request): Response {
        $method = $request->getMethod();

        if ($method === 'POST') {
            return $this->createPost($request);
        }

        return new Response('Method not allowed', 405);
    }

    private function createPost(Request $request): Response {
        if (!$this->isLoggedIn()) {
            return new Response(json_encode(['error' => 'Unauthorized']), 401, ['Content-Type' => 'application/json']);
        }

        $data = json_decode($request->getBody(), true);
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? '';
        $userId = $_SESSION['user_id'];

        $pdo = $this->getDbConnexion();

        $sql = "INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['title' => $title, 'content' => $content, 'user_id' => $userId]);
        $postId = $pdo->lastInsertId();

        return new Response(json_encode(['message' => 'Post created', 'post_id' => $postId]), 201, ['Content-Type' => 'application/json']);
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
}
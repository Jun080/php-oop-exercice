<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use PDO;

class ApiPostController extends AbstractController
{
    public function __construct()
    {
        session_start();
        $_SESSION['user_id'] = 1;
    }

    public function process(Request $request): Response {
        $method = $request->getMethod();

        if ($method === 'GET') {
            return $this->getPosts();
        }

        return new Response('Method not allowed', 405);
    }

    private function getPosts(): Response {
        if (!$this->isLoggedIn()) {
            return new Response(json_encode(['error' => 'Unauthorized']), 401, ['Content-Type' => 'application/json']);
        }

        $sql = "SELECT * FROM posts ORDER BY created_at DESC";
        $stmt = $this->getDbConnexion()->query($sql);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return new Response(json_encode($posts), 200, ['Content-Type' => 'application/json']);
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
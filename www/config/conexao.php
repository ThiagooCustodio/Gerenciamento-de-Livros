<?php
function conectar() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=mysql;dbname=sistema_livros;charset=utf8mb4";
            $pdo = new PDO($dsn, 'livros_user', 'livros_pass', [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }

    return $pdo;
}
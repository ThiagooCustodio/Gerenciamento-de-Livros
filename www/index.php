<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/conexao.php';

$pdo = conectar();
$total_livros  = $pdo->query('SELECT COUNT(*) FROM livros')->fetchColumn();
$total_generos = $pdo->query('SELECT COUNT(*) FROM generos')->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início — Sistema de Livros</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container">
        <a href="index.php" class="logo"> Sistema de Livros</a>
        <nav>
            <a href="index.php">Início</a>
            <a href="generos/listar.php">Gêneros</a>
            <a href="livros/listar.php">Livros</a>
            <a href="logout.php">Sair</a>
        </nav>
    </div>
</header>

<main>
    <div class="secao-header">
        <h2>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h2>
    </div>

    <div class="cards-painel">
        <a href="generos/listar.php" class="card">
            <span class="card-icone">🏷️</span>
            <span class="card-numero"><?= $total_generos ?></span>
            <span class="card-label">Gêneros cadastrados</span>
        </a>

        <a href="livros/listar.php" class="card">
            <span class="card-icone">📖</span>
            <span class="card-numero"><?= $total_livros ?></span>
            <span class="card-label">Livros cadastrados</span>
        </a>
    </div>
</main>

<footer class="site-footer">
    <div class="container">
        <p>Sistema de Gerenciamento de Livros &copy; <?= date('Y') ?></p>
    </div>
</footer>

</body>
</html>
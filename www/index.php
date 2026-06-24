<?php
session_start();

// Redireciona para login se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Início</title>
</head>
<body>

<h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h1>

<nav>
    <a href="generos/listar.php">Gerenciar Gêneros</a> |
    <a href="livros/listar.php">Gerenciar Livros</a> |
    <a href="logout.php">Sair</a>
</nav>

<hr>

<p>Selecione uma opção no menu acima para começar.</p>

</body>
</html>
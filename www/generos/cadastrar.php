<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $genero = trim($_POST['genero'] ?? '');

    if (empty($genero)) {
        $erro = 'O nome do gênero é obrigatório.';
    } else {
        $pdo  = conectar();
        $stmt = $pdo->prepare('INSERT INTO generos (genero) VALUES (?)');
        $stmt->execute([$genero]);
        header('Location: listar.php?sucesso=Gênero cadastrado com sucesso!');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Gênero — Sistema de Livros</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container">
        <a href="../index.php" class="logo">📚 Sistema de Livros</a>
        <nav>
            <a href="../index.php">Início</a>
            <a href="listar.php">Gêneros</a>
            <a href="../livros/listar.php">Livros</a>
            <a href="../logout.php">Sair</a>
        </nav>
    </div>
</header>

<main>
    <div class="secao-header">
        <h2>Novo Gênero</h2>
        <a href="listar.php" class="btn btn-secundario">← Voltar</a>
    </div>

    <?php if ($erro): ?>
        <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="cadastrar.php">
        <div class="campo">
            <label for="genero">Nome do Gênero</label>
            <input type="text" id="genero" name="genero"
                   value="<?= htmlspecialchars($_POST['genero'] ?? '') ?>"
                   required autofocus>
        </div>

        <button type="submit" class="btn btn-sucesso">Salvar</button>
        <a href="listar.php" class="btn btn-secundario">Cancelar</a>
    </form>
</main>

<footer class="site-footer">
    <div class="container">
        <p>Sistema de Gerenciamento de Livros &copy; <?= date('Y') ?></p>
    </div>
</footer>

</body>
</html>
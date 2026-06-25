<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';

$pdo = conectar();

// Verifica se o ID foi passado na URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: listar.php');
    exit;
}

// Busca o gênero no banco
$stmt = $pdo->prepare('SELECT * FROM generos WHERE id = ?');
$stmt->execute([$id]);
$genero = $stmt->fetch();

// Se não encontrou, volta para a listagem
if (!$genero) {
    header('Location: listar.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['genero'] ?? '');

    if (empty($nome)) {
        $erro = 'O nome do gênero é obrigatório.';
    } else {
        $stmt = $pdo->prepare('UPDATE generos SET genero = ? WHERE id = ?');
        $stmt->execute([$nome, $id]);
        header('Location: listar.php?sucesso=Gênero atualizado com sucesso!');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Gênero — Sistema de Livros</title>
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
        <h2>Editar Gênero</h2>
        <a href="listar.php" class="btn btn-secundario">← Voltar</a>
    </div>

    <?php if ($erro): ?>
        <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="editar.php?id=<?= $id ?>">
        <div class="campo">
            <label for="genero">Nome do Gênero</label>
            <input type="text" id="genero" name="genero"
                   value="<?= htmlspecialchars($_POST['genero'] ?? $genero['genero']) ?>"
                   required autofocus>
        </div>

        <button type="submit" class="btn btn-primario">Salvar alterações</button>
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
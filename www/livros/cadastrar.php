<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';

$pdo = conectar();
$generos = $pdo->query('SELECT * FROM generos ORDER BY genero ASC')->fetchAll();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo         = trim($_POST['titulo'] ?? '');
    $autor          = trim($_POST['autor'] ?? '');
    $genero_id      = filter_input(INPUT_POST, 'genero_id', FILTER_VALIDATE_INT);
    $editora        = trim($_POST['editora'] ?? '');
    $ano_publicacao = filter_input(INPUT_POST, 'ano_publicacao', FILTER_VALIDATE_INT);
    $indicacao      = $_POST['indicacao'] ?? '';

    $indicacoes_validas = ['Para crianças', 'Para todas as idades', 'Para adultos'];

    if (empty($titulo) || empty($autor) || empty($editora)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!$genero_id) {
        $erro = 'Selecione um gênero válido.';
    } elseif (!$ano_publicacao || $ano_publicacao < 1000 || $ano_publicacao > date('Y')) {
        $erro = 'Informe um ano de publicação válido.';
    } elseif (!in_array($indicacao, $indicacoes_validas)) {
        $erro = 'Selecione uma indicação válida.';
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO livros (titulo, autor, genero_id, editora, ano_publicacao, indicacao)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$titulo, $autor, $genero_id, $editora, $ano_publicacao, $indicacao]);
        header('Location: listar.php?sucesso=Livro cadastrado com sucesso!');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Livro — Sistema de Livros</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container">
        <a href="../index.php" class="logo">📚 Sistema de Livros</a>
        <nav>
            <a href="../index.php">Início</a>
            <a href="../generos/listar.php">Gêneros</a>
            <a href="listar.php">Livros</a>
            <a href="../logout.php">Sair</a>
        </nav>
    </div>
</header>

<main>
    <div class="secao-header">
        <h2>Novo Livro</h2>
        <a href="listar.php" class="btn btn-secundario">← Voltar</a>
    </div>

    <?php if ($erro): ?>
        <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="cadastrar.php">
        <div class="campo">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo"
                   value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>"
                   required autofocus>
        </div>

        <div class="campo">
            <label for="autor">Autor</label>
            <input type="text" id="autor" name="autor"
                   value="<?= htmlspecialchars($_POST['autor'] ?? '') ?>"
                   required>
        </div>

        <div class="campo">
            <label for="genero_id">Gênero</label>
            <select id="genero_id" name="genero_id" required>
                <option value="">Selecione um gênero</option>
                <?php foreach ($generos as $genero): ?>
                    <option value="<?= $genero['id'] ?>"
                        <?= (($_POST['genero_id'] ?? '') == $genero['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($genero['genero']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="editora">Editora</label>
            <input type="text" id="editora" name="editora"
                   value="<?= htmlspecialchars($_POST['editora'] ?? '') ?>"
                   required>
        </div>

        <div class="campo">
            <label for="ano_publicacao">Ano de Publicação</label>
            <input type="number" id="ano_publicacao" name="ano_publicacao"
                   value="<?= htmlspecialchars($_POST['ano_publicacao'] ?? '') ?>"
                   min="1000" max="<?= date('Y') ?>" required>
        </div>

        <div class="campo">
            <label>Indicação</label>
            <div class="campo-radio">
                <label>
                    <input type="radio" name="indicacao" value="Para crianças"
                        <?= (($_POST['indicacao'] ?? '') === 'Para crianças') ? 'checked' : '' ?>>
                    Para crianças
                </label>
                <label>
                    <input type="radio" name="indicacao" value="Para todas as idades"
                        <?= (($_POST['indicacao'] ?? '') === 'Para todas as idades') ? 'checked' : '' ?>>
                    Para todas as idades
                </label>
                <label>
                    <input type="radio" name="indicacao" value="Para adultos"
                        <?= (($_POST['indicacao'] ?? '') === 'Para adultos') ? 'checked' : '' ?>>
                    Para adultos
                </label>
            </div>
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
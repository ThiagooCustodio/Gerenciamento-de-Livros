<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';

$pdo = conectar();

// Busca o ID do livro na URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: listar.php');
    exit;
}

// Busca os dados do livro no banco
$stmt = $pdo->prepare('SELECT * FROM livros WHERE id = ?');
$stmt->execute([$id]);
$livro = $stmt->fetch();

// Se o livro não existir, volta para a listagem
if (!$livro) {
    header('Location: listar.php');
    exit;
}

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
        // Atualiza o livro no banco
        $stmt = $pdo->prepare('
            UPDATE livros
            SET titulo = ?, autor = ?, genero_id = ?, editora = ?, ano_publicacao = ?, indicacao = ?
            WHERE id = ?
        ');
        $stmt->execute([$titulo, $autor, $genero_id, $editora, $ano_publicacao, $indicacao, $id]);
        header('Location: listar.php?sucesso=Livro atualizado com sucesso!');
        exit;
    }
}

$valores = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $livro;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Livro — Sistema de Livros</title>
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
        <h2>Editar Livro</h2>
        <a href="listar.php" class="btn btn-secundario">← Voltar</a>
    </div>

    <?php if ($erro): ?>
        <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="editar.php?id=<?= $id ?>">

        <div class="campo">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo"
                   value="<?= htmlspecialchars($valores['titulo']) ?>"
                   required autofocus>
        </div>

        <div class="campo">
            <label for="autor">Autor</label>
            <input type="text" id="autor" name="autor"
                   value="<?= htmlspecialchars($valores['autor']) ?>"
                   required>
        </div>

        <div class="campo">
            <label for="genero_id">Gênero</label>
            <select id="genero_id" name="genero_id" required>
                <option value="">Selecione um gênero</option>
                <?php foreach ($generos as $genero): ?>
                    <option value="<?= $genero['id'] ?>"
                        <?= ($valores['genero_id'] == $genero['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($genero['genero']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="editora">Editora</label>
            <input type="text" id="editora" name="editora"
                   value="<?= htmlspecialchars($valores['editora']) ?>"
                   required>
        </div>

        <div class="campo">
            <label for="ano_publicacao">Ano de Publicação</label>
            <input type="number" id="ano_publicacao" name="ano_publicacao"
                   value="<?= htmlspecialchars($valores['ano_publicacao']) ?>"
                   min="1000" max="<?= date('Y') ?>" required>
        </div>

        <div class="campo">
            <label>Indicação</label>
            <div class="campo-radio">
                <label>
                    <input type="radio" name="indicacao" value="Para crianças"
                        <?= ($valores['indicacao'] === 'Para crianças') ? 'checked' : '' ?>>
                    Para crianças
                </label>
                <label>
                    <input type="radio" name="indicacao" value="Para todas as idades"
                        <?= ($valores['indicacao'] === 'Para todas as idades') ? 'checked' : '' ?>>
                    Para todas as idades
                </label>
                <label>
                    <input type="radio" name="indicacao" value="Para adultos"
                        <?= ($valores['indicacao'] === 'Para adultos') ? 'checked' : '' ?>>
                    Para adultos
                </label>
            </div>
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
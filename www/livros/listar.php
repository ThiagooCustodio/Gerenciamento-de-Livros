<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';

$pdo = conectar();

// Captura o termo de busca da URL
$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {
    // Busca por título, autor ou gênero
    $stmt = $pdo->prepare('
        SELECT livros.*, generos.genero AS nome_genero
        FROM livros
        INNER JOIN generos ON livros.genero_id = generos.id
        WHERE livros.titulo LIKE ?
           OR livros.autor LIKE ?
           OR generos.genero LIKE ?
        ORDER BY livros.titulo ASC
    ');
    $termo = '%' . $busca . '%';
    $stmt->execute([$termo, $termo, $termo]);
} else {
    $stmt = $pdo->query('
        SELECT livros.*, generos.genero AS nome_genero
        FROM livros
        INNER JOIN generos ON livros.genero_id = generos.id
        ORDER BY livros.titulo ASC
    ');
}

$livros = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros — Sistema de Livros</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container">
        <a href="../index.php" class="logo">Sistema de Livros</a>
        <nav>
            <a href="../index.php">Início</a>
            <a href="../generos/listar.php">Gêneros</a>
            <a href="listar.php">Livros</a>
            <a href="../logout.php">Sair</a>
        </nav>
    </div>
</header>

<main>
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alerta alerta-sucesso"><?= htmlspecialchars($_GET['sucesso']) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
        <div class="alerta alerta-erro"><?= htmlspecialchars($_GET['erro']) ?></div>
    <?php endif; ?>

    <div class="secao-header">
        <h2>Livros</h2>
        <a href="cadastrar.php" class="btn btn-sucesso">+ Novo Livro</a>
    </div>

    <!-- Campo de busca -->
    <form method="GET" action="listar.php" class="form-busca">
        <input type="text" name="busca" placeholder="Buscar por título, autor ou gênero..."
               value="<?= htmlspecialchars($busca) ?>">
        <button type="submit" class="btn btn-primario">Buscar</button>
        <?php if ($busca !== ''): ?>
            <a href="listar.php" class="btn btn-secundario">Limpar</a>
        <?php endif; ?>
    </form>

    <?php if (empty($livros)): ?>
        <p>Nenhum livro encontrado<?= $busca !== '' ? ' para "' . htmlspecialchars($busca) . '"' : '' ?>.</p>
    <?php else: ?>
        <?php if ($busca !== ''): ?>
            <p class="resultado-busca"><?= count($livros) ?> resultado(s) para "<?= htmlspecialchars($busca) ?>"</p>
        <?php endif; ?>
        <table class="tabela-listagem">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Gênero</th>
                    <th>Editora</th>
                    <th>Ano</th>
                    <th>Indicação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livros as $livro): ?>
                <tr>
                    <td><?= $livro['id'] ?></td>
                    <td><?= htmlspecialchars($livro['titulo']) ?></td>
                    <td><?= htmlspecialchars($livro['autor']) ?></td>
                    <td><?= htmlspecialchars($livro['nome_genero']) ?></td>
                    <td><?= htmlspecialchars($livro['editora']) ?></td>
                    <td><?= $livro['ano_publicacao'] ?></td>
                    <td><?= htmlspecialchars($livro['indicacao']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $livro['id'] ?>" class="btn btn-primario">Editar</a>
                        <a href="excluir.php?id=<?= $livro['id'] ?>" class="btn btn-perigo"
                           onclick="return confirm('Tem certeza que deseja excluir este livro?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<footer class="site-footer">
    <div class="container">
        <p>Sistema de Gerenciamento de Livros &copy; <?= date('Y') ?></p>
    </div>
</footer>

</body>
</html>
<?php
session_start();
require_once 'config/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
    } elseif (strlen($senha) < 5) {
        $erro = 'A senha deve ter no mínimo 5 caracteres.';
    } else {
        $pdo = conectar();

        $stmt = $pdo->prepare('SELECT id FROM usuario WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)');
            $stmt->execute([$nome, $email, $hash]);
            header('Location: login.php?cadastro=1');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Usuário — Sistema de Livros</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="pagina-login">
    <div class="login-box">

        <h1>Sistema de Livros</h1>
        <h2>Criar nova conta</h2>

        <?php if ($erro): ?>
            <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="criar_usuario.php">
            <div class="campo">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome"
                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                       required autofocus>
            </div>

            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required>
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha"
                       minlength="5" required>
            </div>

            <button type="submit" class="btn btn-sucesso btn-block">Criar conta</button>
        </form>

        <p style="text-align:center; margin-top: 20px; font-size: 0.9rem; color: #666;">
            Já tem uma conta? <a href="login.php">Fazer login</a>
        </p>

    </div>
</div>

</body>
</html>
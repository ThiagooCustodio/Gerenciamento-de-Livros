<?php
session_start();
require_once 'config/conexao.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Preencha o e-mail e a senha.';
    } else {
        $pdo  = conectar();
        $stmt = $pdo->prepare('SELECT id, nome, senha FROM usuario WHERE email = ?');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: index.php');
            exit;
        } else {
            $erro = 'E-mail ou senha inválidos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistema de Livros</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="pagina-login">

<div class="login-box">
    <h1>Sistema de Livros</h1>
    <h2>Acesse sua conta</h2>

    <?php if ($erro): ?>
        <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['cadastro'])): ?>
        <div class="alerta alerta-sucesso">Usuário criado com sucesso! Faça seu login.</div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="campo">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" 
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   required autofocus>
        </div>

        <div class="campo">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>
        </div>

        <button type="submit" class="btn btn-primario btn-block">Entrar</button>
    </form>
</div>

</body>
</html>
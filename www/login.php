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
    <title>Login</title>
</head>
<body>

<h1>Login</h1>

<?php if (isset($_GET['cadastro'])): ?>
    <p style="color:green">Usuário criado com sucesso! Faça seu login.</p>
<?php endif; ?>

<?php if ($erro): ?>
    <p style="color:red"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<form method="POST" action="login.php">
    <label>E-mail:<br>
        <input type="email" name="email" required autofocus>
    </label><br><br>

    <label>Senha:<br>
        <input type="password" name="senha" required>
    </label><br><br>

    <button type="submit">Entrar</button>
</form>

</body>
</html>
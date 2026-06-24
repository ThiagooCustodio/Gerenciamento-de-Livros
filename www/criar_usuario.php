<?php
session_start();
require_once 'config/conexao.php';

$sucesso = '';
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

            // Redireciona após cadastro — evita reenvio ao recarregar
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
    <title>Criar Usuário</title>
</head>
<body>

<h1>Criar Usuário</h1>

<?php if (isset($_GET['sucesso'])): ?>
    <p style="color:green">Usuário criado com sucesso!</p>
<?php endif; ?>

<?php if ($erro): ?>
    <p style="color:red"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<form method="POST" action="criar_usuario.php">
    <label>Nome:<br>
        <input type="text" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
    </label><br><br>

    <label>E-mail:<br>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </label><br><br>

    <label>Senha:<br>
        <input type="password" name="senha" required>
    </label><br><br>

    <button type="submit">Criar usuário</button>
</form>

<br>
<a href="login.php">Ir para o login</a>

</body>
</html>
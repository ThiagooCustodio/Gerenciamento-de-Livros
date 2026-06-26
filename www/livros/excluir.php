<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: listar.php');
    exit;
}

$pdo = conectar();

// Verifica se o livro existe antes de tentar excluir
$stmt = $pdo->prepare('SELECT * FROM livros WHERE id = ?');
$stmt->execute([$id]);
$livro = $stmt->fetch();

if (!$livro) {
    header('Location: listar.php');
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM livros WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: listar.php?sucesso=Livro excluído com sucesso!');
} catch (PDOException $e) {
    header('Location: listar.php?erro=Não foi possível excluir o livro.');
}
exit;
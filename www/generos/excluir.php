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

$stmt = $pdo->prepare('SELECT * FROM generos WHERE id = ?');
$stmt->execute([$id]);
$genero = $stmt->fetch();

if (!$genero) {
    header('Location: listar.php');
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM generos WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: listar.php?sucesso=Gênero excluído com sucesso!');
} catch (PDOException $e) {
    // Se houver livros vinculados, o banco vai recusar (FK RESTRICT)
    header('Location: listar.php?erro=Este gênero possui livros vinculados e não pode ser excluído.');
}
exit;
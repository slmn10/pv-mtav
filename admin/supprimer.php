<?php
require '../config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || !ctype_digit((string)$_POST['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_POST['id'];

$stmt = $pdo->prepare("SELECT * FROM publications WHERE id = :id");
$stmt->execute(['id' => $id]);
$publication = $stmt->fetch();

if (!$publication) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM publications WHERE id = :id");
$stmt->execute(['id' => $id]);

if (is_file($publication['fichier_nom'])) {
    @unlink($publication['fichier_nom']);
}

header('Location: index.php');
exit;

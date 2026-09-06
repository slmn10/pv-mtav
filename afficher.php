<?php
require 'config.php';

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
    http_response_code(400);
    exit('Identifiant invalide.');
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM publications WHERE id = :id");
$stmt->execute(['id' => $id]);
$publication = $stmt->fetch();

if (!$publication || !file_exists($publication['fichier_nom'])) {
    http_response_code(404);
    exit('Fichier introuvable.');
}

$chemin = $publication['fichier_nom'];
$extension = strtolower(pathinfo($chemin, PATHINFO_EXTENSION));

$mimes = [
    'pdf'  => 'application/pdf',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
];

$mime = $mimes[$extension] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($chemin));

if (isset($_GET['download'])) {
    header('Content-Disposition: attachment; filename="' . basename($chemin) . '"');
} else {
    header('Content-Disposition: inline; filename="' . basename($chemin) . '"');
    header('X-Content-Type-Options: nosniff');
}

readfile($chemin);
exit;

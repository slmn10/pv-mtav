<?php
require '../config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM publications WHERE id = :id");
$stmt->execute(['id' => $id]);
$publication = $stmt->fetch();

if (!$publication) {
    header('Location: index.php');
    exit;
}

$message = '';
$type = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');

    if ($titre === '') {
        $message = "L'intitulé officiel est obligatoire.";
        $type = 'danger';
    } else {
        $nouveau_chemin = null;
        $nouveau_type   = null;
        $erreur_fichier = false;

        if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
                $message = 'Erreur lors du transfert de la nouvelle pièce jointe.';
                $type = 'danger';
                $erreur_fichier = true;
            } else {
                $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
                $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($ext, $allowed_ext, true)) {
                    $message = 'Type de fichier non autorisé (PDF ou images uniquement).';
                    $type = 'danger';
                    $erreur_fichier = true;
                } else {
                    $upload_dir = __DIR__ . '/../uploads/';

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $nom_fichier = 'resultat_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
                    $destination = $upload_dir . $nom_fichier;

                    if (move_uploaded_file($_FILES['fichier']['tmp_name'], $destination)) {
                        $nouveau_chemin = $destination;
                        $nouveau_type   = ($ext === 'pdf') ? 'pdf' : 'image';
                    } else {
                        $message = "Erreur lors de l'enregistrement du fichier.";
                        $type = 'danger';
                        $erreur_fichier = true;
                    }
                }
            }
        }

        if (!$erreur_fichier) {
            $ancien_chemin = $publication['fichier_nom'];

            if ($nouveau_chemin !== null) {
                $stmt = $pdo->prepare(
                    "UPDATE publications
                        SET titre = :titre, fichier_nom = :fichier, fichier_type = :type
                      WHERE id = :id"
                );
                $stmt->execute([
                    'titre'   => $titre,
                    'fichier' => $nouveau_chemin,
                    'type'    => $nouveau_type,
                    'id'      => $id,
                ]);

                if ($ancien_chemin !== $nouveau_chemin && is_file($ancien_chemin)) {
                    @unlink($ancien_chemin);
                }
            } else {
                $stmt = $pdo->prepare("UPDATE publications SET titre = :titre WHERE id = :id");
                $stmt->execute(['titre' => $titre, 'id' => $id]);
            }

            $stmt = $pdo->prepare("SELECT * FROM publications WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $publication = $stmt->fetch();

            $message = 'Le procès-verbal a été mis à jour avec succès.';
            $type = 'success';
        }
    }
}

$page_title = 'Modifier une publication';
$base_url = '../';
require '../partials/header.php';
?>
<main class="max-w-3xl mx-auto px-6 py-10">
    <div class="mb-6 pb-4 border-b border-gray-300">
        <a href="index.php" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-etat-vert hover:underline mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour au registre
        </a>
        <h1 class="font-serif text-2xl font-bold uppercase tracking-wide">Modifier la publication</h1>
        <p class="text-sm text-gray-600 mt-1">
            Référence N° <?= str_pad((int)$publication['id'], 4, '0', STR_PAD_LEFT) ?>/MEFC/DREFC
        </p>
    </div>

    <?php if ($message): ?>
        <div class="mb-6 border-l-4 px-4 py-3 <?= $type === 'danger' ? 'border-red-700 bg-red-50' : 'border-etat-vert bg-green-50' ?>">
            <p class="text-sm <?= $type === 'danger' ? 'text-red-800' : 'text-green-800' ?>"><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <section class="bg-white border border-gray-300">
        <div class="border-b-2 border-etat-vert px-8 py-4">
            <h2 class="font-serif text-base font-bold uppercase tracking-wide">Informations du procès-verbal</h2>
        </div>
        <div class="px-8 py-8">
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="titre" class="block text-[11px] font-semibold uppercase tracking-wider text-gray-600 mb-1.5">Intitulé officiel</label>
                    <input type="text" id="titre" name="titre" required
                           value="<?= htmlspecialchars($publication['titre']) ?>"
                           class="w-full px-3 py-2.5 border border-gray-400 bg-white text-sm focus:outline-none focus:border-etat-vert focus:ring-1 focus:ring-etat-vert">
                </div>

                <div class="border border-gray-300 bg-[#FAF9F6] px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-600 mb-1">Pièce jointe actuelle</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-block px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider border <?= $publication['fichier_type'] === 'pdf' ? 'border-etat-orange text-etat-orange' : 'border-etat-vert text-etat-vert' ?>">
                            <?= $publication['fichier_type'] === 'pdf' ? 'PDF' : 'Image' ?>
                        </span>
                        <span class="font-serif text-sm text-gray-700"><?= htmlspecialchars(basename($publication['fichier_nom'])) ?></span>
                        <a href="../afficher.php?id=<?= (int)$publication['id'] ?>" target="_blank"
                           class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 border border-etat-vert text-etat-vert text-[10px] font-semibold uppercase tracking-wider hover:bg-etat-vert hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Consulter
                        </a>
                    </div>
                </div>

                <div>
                    <label for="fichier" class="block text-[11px] font-semibold uppercase tracking-wider text-gray-600 mb-1.5">Remplacer la pièce jointe</label>
                    <input type="file" id="fichier" name="fichier" accept=".pdf,.jpg,.jpeg,.png,.gif"
                           class="w-full px-3 py-2.5 border border-gray-400 bg-white text-sm file:mr-4 file:py-1.5 file:px-3 file:border-0 file:bg-etat-encre file:text-white file:text-xs file:font-semibold file:uppercase file:tracking-wider focus:outline-none focus:border-etat-vert">
                    <p class="mt-1.5 text-[11px] text-gray-500">
                        Laissez ce champ vide pour conserver la pièce jointe actuelle.
                        L'ancien fichier est supprimé en cas de remplacement.
                    </p>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                            class="px-7 py-3 bg-etat-vert text-white text-xs font-semibold uppercase tracking-wider hover:bg-[#095733] transition-colors">
                        Enregistrer les modifications
                    </button>
                    <a href="index.php"
                       class="px-7 py-3 border border-gray-500 text-gray-700 text-xs font-semibold uppercase tracking-wider text-center hover:bg-gray-800 hover:text-white hover:border-gray-800 transition-colors">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </section>
</main>
<?php require '../partials/footer.php'; ?>

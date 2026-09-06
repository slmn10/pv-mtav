<?php
require '../config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$type = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier'])) {
    $titre = trim($_POST['titre'] ?? '');

    if ($titre === '' || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Veuillez remplir le titre et sélectionner un fichier valide.';
        $type = 'danger';
    } else {
        $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed_ext)) {
            $message = 'Type de fichier non autorisé (PDF ou images uniquement).';
            $type = 'danger';
        } else {
            $type_fichier = ($ext === 'pdf') ? 'pdf' : 'image';
            $upload_dir = __DIR__ . '/../uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $nom_fichier = 'resultat_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
            $chemin = $upload_dir . $nom_fichier;

            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $chemin)) {
                $stmt = $pdo->prepare("INSERT INTO publications (titre, fichier_nom, fichier_type) VALUES (:titre, :fichier, :type)");
                $stmt->execute([
                    'titre'   => $titre,
                    'fichier' => $chemin,
                    'type'    => $type_fichier,
                ]);
                $message = 'Le résultat a été publié avec succès.';
                $type = 'success';
            } else {
                $message = 'Erreur lors de l\'enregistrement du fichier.';
                $type = 'danger';
            }
        }
    }
}

$page_title = 'Nouvelle publication';
$base_url = '../';
require '../partials/header.php';
?>
<main class="max-w-3xl mx-auto px-6 py-10">
    <div class="mb-6 pb-4 border-b border-gray-300">
        <a href="index.php" class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-etat-vert hover:underline mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour au registre
        </a>
        <h1 class="font-serif text-2xl font-bold uppercase tracking-wide">Nouvelle publication</h1>
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
                           value="Procès-verbal de publication des résultats de l'examen du CEP (2025/2026) - COMMUNE 3 - NIAMEY"
                           placeholder="Ex : Procès-verbal de publication des résultats de l'examen du CEP (2023/2024)"
                           class="w-full px-3 py-2.5 border border-gray-400 bg-white text-sm focus:outline-none focus:border-etat-vert focus:ring-1 focus:ring-etat-vert">
                </div>
                <div>
                    <label for="fichier" class="block text-[11px] font-semibold uppercase tracking-wider text-gray-600 mb-1.5">Pièce jointe</label>
                    <input type="file" id="fichier" name="fichier" required accept=".pdf,.jpg,.jpeg,.png,.gif"
                           class="w-full px-3 py-2.5 border border-gray-400 bg-white text-sm file:mr-4 file:py-1.5 file:px-3 file:border-0 file:bg-etat-encre file:text-white file:text-xs file:font-semibold file:uppercase file:tracking-wider focus:outline-none focus:border-etat-vert">
                    <p class="mt-1.5 text-[11px] text-gray-500">Formats admis : PDF, JPG, JPEG, PNG, GIF.</p>
                </div>
                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                            class="px-7 py-3 bg-etat-vert text-white text-xs font-semibold uppercase tracking-wider hover:bg-[#095733] transition-colors">
                        Enregistrer et publier
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

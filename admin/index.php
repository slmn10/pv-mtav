<?php
require '../config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM publications ORDER BY date_publication DESC");
$publications = $stmt->fetchAll();

$page_title = 'Tableau de bord';
$base_url = '../';
require '../partials/header.php';
?>
<main class="max-w-6xl mx-auto px-6 py-10">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6 pb-4 border-b border-gray-300">
        <div>
            <p class="text-[11px] uppercase tracking-[0.25em] text-etat-orange font-semibold mb-1">Administration</p>
            <h1 class="font-serif text-2xl font-bold uppercase tracking-wide">Registre des publications</h1>
            <p class="text-sm text-gray-600 mt-1"><?= count($publications) ?> procès-verbal(aux) enregistré(s)</p>
        </div>
        <div class="flex gap-3">
            <a href="upload.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-etat-vert text-white text-xs font-semibold uppercase tracking-wider hover:bg-[#095733] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nouvelle publication
            </a>
            <a href="logout.php" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-500 text-gray-700 text-xs font-semibold uppercase tracking-wider hover:bg-gray-800 hover:text-white hover:border-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Déconnexion
            </a>
        </div>
    </div>

    <?php if (empty($publications)): ?>
        <div class="bg-white border border-gray-300 px-8 py-12 text-center">
            <p class="font-serif text-lg mb-1">Registre vide</p>
            <p class="text-sm text-gray-600">Aucun procès-verbal n'a encore été publié.</p>
        </div>
    <?php else: ?>
        <div class="bg-white border border-gray-300 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-etat-encre text-white">
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider w-16">N°</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider">Intitulé du procès-verbal</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider w-28">Support</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider w-44">Date de publication</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider w-48">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($publications as $i => $pub): ?>
                        <tr class="border-t border-gray-300 <?= $i % 2 ? 'bg-[#FAF9F6]' : 'bg-white' ?> hover:bg-[#F1F5F2]">
                            <td class="px-4 py-3 font-serif text-gray-600"><?= str_pad((int)$pub['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td class="px-4 py-3 font-serif font-semibold"><?= htmlspecialchars($pub['titre']) ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider border <?= $pub['fichier_type'] === 'pdf' ? 'border-etat-orange text-etat-orange' : 'border-etat-vert text-etat-vert' ?>">
                                    <?= $pub['fichier_type'] === 'pdf' ? 'PDF' : 'Image' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars(date('d/m/Y à H:i', strtotime($pub['date_publication']))) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="../afficher.php?id=<?= (int)$pub['id'] ?>" target="_blank"
                                       title="Consulter le document"
                                       class="group inline-flex items-center justify-center h-8 w-8 border border-gray-300 text-gray-500 hover:bg-etat-vert hover:border-etat-vert hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span class="sr-only">Consulter</span>
                                    </a>

                                    <a href="modifier.php?id=<?= (int)$pub['id'] ?>"
                                       title="Modifier la publication"
                                       class="inline-flex items-center justify-center h-8 w-8 border border-gray-300 text-gray-500 hover:bg-etat-orange hover:border-etat-orange hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span class="sr-only">Modifier</span>
                                    </a>

                                    <a href="../afficher.php?id=<?= (int)$pub['id'] ?>&download=1"
                                       title="Télécharger la pièce jointe"
                                       class="inline-flex items-center justify-center h-8 w-8 border border-gray-300 text-gray-500 hover:bg-etat-encre hover:border-etat-encre hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <span class="sr-only">Télécharger</span>
                                    </a>

                                    <span class="w-px h-6 bg-gray-300 mx-0.5"></span>

                                    <form method="post" action="supprimer.php"
                                          onsubmit="return confirm('Supprimer définitivement le procès-verbal « <?= htmlspecialchars(addslashes($pub['titre']), ENT_QUOTES) ?> » ainsi que sa pièce jointe ?');">
                                        <input type="hidden" name="id" value="<?= (int)$pub['id'] ?>">
                                        <button type="submit" title="Supprimer définitivement"
                                                class="inline-flex items-center justify-center h-8 w-8 border border-gray-300 text-gray-500 hover:bg-red-700 hover:border-red-700 hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span class="sr-only">Supprimer</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
<?php require '../partials/footer.php'; ?>

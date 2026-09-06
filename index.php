<?php
require 'config.php';

$stmt = $pdo->query("SELECT * FROM publications ORDER BY date_publication DESC LIMIT 1");
$publication = $stmt->fetch();

$page_title = "Résultats d'examens";
$base_url = '';
require 'partials/header.php';
?>
<main class="max-w-6xl mx-auto px-6 py-10">
<?php if ($publication): ?>

    <section class="bg-white border border-gray-300 shadow-[0_1px_3px_rgba(0,0,0,0.08)]">
        <div class="border-b-2 border-etat-vert px-8 py-6 text-center">
            <p class="text-[11px] uppercase tracking-[0.25em] text-etat-orange font-semibold mb-3">
                Procès-verbal de publication
            </p>
            <h1 class="font-serif text-2xl md:text-3xl font-bold uppercase leading-tight">
                <?= htmlspecialchars($publication['titre']) ?>
            </h1>
            <div class="mt-4 flex flex-wrap items-center justify-center gap-x-8 gap-y-2 text-xs text-gray-600">
                <span>
                    <span class="uppercase tracking-wider text-gray-500">Date de publication :</span>
                    <strong class="font-serif text-etat-encre"><?= htmlspecialchars(date('d/m/Y', strtotime($publication['date_publication']))) ?></strong>
                </span>
                <span>
                    <span class="uppercase tracking-wider text-gray-500">Référence :</span>
                    <strong class="font-serif text-etat-encre">N° <?= str_pad((int)$publication['id'], 4, '0', STR_PAD_LEFT) ?>/MEFC/DREFC</strong>
                </span>
                <span class="inline-flex items-center gap-1.5 text-etat-vert font-semibold uppercase tracking-wider">
                    <span class="h-1.5 w-1.5 rounded-full bg-etat-vert"></span>
                    Document authentifié
                </span>
            </div>
        </div>

        <div class="px-4 md:px-8 py-8 bg-[#FAF9F6]">
            <?php if ($publication['fichier_type'] === 'pdf'): ?>
                <div class="mx-auto max-w-4xl border border-gray-400 bg-white shadow-md">
                    <embed src="afficher.php?id=<?= (int)$publication['id'] ?>#toolbar=0&amp;navpanes=0&amp;view=FitH"
                           type="application/pdf" class="w-full" height="1000">
                </div>
            <?php else: ?>
                <figure class="mx-auto max-w-4xl border border-gray-400 bg-white p-3 shadow-md">
                    <img src="afficher.php?id=<?= (int)$publication['id'] ?>"
                         class="w-full h-auto" alt="<?= htmlspecialchars($publication['titre']) ?>">
                </figure>
            <?php endif; ?>
        </div>

        <div class="border-t border-gray-300 px-8 py-5">
            <p class="text-center text-[11px] leading-relaxed text-gray-500 max-w-2xl mx-auto">
                Ce document est publié à titre informatif par la Direction Régionale des Enseignements
                et de la Formation Civile. Seul le procès-verbal original signé fait foi.
            </p>
        </div>
    </section>

<?php else: ?>

    <section class="bg-white border border-gray-300 max-w-2xl mx-auto">
        <div class="border-b-2 border-etat-orange px-8 py-5">
            <h1 class="font-serif text-xl font-bold uppercase tracking-wide">Avis</h1>
        </div>
        <div class="px-8 py-10 text-center">
            <p class="font-serif text-lg text-etat-encre mb-2">
                Aucun résultat n'est actuellement publié.
            </p>
            <p class="text-sm text-gray-600 leading-relaxed">
                Les procès-verbaux de publication des résultats seront mis en ligne sur ce portail
                dès leur validation par les autorités compétentes.
            </p>
        </div>
    </section>

<?php endif; ?>
</main>

<?php require 'partials/footer.php'; ?>

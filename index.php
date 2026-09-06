<?php
require 'config.php';

$stmt = $pdo->query("SELECT * FROM publications ORDER BY date_publication DESC LIMIT 1");
$publication = $stmt->fetch();

$page_title = "Résultats d'examens";
$base_url = '';
require 'partials/header.php';
?>
<main class="max-w-6xl mx-auto px-3 sm:px-6 py-6 sm:py-10">
<?php if ($publication): ?>

    <section class="bg-white border border-gray-300 shadow-[0_1px_3px_rgba(0,0,0,0.08)]">
        <div class="border-b-2 border-etat-vert px-4 sm:px-8 py-5 sm:py-6 text-center">
            <p class="text-[11px] uppercase tracking-[0.25em] text-etat-orange font-semibold mb-3">
                Procès-verbal de publication
            </p>
            <h1 class="font-serif text-lg sm:text-2xl md:text-3xl font-bold uppercase leading-tight">
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

        <div class="px-2 sm:px-4 md:px-8 py-5 sm:py-8 bg-[#FAF9F6]">
            <?php if ($publication['fichier_type'] === 'pdf'): ?>
                <div id="pdf-viewer"
                     data-src="afficher.php?id=<?= (int)$publication['id'] ?>"
                     class="mx-auto max-w-4xl space-y-6">
                    <div id="pdf-loader" class="border border-gray-400 bg-white py-20 text-center">
                        <div class="inline-block h-8 w-8 border-2 border-gray-300 border-t-etat-vert rounded-full animate-spin"></div>
                        <p class="mt-4 text-xs uppercase tracking-wider text-gray-500">Chargement du document…</p>
                    </div>
                </div>
                <noscript>
                    <div class="mx-auto max-w-4xl border border-gray-400 bg-white px-6 py-8 text-center">
                        <p class="text-sm text-gray-700">
                            L'affichage du document nécessite JavaScript.
                        </p>
                    </div>
                </noscript>
            <?php else: ?>
                <figure class="mx-auto max-w-4xl border border-gray-400 bg-white p-3 shadow-md">
                    <img src="afficher.php?id=<?= (int)$publication['id'] ?>"
                         class="w-full h-auto" alt="<?= htmlspecialchars($publication['titre']) ?>">
                </figure>
            <?php endif; ?>
        </div>

        <div class="border-t border-gray-300 px-4 sm:px-8 py-5">
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

<?php if ($publication && $publication['fichier_type'] === 'pdf'): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    var viewer = document.getElementById('pdf-viewer');
    if (!viewer || typeof pdfjsLib === 'undefined') {
        return;
    }

    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    var loader = document.getElementById('pdf-loader');
    var document_pdf = null;
    var largeur_rendue = 0;

    function afficherErreur() {
        viewer.innerHTML =
            '<div class="border border-gray-400 bg-white px-6 py-10 text-center">' +
            '<p class="font-serif text-base text-etat-encre mb-1">Document momentanément indisponible</p>' +
            '<p class="text-sm text-gray-600">Veuillez actualiser la page.</p>' +
            '</div>';
    }

    function rendre() {
        var largeur = viewer.clientWidth;
        if (!document_pdf || largeur === 0 || largeur === largeur_rendue) {
            return;
        }
        largeur_rendue = largeur;

        var ratio = window.devicePixelRatio || 1;
        var pages = [];

        for (var numero = 1; numero <= document_pdf.numPages; numero++) {
            pages.push(document_pdf.getPage(numero).then(function (page) {
                var base = page.getViewport({ scale: 1 });
                var echelle = largeur / base.width;
                var viewport = page.getViewport({ scale: echelle * ratio });

                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.className = 'block w-full h-auto';
                canvas.style.width = '100%';

                var cadre = document.createElement('div');
                cadre.className = 'border border-gray-400 bg-white shadow-md overflow-hidden';
                cadre.appendChild(canvas);

                return page.render({
                    canvasContext: canvas.getContext('2d'),
                    viewport: viewport
                }).promise.then(function () {
                    return cadre;
                });
            }));
        }

        Promise.all(pages).then(function (cadres) {
            viewer.innerHTML = '';
            cadres.forEach(function (cadre) {
                viewer.appendChild(cadre);
            });
        }).catch(afficherErreur);
    }

    pdfjsLib.getDocument(viewer.dataset.src).promise.then(function (pdf) {
        document_pdf = pdf;
        if (loader) {
            loader.remove();
        }
        rendre();
    }).catch(afficherErreur);

    var minuteur = null;
    window.addEventListener('resize', function () {
        clearTimeout(minuteur);
        minuteur = setTimeout(rendre, 250);
    });
})();
</script>
<?php endif; ?>

<?php require 'partials/footer.php'; ?>

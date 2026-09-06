<?php
$page_title = $page_title ?? "Publication des résultats d'examens";
$base_url = $base_url ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> — République du Niger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['"Source Serif 4"', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        etat: {
                            orange: '#E05206',
                            vert:   '#0B6E3F',
                            encre:  '#12261F',
                            or:     '#B8860B',
                        },
                    },
                },
            },
        };
    </script>
    <style>
        body { -webkit-font-smoothing: antialiased; }
        .bande-nationale {
            background: linear-gradient(to bottom,
                #E05206 0%, #E05206 33.33%,
                #FFFFFF 33.33%, #FFFFFF 66.66%,
                #0B6E3F 66.66%, #0B6E3F 100%);
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body class="bg-[#F4F2EC] font-sans text-etat-encre">

<div class="bande-nationale h-1.5 w-full"></div>

<header class="bg-white border-b border-gray-300">
    <div class="max-w-6xl mx-auto px-6 py-6">
        <div class="flex items-center gap-5">
            <img src="<?= $base_url ?>assets/armoiries.png" alt="Armoiries de la République du Niger"
                 class="h-20 w-auto shrink-0" onerror="this.style.display='none'">
            <div class="font-serif leading-snug">
                <p class="text-base md:text-lg font-bold uppercase tracking-wide">République du Niger</p>
                <p class="text-xs md:text-sm italic text-etat-vert">Fraternité &ndash; Travail &ndash; Progrès</p>
                <div class="mt-2 space-y-0.5 text-[11px] md:text-xs uppercase tracking-wide text-gray-700">
                    <p>Ministère de l'Enseignement et de la Formation Civile</p>
                    <p>Direction Régionale des Enseignements et de la Formation Civile</p>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="bg-etat-vert text-white no-print">
    <div class="max-w-6xl mx-auto px-6 py-2.5 flex flex-wrap items-center justify-between gap-3">
        <p class="font-serif text-sm md:text-base font-semibold uppercase tracking-[0.12em]">
            Publication officielle des résultats d'examens
        </p>
        <p class="text-[11px] md:text-xs text-white/80">
            <?= htmlspecialchars(date('d/m/Y')) ?>
        </p>
    </div>
</div>

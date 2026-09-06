<?php
require '../config.php';

$error = '';

if (isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Identifiants incorrects.';
    }
}

$page_title = 'Espace administrateur';
$base_url = '../';
require '../partials/header.php';
?>
<main class="max-w-md mx-auto px-6 py-14">
    <section class="bg-white border border-gray-300 shadow-[0_1px_3px_rgba(0,0,0,0.08)]">
        <div class="border-b-2 border-etat-vert px-8 py-5 text-center">
            <p class="text-[11px] uppercase tracking-[0.25em] text-etat-orange font-semibold mb-1">Accès réservé</p>
            <h1 class="font-serif text-xl font-bold uppercase tracking-wide">Espace administrateur</h1>
        </div>
        <div class="px-8 py-8">
            <?php if ($error): ?>
                <div class="mb-6 border-l-4 border-red-700 bg-red-50 px-4 py-3">
                    <p class="text-sm text-red-800"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>
            <form method="post" class="space-y-5">
                <div>
                    <label for="username" class="block text-[11px] font-semibold uppercase tracking-wider text-gray-600 mb-1.5">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required autofocus
                           class="w-full px-3 py-2.5 border border-gray-400 bg-white text-sm focus:outline-none focus:border-etat-vert focus:ring-1 focus:ring-etat-vert">
                </div>
                <div>
                    <label for="password" class="block text-[11px] font-semibold uppercase tracking-wider text-gray-600 mb-1.5">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2.5 border border-gray-400 bg-white text-sm focus:outline-none focus:border-etat-vert focus:ring-1 focus:ring-etat-vert">
                </div>
                <button type="submit"
                        class="w-full bg-etat-vert text-white text-sm font-semibold uppercase tracking-wider py-3 hover:bg-[#095733] transition-colors">
                    Se connecter
                </button>
            </form>
            <p class="mt-6 text-[11px] text-center text-gray-500 leading-relaxed">
                L'accès à cet espace est strictement réservé au personnel habilité.
                Toute tentative d'intrusion est enregistrée.
            </p>
        </div>
    </section>
</main>
<?php require '../partials/footer.php'; ?>

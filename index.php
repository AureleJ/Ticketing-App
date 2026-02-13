<?php 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $clientIdToDelete = (int) $_POST['id'];
    $clientRepo->deleteClient($clientIdToDelete);
    header('Location: clients.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Ticketing</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <main class="login-wrapper">
        <div class="login-card glass-panel animate-item">
            <div>
                <h1 class="text-logo">Ticketing.</h1>
                <p class="text-muted">Connectez-vous à votre espace.</p>
            </div>
            <form action="Pages/dashboard.php" method="POST">
                <div class="input-group mb-md">
                    <i class="ph ph-user"></i>
                    <input type="name" placeholder="Nom d'utilisateur" required>
                </div>
                <div class="input-group mb-lg">
                    <i class="ph ph-lock-key"></i>
                    <input type="password" placeholder="Mot de passe" required>
                </div>
                <button type="submit" class="btn btn-primary w-full flex-center p-md mt-md">
                    Se connecter <i class="ph-bold ph-arrow-right"></i>
                </button>
            </form>
            <div>
                <a href="Pages/password-lost.php" class="text-muted text-sm">Mot de passe oublié ?</a>
            </div>
        </div>
    </main>

    <script src="./assets/js/script.js"></script>
</body>
</html>
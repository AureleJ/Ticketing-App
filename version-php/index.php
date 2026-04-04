<?php
session_start();

if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] > 0) {
    header('Location: Pages/dashboard.php');
    exit;
}

require_once __DIR__ . '/./Service/AuthService.php';

if (isset($_POST['form_type']) && $_POST['form_type'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (AuthService::login($username, $password)) {
        header('Location: Pages/dashboard.php');
        exit;
    } else {
        echo "<h2 style='color:red'>Nom d'utilisateur ou mot de passe incorrect.</h2>";
    }
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
            <form method="POST">
                <input type="hidden" name="form_type" value="login">
                <div class="input-group mb-md">
                    <i class="ph ph-user"></i>
                    <input type="name" name="username" placeholder="Nom d'utilisateur" required>
                </div>
                <div class="input-group mb-lg">
                    <i class="ph ph-lock-key"></i>
                    <input type="password" name="password" placeholder="Mot de passe" required>
                </div>
                <button type="submit" class="btn btn-primary w-full flex-center p-md mt-md">
                    Se connecter <i class="ph-bold ph-arrow-right"></i>
                </button>
            </form>
            <div>
                <a href="Pages/password-lost.php" class="text-muted text-sm">Mot de passe oublié ?</a>
                <span class="text-muted mx-1">|</span>
                <a href="Pages/signup.php" class="text-muted text-sm">Créer un compte</a>
            </div>
        </div>
    </main>

    <script src="./assets/js/script.js"></script>
</body>

</html>
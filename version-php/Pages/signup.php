<?php
session_start();

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../Service/AuthService.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'signup') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        $error = 'Tous les champs sont requis.';
    }else {
        $result = AuthService::register([
            'firstname' => htmlspecialchars($firstname),
            'lastname' => htmlspecialchars($lastname),
            'username' => htmlspecialchars($username),
            'email' => htmlspecialchars($email),
            'password' => $password,
        ]);

        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | Ticketing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <main class="login-wrapper">
        <div class="login-card glass-panel animate-item">
            <div>
                <h1 class="text-logo">Ticketing.</h1>
                <p class="text-muted">Créez votre compte.</p>
            </div>

            <?php if ($error): ?>
                <div class="text-danger text-sm"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="form_type" value="signup">
                <div class="flex gap-md mb-md">
                    <div class="input-group w-full">
                        <i class="ph ph-user"></i>
                        <input type="text" name="firstname" placeholder="Prénom" required
                            value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">
                    </div>
                    <div class="input-group w-full">
                        <i class="ph ph-user"></i>
                        <input type="text" name="lastname" placeholder="Nom" required
                            value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">
                    </div>
                </div>
                <div class="input-group mb-md">
                    <i class="ph ph-at"></i>
                    <input type="text" name="username" placeholder="Nom d'utilisateur" required
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                <div class="input-group mb-md">
                    <i class="ph ph-envelope-simple"></i>
                    <input type="email" name="email" placeholder="Email" required
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="input-group mb-md">
                    <i class="ph ph-lock-key"></i>
                    <input type="password" name="password" placeholder="Mot de passe" required>
                </div>
                <button type="submit" class="btn btn-primary w-full flex-center p-md mt-md">
                    Créer mon compte <i class="ph-bold ph-arrow-right"></i>
                </button>
            </form>
            <div class="text-center">
                <a href="../index.php" class="text-sm text-muted">
                    <i class="ph-bold ph-arrow-left"></i> Déjà un compte ? Se connecter
                </a>
            </div>
        </div>
    </main>

    <script src="../assets/js/script.js"></script>
</body>

</html>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récupération | Ticketing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <main class="login-wrapper">
        <div class="login-card glass-panel pannel animate-item">
            <div class="text-center mb-lg">
                <h1 class="text-xl font-bold mb-md">Mot de passe oublié ?</h1>
                <p class="text-muted text-sm mt-xs">Entrez votre email pour recevoir un lien de réinitialisation.</p>
            </div>

            <form onsubmit="event.preventDefault(); showToast('Lien envoyé sur votre email!');">
                <div class="flex-col gap-md">
                    <div class="input-group">
                        <i class="ph ph-envelope-simple"></i>
                        <input type="email" placeholder="exemple@email.com" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Envoyer le lien</button>
                </div>
            </form>

            <div class="text-center mt-md">
                <a href="../index.php" class="text-sm text-muted hover-text-white transition">
                    <i class="ph-bold ph-arrow-left"></i> Retour à la connexion
                </a>
            </div>
        </div>
    </main>

    <script src="../assets/js/script.js"></script>
</body>

</html>
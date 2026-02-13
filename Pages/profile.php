<?php
require_once __DIR__ . '/../Service/AuthService.php';

$user = AuthService::getAuthUser();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | Ticketing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="app-container">
        <header class="mobile-header">
            <div class="text-logo"><a href="../index.php">Ticketing.</a></div>
            <a href="../index.php" class="btn-icon"><i class="ph ph-sign-out"></i></a>
        </header>

        <nav class="sidebar glass-panel">
            <div class="text-logo">Ticketing.</div>
            <ul class="nav-links">
                <li><a href="dashboard.php"><i class="ph ph-squares-four"></i> Tableau de bord</a></li>
                <li><a href="clients.php"><i class="ph ph-users"></i> Clients</a></li>
                <li><a href="projects.php"><i class="ph ph-folder-notch"></i> Projets</a></li>
                <li><a href="tickets.php"><i class="ph ph-ticket"></i> Tickets</a></li>
                <li><a href="profile.php" class="active"><i class="ph ph-user"></i>Mon Profil</a></li>
                <li><a href="settings.php"><i class="ph ph-gear"></i> Parametres</a></li>
            </ul>
            <div class="sidebar-footer">
                <div class="user-infos">
                    <div class="user-avatar <?= $user->avatar_color ?>"><?= $user->getInitials() ?></div>
                    <div class="user-info">
                        <div class="user-name"><?= $user->getFullName() ?></div>
                        <div class="user-role"><?= $user->role ?></div>
                    </div>
                </div>
                <a href="../index.php" class="btn-icon"><i class="ph ph-sign-out"></i></a>
            </div>
        </nav>

        <main class="main-content">
            <div class="content-scroll">

                <div class="glass-panel pannel animate-item">
                    <div class="flex-row gap-lg flex-center-y">
                        <div class="user-avatar large <?= $user->avatar_color ?>"
                            style="width: 80px; height: 80px; font-size: 2rem;">
                            <?= $user->getInitials() ?>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold"><?= $user->getFullName() ?></h1>
                            <div class="text-muted"><?= $user->role ?> • <?= $user->status ?></div>
                        </div>
                    </div>
                </div>

                <div class="glass-panel pannel mt-lg animate-item delay-1">
                    <h3 class="text-sm font-bold text-muted uppercase mb-md">Informations Personnelles</h3>
                    <div class="flex-col gap-md">
                        <div class="flex gap-md">
                            <div class="input-group w-full">
                                <label class="text-xs text-muted mb-xs block">Prénom</label>
                                <input type="text" value="<?= $user->firstname ?>">
                            </div>
                            <div class="input-group w-full">
                                <label class="text-xs text-muted mb-xs block">Nom</label>
                                <input type="text" value="<?= $user->lastname ?>">
                            </div>
                        </div>
                        <div class="input-group">
                            <label class="text-xs text-muted mb-xs block">Email professionnel</label>
                            <input type="email" value="<?= $user->email ?>">
                        </div>
                        <div class="input-group">
                            <label class="text-xs text-muted mb-xs block">Rôle</label>
                            <input type="text" value="<?= $user->role ?>" disabled style="opacity: 0.5;">
                        </div>
                        <div class="flex justify-end mt-sm">
                            <button class="btn btn-primary"
                                onclick="showToast('Modifications enregistrées')">Enregistrer les modifications</button>
                        </div>
                    </div>
                </div>

                <div class="glass-panel pannel mt-lg animate-item delay-2">
                    <h3 class="text-sm font-bold text-muted uppercase mb-md">Sécurité</h3>
                    <div class="flex-col gap-md">
                        <div class="input-group">
                            <label class="text-xs text-muted mb-xs block">Mot de passe actuel</label>
                            <input type="password" placeholder="***********">
                        </div>
                        <div class="flex gap-md">
                            <div class="input-group w-full">
                                <label class="text-xs text-muted mb-xs block">Nouveau mot de passe</label>
                                <input type="password">
                            </div>
                            <div class="input-group w-full">
                                <label class="text-xs text-muted mb-xs block">Confirmer</label>
                                <input type="password">
                            </div>
                        </div>
                        <div class="flex justify-end mt-sm">
                            <button class="btn btn-secondary text-danger border-danger"
                                onclick="showToast('Mot de passe modifié avec succès')">Mettre à jour le mot de
                                passe</button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="../assets/js/script.js"></script>
</body>

</html>
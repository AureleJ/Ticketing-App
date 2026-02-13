<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres | Ticketing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="app-container">

        <header class="mobile-header">
            <div class="text-logo">
                <a href="dashboard.php">Ticketing.</a>
            </div>

            <a href="../index.php" class="btn-icon"><i class="ph ph-sign-out"></i></a>
        </header>
        
        <nav class="sidebar glass-panel">
            <div class="text-logo">Ticketing.</div>
            <ul class="nav-links">
                <li><a href="dashboard.php"><i class="ph ph-squares-four"></i> Tableau de bord</a></li>
                <li><a href="projects.php"><i class="ph ph-folder-notch"></i> Projets</a></li>
                <li><a href="tickets.php"><i class="ph ph-ticket"></i> Tickets</a></li>
                <li><a href="clients.php"><i class="ph ph-users"></i> Clients</a></li>
                <li><a href="profile.php"><i class="ph ph-user"></i>Mon Profil</a></li>
                <li><a href="settings.php" class="active"><i class="ph ph-gear"></i> Parametres</a></li>
            </ul>
            <div class="sidebar-footer">
                <div class="user-infos">
                    <div class="user-avatar">AJ</div>
                    <div class="user-info">
                        <div class="user-name">Aurele Joblet</div>
                        <div class="user-role">Developpeur</div>
                    </div>
                </div>
                <a href="../index.php" class="btn-icon"><i class="ph ph-sign-out"></i></a>
            </div>
        </nav>

        <main class="main-content">
            <div class="content-scroll">
                <div class="flex-between top-bar glass-panel animate-item">
                    <h1 class="text-xl font-bold">Paramètres</h1>
                    <button class="btn btn-primary" onclick="showToast('Paramètres enregistrés !')">
                        <i class="ph-bold ph-floppy-disk"></i> Enregistrer
                    </button>
                </div>

                <div class="flex-col gap-lg animate-item delay-1">
                    <div class="glass-panel pannel">
                        <div class="flex-center-y gap-sm mb-md pb-sm">
                            <h3 class="text-sm font-bold text-muted uppercase">Interface & Apparence</h3>
                        </div>
                        <div class="flex-col">
                            <div class="setting-item">
                                <div>
                                    <div class="font-bold text-sm">Mode Sombre</div>
                                    <div class="text-xs text-muted">Thème sombre par défaut pour réduire la fatigue visuelle.</div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="setting-item">
                                <div>
                                    <div class="font-bold text-sm">Animations Fluides</div>
                                    <div class="text-xs text-muted">Activer les transitions et les effets de verre.</div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>
<?php
require_once __DIR__ . '/../Repository/ProjectRepository.php';
require_once __DIR__ . '/../Repository/UserRepository.php'; 
require_once __DIR__ . '/../Repository/ClientRepository.php';
require_once __DIR__ . '/../Service/AuthService.php'; 
require_once __DIR__ . '/../Form/ProjectForm.php';

$projectRepo = new ProjectRepository();
$userRepo = new UserRepository();
$clientRepo = new ClientRepository();
$authUser = AuthService::getAuthUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    $form = new ProjectForm($_POST);
    $projectRepo->create($form->formatData());
}

$filters = [
    'tab' => $_GET['tab'] ?? 'all',
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? 'all',
    'sort' => $_GET['sort'] ?? 'recent'
];

$allProjects = $projectRepo->findAll([]); 
$filteredProjects = $projectRepo->findAll($filters);
$allClients = $clientRepo->findAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets | Ticketing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="app-container">
        <div class="popup-overlay hidden" id="project-popup">
            <div class="glass-panel popup-card">
                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Nouveau projet</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('project-popup')"><i class="ph-bold ph-x"></i></button>
                </div>
                <form id="project-form" method="POST">
                    <div class="popup-body">
                        <div class="input-group mb-md"><i class="ph ph-folder"></i><input type="text" name="name" placeholder="Nom du projet" required></div>
                        <div class="input-group mb-md">
                            <i class="ph ph-building"></i>
                            <select name="client_id" required>
                                <option value="" disabled selected>Client</option>
                                <?php foreach($allClients as $c): ?>
                                    <option value="<?= $c->id ?>"><?= $c->company ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group mb-md"><i class="ph ph-article"></i><textarea name="description" placeholder="Description courte..." style="min-height:80px"></textarea></div>
                    </div>
                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary" onclick="togglePopup('project-popup')">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>

        <header class="mobile-header">
            <div class="text-logo"><a href="../index.php">Ticketing.</a></div>
            <a href="../index.php" class="btn-icon"><i class="ph ph-sign-out"></i></a>
        </header>

        <nav class="sidebar glass-panel">
            <div class="text-logo">Ticketing.</div>
            <ul class="nav-links">
                <li><a href="dashboard.php"><i class="ph ph-squares-four"></i> Tableau de bord</a></li>
                <li><a href="projects.php" class="active"><i class="ph ph-folder-notch"></i> Projets</a></li>
                <li><a href="tickets.php"><i class="ph ph-ticket"></i> Tickets</a></li>
                <li><a href="clients.php"><i class="ph ph-users"></i> Clients</a></li>
                <li><a href="profile.php"><i class="ph ph-user"></i>Mon Profil</a></li>
                <li><a href="settings.php"><i class="ph ph-gear"></i> Parametres</a></li>
            </ul>
            <div class="sidebar-footer">
                <div class="user-infos">
                    <div class="user-avatar <?= $authUser->avatarColor ?>"><?= $authUser->getInitials() ?></div>
                    <div class="user-info">
                        <div class="user-name"><?= $authUser->getFullName() ?></div>
                        <div class="user-role"><?= $authUser->role ?></div>
                    </div>
                </div>
                <a href="../index.php" class="btn-icon"><i class="ph ph-sign-out"></i></a>
            </div>
        </nav>

        <main class="main-content">
            <div class="content-scroll">
                <div class="top-bar glass-panel animate-item"><h2>Projets</h2></div>

                <div class="tabs-nav animate-item delay-1">
                    <a href="?tab=all" class="tab-link <?= $filters['tab'] === 'all' ? 'active' : '' ?>">
                        Tous les projets <span class="text-muted text-sm">(<?= count($allProjects) ?>)</span>
                    </a>
                    <a href="?tab=mine" class="tab-link <?= $filters['tab'] === 'mine' ? 'active' : '' ?>">
                        Mes projets <span class="text-muted text-sm">(<?= count(array_filter($allProjects, fn($p) => $p->owner_id === $authUser->id)) ?>)</span>
                    </a>
                    <a href="?tab=finished" class="tab-link <?= $filters['tab'] === 'finished' ? 'active' : '' ?>">
                        Terminés <span class="text-muted text-sm">(<?= count(array_filter($allProjects, fn($p) => $p->status === "Terminé")) ?>)</span>
                    </a>
                </div>

                <form method="GET" class="filters-bar animate-item delay-2">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($filters['tab']) ?>">
                    <div class="input-group search-wrapper flex-1">
                        <i class="ph ph-magnifying-glass"></i>
                        <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($filters['search']) ?>">
                    </div>
                    <div class="flex gap-sm">
                        <div class="input-group" style="width: 160px;">
                            <i class="ph ph-funnel"></i>
                            <select name="status" onchange="this.form.submit()">
                                <option value="all" <?= $filters['status'] === 'all' ? 'selected' : '' ?>>Statut: Tout</option>
                                <option value="En cours" <?= $filters['status'] === 'En cours' ? 'selected' : '' ?>>En cours</option>
                                <option value="En attente" <?= $filters['status'] === 'En attente' ? 'selected' : '' ?>>En attente</option>
                                <option value="Terminé" <?= $filters['status'] === 'Terminé' ? 'selected' : '' ?>>Terminé</option>
                            </select>
                        </div>
                        <div class="input-group" style="width: 160px;">
                            <i class="ph ph-sort-ascending"></i>
                            <select name="sort" onchange="this.form.submit()">
                                <option value="recent" <?= $filters['sort'] === 'recent' ? 'selected' : '' ?>>Récent</option>
                                <option value="priority" <?= $filters['sort'] === 'priority' ? 'selected' : '' ?>>Priorité</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary">Filtrer</button>
                    </div>
                </form>

                <div class="projects-grid animate-item delay-3" id="projects-grid">
                    <?php if (empty($filteredProjects)): ?>
                        <div class="text-muted text-center w-full" style="grid-column: 1/-1; padding: 40px;">Aucun projet trouvé.</div>
                    <?php else: ?>
                        <?php foreach ($filteredProjects as $project):
                            $client = $clientRepo->findById($project->client_id);
                            $owner = $userRepo->findById($project->owner_id);

                            $statusClass = match ($project->status) {
                                'En cours' => 'badge-active', 'En attente' => 'badge-waiting', 'Terminé' => 'badge', default => 'badge-outline'
                            };
                            $progressColor = $project->progress >= 80 ? 'var(--success-color)' : 'var(--accent-color)';
                        ?>
                            <a href="project-details.php?id=<?= $project->id ?>" class="glass-panel project-card">
                                <div>
                                    <div class="card-header"><div class="badge <?= $statusClass ?>"><?= $project->status ?></div></div>
                                    <h3 class="project-title"><?= $project->name ?></h3>
                                    <div class="user-infos mb-xs">
                                        <div class="user-avatar small <?= $client->avatarColor ?>"><?= $client->getInitials() ?></div>
                                        <span class="text-sm text-muted"><?= $client->company ?></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex-between text-xs text-muted mb-xs">
                                        <span>Progression</span>
                                        <span style="color: <?= $progressColor ?>; font-weight: 600;"><?= $project->progress ?>%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: <?= $project->progress ?>%; background: <?= $progressColor ?>;"></div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="user-infos">
                                            <div class="user-avatar small <?= $owner ? $owner->avatarColor : 'gray' ?>"><?= $owner->getInitials() ?></div>
                                            <span class="text-xs text-muted">Resp: <?= $owner->getFullName() ?></span>
                                        </div>
                                        <div class="text-xs text-muted flex-center-y gap-xs">
                                            <i class="ph ph-calendar-blank"></i> <?= date('M y', strtotime($project->date)) ?>
                                        </div>
                                    </div>
                                </div>
                            </a> 
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <button class="btn btn-primary btn-floating" onclick="togglePopup('project-popup')"><i class="ph-bold ph-plus"></i> <span>Nouveau</span></button>
        </main>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>
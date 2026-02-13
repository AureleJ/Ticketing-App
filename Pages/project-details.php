<?php
require_once __DIR__ . '/../Repository/ProjectRepository.php';
require_once __DIR__ . '/../Repository/TicketRepository.php';
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/ClientRepository.php';
require_once __DIR__ . '/../Service/AuthService.php';

$projectRepo = new ProjectRepository();
$ticketRepo = new TicketRepository();
$userRepo = new UserRepository();
$clientRepo = new ClientRepository();

$id = $_GET['id'];

$project = $projectRepo->findById((int)$id);

$client = $clientRepo->findById($project->client_id);
$tickets = $ticketRepo->findProjectTicket($project->id);

$ticketStats = $ticketRepo->countTickets($tickets); 
$totalTickets = $ticketStats[0];

$budgetPercent = ($project->total_h > 0) ? ($project->budget_h / $project->total_h * 100) : 0;
$progressColor = $project->progress >= 80 ? 'var(--success-color)' : ($project->progress < 30 ? 'var(--warning-color)' : 'var(--accent-color)');
$statusClass = match ($project->status) {
    'En cours' => 'badge-active', 'En attente' => 'badge-waiting', 'Terminé' => 'badge', default => 'badge-outline'
};

$authUser = AuthService::getAuthUser();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>#<?= $project->id ?> | <?= htmlspecialchars($project->name) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="app-container">
        <div class="popup-overlay hidden" id="edit-project-popup">
            <div class="glass-panel popup-card">
                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Modifier le projet</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('edit-project-popup')"><i class="ph-bold ph-x"></i></button>
                </div>
                <form id="edit-project-form">
                    <div class="popup-body">
                        <div class="input-group mb-md"><i class="ph ph-folder"></i><input type="text" value="<?= htmlspecialchars($project->name) ?>" required></div>
                        <div class="input-group mb-md"><i class="ph ph-article"></i><textarea rows="3"><?= htmlspecialchars($project->description) ?></textarea></div>
                    </div>
                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary" onclick="togglePopup('edit-project-popup')">Annuler</button>
                        <button type="submit" class="btn btn-primary">Modifier</button>
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
                <div class="flex-between top-bar glass-panel animate-item">
                    <a href="projects.php" class="btn btn-secondary no-border"><i class="ph-bold ph-arrow-left"></i> Retour</a>
                    <div class="flex gap-sm">
                        <button class="btn btn-secondary" onclick="togglePopup('edit-project-popup')"><i class="ph ph-pencil-simple"></i> <span>Modifier</span></button>
                    </div>
                </div>

                <div class="glass-panel pannel flex-col gap-sm animate-item delay-1">
                    <div class="flex-center-y gap-sm mb-xs">
                        <span class="badge <?= $statusClass ?>"><?= $project->status ?></span>
                        <span class="badge text-muted text-sm">Contrat : <?= $project->total_h ?>h</span>
                    </div>
                    <h1 class="text-title text-2xl"><?= $project->name ?></h1>
                    <div class="text-muted flex-center-y gap-sm">
                        <div class="user-avatar small <?= $client->avatarColor ?>"><?= $client->getInitials() ?></div>
                        <?= $client->company ?>
                    </div>
                </div>

                <div class="flex gap-lg animate-item delay-2 responsive-stack">
                    <div class="glass-panel pannel flex-col gap-sm flex-1">
                        <h3 class="text-lg font-bold">À propos du projet</h3>
                        <p class="text-muted" style="line-height: 1.6;">
                            <?= !empty($project->description) ? $project->description : "Aucune description." ?>
                        </p>
                    </div>

                    <div class="glass-panel pannel flex-col justify-between">
                        <h3 class="text-sm font-bold text-muted uppercase mb-md">Équipe Projet</h3>
                        <div class="flex gap-md flex-wrap">
                            <?php if (!empty($project->team)):
                                foreach ($project->team as $memberData):
                                    $memberUser = $userRepo->findById($memberData['user_id']);
                                    if (!$memberUser) continue;
                            ?>
                                <div class="flex-center-y gap-sm">
                                    <div class="user-avatar <?= $memberUser->avatarColor ?>"><?= $memberUser->getInitials() ?></div>
                                    <div class="flex-1">
                                        <div class="font-bold text-sm"><?= $memberUser->getFullName() ?></div>
                                        <div class="text-muted text-xs"><?= $memberData["role"] ?></div>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <span class="text-muted text-sm">Aucun membre assigné.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="flex-col gap-lg animate-item delay-2">
                    <div class="flex-row gap-lg responsive-stack">
                        <div class="glass-panel pannel flex-col item-between w-full">
                            <span class="text-muted text-sm font-bold uppercase">Avancement</span>
                            <div>
                                <div class="flex-between mb-xs"><span class="text-2xl font-bold"><?= $project->progress ?>%</span></div>
                                <div class="progress-track"><div class="progress-fill" style="width: <?= $project->progress ?>%; background: var(--accent-color);"></div></div>
                            </div>
                        </div>

                        <div class="glass-panel pannel flex-col item-between w-full">
                            <span class="text-muted text-sm font-bold uppercase">Budget Heures</span>
                            <div>
                                <div class="flex-between mb-xs">
                                    <span class="text-2xl font-bold text-warning"><?= $project->budget_h ?>h</span>
                                    <span class="text-xs text-muted">sur <?= $project->total_h ?>h</span>
                                </div>
                                <div class="progress-track"><div class="progress-fill" style="width: <?= min($budgetPercent, 100) ?>%; background: var(--warning-color);"></div></div>
                            </div>
                        </div>

                        <div class="glass-panel pannel flex-col item-between w-full">
                            <span class="text-muted text-sm font-bold uppercase">Tickets (<?= $totalTickets ?>)</span>
                            <div class="flex-between gap-lg mb-xs">
                                <div class="flex-col flex-center"><div class="text-xl font-bold"><?= $ticketStats[1] ?></div><div class="text-xs text-muted">Traités</div></div>
                                <div class="flex-col flex-center"><div class="text-xl font-bold text-success"><?= $ticketStats[2] ?></div><div class="text-xs text-muted">En cours</div></div>
                                <div class="flex-col flex-center"><div class="text-xl font-bold text-warning"><?= $ticketStats[3] ?></div><div class="text-xs text-muted">Attente</div></div>
                                <div class="flex-col flex-center"><div class="text-xl font-bold text-danger"><?= $ticketStats[4] ?></div><div class="text-xs text-muted">Urgents</div></div>
                            </div>
                            <div class="progress-track">
                                <?php if($totalTickets > 0): ?>
                                    <div class="progress-fill" style="width: <?= $ticketStats[1]/$totalTickets*100 ?>%; background: var(--text-primary);"></div>
                                    <div class="progress-fill" style="width: <?= $ticketStats[2]/$totalTickets*100 ?>%; background: var(--success-color);"></div>
                                    <div class="progress-fill" style="width: <?= $ticketStats[3]/$totalTickets*100 ?>%; background: var(--warning-color);"></div>
                                    <div class="progress-fill" style="width: <?= $ticketStats[4]/$totalTickets*100 ?>%; background: var(--danger-color);"></div>
                                <?php else: ?>
                                    <div class="progress-fill" style="width: 0;"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="pannel glass-panel animate-item delay-2">
                        <h3 class="text-lg font-bold mb-md">Tickets liés au projet</h3>
                        <?php if (empty($tickets)): ?>
                            <div class="text-muted text-center p-md">Aucun ticket pour ce projet.</div>
                        <?php else: ?>
                            <div class="table-container">
                                <table class="table" id="ticket-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Sujet</th>
                                            <th>Assigné à</th>
                                            <th>Créé</th>
                                            <th>Statut</th>
                                            <th>Type</th>
                                            <th>Priorité</th
                                            ><th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $ticket):
                                            $assigned = $userRepo->findById($ticket->assigned_id);
                                            $statusClass = match($ticket->status) { 'En cours' => 'badge-active', 'Terminé' => 'badge', 'En attente' => 'badge-waiting', 'Non traité' => 'badge-urgent', default => 'badge' };
                                            $priorityClass = match ($ticket->priority) { 'Haute' => 'text-danger', 'Moyenne' => 'text-warning', 'Basse' => '', default => '' };
                                            $typeClass = $ticket->type === 'Facturable' ? 'badge-urgent' : 'badge-active';
                                        ?>
                                            <tr onclick="window.location='ticket-details.php?id=<?= $ticket->id ?>'" class="ticket-row">
                                                <td class="font-mono text-muted">#<?= $ticket->id ?></td>
                                                <td><div class="text-title line-text"><?= $ticket->subject ?></div></td>
                                                <td>
                                                    <div class="flex-center-y gap-sm">
                                                        <div class="user-avatar small <?= $assigned->avatarColor ?>"><?= $assigned->getInitials() ?></div>
                                                        <span class="text-sm line-text"><?= $assigned->getFullName() ?></span>
                                                    </div>
                                                </td>
                                                <td><span class="text-sm line-text"><?= date('d/m/y', strtotime($ticket->date)) ?></span></td>
                                                <td><span class="badge <?= $statusClass ?> line-text"><?= $ticket->status ?></span></td>
                                                <td><span class="badge line-text <?= $typeClass ?>"><?= $ticket->type ?></span></td>
                                                <td class="font-bold text-sm <?= $priorityClass ?>"><?= $ticket->priority ?></td>
                                                <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>
<?php
require_once __DIR__ . '/../Repository/ClientRepository.php';
require_once __DIR__ . '/../Repository/ProjectRepository.php';
require_once __DIR__ . '/../Repository/TicketRepository.php';
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Service/AuthService.php';
require_once __DIR__ . '/../Utils/Debug.php';

$debug = new Debug();

$clientRepo = new ClientRepository();
$projectRepo = new ProjectRepository();
$ticketRepo = new TicketRepository();
$userRepo = new UserRepository();

$id = $_GET['id'] ?? null;

$client = $clientRepo->getClientsById((int)$id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $clientIdToDelete = (int) $_POST['id'];
    $clientRepo->deleteClient($clientIdToDelete);
    header('Location: clients.php');
    exit;
}

$clientProjects = $projectRepo->getClientProjects($client->id);
$clientTickets = $ticketRepo->findClientTicket($client->id);

$authUser = AuthService::getAuthUser();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $client->company ?> | Clients</title>
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
                <li><a href="clients.php" class="active"><i class="ph ph-users"></i> Clients</a></li>
                <li><a href="projects.php"><i class="ph ph-folder-notch"></i> Projets</a></li>
                <li><a href="tickets.php"><i class="ph ph-ticket"></i> Tickets</a></li>
                <li><a href="profile.php"><i class="ph ph-user"></i>Mon Profil</a></li>
                <li><a href="settings.php"><i class="ph ph-gear"></i> Parametres</a></li>
            </ul>
            <div class="sidebar-footer">
                <div class="user-infos">
                    <div class="user-avatar <?= $authUser->avatar_color ?>"><?= $authUser->getInitials() ?></div>
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
                    <a href="clients.php" class="btn btn-secondary no-border"><i class="ph-bold ph-arrow-left"></i> Retour</a>
                    <div class="flex gap-sm">
                        <button class="btn btn-secondary"><i class="ph ph-pencil-simple"></i> <span>Modifier</span></button>
                        <form method="POST" onsubmit="return confirm('Etes vous sur de vouloir supprimer ce client ? Si vous faites cela suprimmera touts les tickets et les projets liés au client !');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $client->id ?>">
                            <button type="submit" class="btn btn-primary btn-danger">
                                <i class="ph ph-trash"></i> <span>Supprimer</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="glass-panel pannel animate-item delay-1">
                    <div class="flex-between flex-wrap gap-lg">
                        <div class="flex-row gap-md flex-center-y">
                            <div class="user-avatar large <?= $client->avatar_color ?>"><?= $client->getInitials() ?></div>
                            <div>
                                <h1 class="text-xl font-bold mb-xs"><?= $client->company ?></h1>
                                <span class="badge badge-active"><?= $client->status ?></span>
                            </div>
                        </div>
                        <div class="flex gap-xl text-center">
                            <div>
                                <div class="text-muted text-xs uppercase font-bold mb-xs">Projets</div>
                                <div class="text-xl font-bold"><?= count($clientProjects) ?></div>
                            </div>
                            <div style="border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">
                                <div class="text-muted text-xs uppercase font-bold mb-xs">Tickets</div>
                                <div class="text-xl font-bold"><?= count($clientTickets) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-panel pannel animate-item delay-2">
                    <h3 class="text-sm font-bold uppercase text-muted mb-md">Projets associés au client</h3>
                    <div class="projects-grid" id="projects-grid">
                        <?php foreach($clientProjects as $project): 
                            $progressColor = $project->progress >= 80 ? 'var(--success-color)' : 'var(--accent-color)';
                            $owner = $userRepo->getClientsById($project->owner_id);
                        ?>
                            <a href="project-details.php?id=<?= $project->id ?>" class="glass-panel project-card">
                                <div>
                                    <div class="card-header">
                                        <div class="badge badge-active"><?= $project->status ?></div>
                                    </div>
                                    <h3 class="project-title"><?= $project->name ?></h3>
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
                                            <div class="user-avatar small <?= $owner->avatar_color ?>"><?= $owner->getInitials() ?></div>
                                            <span class="text-xs text-muted">Resp: <?= $owner->getFullName() ?></span>
                                        </div>
                                        <div class="text-xs text-muted flex-center-y gap-xs">
                                            <i class="ph ph-calendar-blank"></i> <?= date('M y', strtotime($project->date)) ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="glass-panel pannel animate-item delay-3">
                    <div class="flex-between mb-md">
                        <h3 class="text-sm font-bold uppercase text-muted">Tickets associés au client</h3>
                    </div>

                    <div class="table-container">
                        <table class="table" id="ticket-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sujet</th>
                                    <th>Assigné à</th>
                                    <th>Créé</th>
                                    <th>Statut</th>
                                    <th>Type</th>
                                    <th>Priorité</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($clientTickets as $ticket): 
                                    $assigned = $userRepo->getClientsById($ticket->assigned_id);

                                    $statusClass = match($ticket->status) { 'En cours' => 'badge-active', 'Terminé' => 'badge', 'En attente' => 'badge-waiting', 'Non traité' => 'badge-urgent', default => 'badge' };
                                    $priorityClass = match ($ticket->priority) { 'Haute' => 'text-danger', 'Moyenne' => 'text-warning', 'Basse' => '', default => '' };
                                    $typeClass = $ticket->type === 'Facturable' ? 'badge-urgent' : 'badge-active';
                                ?>
                                <tr onclick="window.location='ticket-details.php?id=<?= $ticket->id ?>'" class="ticket-row">
                                    <td class="font-mono text-muted">#<?= $ticket->id ?></td>
                                    <td><div class="text-title line-text"><?= $ticket->subject ?></div></td>
                                    <td>
                                        <div class="flex-center-y gap-sm">
                                            <div class="user-avatar small <?= $assigned->avatar_color ?>"><?= $assigned->getInitials() ?></div>
                                            <span class="text-sm line-text"><?= $assigned->getFullName() ?></span>
                                        </div>
                                    </td>
                                    <td><span class="text-sm line-text"><?= date('d/m/y', strtotime($ticket->date)) ?></span></td>
                                    <td><span class="badge line-text <?= $statusClass ?>"><?= $ticket->status ?></span></td>
                                    <td><span class="badge line-text <?= $typeClass ?>"><?= $ticket->type ?></span></td>
                                    <td class="font-bold text-sm <?= $priorityClass ?>"><?= $ticket->priority ?></td>
                                    <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>
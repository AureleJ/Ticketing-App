<?php
require_once __DIR__ . '/../Repository/TicketRepository.php';
require_once __DIR__ . '/../Repository/ProjectRepository.php';
require_once __DIR__ . '/../Repository/ClientRepository.php';
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Service/AuthService.php';
require_once __DIR__ . '/../Utils/Debug.php';

$debug = new Debug();

$ticketRepo = new TicketRepository();
$projectRepo = new ProjectRepository();
$clientRepo = new ClientRepository();
$userRepo = new UserRepository();

$id = $_GET['id'];

$ticket = $ticketRepo->getTicketsById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $ticketIdToDelete = (int) $_POST['id'];
    $ticketRepo->deleteTicket($ticketIdToDelete);
    header('Location: tickets.php');
    exit;
}

$project = $projectRepo->getProjectsById($ticket->project_id);
$client = $clientRepo->getClientsById($ticket->client_id);
$assigned = $userRepo->getClientsById($ticket->assigned_id);

$authUser = AuthService::getAuthUser();

$statusClass = match($ticket->status) {
    'En cours' => 'badge-active', 'Terminé' => 'badge', 'En attente' => 'badge-waiting', default => 'badge-urgent'
};
$typeClass = $ticket->type === 'Facturable' ? 'badge-urgent' : 'badge-active';
$priorityClass = match ($ticket->priority) { 'Haute' => 'text-danger', 'Moyenne' => 'text-warning', 'Basse' => '', default => '' };

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail #<?= $ticket->id ?> | Ticketing</title>
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
                <li><a href="tickets.php" class="active"><i class="ph ph-ticket"></i> Tickets</a></li>
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
                    <a href="tickets.php" class="btn btn-secondary no-border"><i class="ph-bold ph-arrow-left"></i> Retour</a>
                    <div class="flex gap-sm">
                        <button class="btn btn-secondary"><i class="ph ph-pencil-simple"></i> <span>Modifier</span></button>
                        <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir clôturer ce ticket ?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $ticket->id ?>">
                            <button type="submit" class="btn btn-primary btn-danger">
                                <i class="ph ph-trash"></i> <span>Clôturer</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="glass-panel pannel animate-item delay-1">
                    <div class="flex-col gap-md">
                        <div class="flex gap-sm">
                            <span class="badge <?= $statusClass ?>"><?= $ticket->status ?></span>
                            <span class="badge <?= $typeClass ?>"><?= $ticket->type ?></span>
                            <span class="text-muted text-sm flex-center-y ml-auto">Ouvert le <?= date('d/m/Y H:i', strtotime($ticket->date)) ?></span>
                        </div>
                        <h1 class="text-xl font-bold"><?= $ticket->subject ?></h1>
                    </div>
                </div>

                <div class="ticket-layout animate-item delay-2">
                    <div class="flex-col gap-lg">
                        <div class="glass-panel pannel">
                            <h3 class="text-sm font-bold uppercase mb-md">Description</h3>
                            <p class="text-muted" style="line-height: 1.6;"><?= $ticket->description ?></p>
                        </div>
                        
                        <!-- <div class="chat glass-panel pannel">
                            <div class="discussion-feed">
                                <div class="discussion-item">
                                    <div class="user-avatar small <?= $client->avatar_color ?>"><?= $client->getInitials() ?></div>
                                    <div class="bubble">
                                        <div class="text-xs text-muted mb-xs"><?= $client->contact_name ?> - Hier</div>
                                        BlbaBlbaBlba Blba BlbaBlbaBlba BlbaBlba BlbaBlbaBlba BlbaBlba BlbaBlbaBlba Blba
                                    </div>
                                </div>
                                <div class="discussion-item own">
                                    <div class="bubble">
                                        <div class="text-xs text-muted mb-xs text-right">Moi - À l'instant</div>
                                        BlbaBlbaBlba Blba BlbaBlbaBlba BlbaBlba BlbaBlbaBlba BlbaBlba BlbaBlbaBlba Blba
                                    </div>
                                    <div class="user-avatar small <?= $authUser->avatar_color ?>"><?= $authUser->getInitials() ?></div>
                                </div>
                            </div>
                            <div class="chat-input-area">
                                <div class="chat-input-box">
                                    <textarea class="chat-textarea w-full" placeholder="Écrire une réponse..."></textarea>
                                    <button class="btn btn-primary btn-icon"><i class="ph-bold ph-paper-plane-right"></i></button>
                                </div>
                            </div>
                        </div> -->
                    </div>

                    <div class="flex-col gap-lg">
                        <div class="glass-panel pannel">
                            <h3 class="text-sm font-bold text-muted uppercase mb-md">Détails</h3>
                            <ul class="flex-col gap-md text-sm">
                                <li class="flex-center-y gap-xs">
                                    <span class="text-muted">Client</span>
                                    <div class="flex-center-y gap-xs font-bold">
                                        <div class="user-avatar small <?= $client->avatar_color ?>"><?= $client->getInitials() ?></div> <?= $client->company ?>
                                    </div>
                                </li>
                                <li class="flex-center-y gap-xs">
                                    <span class="text-muted">Projet</span>
                                    <span><?= $project->name ?></span>
                                </li>
                                <li class="flex-center-y gap-xs">
                                    <span class="text-muted">Priorité</span>
                                    <span class="font-bold <?= $priorityClass ?>"><?= $ticket->priority ?></span>
                                </li>
                                <li class="flex-center-y gap-xs">
                                    <span class="text-muted">Assigné à</span>
                                    <div class="flex-center-y gap-xs">
                                        <div class="user-avatar small <?= $assigned->avatar_color ?>"><?= $assigned->getInitials() ?></div> <?= $assigned->getFullName() ?>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="glass-panel pannel">
                            <h3 class="text-sm font-bold text-muted uppercase mb-md">Temps passé</h3>
                            <div class="text-center mb-md">
                                <div class="text-2xl font-bold text-success">2h 30m</div>
                                <span class="text-muted text-sm">Estimé : 4h 00m</span>
                            </div>
                            <div class="progress-track mb-sm">
                                <div class="progress-fill" style="width: 65%; background: var(--success-color);"></div>
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
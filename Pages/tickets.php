<?php
require_once __DIR__ . '/../Repository/TicketRepository.php';
require_once __DIR__ . '/../Repository/ClientRepository.php';
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/ProjectRepository.php';
require_once __DIR__ . '/../Service/AuthService.php';
require_once __DIR__ . '/../Form/TicketForm.php';

$ticketRepo = new TicketRepository();
$clientRepo = new ClientRepository();
$userRepo = new UserRepository();
$projectRepo = new ProjectRepository();
$authUser = AuthService::getAuthUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    $form = new TicketForm($_POST);
    $newTicket = $form->formatData();
    $ticketRepo->create($newTicket);
}

$filters = [
    'tab' => $_GET['tab'] ?? 'all',
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? 'all',
];

$allTickets = $ticketRepo->findAll($filters);
$allClients = $clientRepo->findAll();
$allProjects = $projectRepo->findAll();
$allUsers = $userRepo->findAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets | Ticketing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="app-container">
        <div class="popup-overlay hidden" id="ticket-popup">
            <div class="glass-panel popup-card">
                <div class="popup-header">
                    <h3>Nouveau Ticket</h3><button class="btn-icon" onclick="togglePopup('ticket-popup')"><i
                            class="ph-bold ph-x"></i></button>
                </div>
                <form id="ticket-form" method="POST">
                    <div class="popup-body">
                        <div class="input-group mb-md">
                            <i class="ph ph-text-t"></i>
                            <input type="text" name="subject" placeholder="Sujet du ticket" required autofocus>
                        </div>
                        <div class="input-group mb-md">
                            <i class="ph ph-folder-notch"></i>
                            <select name="project_id" required>
                                <option value="" disabled selected>Projet</option>
                                <?php foreach ($allProjects as $p): ?>
                                    <option value="<?= $p->id ?>"><?= $p->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group mb-md">
                            <i class="ph ph-users"></i>
                            <select name="client_id" required>
                                <option value="" disabled selected>Client</option>
                                <?php foreach ($allClients as $c): ?>
                                    <option value="<?= $c->id ?>"><?= $c->company ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group mb-md">
                            <i class="ph ph-user"></i>
                            <select name="assigned_id">
                                <option value="0" selected>Non assigné</option>
                                <?php foreach ($allUsers as $u): ?>
                                    <option value="<?= $u->id ?>"><?= $u->getFullName() ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex gap-md mb-md">
                            <div class="input-group w-full">
                                <i class="ph ph-warning-circle"></i>
                                <select name="priority">
                                    <option value="Basse">Basse</option>
                                    <option value="Moyenne">Moyenne</option>
                                    <option value="Haute">Haute</option>
                                </select>
                            </div>
                            <div class="input-group w-full">
                                <i class="ph ph-tag"></i>
                                <select name="type">
                                    <option value="Inclus">Inclus</option>
                                    <option value="Facturable">Facturable</option>
                                </select>
                            </div>
                        </div>
                        <div class="input-group textarea-group">
                            <i class="ph ph-article"></i>
                            <textarea name="description" placeholder="Description détaillée..." rows="4"></textarea>
                        </div>
                    </div>
                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary"
                            onclick="togglePopup('ticket-popup')">Annuler</button>
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
                <li><a href="projects.php"><i class="ph ph-folder-notch"></i> Projets</a></li>
                <li><a href="tickets.php" class="active"><i class="ph ph-ticket"></i> Tickets</a></li>
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
            <button class="btn btn-primary btn-floating" onclick="togglePopup('ticket-popup')"><i
                    class="ph-bold ph-plus"></i> <span>Nouveau</span></button>

            <div class="content-scroll">
                <div class="top-bar glass-panel animate-item">
                    <h2>Tickets</h2>
                </div>
                
                <div class="tabs-nav animate-item delay-1">
                    <a href="?tab=all" class="tab-link <?= $filters['tab'] === 'all' ? 'active' : '' ?>">
                        Tous les tickets <span class="text-muted text-sm">(<?= count($allTickets) ?>)</span>
                    </a>
                    <a href="?tab=mine" class="tab-link <?= $filters['tab'] === 'mine' ? 'active' : '' ?>">
                        Mes tickets <span class="text-muted text-sm">(<?= count(array_filter($allTickets, fn($t) => $t->assigned_id === $authUser->id)) ?>)</span>
                    </a>
                    <a href="?tab=finished" class="tab-link <?= $filters['tab'] === 'finished' ? 'active' : '' ?>">
                        Terminés <span class="text-muted text-sm">(<?= count(array_filter($allTickets, fn($t) => $t->status === "Terminé")) ?>)</span>
                    </a>
                </div>

                <div class="filters-bar animate-item delay-2">
                    <form method="GET" class="w-full flex gap-sm">
                        <input type="hidden" name="tab" value="<?= $filters['tab'] ?>">
                        <div class="input-group search-wrapper flex-1">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" name="search" placeholder="Rechercher..."
                                value="<?= htmlspecialchars($filters['search']) ?>">
                        </div>
                        <div class="flex gap-sm">
                            <div class="input-group" style="width: 160px;">
                                <i class="ph ph-funnel"></i>
                                <select name="status" onchange="this.form.submit()">
                                    <option value="all" <?= $filters['status'] === 'all' ? 'selected' : '' ?>>Tout</option>
                                    <option value="En cours" <?= $filters['status'] === 'En cours' ? 'selected' : '' ?>>En
                                        cours</option>
                                    <option value="Non traité" <?= $filters['status'] === 'Non traité' ? 'selected' : '' ?>>Non traité</option>
                                </select>
                            </div>
                            <div class="input-group" style="width: 160px;">
                                <i class="ph ph-sort-ascending"></i>
                                <button type="submit" class="btn btn-secondary w-full">Filtrer</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="pannel glass-panel animate-item delay-2">
                    <div class="table-container">
                        <table class="table" id="ticket-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sujet</th>
                                    <th>Client</th>
                                    <th>Assigné à</th>
                                    <th>Créé</th>
                                    <th>Statut</th>
                                    <th>Type</th>
                                    <th>Priorité</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($allTickets)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted p-md">Aucun ticket trouvé.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($allTickets as $ticket):
                                        $client = $clientRepo->findById($ticket->client_id);
                                        $assigned = $userRepo->findById($ticket->assigned_id);

                                        $statusClass = match($ticket->status) { 'En cours' => 'badge-active', 'Terminé' => 'badge', 'En attente' => 'badge-waiting', 'Non traité' => 'badge-urgent', default => 'badge' };
                                        $priorityClass = match ($ticket->priority) { 'Haute' => 'text-danger', 'Moyenne' => 'text-warning', 'Basse' => '', default => '' };
                                        $typeClass = $ticket->type === 'Facturable' ? 'badge-urgent' : 'badge-active';
                                        ?>
                                        <tr onclick="window.location='ticket-details.php?id=<?= $ticket->id ?>'"
                                            class="ticket-row">
                                            <td class="font-mono text-muted">#<?= $ticket->id ?></td>
                                            <td>
                                                <div class="text-title line-text"><?= $ticket->subject ?></div>
                                            </td>
                                            <td>
                                                <div class="flex-center-y gap-sm">
                                                    <div
                                                        class="user-avatar small <?= $client->avatarColor ?>">
                                                        <?= $client ? $client->getInitials() : '?' ?></div>
                                                    <span
                                                        class="text-sm line-text"><?= $client->company ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex-center-y gap-sm">
                                                    <div
                                                        class="user-avatar small <?= $assigned->avatarColor ?>">
                                                        <?= $assigned->getInitials() ?></div>
                                                    <span
                                                        class="text-sm line-text"><?= $assigned->getFullName() ?></span>
                                                </div>
                                            </td>
                                            <td><span class="text-sm line-text"><?= date('d/m/y', strtotime($ticket->date)) ?></span></td>
                                            <td><span class="badge line-text <?= $statusClass ?>"><?= $ticket->status ?></span>
                                            </td>
                                            <td><span class="badge line-text <?= $typeClass ?>"><?= $ticket->type ?></span></td>
                                            <td class="font-bold text-sm <?= $priorityClass ?>">
                                                <?= $ticket->priority ?>
                                            </td>
                                            <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
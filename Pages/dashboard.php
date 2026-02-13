<?php
require_once __DIR__ . '/../Repository/ProjectRepository.php';
require_once __DIR__ . '/../Repository/TicketRepository.php';
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/ClientRepository.php';
require_once __DIR__ . '/../Service/AuthService.php';
require_once __DIR__ . '/../Form/TicketForm.php';
require_once __DIR__ . '/../Form/ProjectForm.php';
require_once __DIR__ . '/../Form/ClientForm.php';
require_once __DIR__ . '/../Utils/Debug.php';

$debug = new Debug();

$ticketRepo = new TicketRepository();
$projectRepo = new ProjectRepository();
$clientRepo = new ClientRepository();
$userRepo = new UserRepository();

$authUser = AuthService::getAuthUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'ticket') {
        $form = new TicketForm($_POST);
        $ticketRepo->createTicket($form->formatData());
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'project') {
        $form = new ProjectForm($_POST);
        $projectRepo->createProject($form->formatData());
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'client') {
        $form = new ClientForm($_POST);
        $clientRepo->createClient($form->formatData());
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$allProjects = $projectRepo->getAllProjects();
$allClients = $clientRepo->getAllClients();
$allTickets = $ticketRepo->getAllTickets();
$allUsers = $userRepo->getAllUser();

$tickets = array_slice($allTickets, 0, 5);
$projects = array_slice($allProjects, 0, 5);
$clients = array_slice($allClients, 0, 3);

$stats = [
    'inProgress' => count(array_filter($allTickets, fn($t) => $t->status === 'En cours')),
    'waiting' => count(array_filter($allTickets, fn($t) => $t->status === 'En attente')),
    'untraited' => count(array_filter($allTickets, fn($t) => $t->status === 'Non traité')),
    'hours' => 142
];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Ticketing</title>
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

        <div class="popup-overlay hidden" id="project-popup">
            <div class="glass-panel popup-card">
                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Nouveau Projet</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('project-popup')"><i
                            class="ph-bold ph-x"></i></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="form_type" value="project">
                    <div class="popup-body">
                        <div class="input-group mb-md">
                            <i class="ph ph-folder"></i>
                            <input type="text" name="name" placeholder="Nom du projet" required>
                        </div>
                        <div class="input-group mb-md">
                            <i class="ph ph-building"></i>
                            <select name="client_id" required>
                                <option value="" disabled selected>Client</option>
                                <?php foreach ($allClientsList as $c): ?>
                                    <option value="<?= $c->id ?>"><?= $c->company ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group mb-md">
                            <i class="ph ph-article"></i>
                            <textarea name="description" placeholder="Description courte..."
                                style="min-height:80px"></textarea>
                        </div>
                    </div>
                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary"
                            onclick="togglePopup('project-popup')">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="popup-overlay hidden" id="client-popup">
            <div class="glass-panel popup-card">
                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Nouveau Client</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('client-popup')"><i
                            class="ph-bold ph-x"></i></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="form_type" value="client">
                    <div class="popup-body">
                        <div class="input-group mb-md"><i class="ph ph-building"></i><input type="text"
                                name="entreprise" placeholder="Entreprise" required></div>
                        <div class="input-group mb-md"><i class="ph ph-envelope"></i><input type="text" name="mail"
                                placeholder="Mail" required></div>
                        <div class="input-group mb-md"><i class="ph ph-user"></i><input type="text" name="contact"
                                placeholder="Contact" required></div>
                        <div class="input-group mb-md"><i class="ph ph-phone"></i><input type="text" name="phone"
                                placeholder="Téléphone"></div>
                    </div>
                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary"
                            onclick="togglePopup('client-popup')">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>

        <header class="mobile-header">
            <div class="text-logo"><a href="dashboard.php">Ticketing.</a></div>
            <a href="../index.php" class="btn-icon"><i class="ph ph-sign-out"></i></a>
        </header>

        <nav class="sidebar glass-panel">
            <div class="text-logo">Ticketing.</div>
            <ul class="nav-links">
                <li><a href="dashboard.php" class="active"><i class="ph ph-squares-four"></i> Tableau de bord</a></li>
                <li><a href="clients.php"><i class="ph ph-users"></i> Clients</a></li>
                <li><a href="projects.php"><i class="ph ph-folder-notch"></i> Projets</a></li>
                <li><a href="tickets.php"><i class="ph ph-ticket"></i> Tickets</a></li>
                <li><a href="profile.php"><i class="ph ph-user"></i>Mon Profil</a></li>
                <li><a href="settings.php"><i class="ph ph-gear"></i> Parametres</a></li>
            </ul>
            <div class="sidebar-footer">
                <div class="user-infos">
                    <div class="user-avatar <?= $authUser->getAvatarColor() ?>"><?= $authUser->getInitials() ?></div>
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

                <div class="top-bar glass-panel animate-item">
                    <h2>Tableau de bord</h2>
                </div>

                <div class="flex gap-md animate-item delay-1 dashboard-stack">

                    <div class="flex gap-md w-full stats-stack">
                        <div class="flex-col gap-md w-full">
                            <div class="stat-item glass-panel">
                                <i class="ph ph-ticket stat-icon-bg"></i>
                                <div class="stat-label">Tickets en cours</div>
                                <div class="stat-number"><?= $stats['inProgress'] ?></div>
                            </div>
                            <div class="stat-item glass-panel">
                                <i class="ph ph-check-circle stat-icon-bg"></i>
                                <div class="stat-label">À Valider</div>
                                <div class="stat-number text-warning"><?= $stats['waiting'] ?></div>
                            </div>
                        </div>

                        <div class="flex-col gap-md w-full">
                            <div class="stat-item glass-panel">
                                <i class="ph ph-fire stat-icon-bg"></i>
                                <div class="stat-label">Urgents</div>
                                <div class="stat-number text-danger"><?= $stats['untraited'] ?></div>
                            </div>
                            <div class="stat-item glass-panel">
                                <i class="ph ph-clock stat-icon-bg"></i>
                                <div class="stat-label">Temps total</div>
                                <div class="stat-number"><?= $stats['hours'] ?>h</div>
                            </div>
                        </div>
                    </div>

                    <div class="pannel glass-panel w-full">
                        <h3 class="text-lg mb-md">Actions Rapides</h3>
                        <div class="stats-grid">
                            <button class="btn btn-secondary w-full" onclick="togglePopup('ticket-popup')">
                                <i class="ph-bold ph-plus-circle text-info"></i>Nouveau Ticket
                            </button>
                            <button class="btn btn-secondary w-full" onclick="togglePopup('project-popup')">
                                <i class="ph-bold ph-folder-plus text-info"></i>Nouveau Projet
                            </button>
                            <button class="btn btn-secondary w-full" onclick="togglePopup('client-popup')">
                                <i class="ph-bold ph-user-plus text-info"></i>Nouveau Client
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pannel glass-panel animate-item delay-2">
                    <div class="flex-between mb-md">
                        <h3 class="text-lg">Tickets Récents</h3>
                        <a href="tickets.php" class="text-muted text-sm">Voir tout</a>
                    </div>
                    <div class="table-container">
                        <table class="table" id="ticket-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sujet</th>
                                    <th>Client</th>
                                    <th>Assigné à</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Type</th>
                                    <th>Priorité</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket):
                                    $client = $clientRepo->getClientsById($ticket->client_id);
                                    $assigned = $userRepo->getClientsById($ticket->assigned_id);

                                    $statusClass = match ($ticket->status) { 'En cours' => 'badge-active', 'Terminé' => 'badge', 'En attente' => 'badge-waiting', 'Non traité' => 'badge-urgent', default => 'badge'};
                                    $priorityClass = match ($ticket->priority) { 'Haute' => 'text-danger', 'Moyenne' => 'text-warning', 'Basse' => '', default => ''};
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
                                                <div class="user-avatar small <?= $client->getAvatarColor() ?>">
                                                    <?= $client->getInitials() ?>
                                                </div>
                                                <span class="text-sm line-text"><?= $client->company ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-center-y gap-sm">
                                                <div class="user-avatar small <?= $assigned->getAvatarColor() ?>">
                                                    <?= $assigned->getInitials() ?>
                                                </div>
                                                <span class="text-sm line-text"><?= $assigned->getFullName() ?></span>
                                            </div>
                                        </td>
                                        <td><span
                                                class="text-sm line-text"><?= date('d/m', strtotime($ticket->date)) ?></span>
                                        </td>
                                        <td><span class="badge line-text <?= $statusClass ?>"><?= $ticket->status ?></span>
                                        </td>
                                        <td><span class="badge line-text <?= $typeClass ?>"><?= $ticket->type ?></span></td>
                                        <td class="font-bold text-sm <?= $priorityClass ?>"><?= $ticket->priority ?></td>
                                        <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pannel glass-panel animate-item delay-2">
                    <div class="flex-between mb-md">
                        <h3 class="text-lg">Projets Récents</h3>
                        <a href="projects.php" class="text-muted text-sm">Voir tout</a>
                    </div>
                    <div class="table-container">
                        <table class="table" id="project-table">
                            <thead>
                                <tr>
                                    <th>Projet</th>
                                    <th>Client</th>
                                    <th>Progression</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($projects as $project):
                                    $client = $clientRepo->getClientsById($project->client_id);
                                    $progressColor = $project->progress == 100 ? 'var(--success-color)' : 'var(--accent-color)';
                                    ?>
                                    <tr onclick="window.location='project-details.php?id=<?= $project->id ?>'">
                                        <td>
                                            <div class="text-title line-text"><?= $project->name ?></div>
                                        </td>
                                        <td>
                                            <div class="user-infos">
                                                <div class="user-avatar small <?= $client->getAvatarColor() ?>">
                                                    <?= $client->getInitials() ?>
                                                </div>
                                                <div class="user-name line-text">
                                                    <?= $client->company ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mt-auto">
                                                <div class="flex-between text-xs text-muted mb-sm">
                                                    <span><?= $project->progress ?>%</span>
                                                </div>
                                                <div class="progress-track">
                                                    <div class="progress-fill"
                                                        style="width: <?= $project->progress ?>%; background: <?= $progressColor ?>;">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pannel glass-panel animate-item delay-2">
                    <div class="flex-between mb-md">
                        <h3 class="text-lg">Clients Récents</h3>
                        <a href="clients.php" class="text-muted text-sm">Voir tout</a>
                    </div>
                    <div class="clients-grid animate-item delay-2" id="clients-grid">
                        <?php foreach ($clients as $client): ?>
                            <a class="glass-panel client-card" href="clients-details.php?id=<?= $client->id ?>">
                                <div class="client-header">
                                    <div class="user-avatar large <?= $client->getAvatarColor() ?>">
                                        <?= $client->getInitials() ?>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold"><?= $client->company ?></h3>
                                    </div>
                                    <div class="ml-auto"><span class="badge badge-active"><?= $client->status ?></span>
                                    </div>
                                </div>
                                <div class="client-body">
                                    <div class="contact-row"><i class="ph-bold ph-user"></i>
                                        <span><?= $client->contact_name ?></span>
                                    </div>
                                    <div class="contact-row"><i class="ph-bold ph-envelope-simple"></i>
                                        <span><?= $client->email ?></span>
                                    </div>
                                </div>
                                <div class="client-footer">
                                    <div class="client-stat"><span>2</span> Projets</div>
                                    <div class="client-stat"><span>5</span> Tickets</div>
                                    <div class="btn-icon"><i class="ph-bold ph-caret-right"></i></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/script.js"></script>
</body>

</html>
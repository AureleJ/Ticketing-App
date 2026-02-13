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
    $form = new ClientForm($_POST);
    $newClientData = $form->formatData();
    $clientRepo->createClient($newClientData);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$allClients = $clientRepo->getAllClients();
$allProjects = $projectRepo->getAllProjects();
$allTickets = $ticketRepo->getAllTickets();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients | Ticketing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="app-container">
        <div class="popup-overlay hidden" id="client-popup">
            <div class="glass-panel popup-card">
                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Nouveau Client</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('client-popup')"><i class="ph-bold ph-x"></i></button>
                </div>
                <form id="client-form" method="POST">
                    <div class="popup-body">
                        <div class="input-group mb-md"><i class="ph ph-building"></i><input type="text" name="entreprise" placeholder="Entreprise" required></div>
                        <div class="input-group mb-md"><i class="ph ph-envelope"></i><input type="text" name="mail" placeholder="Mail" required></div>
                        <div class="input-group mb-md"><i class="ph ph-user"></i><input type="text" name="contact" placeholder="Contact" required></div>
                        <div class="input-group mb-md"><i class="ph ph-phone"></i><input type="text" name="phone" placeholder="Téléphone"></div>
                    </div>
                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary" onclick="togglePopup('client-popup')">Annuler</button>
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
            <button class="btn btn-primary btn-floating" onclick="togglePopup('client-popup')">
                <i class="ph-bold ph-plus"></i> <span>Nouveau</span>
            </button>

            <div class="content-scroll">
                <div class="top-bar glass-panel animate-item">
                    <div class="flex-center-y gap-md"><h2>Clients</h2></div>
                </div>

                <div class="filters-bar animate-item delay-1">
                    <div class="input-group search-wrapper flex-1">
                        <i class="ph ph-magnifying-glass"></i>
                        <input type="text" placeholder="Rechercher...">
                    </div>
                </div>

                <div class="clients-grid animate-item delay-2" id="clients-grid">
                    <?php foreach ($allClients as $client): 
                        $nbProjects = count(array_filter($allProjects, fn($p) => $p->client_id === $client->id));
                        $nbTickets = count(array_filter($allTickets, fn($t) => $t->client_id === $client->id));
                        
                        $statusClass = $client->status === 'Active' ? 'badge-active' : 'badge-outline';
                    ?>
                        <a class="glass-panel client-card" href="clients-details.php?id=<?= $client->id ?>">
                            <div class="client-header">
                                <div class="user-avatar large <?= $client->avatar_color ?>">
                                    <?= $client->getInitials() ?>
                                </div>
                                <div><h3 class="text-lg font-bold"><?= $client->company ?></h3></div>
                                <div class="ml-auto"><span class="badge <?= $statusClass ?>"><?= $client->status ?></span></div>
                            </div>
                            <div class="client-body">
                                <div class="contact-row"><i class="ph-bold ph-user"></i> <span><?= $client->contact_name ?></span></div>
                                <div class="contact-row"><i class="ph-bold ph-envelope-simple"></i> <span><?= $client->email ?></span></div>
                            </div>
                            <div class="client-footer">
                                <div class="client-stat"><span><?= $nbProjects ?></span> Projets</div>
                                <div class="client-stat"><span><?= $nbTickets ?></span> Tickets</div>
                                <div class="btn-icon"><i class="ph-bold ph-caret-right"></i></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>
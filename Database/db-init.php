<?php
require_once __DIR__ . '/../Service/DatabaseService.php';

$dbService = new DatabaseService();
$pdo = $dbService->connect();

echo "<h1>Initialisation de la Base de Données...</h1>";

try {
    echo "Suppression des anciennes tables...<br>";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS project_members");
    $pdo->exec("DROP TABLE IF EXISTS tickets");
    $pdo->exec("DROP TABLE IF EXISTS projects");
    $pdo->exec("DROP TABLE IF EXISTS clients");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "Création de la structure...<br>";

    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(100) NOT NULL,
        lastname VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        mdp VARCHAR(20) NOT NULL,
        role VARCHAR(50) DEFAULT 'User',
        status VARCHAR(50) DEFAULT 'Active',
        avatar_color VARCHAR(20) DEFAULT 'blue'
    ) ENGINE=InnoDB");

    $pdo->exec("CREATE TABLE clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company VARCHAR(255) NOT NULL,
        contact_name VARCHAR(100),
        email VARCHAR(255),
        phone VARCHAR(50),
        status VARCHAR(50) DEFAULT 'Active',
        avatar_color VARCHAR(20) DEFAULT 'blue'
    ) ENGINE=InnoDB");

    $pdo->exec("CREATE TABLE projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        client_id INT,
        owner_id INT,
        progress INT DEFAULT 0,
        budget_h INT DEFAULT 0,
        total_h INT DEFAULT 0,
        status VARCHAR(50) DEFAULT 'En attente',
        priority VARCHAR(50) DEFAULT 'Moyenne',
        date DATE,
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
        FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB");

    $pdo->exec("CREATE TABLE project_members (
        project_id INT,
        user_id INT,
        role VARCHAR(50),
        PRIMARY KEY (project_id, user_id),
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    $pdo->exec("CREATE TABLE tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject VARCHAR(255) NOT NULL,
        description TEXT,
        project_id INT,
        client_id INT,
        assigned_id INT,
        status VARCHAR(50) DEFAULT 'Non traité',
        priority VARCHAR(50) DEFAULT 'Basse',
        type VARCHAR(50) DEFAULT 'Inclus',
        date DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
        FOREIGN KEY (assigned_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB");

    echo "Insertion des données...<br>";

    $users = [
        [1, "Aurele", "Joblet", "aurele@ticketing.com", "1234", "Admin", "Active", "blue"],
        [2, "Jean", "Dev", "jean@ticketing.com", "1234", "Lead Dev", "Active", "yelBasse"],
        [3, "Sophie", "Graph", "sophie@design.com", "1234", "Designer UI/UX", "Active", "purple"],
        [4, "Paul", "Sysadmin", "paul@ops.com", "1234", "DevOps", "Inactive", "red"],
        [5, "Julie", "Front", "julie@ticketing.com", "1234", "Dev Front", "Active", "cyan"],
        [6, "Thomas", "Mark", "thomas@marketing.com", "1234", "SEO Manager", "Active", "green"],
        [7, "Admin", "Admin", "admin@ticketing.com", "Admin", "Admin", "Active", "red"]
    ];
    $stmt = $pdo->prepare("INSERT INTO users (id, firstname, lastname, email, mdp, role, status, avatar_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($users as $u)
        $stmt->execute($u);

    $clients = [
        [1, "Bio Store", "Patrick Fabre", "direction@biostore.fr", "01 45 89 23 11", "Active", "green"],
        [2, "Tech Consult", "Béatrice Joly", "support@techconsult.io", "09 78 54 12 30", "Active", "blue"],
        [3, "Bakery & Co", "Sophie Martin", "commande@bakery.co", "06 12 34 56 78", "Active", "yelBasse"],
        [4, "Banque S.A.", "M. Bernard (DSI)", "secu@banquesa.fr", "01 00 00 00 00", "Active", "red"],
        [5, "Green Energy", "Lucie Power", "lucie@greenenergy.io", "07 99 88 77 66", "Prospect", "cyan"],
        [6, "Garage Auto 2000", "André Mécano", "garage2000@orange.fr", "02 44 55 66 77", "Inactive", "gray"],
        [7, "Start-up Nation", "Kevin Founder", "ceo@startupnation.com", "06 00 11 22 33", "Active", "purple"]
    ];
    $stmt = $pdo->prepare("INSERT INTO clients (id, company, contact_name, email, phone, status, avatar_color) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($clients as $c)
        $stmt->execute($c);


    $projects = [
        [0, "App Mobile E-commerce v2", "Refonte complète de l'application mobile sous Flutter.", 1, 1, 45, 150, 200, "En cours", "Haute", "2023-10-24"],
        [1, "TMA Site Web", "Tierce Maintenance Applicative mensuelle.", 2, 2, 15, 10, 50, "En cours", "Moyenne", "2023-10-20"],
        [2, "Refonte Identité Visuelle", "Création du nouveau logo et charte.", 3, 3, 100, 42, 40, "Terminé", "Basse", "2023-09-15"],
        [3, "Audit de Sécurité Infra", "Pentest complet de l'infrastructure bancaire.", 4, 1, 10, 5, 80, "En attente", "Haute", "2023-11-01"],
        [4, "Dashboard IoT & Data", "Développement d'un tableau de bord React.", 5, 2, 95, 190, 200, "En cours", "Haute", "2023-08-10"],
        [5, "Campagne SEO Q4", "Optimisation du référencement naturel.", 6, 6, 30, 10, 35, "En cours", "Moyenne", "2023-11-10"],
        [6, "Application SaaS RH", "MVP pour la gestion des congés.", 7, 1, 60, 120, 300, "En cours", "Haute", "2023-09-01"]
    ];
    $stmt = $pdo->prepare("INSERT INTO projects (id, name, description, client_id, owner_id, progress, budget_h, total_h, status, priority, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($projects as $p) {
        if ($p[0] === 0)
            $p[0] = 100;
        $stmt->execute($p);
    }

    $members = [
        [100, 1, "PM"],
        [100, 5, "Dev Mobile"],
        [1, 2, "Back-end"],
        [2, 3, "Lead Design"],
        [3, 4, "Expert Sécu"],
        [3, 2, "Support"],
        [4, 2, "Lead Dev"],
        [4, 5, "Front-end"],
        [5, 6, "SEO Specialist"],
        [6, 1, "Lead"],
        [6, 2, "Back"]
    ];
    $stmt = $pdo->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, ?)");
    foreach ($members as $m)
        $stmt->execute($m);

    $tickets = [
        [1001, "Crash lancement Android 12", "Crash au splash screen.", 100, 1, 5, "En cours", "Haute", "Inclus", "2023-11-20 09:30:00"],
        [1002, "Intégration Stripe v3", "Mise à jour webhooks.", 100, 1, 1, "En cours", "Haute", "Facturable", "2023-11-19 14:00:00"],
        [1003, "Traductions manquantes", "Section commandes en EN.", 100, 1, 5, "Non traité", "Basse", "Inclus", "2023-11-18 10:15:00"],
        [1004, "Faille XSS Form Contact", "Patch urgent requis.", 3, 4, 4, "En cours", "Haute", "Inclus", "2023-11-20 11:45:00"],
        [1005, "Renouvellement SSL", "Wildcard expiré.", 3, 4, 4, "Terminé", "Haute", "Facturable", "2023-11-15 09:00:00"],
        [1006, "Validation Maquette Home", "Attente retour client.", 2, 3, 3, "En attente", "Moyenne", "Inclus", "2023-11-17 16:30:00"],
        [1007, "Export Logo SVG", "Fichiers sources envoyés.", 2, 3, 3, "Terminé", "Basse", "Inclus", "2023-11-14 11:00:00"],
        [1008, "Update WordPress 6.4", "Backup fait.", 1, 2, 2, "En attente", "Basse", "Inclus", "2023-11-18 08:30:00"],
        [1009, "Lenteur Dashboard", "Optimisation SQL.", 1, 2, 2, "En cours", "Moyenne", "Facturable", "2023-11-19 15:45:00"],
        [1010, "Connexion MQTT instable", "Perte de paquets.", 4, 5, 2, "Non traité", "Haute", "Facturable", "2023-11-20 10:00:00"],
        [1011, "Design Graphiques Conso", "Intégration Chart.js.", 4, 5, 5, "En cours", "Moyenne", "Inclus", "2023-11-16 14:00:00"],
        [1012, "Bug calcul congés", "Années bissextiles HS.", 6, 7, 1, "Non traité", "Haute", "Inclus", "2023-11-20 08:00:00"],
        [1013, "Setup CI/CD Pipeline", "GitHub Actions.", 6, 7, 4, "Terminé", "Moyenne", "Facturable", "2023-11-10 10:00:00"]
    ];
    $stmt = $pdo->prepare("INSERT INTO tickets (id, subject, description, project_id, client_id, assigned_id, status, priority, type, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($tickets as $t)
        $stmt->execute($t);

    echo "<h2 style='color:green'>✅ Succès ! La base de données est prête.</h2>";
    echo "<a href='../index.php'>Aller à l'accueil</a>";

} catch (PDOException $e) {
    echo "<h2 style='color:red'>❌ Erreur SQL :</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
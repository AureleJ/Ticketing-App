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

    $pdo->exec("CREATE TABLE clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company VARCHAR(255) NOT NULL,
        contact_name VARCHAR(100),
        email VARCHAR(255),
        phone VARCHAR(50),
        status VARCHAR(50) DEFAULT 'Active',
        avatar_color VARCHAR(20) DEFAULT 'blue'
    ) ENGINE=InnoDB");

    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(50) DEFAULT 'Member',
        firstname VARCHAR(100) NOT NULL,
        lastname VARCHAR(100) NOT NULL,
        username VARCHAR(255) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'User',
        status VARCHAR(50) DEFAULT 'Active',
        avatar_color VARCHAR(20) DEFAULT 'blue',
        client_id INT DEFAULT NULL,
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
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

    // Start transaction for data insertion
    $pdo->beginTransaction();

    // Insert clients first (users reference them)
    $clients = [
        ["Bio Store", "Patrick Fabre", "biostore@ticketing.com", "01 45 89 23 11", "Active", "green"],
        ["Tech Consult", "Béatrice Joly", "techconsult@ticketing.com", "09 78 54 12 30", "Active", "blue"],
        ["Bakery & Co", "Jeanne Martin", "bakery@ticketing.com", "06 12 34 56 78", "Active", "yellow"],
        ["Banque S.A.", "Sean Bernard", "banque@ticketing.com", "01 00 00 00 00", "Active", "red"],
        ["Green Energy", "Lucie Power", "greenenergy@ticketing.com", "07 99 88 77 66", "Prospect", "cyan"],
        ["Garage Auto 2000", "André Mécano", "garage@ticketing.com", "02 44 55 66 77", "Inactive", "gray"]
    ];
    $stmt = $pdo->prepare("INSERT INTO clients (company, contact_name, email, phone, status, avatar_color) VALUES (?, ?, ?, ?, ?, ?)");
    $clientCount = 0;
    $clientIds = [];
    foreach ($clients as $c) {
        $stmt->execute($c);
        $clientIds[] = $pdo->lastInsertId();
        $clientCount++;
    }
    echo "$clientCount clients insérés<br>";

    // Insert users (client users linked to their company via client_id)
    $users = [
        ["Admin", "Aurele", "Joblet", "aurele", "aurele@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Admin", "Active", "blue", null],
        ["Member", "Jean", "Dev", "jean", "jean@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Lead Dev", "Active", "yellow", null],
        ["Member", "Sophie", "Graph", "sophie", "sophie@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Designer UI/UX", "Active", "purple", null],
        ["Member", "Paul", "Sysadmin", "paul", "paul@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "DevOps", "Inactive", "red", null],
        ["Member", "Julie", "Front", "julie", "julie@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Dev Front", "Active", "cyan", null],
        ["Member", "Thomas", "Mark", "thomas", "thomas@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "SEO Manager", "Active", "green", null],
        ["Admin", "Admin", "admin", "admin", "admin@ticketing.com", password_hash("Admin", PASSWORD_DEFAULT), "Admin", "Active", "red", null],
        ["Client", "Patrick", "Fabre", "patrick", "patrick@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Client", "Active", "blue", $clientIds[0]],
        ["Client", "Béatrice", "Joly", "beatrice", "beatrice@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Client", "Active", "blue", $clientIds[1]],
        ["Client", "Jeanne", "Martin", "jeanne", "jeanne@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Client", "Active", "blue", $clientIds[2]],
        ["Client", "Sean", "Bernard", "sean", "sean@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Client", "Active", "blue", $clientIds[3]],
        ["Client", "Lucie", "Power", "lucie", "lucie@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Client", "Active", "blue", $clientIds[4]],
        ["Client", "André", "Mécano", "andre", "andre@ticketing.com", password_hash("1234", PASSWORD_DEFAULT), "Client", "Active", "blue", $clientIds[5]]
    ];
    $stmt = $pdo->prepare("INSERT INTO users (type, firstname, lastname, username, email, password_hash, role, status, avatar_color, client_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $userCount = 0;
    foreach ($users as $u) {
        $stmt->execute($u);
        $userCount++;
    }
    echo "$userCount utilisateurs insérés<br>";

    // Insert projects
    $projects = [
        ["App Mobile E-commerce v2", "Refonte complète de l'application mobile sous Flutter.", 1, 1, 45, 150, 200, "En cours", "Haute", "2023-10-24"],
        ["TMA Site Web", "Tierce Maintenance Applicative mensuelle.", 2, 2, 15, 10, 50, "En cours", "Moyenne", "2023-10-20"],
        ["Refonte Identité Visuelle", "Création du nouveau logo et charte.", 3, 3, 100, 42, 40, "Terminé", "Basse", "2023-09-15"],
        ["Audit de Sécurité Infra", "Pentest complet de l'infrastructure bancaire.", 4, 1, 10, 5, 80, "En attente", "Haute", "2023-11-01"],
        ["Dashboard IoT & Data", "Développement d'un tableau de bord React.", 5, 2, 95, 190, 200, "En cours", "Haute", "2023-08-10"],
        ["Campagne SEO Q4", "Optimisation du référencement naturel.", 6, 6, 30, 10, 35, "En cours", "Moyenne", "2023-11-10"],
        ["Application SaaS RH", "MVP pour la gestion des congés.", 1, 1, 60, 120, 300, "En cours", "Haute", "2023-09-01"]
    ];
    $stmt = $pdo->prepare("INSERT INTO projects (name, description, client_id, owner_id, progress, budget_h, total_h, status, priority, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $projectCount = 0;
    $projectIds = [];
    foreach ($projects as $p) {
        $stmt->execute($p);
        $projectIds[] = $pdo->lastInsertId();
        $projectCount++;
    }
    echo "$projectCount projets insérés<br>";

    // Insert project members (using captured project IDs)
    $members = [
        [$projectIds[0], 1, "PM"],
        [$projectIds[0], 5, "Dev Mobile"],
        [$projectIds[1], 2, "Back-end"],
        [$projectIds[2], 3, "Lead Design"],
        [$projectIds[3], 4, "Expert Sécu"],
        [$projectIds[3], 2, "Support"],
        [$projectIds[4], 2, "Lead Dev"],
        [$projectIds[4], 5, "Front-end"],
        [$projectIds[5], 6, "SEO Specialist"],
        [$projectIds[6], 1, "Lead"],
        [$projectIds[6], 2, "Back"]
    ];
    $stmt = $pdo->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, ?)");
    $memberCount = 0;
    foreach ($members as $m) {
        $stmt->execute($m);
        $memberCount++;
    }
    echo "$memberCount associations membres-projets insérées<br>";

    // Insert tickets (using captured project IDs)
    $tickets = [
        ["Crash lancement Android 12", "Crash au splash screen.", $projectIds[0], 1, 5, "En cours", "Haute", "Inclus", "2023-11-20 09:30:00"],
        ["Intégration Stripe v3", "Mise à jour webhooks.", $projectIds[0], 1, 1, "En cours", "Haute", "Facturable", "2023-11-19 14:00:00"],
        ["Traductions manquantes", "Section commandes en EN.", $projectIds[0], 1, 5, "Non traité", "Basse", "Inclus", "2023-11-18 10:15:00"],
        ["Faille XSS Form Contact", "Patch urgent requis.", $projectIds[2], 4, 4, "En cours", "Haute", "Inclus", "2023-11-20 11:45:00"],
        ["Renouvellement SSL", "Wildcard expiré.", $projectIds[2], 4, 4, "Terminé", "Haute", "Facturable", "2023-11-15 09:00:00"],
        ["Validation Maquette Home", "Attente retour client.", $projectIds[1], 3, 3, "En attente", "Moyenne", "Inclus", "2023-11-17 16:30:00"],
        ["Export Logo SVG", "Fichiers sources envoyés.", $projectIds[1], 3, 3, "Terminé", "Basse", "Inclus", "2023-11-14 11:00:00"],
        ["Update WordPress 6.4", "Backup fait.", $projectIds[0], 2, 2, "En attente", "Basse", "Inclus", "2023-11-18 08:30:00"],
        ["Lenteur Dashboard", "Optimisation SQL.", $projectIds[0], 2, 2, "En cours", "Moyenne", "Facturable", "2023-11-19 15:45:00"],
        ["Connexion MQTT instable", "Perte de paquets.", $projectIds[4], 5, 2, "Non traité", "Haute", "Facturable", "2023-11-20 10:00:00"],
        ["Design Graphiques Conso", "Intégration Chart.js.", $projectIds[4], 5, 5, "En cours", "Moyenne", "Inclus", "2023-11-16 14:00:00"],
        ["Bug calcul congés", "Années bissextiles HS.", $projectIds[6], 1, 1, "Non traité", "Haute", "Inclus", "2023-11-20 08:00:00"],
        ["Setup CI/CD Pipeline", "GitHub Actions.", $projectIds[6], 1, 4, "Terminé", "Moyenne", "Facturable", "2023-11-10 10:00:00"]
    ];
    $stmt = $pdo->prepare("INSERT INTO tickets (subject, description, project_id, client_id, assigned_id, status, priority, type, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ticketCount = 0;
    $ticketIds = [];
    foreach ($tickets as $t) {
        $stmt->execute($t);
        $ticketIds[] = $pdo->lastInsertId();
        $ticketCount++;
    }
    echo "$ticketCount tickets insérés<br>";

    // Commit transaction
    $pdo->commit();

    echo "<h2 style='color:green'>✅ Succès ! La base de données est prête.</h2>";
    echo "<p style='color:green'><strong>Résumé :</strong><br>
            - $userCount utilisateurs<br>
            - $clientCount clients<br>
            - $projectCount projets<br>
            - $memberCount associations membres-projets<br>
            - $ticketCount tickets<br>
            - $ticketMemberCount associations membres-tickets</p>";
    echo "<a href='../index.php'>Aller à l'accueil</a>";

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<h2 style='color:red'>❌ Erreur SQL :</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<h2 style='color:red'>❌ Erreur :</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
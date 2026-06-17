<?php
session_start();
require_once "connexion.php";

// AJOUT CLIENT
if (isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $id_user = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    if ($nom !== '' && $prenom !== '' && $telephone !== '' && $mail !== '' && $adresse !== '' && $id_user > 0) {
        $stmt = $pdo->prepare("INSERT INTO clients(nom, prenom, telephone, mail, adresse, id_user) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $telephone, $mail, $adresse, $id_user]);
    }

    header("Location: clients.php");
    exit();
}

// SUPPRESSION CLIENT
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM clients WHERE id_client = ?");
        $stmt->execute([$id]);
    }

    header("Location: clients.php");
    exit();
}

// LISTE CLIENTS
$clients = $pdo->query("SELECT id_client, nom, prenom, telephone, mail, adresse FROM clients ORDER BY id_client DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestion Clients</title>
<link rel="stylesheet" href="style.css?v=2">
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <h2>Espace <br><span>Administrateur</span></h2>

     <ul>
        <li class="active"><a href="dashboardadmin.php">🏠 Dashboard</a></li>
        <li><a href="employe.php">👨‍💼 Employés</a></li>
        <li><a href="clients.php">👥 Clients</a></li>
        <li><a href="produits.php">📦 Produits</a></li>
        <li><a href="ventes.php">🧾 Ventes</a></li>
        <li><a href="factures.php">🧾 Factures</a></li>
        <li><a href="statistiques.php">📊 Statistiques</a></li>
        <li class="logout"><a href="logout.php">🚪 Déconnexion</a></li>
    </ul>

</div>

<!-- MAIN -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <button id="hamburger" class="hamburger" aria-label="Menu">☰</button>

        <div class="user-menu">
            <a href="profil.php" class="profile-icon" title="Mon Profil">
                👤 <?= htmlspecialchars($_SESSION['mail'] ?? 'Utilisateur'); ?>
            </a>
        </div>

    </div>
    <!-- TITRE -->
    <div class="welcome">
        <h1>Gestion des Clients 👥</h1>
    </div>

 <!-- FORMULAIRE AJOUT -->
<div class="card">

    <h2>➕ Ajouter un client</h2>

    <form method="POST" class="form-grid">

        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>

        <input type="text" name="telephone" placeholder="Téléphone" required>
        <input type="email" name="mail" placeholder="Email" required>

        <input type="text" name="adresse" placeholder="Adresse" required>

        <button type="submit" name="ajouter">Ajouter client</button>

    </form>

</div>

    <!-- TABLE CLIENTS -->
<table style="width:100%; border-collapse:collapse; background:white; margin-top:20px;">

    <tr style="background:#3498db; color:white;">
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Téléphone</th>
        <th>Mail</th>
        <th>Adresse</th>
        <th>Actions</th>
    </tr>

    <?php if (empty($clients)): ?>
        <tr>
            <td colspan="7" style="text-align:center; padding:20px;">
                Aucun client enregistré.
            </td>
        </tr>
    <?php else: ?>

        <?php foreach ($clients as $c): ?>
        <tr style="text-align:center; border-bottom:1px solid #eee;">

            <td><?= htmlspecialchars($c['id_client']) ?></td>
            <td><?= htmlspecialchars($c['nom']) ?></td>
            <td><?= htmlspecialchars($c['prenom']) ?></td>
            <td><?= htmlspecialchars($c['telephone']) ?></td>
            <td><?= htmlspecialchars($c['mail']) ?></td>
            <td><?= htmlspecialchars($c['adresse']) ?></td>

          <td style="white-space:nowrap;">

    <a href="edit_client.php?id=<?= $c['id_client'] ?>" 
       style="color:#2980b9; font-weight:bold; margin-right:12px; text-decoration:none;">
        Modifier
    </a>

    <a href="clients.php?delete=<?= $c['id_client'] ?>" 
       style="color:#e74c3c; font-weight:bold; text-decoration:none;"
       onclick="return confirm('Supprimer ce client ?')">
        Supprimer
    </a>

</td>
        </tr>
        <?php endforeach; ?>

    <?php endif; ?>

</table>
</div>

<div class="menu-overlay" onclick="document.body.classList.remove('menu-open')"></div>
<script src="assets/js/app.js?v=1"></script>
</body>
</html>
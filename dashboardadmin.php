<?php
session_start();
require_once "connexion.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* STATISTIQUES */
$nbClients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$nbProduits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$nbVentes   = $pdo->query("SELECT COUNT(*) FROM vente")->fetchColumn();
$caTotal    = $pdo->query("SELECT SUM(total) FROM vente")->fetchColumn();
if ($caTotal === null) $caTotal = 0;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Administrateur</title>
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
                👤 <span class="user"><?= htmlspecialchars($_SESSION['mail'] ?? '') ?></span>
            </a>
        </div>

    </div>

    <!-- BIENVENUE -->
    <div class="welcome">
        <h1>Bienvenue dans votre espace administrateur 👋</h1>
    </div>

    <!-- STATISTIQUES -->
    <div class="cards">

        <div class="card">
            <h3>👥 Clients</h3>
            <p><?= $nbClients ?></p>
        </div>

        <div class="card">
            <h3>📦 Produits</h3>
            <p><?= $nbProduits ?></p>
        </div>

        <div class="card">
            <h3>🧾 Ventes</h3>
            <p><?= $nbVentes ?></p>
        </div>

        <div class="card">
            <h3>💰 Chiffre d'affaires</h3>
            <p><?= number_format($caTotal, 2, ',', ' ') ?> DH</p>
        </div>

    </div>

    <!-- ACTIONS RAPIDES -->
    <div class="quick-actions">

        <h2>⚡ Actions Rapides</h2>

        <div class="action-grid">

            <a href="employe.php" class="action-card">
                <h3>👨‍💼 Employés</h3>
                <p>Gérer les employés</p>
            </a>

            <a href="clients.php" class="action-card">
                <h3>👥 Clients</h3>
                <p>Ajouter et gérer les clients</p>
            </a>

            <a href="produits.php" class="action-card">
                <h3>📦 Produits</h3>
                <p>Gérer le catalogue</p>
            </a>

            <a href="ventes.php" class="action-card">
                <h3>🧾 Ventes</h3>
                <p>Consulter les ventes</p>
            </a>

            <a href="factures.php" class="action-card">
                <h3>🧾 Factures</h3>
                <p>Gérer les factures</p>
            </a>

        </div>

    </div>

</div>

<div class="menu-overlay" onclick="document.body.classList.remove('menu-open')"></div>
<script src="assets/js/app.js?v=1"></script>

</body>
</html>
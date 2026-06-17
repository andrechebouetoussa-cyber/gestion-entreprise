<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employe') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Employé</title>
<link rel="stylesheet" href="style.css?v=2">
</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <h2>Espace <br><span>Employé</span></h2>

    <ul>
        <li class="active"><a href="dashboardemploye.php">🏠 Dashboard</a></li>
        <li><a href="clients.php">👥 Clients</a></li>
        <li><a href="produits.php">📦 Produits</a></li>
        <li><a href="ventes.php">🧾 Ventes</a></li>
        <li><a href="nouvelle_vente.php">💰 Nouvelle Vente</a></li>
        <li><a href="factures.php">🧾 Factures</a></li>
        <li class="logout"><a href="logout.php">🚪 Déconnexion</a></li>
    </ul>

</div>

<!-- MAIN -->

<div class="main">

    <!-- TOPBAR -->

    <div class="topbar">
        <button id="hamburger" class="hamburger" aria-label="Menu">☰</button>
        <div class="user-menu">
            <span class="user">👤 <?= $_SESSION['email']; ?></span>
            <a href="profil.php" class="profile-icon" title="Mon Profil">👤</a>
        </div>
    </div>

    <!-- BIENVENUE -->

    <div class="welcome">
        <h1>Bienvenue Employé 👋</h1>
        <p>Gérez vos ventes et clients facilement</p>
    </div>

    <!-- STATISTIQUES -->

    <div class="cards">

        <div class="card">
            <h3>👥 Clients</h3>
            <p>0</p>
        </div>

        <div class="card">
            <h3>🧾 Ventes</h3>
            <p>0</p>
        </div>

        <div class="card">
            <h3>💰 CA généré</h3>
            <p>0 DH</p>
        </div>

        <div class="card">
            <h3>📦 Produits disponibles</h3>
            <p>0</p>
        </div>

    </div>

    <!-- ACTIONS RAPIDES -->

    <div class="quick-actions">

        <h2>⚡ Actions Rapides</h2>

        <div class="action-grid">

            <a href="clients.php" class="action-card">
                <h3>👥 Clients</h3>
                <p>Gérer les clients</p>
            </a>

            <a href="produits.php" class="action-card">
                <h3>📦 Produits</h3>
                <p>Consulter les produits</p>
            </a>

            <a href="nouvelle_vente.php" class="action-card">
                <h3>💰 Nouvelle Vente</h3>
                <p>Créer une vente</p>
            </a>

            <a href="ventes.php" class="action-card">
                <h3>🧾 Historique</h3>
                <p>Voir les ventes</p>
            </a>

        </div>

    </div>

</div>

<div class="menu-overlay" onclick="document.body.classList.remove('menu-open')"></div>
<script src="assets/js/app.js?v=1"></script>

</body>
</html>
<?php
session_start();
require_once "connexion.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Bloquer l'accès à l'employé
if ($_SESSION['role'] === 'employe') {
    header("Location: dashboard.php");
    exit();
}

/* STATISTIQUES */
$clients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$produits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$ventes = $pdo->query("SELECT COUNT(*) FROM vente")->fetchColumn();
$ca = $pdo->query("SELECT SUM(total) FROM vente")->fetchColumn();

$ca = $ca ?? 0;

/* produit le plus vendu */
$topProduit = $pdo->query("
    SELECT p.nom_produit, SUM(lv.quantite) AS total_vendu
    FROM ligne_vente lv
    JOIN produits p ON p.id_prod = lv.id_prod
    GROUP BY lv.id_prod
    ORDER BY total_vendu DESC
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Statistiques</title>
<link rel="stylesheet" href="style.css?v=2">
</head>

<body>

<div class="sidebar">

    <h2>Espace <br><span>Administrateur</span></h2>

     <ul>
        <li class="active"><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="employe.php">👨‍💼 Employés</a></li>
        <li><a href="clients.php">👥 Clients</a></li>
        <li><a href="produits.php">📦 Produits</a></li>
        <li><a href="ventes.php">🧾 Ventes</a></li>
        <li><a href="factures.php">🧾 Factures</a></li>
        <li><a href="statistiques.php">📊 Statistiques</a></li>
        <li class="logout"><a href="logout.php">🚪 Déconnexion</a></li>
    </ul>

</div>

<div class="main">

<div class="topbar">
    <button id="hamburger" class="hamburger" aria-label="Menu">☰</button>
    <div class="user-menu">👤 <?= $_SESSION['email'] ?? '' ?></div>
</div>

<h1>📊 Statistiques</h1>

<div class="cards">

    <div class="card">
        <h3>👥 Clients</h3>
        <p><?= $clients ?></p>
    </div>

    <div class="card">
        <h3>📦 Produits</h3>
        <p><?= $produits ?></p>
    </div>

    <div class="card">
        <h3>🧾 Ventes</h3>
        <p><?= $ventes ?></p>
    </div>

    <div class="card">
        <h3>💰 Chiffre d'affaires</h3>
        <p><?= number_format($ca,2,',',' ') ?> DH</p>
    </div>

</div>

<?php if ($topProduit): ?>
<div class="card">
    <h3>🏆 Produit le plus vendu</h3>
    <p><?= $topProduit['nom_produit'] ?> (<?= $topProduit['total_vendu'] ?> unités)</p>
</div>
<?php endif; ?>

</div>

</body>
</html>

<div class="menu-overlay" onclick="document.body.classList.remove('menu-open')"></div>
<script src="assets/js/app.js?v=1"></script>
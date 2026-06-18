<?php
session_start();
require_once "connexion.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'] ?? 'employee';

/* STATISTIQUES */
$nbClients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$nbProduits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$nbVentes   = $pdo->query("SELECT COUNT(*) FROM vente")->fetchColumn();
$caTotal    = $pdo->query("SELECT SUM(total) FROM vente")->fetchColumn();
if (!$caTotal) $caTotal = 0;

/* GRAPH DATA (exemple simple) */
$data = $pdo->query("
    SELECT DATE(date_vente) as d, SUM(total) as t
    FROM vente
    GROUP BY DATE(date_vente)
    ORDER BY d DESC
    LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$values = [];

foreach ($data as $row) {
    $labels[] = $row['d'];
    $values[] = $row['t'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard SaaS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    margin:0;
    font-family: Arial;
    background:#f4f6f9;
}

/* SIDEBAR */
.sidebar {
    position:fixed;
    width:240px;
    height:100%;
    background:#111827;
    color:white;
    padding:20px;
}

.sidebar h2 {
    font-size:18px;
    margin-bottom:30px;
}

.sidebar a {
    display:block;
    color:#cbd5e1;
    text-decoration:none;
    padding:10px;
    border-radius:8px;
    margin-bottom:5px;
}

.sidebar a:hover {
    background:#1f2937;
}

/* MAIN */
.main {
    margin-left:260px;
    padding:20px;
}

/* TOPBAR */
.topbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

/* CARDS */
.cards {
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
}

.card {
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.card h3 {
    margin:0;
    font-size:14px;
    color:#6b7280;
}

.card p {
    font-size:22px;
    margin-top:10px;
    font-weight:bold;
}

/* GRID */
.grid {
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
    margin-top:20px;
}

.box {
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

/* BUTTONS */
.btn {
    padding:10px 15px;
    border:none;
    border-radius:8px;
    background:#2563eb;
    color:white;
    cursor:pointer;
}

.btn:hover {
    background:#1d4ed8;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>📊 MyERP</h2>

    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="clients.php">👥 Clients</a>
    <a href="produits.php">📦 Produits</a>
    <a href="ventes.php">🧾 Ventes</a>

    <?php if ($role === 'admin' || $role === 'manager'): ?>
        <a href="factures.php">💰 Factures</a>
        <a href="statistiques.php">📈 Statistiques</a>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
        <a href="employe.php">👨‍💼 Employés</a>
    <?php endif; ?>

    <a href="logout.php">🚪 Déconnexion</a>
</div>

<!-- MAIN -->
<div class="main">

    <div class="topbar">
        <h2>Bienvenue 👋 <?= htmlspecialchars($_SESSION['mail']) ?></h2>
        <button class="btn">+ Nouvelle action</button>
    </div>

    <!-- CARDS -->
    <div class="cards">
        <div class="card">
            <h3>Clients</h3>
            <p><?= $nbClients ?></p>
        </div>

        <div class="card">
            <h3>Produits</h3>
            <p><?= $nbProduits ?></p>
        </div>

        <div class="card">
            <h3>Ventes</h3>
            <p><?= $nbVentes ?></p>
        </div>

        <div class="card">
            <h3>Chiffre d'affaires</h3>
            <p><?= number_format($caTotal,2,',',' ') ?> DH</p>
        </div>
    </div>

    <!-- GRAPHIQUE + ACTIONS -->
    <div class="grid">

        <div class="box">
            <h3>📈 Ventes (7 derniers jours)</h3>
            <canvas id="chart"></canvas>
        </div>

        <div class="box">
            <h3>⚡ Actions rapides</h3>
            <button class="btn">+ Client</button><br><br>
            <button class="btn">+ Vente</button><br><br>
            <button class="btn">+ Produit</button>
        </div>

    </div>

</div>

<script>
const ctx = document.getElementById('chart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Ventes',
            data: <?= json_encode($values) ?>,
            borderWidth: 2
        }]
    }
});
</script>

</body>
</html>
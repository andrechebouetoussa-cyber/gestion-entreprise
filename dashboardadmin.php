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

/* ALERTES STOCK */
$produitsFaibles = $pdo->query("
    SELECT nom_produit, stock
    FROM produits
    WHERE stock <= 5
")->fetchAll(PDO::FETCH_ASSOC);

$nbAlertes = count($produitsFaibles);

/* GRAPH VENTES */
$labels = [];
$values = [];

$ventesParJour = $pdo->query("
    SELECT DATE(date_vente) as jour, SUM(total) as total
    FROM vente
    GROUP BY DATE(date_vente)
    ORDER BY jour DESC
    LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($ventesParJour as $v) {
    $labels[] = $v['jour'];
    $values[] = $v['total'];
}

/* DERNIÈRES VENTES */
$dernieresVentes = $pdo->query("
    SELECT id_vente, total, date_vente
    FROM vente
    ORDER BY id_vente DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Administrateur</title>
<link rel="stylesheet" href="style.css?v=2">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.box{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    margin-top:20px;
}

.stock-alert{
    background:#fff3cd;
    border-left:5px solid #ffc107;
    padding:20px;
    border-radius:12px;
    margin-top:20px;
}

.alert-item{
    background:white;
    padding:10px;
    margin-top:10px;
    border-radius:8px;
}
</style>

</head>

<body>

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

<div class="main">

    <div class="topbar">
        <h2>Bienvenue 👋 <?= htmlspecialchars($_SESSION['mail'] ?? '') ?></h2>
    </div>

    <!-- STATS -->
    <div class="cards">

        <div class="card"><h3>👥 Clients</h3><p><?= $nbClients ?></p></div>
        <div class="card"><h3>📦 Produits</h3><p><?= $nbProduits ?></p></div>
        <div class="card"><h3>🧾 Ventes</h3><p><?= $nbVentes ?></p></div>
        <div class="card"><h3>💰 CA</h3><p><?= number_format($caTotal,2,',',' ') ?> DH</p></div>
        <div class="card"><h3>⚠️ Alertes</h3><p><?= $nbAlertes ?></p></div>

    </div>

    <!-- ALERTES STOCK -->
    <?php if (!empty($produitsFaibles)): ?>
    <div class="stock-alert">

        <h2>⚠️ Produits à réapprovisionner</h2>

        <?php foreach ($produitsFaibles as $p): ?>
            <div class="alert-item">
                📦 <strong><?= htmlspecialchars($p['nom_produit']) ?></strong>
                — Stock : <?= $p['stock'] ?>
            </div>
        <?php endforeach; ?>

    </div>
    <?php endif; ?>

    <!-- GRAPHIQUE -->
    <div class="box">
        <h2>📈 Ventes des 7 derniers jours</h2>
        <canvas id="chart"></canvas>
    </div>

    <!-- DERNIÈRES VENTES -->
    <div class="box">
        <h2>🧾 Dernières ventes</h2>

        <table style="width:100%">
            <tr>
                <th>ID</th>
                <th>Total</th>
                <th>Date</th>
            </tr>

            <?php foreach ($dernieresVentes as $v): ?>
            <tr>
                <td><?= $v['id_vente'] ?></td>
                <td><?= number_format($v['total'],2,',',' ') ?> DH</td>
                <td><?= $v['date_vente'] ?></td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>

    <div class="quick-actions">

    <h2>⚡ Actions Rapides</h2>

    <div class="action-grid">

        <a href="employe.php" class="action-card">
            <h3>👨‍💼 Employés</h3>
            <p>Gérer les employés</p>
        </a>
         <a href="managers.php" class="action-card">
            <h3>👥 Managers</h3>
            <p>Ajouter et gérer les managers</p>
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

<script>
const ctx = document.getElementById('chart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Ventes (DH)',
            data: <?= json_encode($values) ?>,
            borderWidth: 2,
            tension: 0.3
        }]
    }
});
</script>

</body>
</html>
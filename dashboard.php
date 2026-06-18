<?php
session_start();
require_once "connexion.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'] ?? 'employe';

/* STATISTIQUES - Accessibles selon le rôle */
if ($role === 'admin' || $role === 'manager') {
    $nbClients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
} else {
    $nbClients = 0; // employé ne voit pas ce stat
}

$nbProduits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$nbVentes = $pdo->query("SELECT COUNT(*) FROM vente")->fetchColumn();
$caTotal = $pdo->query("SELECT SUM(total) FROM vente")->fetchColumn();
if ($caTotal === null) $caTotal = 0;

/* GRAPH VENTES - Tous sauf employé */
$labels = [];
$values = [];
if ($role !== 'employe') {
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
}

/* ALERTES STOCK - Admin et Manager */
$produitsFaibles = [];
$nbAlertes = 0;
if ($role === 'admin' || $role === 'manager') {
    $produitsFaibles = $pdo->query("
        SELECT nom_produit, stock
        FROM produits
        WHERE stock <= 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    $nbAlertes = count($produitsFaibles);
}

/* DERNIÈRES VENTES - Tous */
$dernieresVentes = $pdo->query("
    SELECT id_vente, total, date_vente
    FROM vente
    ORDER BY id_vente DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$roleLabel = [
    'admin' => 'Administrateur',
    'employe' => 'Employé',
    'manager' => 'Manager'
][$role] ?? 'Utilisateur';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css?v=2">
<style>
    .box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Espace <br><span><?= $roleLabel ?></span></h2>

    <ul>
        <li class="active"><a href="dashboard.php">🏠 Dashboard</a></li>

        <!-- ADMIN, MANAGER et EMPLOYE voient les clients -->
        <?php if ($role === 'admin' || $role === 'manager' || $role === 'employe'): ?>
            <li><a href="clients.php">👥 Clients</a></li>
        <?php endif; ?>

        <!-- Tous sauf admin voient produits -->
        <li><a href="produits.php">📦 Produits</a></li>
        
        <!-- Tous voient les ventes -->
        <li><a href="ventes.php">🧾 Ventes</a></li>

        <!-- ADMIN UNIQUEMENT voit les employés -->
        <?php if ($role === 'admin'): ?>
            <li><a href="employe.php">👨‍💼 Employés</a></li>
        <?php endif; ?>

        <!-- Employé peut créer une vente -->
        <?php if ($role === 'employe'): ?>
            <li><a href="nouvelle_vente.php">💰 Nouvelle Vente</a></li>
        <?php endif; ?>

        <!-- Tous voient les factures -->
        <li><a href="factures.php">🧾 Factures</a></li>

        <!-- ADMIN et MANAGER voient les stats -->
        <?php if ($role === 'admin' || $role === 'manager'): ?>
            <li><a href="statistiques.php">📊 Statistiques</a></li>
        <?php endif; ?>

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
        <h1>Bienvenue <?= htmlspecialchars($_SESSION['nom'] ?? '') ?> 👋</h1>
    </div>

    <!-- STATISTIQUES -->
    <div class="cards">

        <!-- Admin et Manager voient les clients -->
        <?php if ($role === 'admin' || $role === 'manager'): ?>
            <div class="card">
                <h3>👥 Clients</h3>
                <p><?= $nbClients ?></p>
            </div>
        <?php endif; ?>

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

    <!-- GRAPHIQUE - Admin et Manager uniquement -->
    <?php if ($role !== 'employe'): ?>
        <div class="box">
            <h3>📈 Ventes (7 derniers jours)</h3>
            <canvas id="chart"></canvas>
        </div>
    <?php endif; ?>

    <!-- HISTORIQUE VENTES - Tous -->
    <div class="box">
        <h3>📋 Dernières ventes</h3>
        <table style="width:100%; border-collapse: collapse;">
            <tr style="background: #f0f0f0; border-bottom: 1px solid #ddd;">
                <th style="padding: 10px; text-align: left;">ID Vente</th>
                <th style="padding: 10px; text-align: left;">Total</th>
                <th style="padding: 10px; text-align: left;">Date</th>
            </tr>
            <?php foreach ($dernieresVentes as $v): ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">#<?= $v['id_vente'] ?></td>
                    <td style="padding: 10px;"><?= number_format($v['total'], 2, ',', ' ') ?> DH</td>
                    <td style="padding: 10px;"><?= $v['date_vente'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- ALERTES STOCK - Admin et Manager -->
    <?php if (($role === 'admin' || $role === 'manager') && $nbAlertes > 0): ?>
        <div class="box" style="background: #fff3cd; border-left: 4px solid #ffc107;">
            <h3>⚠️ Alertes Stock (<?= $nbAlertes ?> produits)</h3>
            <ul style="padding-left: 20px;">
                <?php foreach ($produitsFaibles as $p): ?>
                    <li><?= htmlspecialchars($p['nom_produit']) ?> - Stock: <?= $p['stock'] ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- ACTIONS RAPIDES -->
    <div class="quick-actions">
        <h2>⚡ Actions Rapides</h2>
        <div class="action-grid">

            <?php if ($role === 'admin' || $role === 'manager' || $role === 'employe'): ?>
                <a href="clients.php" class="action-card">
                    <h3>👥 Clients</h3>
                    <p>Gérer les clients</p>
                </a>
            <?php endif; ?>

            <a href="produits.php" class="action-card">
                <h3>📦 Produits</h3>
                <p>Gérer le catalogue</p>
            </a>

            <a href="ventes.php" class="action-card">
                <h3>🧾 Ventes</h3>
                <p>Consulter les ventes</p>
            </a>

            <?php if ($role === 'employe'): ?>
                <a href="nouvelle_vente.php" class="action-card">
                    <h3>💰 Nouvelle Vente</h3>
                    <p>Créer une vente</p>
                </a>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <a href="employe.php" class="action-card">
                    <h3>👨‍💼 Employés</h3>
                    <p>Gérer les employés</p>
                </a>
            <?php endif; ?>

        </div>
    </div>

</div>

<div class="menu-overlay" onclick="document.body.classList.remove('menu-open')"></div>
<script src="assets/js/app.js?v=1"></script>

<?php if ($role !== 'employe'): ?>
<script>
const ctx = document.getElementById('chart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Ventes',
                data: <?= json_encode($values) ?>,
                borderWidth: 2,
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderColor: 'rgba(79, 70, 229, 1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
</script>
<?php endif; ?>

</body>
</html>

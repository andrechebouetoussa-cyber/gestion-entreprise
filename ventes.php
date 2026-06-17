<?php
session_start();
require_once "connexion.php";

// sécurité
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// CLIENTS + PRODUITS
$clients = $pdo->query("SELECT * FROM clients")->fetchAll(PDO::FETCH_ASSOC);
$produits = $pdo->query("SELECT * FROM produits")->fetchAll(PDO::FETCH_ASSOC);

// LISTE DES VENTES
$ventes = $pdo->query(
    "SELECT v.id_vente, v.date_vente, v.total,
        c.nom AS client_nom, c.prenom AS client_prenom,
        u.nom AS emp_nom, u.prenom AS emp_prenom
     FROM vente v
     LEFT JOIN clients c ON c.id_client = v.id_client
     LEFT JOIN utilisateurs u ON u.id_user = v.id_emp
     ORDER BY v.id_vente DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$message = '';

// AJOUT VENTE
if (isset($_POST['valider'])) {

    $client_id = (int)$_POST['client_id'];
    $produit_id = (int)$_POST['produit_id'];
    $quantite = (int)$_POST['quantite'];
    $emp_id = (int)$_SESSION['user_id'];

    if ($client_id <= 0 || $produit_id <= 0 || $quantite <= 0) {
        $message = "❌ Champs invalides";
    } else {

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT prix, stock FROM produits WHERE id_prod = ?");
            $stmt->execute([$produit_id]);
            $produit = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produit) {
                throw new Exception("Produit introuvable");
            }

            if ($quantite > $produit['stock']) {
                throw new Exception("Stock insuffisant !");
            }

            $prix = $produit['prix'];
            $sous_total = $prix * $quantite;

            // vente
            $stmt = $pdo->prepare("INSERT INTO vente(date_vente, total, id_client, id_emp) VALUES (NOW(), 0, ?, ?)");
            $stmt->execute([$client_id, $emp_id]);
            $vente_id = $pdo->lastInsertId();

            // ligne vente
            $stmt = $pdo->prepare("INSERT INTO ligne_vente(quantite, prix_unitaire, sous_total, id_prod, id_vente) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$quantite, $prix, $sous_total, $produit_id, $vente_id]);

            // update total
            $stmt = $pdo->prepare("UPDATE vente SET total = ? WHERE id_vente = ?");
            $stmt->execute([$sous_total, $vente_id]);

            // update stock
            $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id_prod = ?");
            $stmt->execute([$quantite, $produit_id]);

            $pdo->commit();

            $message = "✔ Vente enregistrée avec succès";

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "❌ " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ventes</title>
<link rel="stylesheet" href="style.css?v=2">
<style>

.profile-icon{
    text-decoration:none;
    font-size:18px;
}

.message{
    margin-bottom:10px;
    font-weight:bold;
}

.container{
    background:white;
    padding:20px;
    border-radius:10px;
}

select, input{
    padding:10px;
    margin:5px;
    width:220px;
}

button{
    padding:10px 15px;
    background:#3498db;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

</style>

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
                👤 <?= $_SESSION['email']; ?>
            </a>
        </div>

    </div>

    <h1>🧾 Gestion des Ventes</h1>

    <!-- MESSAGE -->
    <?php if (!empty($message)): ?>
        <div class="message" style="color:<?= str_contains($message,'✔') ? 'green' : 'red' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <div class="container">

        <form method="POST">

            <select name="client_id" required>
                <option value="">Client</option>
                <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['id_client'] ?>">
                        <?= $c['nom'] ?> <?= $c['prenom'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="produit_id" required>
                <option value="">Produit</option>
                <?php foreach ($produits as $p): ?>
                    <option value="<?= $p['id_prod'] ?>">
                        <?= $p['nom_produit'] ?> (<?= $p['prix'] ?> DH)
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number" name="quantite" min="1" placeholder="Quantité" required>

            <button type="submit" name="valider">Valider</button>

        </form>

    </div>

    <!-- TABLE VENTES -->
    <div class="container" style="margin-top:20px;">
        <h2>Liste des ventes</h2>
        <table style="width:100%; border-collapse:collapse; background:white;">
            <tr style="background:#3498db; color:white;">
                <th>ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Client</th>
                <th>Employé</th>
            </tr>
            <?php if (empty($ventes)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px;">Aucune vente enregistrée.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($ventes as $v): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td><?= htmlspecialchars($v['id_vente']) ?></td>
                    <td><?= htmlspecialchars($v['date_vente']) ?></td>
                    <td><?= htmlspecialchars($v['total']) ?> DH</td>
                    <td><?= htmlspecialchars(trim($v['client_nom'].' '.$v['client_prenom'])) ?></td>
                    <td><?= htmlspecialchars(trim($v['emp_nom'].' '.$v['emp_prenom'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

</div>

<div class="menu-overlay" onclick="document.body.classList.remove('menu-open')"></div>
<script src="assets/js/app.js?v=1"></script>

</body>
</html>
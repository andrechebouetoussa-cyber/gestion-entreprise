<?php
session_start();
require_once "connexion.php";

/* AJOUT PRODUIT */
if (isset($_POST['ajouter'])) {

    $nom = trim($_POST['nom_produit']);
    $description = trim($_POST['description']);
    $prix = (float) $_POST['prix'];
    $stock = (int) $_POST['stock'];

    if ($nom && $prix >= 0) {

        $stmt = $pdo->prepare("
            INSERT INTO produits(nom_produit, description, prix, stock)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$nom, $description, $prix, $stock]);
    }

    header("Location: produits.php");
    exit();
}

/* SUPPRESSION PRODUIT */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM produits WHERE id_prod = ?");
    $stmt->execute([$id]);

    header("Location: produits.php");
    exit();
}

/* LISTE PRODUITS */
$produits = $pdo->query("
    SELECT id_prod, nom_produit, description, prix, stock
    FROM produits
    ORDER BY id_prod DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Produits</title>
<link rel="stylesheet" href="style.css?v=2">

<style>
.card{
    background:white;
    padding:20px;
    border-radius:10px;
    margin:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:10px;
}

.form-grid input,
.form-grid textarea{
    padding:10px;
    border:1px solid #ddd;
    border-radius:6px;
}

.form-grid button{
    grid-column:span 2;
    padding:10px;
    background:#3498db;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

.form-grid button:hover{
    background:#2980b9;
}

table{
    width:95%;
    margin:20px;
    border-collapse:collapse;
    background:white;
}

th{
    background:#3498db;
    color:white;
    padding:10px;
}

td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #eee;
}

.btn-edit{
    color:#2980b9;
    font-weight:bold;
    margin-right:10px;
    text-decoration:none;
}

.btn-delete{
    color:#e74c3c;
    font-weight:bold;
    text-decoration:none;
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
    </table>

    <div class="menu-overlay" onclick="document.body.classList.remove('menu-open')"></div>
    <script src="assets/js/app.js?v=1"></script>
        <li><a href="produits.php">📦 Produits</a></li>
        <li><a href="ventes.php">🧾 Ventes</a></li>
        <li><a href="factures.php">🧾 Factures</a></li>
        <li><a href="statistiques.php">📊 Statistiques</a></li>
        <li class="logout"><a href="logout.php">🚪 Déconnexion</a></li>
    </ul>

</div>

<!-- MAIN -->
<div class="main">

    <div class="topbar">
        <button id="hamburger" class="hamburger" aria-label="Menu">☰</button>
        <div class="user-menu">👤 <?= $_SESSION['email'] ?? 'Admin' ?></div>
    </div>

    <h1 style="margin:20px;">📦 Gestion des Produits</h1>

    <!-- FORM -->
    <div class="card">

        <h2>➕ Ajouter un produit</h2>

        <form method="POST" class="form-grid">

            <input type="text" name="nom_produit" placeholder="Nom produit" required>

            <textarea name="description" placeholder="Description"></textarea>

            <input type="number" step="0.01" name="prix" placeholder="Prix" required>

            <input type="number" name="stock" placeholder="Stock" required>

            <button type="submit" name="ajouter">Ajouter produit</button>

        </form>

    </div>

    <!-- TABLE -->
    <table>

        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Prix</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($produits as $p): ?>
        <tr>

            <td><?= $p['id_prod'] ?></td>
            <td><?= htmlspecialchars($p['nom_produit']) ?></td>
            <td><?= htmlspecialchars($p['description']) ?></td>
            <td><?= $p['prix'] ?> DH</td>
            <td><?= $p['stock'] ?></td>

            <td style="white-space:nowrap;">

                <a href="edit_produit.php?id=<?= $p['id_prod'] ?>" class="btn-edit">
                    Modifier
                </a>

                <a href="produits.php?delete=<?= $p['id_prod'] ?>" class="btn-delete"
                   onclick="return confirm('Supprimer ce produit ?')">
                    Supprimer
                </a>

            </td>

        </tr>
        <?php endforeach; ?>

    </table>

</div>

</body>
</html>
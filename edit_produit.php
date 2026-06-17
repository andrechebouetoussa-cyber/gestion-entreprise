<?php
session_start();
require_once "connexion.php";

/* Vérifier ID */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID produit manquant");
}

$id = (int) $_GET['id'];

/* Récupérer produit */
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id_prod = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    die("Produit introuvable");
}

/* UPDATE PRODUIT */
if (isset($_POST['modifier'])) {

    $nom = trim($_POST['nom_produit']);
    $description = trim($_POST['description']);
    $prix = (float) $_POST['prix'];
    $stock = (int) $_POST['stock'];

    $stmt = $pdo->prepare("
        UPDATE produits
        SET nom_produit = ?, description = ?, prix = ?, stock = ?
        WHERE id_prod = ?
    ");

    $stmt->execute([$nom, $description, $prix, $stock, $id]);

    header("Location: produits.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>Modifier Produit</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    background:white;
    padding:25px;
    width:400px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

input, textarea{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    border:1px solid #ddd;
    border-radius:6px;
}

button{
    width:100%;
    padding:10px;
    background:#3498db;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#2980b9;
}

a{
    display:block;
    text-align:center;
    margin-top:10px;
    color:#e74c3c;
    text-decoration:none;
}
</style>

</head>

<body>

<div class="card">

    <h2>✏️ Modifier Produit</h2>

    <form method="POST">

        <input type="text" name="nom_produit"
               value="<?= htmlspecialchars($produit['nom_produit']) ?>"
               required>

        <textarea name="description"><?= htmlspecialchars($produit['description']) ?></textarea>

        <input type="number" step="0.01" name="prix"
               value="<?= $produit['prix'] ?>" required>

        <input type="number" name="stock"
               value="<?= $produit['stock'] ?>" required>

        <button type="submit" name="modifier">Modifier</button>

    </form>

    <a href="produits.php">← Retour</a>

</div>

</body>
</html>
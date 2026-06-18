<?php
session_start();
require_once "connexion.php";

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
</body>
</html>
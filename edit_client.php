<?php
session_start();
require_once "connexion.php";

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Bloquer l'accès à l'employé
if ($_SESSION['role'] === 'employe') {
    header("Location: dashboard.php");
    exit();
}

// Vérifier ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID client manquant");
}

$id = (int) $_GET['id'];

// Récupérer client
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id_client = ?");
$stmt->execute([$id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    die("Client introuvable");
}

// UPDATE CLIENT
if (isset($_POST['modifier'])) {

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $mail = trim($_POST['mail']);
    $adresse = trim($_POST['adresse']);

    if ($nom && $prenom && $mail) {

        $stmt = $pdo->prepare("
            UPDATE clients 
            SET nom = ?, prenom = ?, telephone = ?, mail = ?, adresse = ?
            WHERE id_client = ?
        ");

        $stmt->execute([$nom, $prenom, $telephone, $mail, $adresse, $id]);

        header("Location: clients.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier Client</title>
<link rel="stylesheet" href="style.css?v=2">

<style>
.card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 500px;
    margin: 40px auto;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 6px;
}

button {
    width: 100%;
    padding: 10px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

button:hover {
    background: #2980b9;
}
</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <h2>Espace <br><span>Administrateur</span></h2>

    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="employes.php">👨‍💼 Employés</a></li>
        <li class="active"><a href="clients.php">👥 Clients</a></li>
        <li><a href="produits.php">📦 Produits</a></li>
        <li><a href="ventes.php">🧾 Ventes</a></li>
        <li><a href="factures.php">🧾 Factures</a></li>
        <li><a href="statistiques.php">📊 Statistiques</a></li>
        <li class="logout"><a href="logout.php">🚪 Déconnexion</a></li>
    </ul>

</div>

<!-- MAIN -->
<div class="main">

    <div class="card">

        <h2>✏️ Modifier Client</h2>

        <form method="POST">

            <input type="text" name="nom" value="<?= htmlspecialchars($client['nom']) ?>" required>
            <input type="text" name="prenom" value="<?= htmlspecialchars($client['prenom']) ?>" required>
            <input type="text" name="telephone" value="<?= htmlspecialchars($client['telephone']) ?>" required>
            <input type="email" name="mail" value="<?= htmlspecialchars($client['mail']) ?>" required>
            <input type="text" name="adresse" value="<?= htmlspecialchars($client['adresse']) ?>" required>

            <button type="submit" name="modifier">Modifier</button>

        </form>

    </div>

</div>

</body>
</html>
<?php
session_start();
require_once "connexion.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];

/* RÉCUPÉRER LES INFOS UTILISATEUR */
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_user = ?");
$stmt->execute([$id_user]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable");
}

/* UPDATE PROFIL */
$message = '';

if (isset($_POST['update'])) {

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $mail = trim($_POST['mail']);
    $adresse = trim($_POST['adresse']);

    if ($nom && $prenom && $mail) {

        $stmt = $pdo->prepare("
            UPDATE utilisateurs
            SET nom = ?, prenom = ?, telephone = ?, mail = ?, adresse = ?
            WHERE id_user = ?
        ");

        $stmt->execute([$nom, $prenom, $telephone, $mail, $adresse, $id_user]);

        $_SESSION['mail'] = $mail;
        $_SESSION['nom'] = $nom;

        $message = "✔ Profil mis à jour avec succès";
        
        // recharger les données
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_user = ?");
        $stmt->execute([$id_user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mon Profil</title>

<style>
body{
    font-family: Arial;
    background:#f4f6f9;
    padding:20px;
}

.card{
    background:white;
    padding:20px;
    border-radius:10px;
    max-width:500px;
    margin:auto;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

input{
    width:100%;
    padding:10px;
    margin:8px 0;
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

h1{
    text-align:center;
}

.msg{
    text-align:center;
    color:green;
    margin-bottom:10px;
}
</style>

</head>

<body>

<h1>👤 Mon Profil</h1>

<div class="card">

<?php if ($message): ?>
    <div class="msg"><?= $message ?></div>
<?php endif; ?>

<form method="POST">

    <label>Nom</label>
    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

    <label>Prénom</label>
    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>

    <label>Téléphone</label>
    <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>">

    <label>Email</label>
    <input type="email" name="mail" value="<?= htmlspecialchars($user['mail']) ?>" required>

    <label>Adresse</label>
    <input type="text" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>">

    <button type="submit" name="update">Mettre à jour</button>

</form>

</div>

</body>
</html>
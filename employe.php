<?php
session_start();
require_once "connexion.php";

// Vérification de l'authentification et du rôle
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Bloquer l'accès si ce n'est pas un admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

/* AJOUT EMPLOYÉ */
if (isset($_POST['ajouter'])) {

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $mail = trim($_POST['mail']);
    $adresse = trim($_POST['adresse']);
    $poste = trim($_POST['poste']);
    $role = trim($_POST['role']);

    if (!in_array($role, ['employe', 'manager'])) {
        die("❌ Rôle invalide");
    }

    try {

        $pdo->beginTransaction();

        // 🔎 vérifier email unique
        $check = $pdo->prepare("SELECT id_user FROM utilisateurs WHERE mail = ?");
        $check->execute([$mail]);

        if ($check->fetch()) {
            throw new Exception("Email déjà utilisé !");
        }

        // 🔐 génération mot de passe automatique
        $motDePasseTemp = "emp" . rand(1000, 9999);
        $hash = password_hash($motDePasseTemp, PASSWORD_BCRYPT);

        // 1. INSERT utilisateur (AVEC PASSWORD et le rôle choisi)
        $stmt = $pdo->prepare("
            INSERT INTO utilisateurs(nom, prenom, telephone, mail, adresse, password, role)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nom, $prenom, $telephone, $mail, $adresse, $hash, $role]);

        $id_user = $pdo->lastInsertId();

        // 2. INSERT employe
        $stmt = $pdo->prepare("
            INSERT INTO employe(id_user, poste)
            VALUES (?, ?)
        ");
        $stmt->execute([$id_user, $poste]);

        $pdo->commit();

        // (optionnel) afficher mot de passe temporaire
        echo "<script>alert('Employé créé. Mot de passe: $motDePasseTemp');</script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur : " . $e->getMessage());
    }

    header("Location: employe.php");
    exit();
}

$employes = $pdo->query(
    "SELECT e.id_emp, u.nom, u.prenom, u.telephone, u.mail, u.adresse, e.poste
    FROM employe e
    JOIN utilisateurs u ON u.id_user = e.id_user
    ORDER BY e.id_emp DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Gestion Employés</title>
<link rel="stylesheet" href="style.css?v=2">


</head>

<body>

<!-- SIDEBAR -->
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

<!-- MAIN -->
<div class="main">

    <div class="topbar">
        <button id="hamburger" class="hamburger" aria-label="Menu">☰</button>
        <div class="user-menu">
            👤 <?= $_SESSION['email'] ?? 'Admin' ?>
        </div>
    </div>

    <h1>👨‍💼 Gestion des Employés</h1>

    <!-- FORM -->
    <div class="card">

        <h2>➕ Ajouter un employé</h2>

        <form method="POST" class="form-grid">

            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prénom" required>
            <input type="text" name="telephone" placeholder="Téléphone">
            <input type="email" name="mail" placeholder="Email" required>
            <input type="text" name="adresse" placeholder="Adresse">
            <input type="text" name="poste" placeholder="Poste (ex: vendeur, admin)" required>
            
            <select name="role" required>
                <option value="">-- Sélectionner un rôle --</option>
                <option value="employe">👤 Employé</option>
                <option value="manager">👨‍💼 Manager</option>
            </select>

            <button type="submit" name="ajouter">Ajouter employé</button>

        </form>

    </div>

    <!-- TABLE -->
    <table>

        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Téléphone</th>
            <th>Mail</th>
            <th>Adresse</th>
            <th>Poste</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($employes as $e): ?>
        <tr>

            <td><?= $e['id_emp'] ?></td>
            <td><?= $e['nom'] ?></td>
            <td><?= $e['prenom'] ?></td>
            <td><?= $e['telephone'] ?></td>
            <td><?= $e['mail'] ?></td>
            <td><?= $e['adresse'] ?></td>
            <td><?= $e['poste'] ?></td>

            <td style="white-space:nowrap;">

                <a href="edit_employe.php?id=<?= $e['id_emp'] ?>" class="btn-edit">
                    Modifier
                </a>

                <a href="employes.php?delete=<?= $e['id_emp'] ?>" class="btn-delete"
                   onclick="return confirm('Supprimer cet employé ?')">
                    Supprimer
                </a>

            </td>

        </tr>
        <?php endforeach; ?>

    </table>

</div>
</div>

<div class="menu-overlay" onclick="document.body.classList.remove('menu-open')"></div>
<script src="assets/js/app.js?v=1"></script>
</body>
</html>
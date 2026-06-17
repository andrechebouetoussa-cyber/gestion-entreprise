<?php
require_once "connexion.php";

$message = "";

if (isset($_POST['reset'])) {

    $mail = trim($_POST['mail']);

    $stmt = $pdo->prepare("SELECT id_user FROM utilisateurs WHERE mail = ?");
    $stmt->execute([$mail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $message = "Email introuvable";
    } else {

        // 🔐 nouveau mot de passe
        $newPassword = "new" . rand(1000,9999);
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("UPDATE utilisateurs SET password = ? WHERE mail = ?");
        $stmt->execute([$hash, $mail]);

        $message = "Nouveau mot de passe : " . $newPassword;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mot de passe oublié</title>
</head>
<body>

<h2>Réinitialisation mot de passe</h2>

<form method="POST">

    <input type="email" name="mail" placeholder="Votre email" required>
    <button type="submit" name="reset">Réinitialiser</button>

</form>

<p style="color:green;">
    <?= $message ?>
</p>

</body>
</html>
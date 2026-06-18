<?php
session_start();
require_once __DIR__ . "/connexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['mail'], $_POST['password'])) {
        die("❌ Données manquantes");
    }

    $mail = filter_var(trim($_POST['mail']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (empty($mail) || empty($password)) {
        die("❌ Veuillez remplir tous les champs.");
    }

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE mail = ?");
    $stmt->execute([$mail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("❌ Email inexistant");
    }

    if (!password_verify($password, $user['password'])) {
        die("❌ Mot de passe incorrect");
    }

    session_regenerate_id(true);

    $_SESSION["user_id"] = $user['id_user'];
    $_SESSION["role"] = $user['role'];
    $_SESSION["nom"] = $user['nom'];
    $_SESSION["mail"] = $user['mail'];

    // Tous les rôles vont vers le même dashboard
    if (in_array($user['role'], ['admin', 'employe', 'manager'])) {
        header("Location: dashboard.php");
        exit();
    } else {
        die("❌ Rôle inconnu");
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        /*general*/

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: linear-gradient(135deg, #4f46e5, #9333ea);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}


/* NAVBAR */
.navbar {
    width: 100%;
    padding: 15px 30px;
    background: rgba(0,0,0,0.2);
    backdrop-filter: blur(10px);
    color: white;
}

.logo-text {
    font-size: 18px;
    font-weight: bold;
}

/*page login*/

/* LOGIN BOX */
.login-box {
    width: 100%;
    max-width: 400px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

/* FORM GROUP */
.form-group,
.password-container {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

input {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    outline: none;
    transition: 0.3s;
}

input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 5px rgba(79,70,229,0.4);
}

/* PASSWORD ICON */
.password-container {
    position: relative;
}

#togglePassword {
    position: absolute;
    right: 10px;
    top: 38px;
    cursor: pointer;
}

/* BUTTONS */
.button-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
}

button {
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

button[type="submit"] {
    background: #4f46e5;
    color: white;
}

button[type="submit"]:hover {
    background: #3730a3;
}

button[type="button"] {
    background: #e5e7eb;
    color: #333;
}

button[type="button"]:hover {
    background: #d1d5db;
}

/* LINKS */
a {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #4f46e5;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
    </style>


</head>
<body>
    <nav class="navbar">
    <div class="logo-container">
        <span class="logo-text">Gestion d'entreprise</span>
    </div>

</nav>

    <div class="login-box">
       
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                
                <div class="form-group" data-icon="📧">
                    <label for="mail">Email*</label>
                    <input type="email" placeholder="votre.email@exemple.com**" name="mail" id="mail" required>
                </div>
                
                <div class="password-container">
                    <label for="password">Mot de passe*</label>
                    <input type="password" placeholder="votre mot de passe**" name="password" id="password" required>
                    <span id="togglePassword">🙈</span>
                </div>
                <a href="forgot_password.php">Mot de passe oublié ?</a>
                <div class="button-group">
                    <button type="submit" value="login" name="login">Se connecter</button>
                    <p>Vous n'avez pas de compte?</p>
                    <button type="button" onclick="window.location.href='register.php'">Créer un compte</button>
                </div>
               
            </form>
        </div>
    </div>


</body>
</html>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    togglePassword.addEventListener("click", function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePassword.textContent = "👁️";
        } else {
            passwordInput.type = "password";
            togglePassword.textContent = "🙈";
        }
    });

});
</script>
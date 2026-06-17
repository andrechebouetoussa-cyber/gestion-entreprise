<?php
ob_start();
session_start();
require_once "connexion.php";

if (isset($_POST['inscrire'])) {

  $nom = htmlspecialchars(trim($_POST['nom']));
  $prenom = htmlspecialchars(trim($_POST['prenom']));
  $mail = htmlspecialchars(trim($_POST['mail']));
  $telephone = htmlspecialchars(trim($_POST['telephone']));
  $adresse = htmlspecialchars(trim($_POST['adresse']));
  $password = trim($_POST['password']);
  $conf_password = trim($_POST['conf_password']);

  if (empty($nom) || empty($prenom) || empty($mail) || empty($telephone) || empty($adresse) || empty($password) || empty($conf_password)) {
    die("❌ Veuillez remplir tous les champs.");
  }

  if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    die("❌ Email invalide.");
  }

  $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE mail = ?");
  $stmt->execute([$mail]);

  if ($stmt->rowCount() > 0) {
    die("❌ Cet email existe déjà.");
  }

  if ($password !== $conf_password) {
    die("❌ Mots de passe non identiques.");
  }

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("
    INSERT INTO utilisateurs (nom, prenom, mail, telephone, password, adresse, role)
    VALUES (?, ?, ?, ?, ?, ?, ?)
  ");

  $result = $stmt->execute([
    $nom,
    $prenom,
    $mail,
    $telephone,
    $hashed_password,
    $adresse,
    'client'
  ]);

  if ($result) {
    ob_end_clean();
    header("Location: login.php");
    exit;
  } else {
    echo "❌ Erreur lors de l'inscription.";
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


.login-box{
     width: 100%;
    max-width: 400px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    
}

/* TITRES */

.login-box h2{
    text-align: center;
    color: #4f46e5;
    margin-bottom: 10px;
    font-size: 20px;
}

.login-box h3{
    text-align: center;
    color:black;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: normal;
}


form{
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* INPUTS */

form input{
    width: 100%;
    padding: 14px;
    border: 1px solid #ccc;
    border-radius: 10px;
    outline: none;
    font-size: 15px;
    transition: 0.3s;
    margin-bottom: 10px;
}

form input:focus{
    border-color: #0a7c6d;
    box-shadow: 0 0 8px rgba(10,124,109,0.3);
}

.password-container{
    position: relative;
    width: 100%;
}

.password-container input{
    padding-right: 45px;
}

.password-container span{
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
}

button{
    padding: 14px;
    border: none;
    border-radius: 10px;
    background:#4f46e5;
    color: white;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}

button:hover{
    background: #3730a3;
    transform: scale(1.02);
}

p{
    text-align: center;
    margin-top: 15px;
    color: black;
}

a{
    text-align: center;
    margin-top: 18px;
    text-decoration: none;
    color: black;
    font-weight: bold;
    transition: 0.3s;
}

a:hover{
    color:#3730a3;
}

.progress-container{
    width:100%;
    display:flex;
    flex-direction:row;
    align-items:center;
    justify-content:center;
    margin:25px 0;
}

.progress-step{
    display:flex;
    flex-direction:column;
    align-items:center;
}

.progress-step span{
    display:flex;
    align-items:center;
    justify-content:center;
    width:45px;
    height:45px;
    border-radius:50%;
 background:#4f46e5;
    color:#555;

    font-size:18px;
    font-weight:bold;
}

.progress-step p{
    margin-top:6px;
    font-size:13px;
    color:black;
    text-align:center;
}

.progress-line{
    width:80px;
    height:4px;
    background: #4f46e5;
    margin:0 10px;
    border-radius:20px;
}


.progress-step.active span{
    background:#4f46e5;
    color:white;
}

.progress-step.active p{
    color:black;
    font-weight:bold;
}

.progress-line.active{
    background:#4f46e5;
}
h3{
    color: black;
}
/*
   RESPONSIVE
*/

@media(max-width: 450px){
    .login-box{
        width: 90%;
        padding: 25px;
    }

    .progress-line{
        width:40px;
    }
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

<h2>Créer votre compte</h2>

<!-- BARRE DE PROGRESSION -->
<div class="progress-container">

    <div class="progress-step active" id="prog1">
        <span>1</span>
        <p>Identité</p>
    </div>

    <div class="progress-line" id="line1"></div>

    <div class="progress-step" id="prog2">
        <span>2</span>
        <p>Contact</p>
    </div>

    <div class="progress-line" id="line2"></div>

    <div class="progress-step" id="prog3">
        <span>3</span>
        <p>Sécurité</p>
    </div>

</div>

<!-- STEP 1 -->
<div id="step1">

    <h3>Informations personnelles</h3>

    <input type="text" name="nom" placeholder="Votre nom*" required>

    <input type="text" name="prenom" placeholder="Votre prénom*" required>

    <button type="button" onclick="next1()">Suivant</button>

</div>

<!-- STEP 2 -->
<div id="step2" style="display:none;">

    <h3>Coordonnées</h3>

    <input type="email" name="mail" placeholder="Votre email*" required>

    <input type="tel" name="telephone" placeholder="Votre numéro de téléphone*" required>

    <input type="text" name="adresse" placeholder="Votre adresse*" required>

    <button type="button" onclick="prev1()">Retour</button>
    <button type="button" onclick="next2()">Suivant</button>

</div>

<!-- STEP 3 -->
<div id="step3" style="display:none;">

    <h3>Sécurité du compte</h3>

 <div class="password-container">
    <input type="password"
           name="password"
           id="password"
           placeholder="Mot de passe*"
           required>
    <span id="togglePassword">🙈</span>
</div>

<div class="password-container">
    <input type="password"
           name="conf_password"
           id="conf_password"
           placeholder="Confirmer le mot de passe*"
           required>
    <span id="toggleConfPassword">🙈</span>
</div>

    <button type="button" onclick="prev2()">Retour</button>

    <button type="submit" name="inscrire" value="inscrire">
        S'inscrire
    </button>

</div>



</form> 

</div>
</body>
</html>

<script>

const password = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");

const confPassword = document.getElementById("conf_password");
const toggleConfPassword = document.getElementById("toggleConfPassword");

togglePassword.addEventListener("click", function () {
    if (password.type === "password") {
        password.type = "text";
        togglePassword.textContent = "👁️";
    } else {
        password.type = "password";
        togglePassword.textContent = "🙈";
    }
});

toggleConfPassword.addEventListener("click", function () {
    if (confPassword.type === "password") {
        confPassword.type = "text";
        toggleConfPassword.textContent = "👁️";
    } else {
        confPassword.type = "password";
        toggleConfPassword.textContent = "🙈";
    }
});


// STEP 1
function next1() {
    const nom = document.querySelector("input[name='nom']");
    const prenom = document.querySelector("input[name='prenom']");

    if (nom.value.trim() === "" || prenom.value.trim() === "") {
        alert("Veuillez remplir votre nom et prénom");
        return;
    }

    document.getElementById("step1").style.display = "none";
    document.getElementById("step2").style.display = "block";

    document.getElementById("prog2").classList.add("active");
    document.getElementById("line1").classList.add("active");
}


// STEP 2
function next2() {
    const email = document.querySelector("input[name='mail']");
    const tel = document.querySelector("input[name='telephone']");
    const adresse = document.querySelector("input[name='adresse']");

    if (
        email.value.trim() === "" ||
        tel.value.trim() === "" ||
        adresse.value.trim() === ""
    ) {
        alert("Veuillez remplir tous les champs de contact");
        return;
    }

    document.getElementById("step2").style.display = "none";
    document.getElementById("step3").style.display = "block";

    document.getElementById("prog3").classList.add("active");
    document.getElementById("line2").classList.add("active");
}


// RETOURS
function prev1() {
    document.getElementById("step2").style.display = "none";
    document.getElementById("step1").style.display = "block";
}

function prev2() {
    document.getElementById("step3").style.display = "none";
    document.getElementById("step2").style.display = "block";
}
</script>
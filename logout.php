!<?php
session_start();

/* vider session */
$_SESSION = [];

/* détruire session */
session_destroy();

/* supprimer cookie (optionnel mais pro) */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* redirection */
header("Location: login.php");
exit();
?>
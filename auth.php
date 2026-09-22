<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redireciona para o login caso o usuário não esteja autenticado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit();
}
?>
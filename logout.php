<?php
// Inicia a sessão para poder acessar e apagar os dados armazenados
session_start();

// Esvazia o array $_SESSION eliminando todas as variáveis gravadas
$_SESSION = array();

// Destrói totalmente a sessão ativa no servidor
session_destroy();

// Redireciona o usuário para a tela de login
header("location: login.php");

// Interrompe a execução do script
exit();
?>
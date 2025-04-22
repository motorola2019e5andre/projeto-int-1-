<?php
session_start();

// Permite acesso público à área de agendamento
$public_pages = ['index.php', 'sistema.php'];
$current_page = basename($_SERVER['PHP_SELF']);

// Se não for página pública, verifica autenticação
if (!in_array($current_page, $public_pages)) {
    if (empty($_SESSION['user_type'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: index.php');
        exit;
    }
    
    // Verifica inatividade apenas para usuários logados
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }
    
    $_SESSION['last_activity'] = time();
}
?>
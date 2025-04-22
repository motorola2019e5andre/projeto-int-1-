<?php
// Configurações de conexão com tratamento robusto de erros
define('DB_HOST', 'localhost');
define('DB_NAME', 'clinica_mentalize');
define('DB_USER', 'clinica_user');
define('DB_PASS', 'SenhaSegura123');
define('DB_CHARSET', 'utf8mb4');  // Corrigido: removido o "=UTF-8"

// Configurações de codificação
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

try {
    // Configuração da conexão PDO
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;  // Adicionado esta linha
    $options = [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Verificação adicional da conexão
    $pdo->query("SELECT 1");
    
} catch (PDOException $e) {
    // Log detalhado do erro
    error_log("[" . date('Y-m-d H:i:s') . "] Erro de conexão: " . $e->getMessage() . "\n", 3, __DIR__ . '/database_errors.log');
    
    // Mensagem amigável sem expor detalhes sensíveis
    die("Sistema temporariamente indisponível. Por favor, tente novamente mais tarde.");
}
?>
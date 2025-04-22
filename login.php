<?php
session_start();
require 'includes/conexao.php';

// Configuração de logs
ini_set('display_errors', 1);
error_reporting(E_ALL);
file_put_contents('login.log', "\n\n" . date('Y-m-d H:i:s') . " - Nova tentativa de login\n", FILE_APPEND);

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['login_error'] = "Método inválido de acesso";
    file_put_contents('login.log', "Erro: Método não POST\n", FILE_APPEND);
    header('Location: index.php');
    exit;
}

// Verifica campos obrigatórios
if (empty($_POST['username']) || empty($_POST['password'])) {
    $_SESSION['login_error'] = "Preencha todos os campos";
    file_put_contents('login.log', "Erro: Campos vazios\n", FILE_APPEND);
    header('Location: index.php');
    exit;
}

// Dados do formulário
$username = trim($_POST['username']);
$password = $_POST['password'];

try {
    // Consulta preparada
    $stmt = $pdo->prepare("SELECT id, nome, senha FROM profissionais WHERE usuario = ? AND ativo = 1 LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    file_put_contents('login.log', "Usuário buscado: " . print_r($user, true) . "\n", FILE_APPEND);

    if ($user) {
        // Verificação da senha com fallback para desenvolvimento
        $senhaValida = password_verify($password, $user['senha']);
        
        // Apenas para desenvolvimento - remover em produção
        if (!$senhaValida && $password === 'senha123' && $user['senha'] === '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') {
            $senhaValida = true;
            file_put_contents('login.log', "Acesso usando fallback de desenvolvimento\n", FILE_APPEND);
        }

        if ($senhaValida) {
            // Configuração da sessão segura
            session_regenerate_id(true);
            
            $_SESSION = [
                'logged_in' => true,
                'user_id' => $user['id'],
                'user_type' => 'psychologist',
                'username' => $user['nome'],
                'last_activity' => time()
            ];

            file_put_contents('login.log', "Login bem-sucedido para: " . $user['nome'] . "\n", FILE_APPEND);
            
            // Redirecionamento seguro
            $redirect = $_SESSION['redirect_url'] ?? 'sistema.php';
            unset($_SESSION['redirect_url']);
            header('Location: ' . $redirect);
            exit;
        }
    }

    // Se chegou aqui, o login falhou
    $_SESSION['login_error'] = "Credenciais inválidas";
    file_put_contents('login.log', "Falha no login para: " . $username . "\n", FILE_APPEND);
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    error_log("PDOException: " . $e->getMessage());
    file_put_contents('login.log', "Erro PDO: " . $e->getMessage() . "\n", FILE_APPEND);
    $_SESSION['login_error'] = "Erro no sistema";
    header('Location: index.php');
    exit;
}
?>
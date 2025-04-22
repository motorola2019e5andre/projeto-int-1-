<?php
// Configurações de erro
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    // Substitua por consulta ao banco de dados na versão final
    if ($_POST['username'] === 'cicera.santana' && $_POST['password'] === 'senha123') {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_type'] = 'psychologist';
        $_SESSION['user_id'] = 1;
        $_SESSION['last_activity'] = time();
        
        // Redireciona para a página inicial do sistema
        header('Location: sistema.php');
        exit;
    } else {
        $login_error = "Credenciais inválidas!";
    }
}

// Processamento do login
$login_error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    if ($_POST['username'] === 'cicera.santana' && $_POST['password'] === 'senha123') {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_type'] = 'psychologist';
        header('Location: sistema.php');
        exit;
    }
    $login_error = "Usuário ou senha incorretos!";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo à Clínica Mentalize</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .btn {
            background: #003cff;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 0 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #002aa3;
        }
        .login-form {
            display: none;
            margin-top: 30px;
            text-align: left;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .login-form input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="logo_clinica.png" alt="Clínica Mentalize" class="logo">
        
        <h1>Bem-vindo à Clínica Mentalize</h1>
        <p>Selecione o tipo de usuário para continuar:</p>
        
        <div class="button-group">
        <a href="sistema.php?user_type=patient#agendamento" class="btn" onclick="sessionStorage.setItem('userType', 'patient')">Sou Paciente</a>
            <button class="btn" onclick="showLoginForm()">Sou Psicólogo</button>
        </div>
        
        <div id="psychologistLogin" class="login-form">
            <h3>Acesso Restrito</h3>
            <?php if ($login_error): ?>
                <p class="error-message"><?= $login_error ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Usuário" required>
                <input type="password" name="password" placeholder="Senha" required>
                <button type="submit" class="btn">Entrar</button>
            </form>
        </div>
    </div>

    <script>
        function showLoginForm() {
            document.getElementById('psychologistLogin').style.display = 'block';
        }
    </script>
</body>
</html>
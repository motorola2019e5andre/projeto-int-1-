<?php
require_once 'auth.php';
require 'includes/conexao.php';
if (isset($_GET['user_type'])) {
    if ($_GET['user_type'] === 'patient') {
        $_SESSION['user_type'] = 'patient';
        $_SESSION['last_activity'] = time();
        header('Location: sistema.php#agendamento');
        exit;
    }
}
// Processamento do logout
if (isset($_GET['logout'])) {
    require_once 'logout.php';
    exit;
}


// Controle de autenticação
$userType = $_SESSION['user_type'] ?? null;
$isPsychologist = ($userType === 'psychologist');
$isPatient = ($userType === 'patient');

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Redirecionamento para não autorizados
if (isset($_GET['user_type']) && $_GET['user_type'] === 'psychologist' && !$isPsychologist) {
    header('Location: index.php');
    exit;
}

// Processamento do formulário de agendamento
$sucesso = $erro = '';
if (!$isPsychologist && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendar'])) {
    $dados = filter_input_array(INPUT_POST, [
        'profissional' => FILTER_VALIDATE_INT,
        'data' => FILTER_DEFAULT,
        'hora' => FILTER_DEFAULT,
        'nome' => FILTER_SANITIZE_STRING,
        'email' => FILTER_VALIDATE_EMAIL,
        'telefone' => FILTER_SANITIZE_STRING
    ]);
    
    if (!in_array(false, $dados, true)) {
        try {
            $pdo->beginTransaction();
            
            $stmtPaciente = $pdo->prepare("INSERT INTO pacientes (nome, email, telefone) VALUES (?, ?, ?)");
            $stmtPaciente->execute([$dados['nome'], $dados['email'], $dados['telefone']]);
            
            $stmtAgendamento = $pdo->prepare("INSERT INTO agendamentos (id_profissional, id_paciente, data_agendamento, hora_agendamento) VALUES (?, ?, ?, ?)");
            $stmtAgendamento->execute([$dados['profissional'], $pdo->lastInsertId(), $dados['data'], $dados['hora']]);
            
            $pdo->commit();
            $sucesso = "Consulta agendada com sucesso para {$dados['data']} às {$dados['hora']}!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $erro = "Erro ao agendar consulta: " . $e->getMessage();
        }
    } else {
        $erro = "Por favor, preencha todos os campos corretamente.";
    }
}

// Busca dados do banco
$profissionais = [];
$agendamentos = [];

try {
    $profissionais = $pdo->query("SELECT id, nome FROM profissionais")->fetchAll(PDO::FETCH_ASSOC);
    
    $agendamentos = $pdo->query("
        SELECT a.id, p.id as id_paciente, p.nome as paciente, a.data_agendamento, a.hora_agendamento 
        FROM agendamentos a
        JOIN pacientes p ON a.id_paciente = p.id
        ORDER BY a.data_agendamento DESC
        LIMIT 100
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Erro no banco de dados: " . $e->getMessage();
}
?>  <!-- Fechamento do bloco PHP -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Mentalize | Sistema de Agendamentos</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background-color: #f5f5f5; color: #333; line-height: 1.6; }
        header { background-color: #003cff; color: #fff; padding: 30px 0; text-align: center; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); position: relative; }
        header h1 { margin: 0; font-size: 2.5em; font-weight: 700; }
        header p { margin: 10px 0 0; font-size: 1.2em; }
        .logout-container { position: absolute; top: 20px; right: 20px; }
        .logout-btn { background-color: #ff0000; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: all 0.3s; }
        .logout-btn:hover { background-color: #cc0000; transform: translateY(-2px); box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        nav { background-color: #fff; padding: 15px 0; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); text-align: center; position: sticky; top: 0; z-index: 100; }
        nav a { color: #003cff; text-decoration: none; font-weight: bold; margin: 0 15px; padding: 8px 15px; border-radius: 5px; transition: all 0.3s ease; font-size: 1.1em; }
        nav a:hover { background-color: #003cff; color: white; transform: translateY(-2px); }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .card { background-color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); margin-bottom: 30px; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }
        .card h2 { color: #003cff; border-bottom: 2px solid #003cff; padding-bottom: 10px; margin-top: 0; font-size: 1.8em; }
        form { display: flex; flex-direction: column; gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-weight: bold; color: #555; font-size: 1.1em; }
        form input, form select, form textarea { padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1em; transition: border 0.3s ease; }
        form input:focus, form select:focus, form textarea:focus { border-color: #003cff; outline: none; box-shadow: 0 0 0 2px rgba(0, 60, 255, 0.2); }
        form button, .btn-primary, .btn-sm { background-color: #003cff; color: white; border: none; padding: 14px; border-radius: 5px; cursor: pointer; font-size: 1.1em; transition: all 0.3s ease; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        form button:hover, .btn-primary:hover, .btn-sm:hover { background-color: #002aa3; transform: translateY(-2px); }
        .btn-sm { padding: 8px 15px; font-size: 0.9em; letter-spacing: normal; }
        footer { background-color: #003cff; color: #fff; text-align: center; padding: 25px 0; margin-top: 50px; font-size: 1em; }
        .map-container { margin-top: 20px; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); height: 400px; }
        .map-container iframe { width: 100%; height: 100%; border: none; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; }
        .success { background-color: #dff0d8; color: #3c763d; border: 1px solid #d6e9c6; }
        .error { background-color: #f2dede; color: #a94442; border: 1px solid #ebccd1; }
        .agendamentos-list { overflow-x: auto; }
        .agendamentos-list table { width: 100%; border-collapse: collapse; margin-top: 20px; min-width: 600px; }
        .agendamentos-list th, .agendamentos-list td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        .agendamentos-list th { background-color: #003cff; color: white; font-weight: bold; }
        .agendamentos-list tr:nth-child(even) { background-color: #f9f9f9; }
        .agendamentos-list tr:hover { background-color: #f1f1f1; }
        .psychologist-badge { background-color: #003cff; color: white; padding: 3px 10px; border-radius: 20px; font-size: 0.7em; vertical-align: middle; margin-left: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        
        @media (max-width: 768px) {
            nav a { margin: 0 5px; padding: 5px 8px; font-size: 0.9em; }
            .card { padding: 20px; }
            .container { padding: 0 15px; }
            .logout-container { position: static; margin-top: 15px; }
        }
    </style>
</head>
<body>
    <header>
    <h1>Clínica Mentalize</h1>
<p>Atendimento Psicológico</p>
<?php if ($isPsychologist): ?>
    <div class="logout-container">
        <a href="?logout=1" class="logout-btn">Sair</a>
    </div>
<?php endif; ?>
</header>

<nav>
    <a href="#agendamento">Agendamentos</a>
    <?php if ($isPatient || !isset($_SESSION['logged_in'])): ?>
        <a href="#localizacao">Localização</a>
        <a href="#contato">Contato</a>
    <?php endif; ?>
</nav>
        <a href="#agendamento">Agendamentos</a>
        <?php if ($isPatient || !isset($_SESSION['logged_in'])): ?>
            <a href="#localizacao">Localização</a>
            <a href="#contato">Contato</a>
        <?php endif; ?>
    </nav>

    <div class="container">
        <section id="agendamento" class="card">
            <h2><?= $isPsychologist ? 'Agendamentos Confirmados <span class="psychologist-badge">Psicólogo</span>' : 'Agendar Consulta' ?></h2>
            
            <?php if ($sucesso || $erro): ?>
                <div class="alert <?= $sucesso ? 'success' : 'error' ?>"><?= htmlspecialchars($sucesso ?: $erro) ?></div>
            <?php endif; ?>
            
            <?php if ($isPsychologist): ?>
                <?php if (!empty($agendamentos)): ?>
                    <div class="agendamentos-list">
                        <table>
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agendamentos as $agendamento): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($agendamento['paciente']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($agendamento['data_agendamento'])) ?></td>
                                        <td><?= substr($agendamento['hora_agendamento'], 0, 5) ?></td>
                                        <td><a href="prontuario.php?id_paciente=<?= $agendamento['id_paciente'] ?>" class="btn-sm">Ver Prontuário</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Nenhum agendamento encontrado.</p>
                <?php endif; ?>
            <?php else: ?>
                <form id="formAgendamento" method="POST">
                    <div class="form-group">
                        <label for="profissional">Profissional:</label>
                        <select id="profissional" name="profissional" required>
                            <option value="">Selecione um profissional</option>
                            <?php foreach ($profissionais as $prof): ?>
                                <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="data">Data:</label>
                        <input type="date" id="data" name="data" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="hora">Hora:</label>
                        <input type="time" id="hora" name="hora" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nome">Seu Nome:</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="tel" id="telefone" name="telefone" required>
                    </div>
                    
                    <button type="submit" name="agendar" class="btn-primary">Agendar Consulta</button>
                </form>
            <?php endif; ?>
        </section>

        <?php if ($isPatient || !isset($_SESSION['logged_in'])): ?>
            <section id="localizacao" class="card">
                <h2>Onde Estamos</h2>
                <p><strong>Endereço:</strong> R. Progresso, 735 - Centro, Francisco Morato - SP, 07901-080/SP</p>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3657.1975844558826!2d-46.7434858!3d-23.2844697!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94cee7e0d3c98ae1%3A0x33f7316f02eba800!2sMentalize%20Psicologia!5e0!3m2!1spt-BR!2sbr!4v1711927371234!5m2!1spt-BR!2sbr" 
                        allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </section>
            
            <section id="contato" class="card">
                <h2>Contato</h2>
                <p><strong>Telefone:</strong> (11) 96331-3561</p>
                <p><strong>E-mail:</strong> contato@clinicamentalize.com.br</p>
            </section>
        <?php endif; ?>
    </div>

    <footer>
        <p>© 2025 Clínica Mentalize | Atendimento Psicológico - Todos os direitos reservados</p>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Rolagem automática
        if (window.location.hash === '#agendamento') {
            setTimeout(() => {
                const section = document.getElementById('agendamento');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth' });
                    <?php if ($isPsychologist): ?>
                        section.style.border = '2px solid #003cff';
                        setTimeout(() => {
                            section.style.border = 'none';
                        }, 2000);
                    <?php endif; ?>
                }
            }, 300);
        }
        
        // Validação do formulário
        const form = document.getElementById('formAgendamento');
        if (form) {
            form.addEventListener('submit', function(e) {
                const data = document.getElementById('data').value;
                if (data < new Date().toISOString().split('T')[0]) {
                    e.preventDefault();
                    alert('Não é possível agendar para datas passadas.');
                }
            });
        }
    });
    </script>
</body>
</html>
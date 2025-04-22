<?php
require_once 'auth.php';
require 'includes/conexao.php';

// Configuração de charset
header('Content-Type: text/html; charset=utf-8');
$pdo->exec("SET NAMES 'utf8mb4'");
$pdo->exec("SET CHARACTER SET utf8mb4");
$pdo->exec("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");

// Proteção CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Valida e obtém o ID do paciente
$id_paciente = filter_input(INPUT_GET, 'id_paciente', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

if (!$id_paciente) {
    $_SESSION['error'] = "ID do paciente inválido";
    header('Location: sistema.php');
    exit;
}

try {
    // Busca informações do paciente
    $stmt = $pdo->prepare("SELECT id, nome, email, telefone, data_nascimento FROM pacientes WHERE id = ?");
    $stmt->execute([$id_paciente]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        $_SESSION['error'] = "Paciente não encontrado";
        header('Location: sistema.php');
        exit;
    }

    // Busca prontuários
    $stmt = $pdo->prepare("
        SELECT p.id_prontuario, p.data_criacao, p.observacoes, p.data_atualizacao,
               ps.nome as profissional_nome
        FROM prontuarios p
        LEFT JOIN profissionais ps ON p.id_profissional = ps.id
        WHERE p.id_paciente = ? 
        ORDER BY p.data_criacao DESC
    ");
    $stmt->execute([$id_paciente]);
    $prontuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Processa o formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_prontuario'])) {
        // Verifica CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Token CSRF inválido!');
        }
        
        $observacoes = trim($_POST['observacoes'] ?? '');
        
        if (empty($observacoes)) {
            $erro = "Por favor, preencha as observações.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO prontuarios 
                (id_paciente, id_profissional, observacoes) 
                VALUES (?, ?, ?)
            ");
            
            if ($stmt->execute([$id_paciente, $_SESSION['user_id'], $observacoes])) {
                $sucesso = "Prontuário adicionado com sucesso!";
                
                // Recarrega os prontuários
                $stmt = $pdo->prepare("
                    SELECT p.id_prontuario, p.data_criacao, p.observacoes, p.data_atualizacao,
                           ps.nome as profissional_nome
                    FROM prontuarios p
                    LEFT JOIN profissionais ps ON p.id_profissional = ps.id
                    WHERE p.id_paciente = ? 
                    ORDER BY p.data_criacao DESC
                ");
                $stmt->execute([$id_paciente]);
                $prontuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $erro = "Erro ao salvar prontuário. Tente novamente.";
            }
        }
    }
} catch (PDOException $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] Erro no prontuário: " . $e->getMessage() . " em " . $e->getFile() . " linha " . $e->getLine());
    $erro = "Erro no sistema. Por favor, tente mais tarde. Código: " . uniqid();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prontuário - <?= htmlspecialchars($paciente['nome'] ?? 'Paciente', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: #f5f5f5; 
            color: #333; 
            line-height: 1.6;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: white; 
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        h1, h2 { 
            color: #003cff; 
            margin-bottom: 15px;
        }
        h1 { 
            border-bottom: 2px solid #003cff; 
            padding-bottom: 10px;
        }
        .prontuario-item { 
            margin-bottom: 20px; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            background-color: #f9f9f9;
        }
        .prontuario-header {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        .prontuario-content {
            padding: 10px;
            background: white;
            border-radius: 4px;
        }
        .prontuario-footer {
            margin-top: 10px;
            font-size: 0.85em;
            color: #666;
            text-align: right;
        }
        .prontuario-id {
            font-weight: bold;
            color: #003cff;
        }
        .prontuario-date {
            color: #555;
        }
        .prontuario-professional {
            color: #006400;
        }
        textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            font-size: 1em; 
            min-height: 150px;
            margin-bottom: 15px;
        }
        .alert { 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px; 
            font-weight: bold;
        }
        .success { 
            background-color: #dff0d8; 
            color: #3c763d; 
            border: 1px solid #d6e9c6; 
        }
        .error { 
            background-color: #f2dede; 
            color: #a94442; 
            border: 1px solid #ebccd1; 
        }
        .info {
            background-color: #e7f3fe;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .btn {
            background-color: #003cff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #002aa3;
            transform: translateY(-2px);
        }
        .voltar-btn {
            margin-top: 20px;
            display: inline-block;
        }
        .paciente-info {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .fas {
            margin-right: 5px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            h1 {
                font-size: 1.5em;
            }
            .prontuario-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Prontuário de <?= htmlspecialchars($paciente['nome'] ?? 'Paciente', ENT_QUOTES, 'UTF-8') ?></h1>
        
        <div class="paciente-info">
            <div class="info-item"><strong>ID:</strong> <?= htmlspecialchars($paciente['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            <?php if (!empty($paciente['email'])): ?>
                <div class="info-item"><strong>E-mail:</strong> <?= htmlspecialchars($paciente['email'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            
            <?php if (!empty($paciente['telefone'])): ?>
                <div class="info-item"><strong>Telefone:</strong> <?= htmlspecialchars($paciente['telefone'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            
            <?php if (!empty($paciente['data_nascimento'])): ?>
                <div class="info-item">
                    <strong>Data Nasc.:</strong> 
                    <?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?>
                    (<?= calcularIdade($paciente['data_nascimento']) ?> anos)
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($sucesso)): ?>
            <div class="alert success"><?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        
        <?php if (!empty($erro)): ?>
            <div class="alert error"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        
        <h2>Adicionar novo prontuário</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <textarea name="observacoes" placeholder="Digite as observações do prontuário aqui..." 
                     required rows="6"><?= isset($_POST['observacoes']) ? htmlspecialchars($_POST['observacoes'], ENT_QUOTES, 'UTF-8') : '' ?></textarea>
            <button type="submit" name="adicionar_prontuario" class="btn">Salvar Prontuário</button>
        </form>
        
        <h2>Histórico de Prontuários</h2>
        <?php if (!empty($prontuarios)): ?>
            <?php foreach ($prontuarios as $prontuario): ?>
                <div class="prontuario-item">
                    <div class="prontuario-header">
                        <span class="prontuario-id">#<?= htmlspecialchars($prontuario['id_prontuario'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="prontuario-date">
                            <?= date('d/m/Y H:i', strtotime($prontuario['data_criacao'])) ?>
                        </span>
                        <?php if (!empty($prontuario['profissional_nome'])): ?>
                            <span class="prontuario-professional">
                                <i class="fas fa-user-md"></i> <?= htmlspecialchars($prontuario['profissional_nome'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="prontuario-content">
                        <?= nl2br(htmlspecialchars($prontuario['observacoes'], ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                    <?php if ($prontuario['data_criacao'] != $prontuario['data_atualizacao']): ?>
                        <div class="prontuario-footer">
                            <small>
                                <i class="fas fa-sync-alt"></i> Atualizado em: 
                                <?= date('d/m/Y H:i', strtotime($prontuario['data_atualizacao'])) ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert info">
                <i class="fas fa-info-circle"></i> Nenhum prontuário registrado para este paciente.
            </div>
        <?php endif; ?>
        
        <a href="sistema.php" class="btn voltar-btn">Voltar para a lista de agendamentos</a>
    </div>

    <?php
    // Função para calcular idade
    function calcularIdade($data_nascimento) {
        $nasc = new DateTime($data_nascimento);
        $hoje = new DateTime();
        $idade = $hoje->diff($nasc);
        return $idade->y;
    }
    ?>
</body>
</html>
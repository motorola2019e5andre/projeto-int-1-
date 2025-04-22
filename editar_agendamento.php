<?php
session_start();
require 'includes/conexao.php';

// Verificar se é psicólogo
if ($_SESSION['user_type'] !== 'psychologist') {
    header('Location: index.php');
    exit;
}

// Processar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $data = filter_input(INPUT_POST, 'data');
    $hora = filter_input(INPUT_POST, 'hora');
    
    try {
        $stmt = $pdo->prepare("UPDATE agendamentos SET data_agendamento = ?, hora_agendamento = ? WHERE id = ?");
        $stmt->execute([$data, $hora, $id]);
        header('Location: sistema.php?success=Agendamento+atualizado+com+sucesso');
        exit;
    } catch (PDOException $e) {
        header('Location: sistema.php?error=Erro+ao+atualizar+agendamento');
        exit;
    }
}

// Buscar agendamento para edição
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$agendamento = $pdo->query("SELECT * FROM agendamentos WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Agendamento</title>
    <style>
        /* Estilos similares ao sistema.php */
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 20px auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; }
        button { background-color: #003cff; color: white; padding: 10px 15px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Agendamento</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $agendamento['id'] ?>">
            
            <div class="form-group">
                <label for="data">Nova Data:</label>
                <input type="date" id="data" name="data" value="<?= $agendamento['data_agendamento'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="hora">Nova Hora:</label>
                <input type="time" id="hora" name="hora" value="<?= substr($agendamento['hora_agendamento'], 0, 5) ?>" required>
            </div>
            
            <button type="submit">Salvar Alterações</button>
            <a href="sistema.php">Cancelar</a>
        </form>
    </div>
</body>
</html>
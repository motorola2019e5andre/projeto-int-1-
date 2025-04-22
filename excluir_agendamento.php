<?php
session_start();
require 'includes/conexao.php';

// Verificar se é psicólogo
if ($_SESSION['user_type'] !== 'psychologist') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

try {
    $pdo->beginTransaction();
    
    // Primeiro precisamos obter o id_paciente para possível exclusão
    $agendamento = $pdo->query("SELECT id_paciente FROM agendamentos WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
    
    // Excluir o agendamento
    $pdo->exec("DELETE FROM agendamentos WHERE id = $id");
    
    // Opcional: excluir também o paciente se não houver mais agendamentos
    $count = $pdo->query("SELECT COUNT(*) FROM agendamentos WHERE id_paciente = {$agendamento['id_paciente']}")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("DELETE FROM pacientes WHERE id = {$agendamento['id_paciente']}");
    }
    
    $pdo->commit();
    header('Location: sistema.php?success=Agendamento+excluído+com+sucesso');
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: sistema.php?error=Erro+ao+excluir+agendamento');
    exit;
}
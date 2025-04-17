<?php
namespace App\Models;

class AgendamentoModel {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function criar(array $dados): array {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO agendamentos (paciente, psicologo, data, hora, observacoes) 
                VALUES (:paciente, :psicologo, :data, :hora, :observacoes)
            ");

            $stmt->execute([
                ':paciente' => htmlspecialchars($dados['paciente']),
                ':psicologo' => (int)$dados['psicologo'],
                ':data' => $dados['data'],
                ':hora' => $dados['hora'],
                ':observacoes' => htmlspecialchars($dados['observacoes'] ?? '')
            ]);

            return ['success' => true, 'id' => $this->pdo->lastInsertId()];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Erro no banco de dados'];
        }
    }
}

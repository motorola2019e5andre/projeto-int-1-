<?php
namespace App\Controllers;

use App\Models\AgendamentoModel;

class AgendamentoController {
    private $model;

    public function __construct(AgendamentoModel $model) {
        $this->model = $model;
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->model->criar($_POST);

            if ($resultado['success']) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Agendamento realizado com sucesso!'];
                header('Location: /agendamento/sucesso');
                exit;
            }

            return require '../src/views/agendamento.php';
        }

        require '../src/views/agendamento.php';
    }
}

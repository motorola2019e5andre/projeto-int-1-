<?php
require_once 'auth.php';
require_once 'includes/conexao.php';

// Verificar se é admin
if ($_SESSION['user_type'] !== 'admin') {
    header('Location: sistema.php');
    exit;
}

// Lógica para listar, adicionar, editar profissionais
$profissionais = $pdo->query("SELECT id, nome, usuario, email FROM profissionais")->fetchAll();
?>

<!-- Formulário para adicionar/editar profissional -->
<form method="POST" action="salvar_profissional.php">
    <input type="hidden" name="id" value="<?= $profissional['id'] ?? '' ?>">
    <input type="text" name="nome" required placeholder="Nome Completo">
    <input type="text" name="usuario" required placeholder="Nome de Usuário">
    <input type="email" name="email" required placeholder="E-mail">
    <input type="password" name="senha" placeholder="Senha (deixe em branco para manter)">
    <button type="submit">Salvar</button>
</form>

<!-- Tabela listando profissionais -->
<table>
    <!-- Código para listar profissionais -->
</table>
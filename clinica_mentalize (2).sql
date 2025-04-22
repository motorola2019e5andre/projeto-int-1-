-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/04/2025 às 23:20
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `clinica_mentalize`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `id_profissional` int(11) DEFAULT NULL,
  `data_agendamento` date NOT NULL,
  `hora_agendamento` time NOT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('agendado','cancelado','realizado') DEFAULT 'agendado',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `id_paciente`, `id_profissional`, `data_agendamento`, `hora_agendamento`, `observacoes`, `status`, `created_at`) VALUES
(1, 1, 1, '2024-03-15', '14:00:00', 'Primeira consulta', 'agendado', '2025-04-17 01:14:03'),
(2, 2, 1, '2024-03-16', '10:30:00', 'Acompanhamento mensal', 'agendado', '2025-04-17 01:14:03'),
(3, 3, 1, '2025-04-17', '08:00:00', 'TESTE NUMERO 1', 'agendado', '2025-04-17 02:02:29'),
(4, 4, 1, '2025-04-24', '09:00:00', '', 'agendado', '2025-04-19 02:52:00'),
(5, 5, 1, '2025-06-15', '09:00:00', 'TESTE', 'agendado', '2025-04-19 18:12:11'),
(6, 6, 1, '2025-04-27', '10:00:00', '', 'agendado', '2025-04-19 20:03:03'),
(7, 7, 1, '2025-04-26', '11:00:00', 'fgfsfhbb ', 'agendado', '2025-04-19 20:44:38'),
(8, 8, 1, '2025-04-24', '21:33:00', NULL, 'agendado', '2025-04-19 23:33:11'),
(9, 9, 1, '2025-05-20', '08:30:00', NULL, 'agendado', '2025-04-20 19:30:10'),
(10, 10, 1, '2025-06-25', '10:00:00', NULL, 'agendado', '2025-04-21 01:19:54'),
(11, 11, 1, '2025-06-25', '09:00:00', NULL, 'agendado', '2025-04-21 18:33:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pacientes`
--

CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pacientes`
--

INSERT INTO `pacientes` (`id`, `nome`, `telefone`, `email`, `data_nascimento`, `created_at`) VALUES
(1, 'Maria Oliveira', '(11) 99999-1111', NULL, NULL, '2025-04-17 01:13:34'),
(2, 'Jo?o Santos', '(11) 99999-2222', NULL, NULL, '2025-04-17 01:13:34'),
(3, 'Teste de agendamento 1', NULL, NULL, NULL, '2025-04-17 02:02:29'),
(4, 'Teste de agendamento 2', NULL, NULL, NULL, '2025-04-19 02:52:00'),
(5, 'Teste de agendamento 3', NULL, NULL, NULL, '2025-04-19 18:12:11'),
(6, 'Teste de agendamento 0', NULL, NULL, NULL, '2025-04-19 20:03:03'),
(7, 'Teste de agendamento 30', NULL, NULL, NULL, '2025-04-19 20:44:38'),
(8, 'teste de agendamento ', '11944445555', 'testedeagendamento@gmail.com', NULL, '2025-04-19 23:33:11'),
(9, 'teste de agendamento 20/05', '11 956568789', 'testede@agendamento.com', NULL, '2025-04-20 19:30:10'),
(10, 'teste de agendaemndsfs', '11 9788885552', 'testede@agendamento.com', NULL, '2025-04-21 01:19:54'),
(11, 'teste 300', '11986665555', 'testede@agendamento.com', NULL, '2025-04-21 18:33:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `profissionais`
--

INSERT INTO `profissionais` (`id`, `nome`, `usuario`, `senha`, `especialidade`, `created_at`) VALUES
(1, 'Cícera Santana', 'cicera.santana', '$2y$10$N9qo8uLOickgx2ZMRZoMy.MrU1t7X7uS5Q6Q6b/s1YvREgQ7qRV0K', 'Psicologia Clinica', '2025-04-17 01:12:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `prontuarios`
--

CREATE TABLE `prontuarios` (
  `id_prontuario` int(11) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `id_paciente` int(11) NOT NULL,
  `id_profissional` int(11) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `prontuarios`
--

INSERT INTO `prontuarios` (`id_prontuario`, `data_criacao`, `id_paciente`, `id_profissional`, `observacoes`, `data_atualizacao`) VALUES
(1, '2025-04-20 16:45:17', 1, 1, 'Paciente relatou ansiedade generalizada e dificuldades para dormir', '2025-04-20 19:45:17'),
(2, '2025-04-20 16:45:58', 2, 1, 'Avalia??o inicial - poss?vel TDAH. Agendada bateria de testes.', '2025-04-20 19:45:58'),
(3, '2025-04-20 16:46:15', 1, 1, 'Segunda sess?o - paciente relatou melhora na qualidade do sono', '2025-04-20 19:46:15');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_profissional` (`id_profissional`);

--
-- Índices de tabela `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `profissionais`
--
ALTER TABLE `profissionais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices de tabela `prontuarios`
--
ALTER TABLE `prontuarios`
  ADD PRIMARY KEY (`id_prontuario`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_profissional` (`id_profissional`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `prontuarios`
--
ALTER TABLE `prontuarios`
  MODIFY `id_prontuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`id_profissional`) REFERENCES `profissionais` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `prontuarios`
--
ALTER TABLE `prontuarios`
  ADD CONSTRAINT `prontuarios_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`),
  ADD CONSTRAINT `prontuarios_ibfk_2` FOREIGN KEY (`id_profissional`) REFERENCES `profissionais` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

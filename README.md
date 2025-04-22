README - Clínica Mentalize
📌 Visão Geral
O Sistema Clínica Mentalize é uma aplicação web para gerenciamento de agendamentos e prontuários de uma clínica psicológica. O sistema oferece:

Área pública para pacientes agendarem consultas

Área administrativa para psicólogos acompanharem agendamentos

Gerenciamento completo de prontuários eletrônicos

Controle de profissionais e pacientes

✨ Funcionalidades Principais
Para Pacientes
Agendamento online de consultas

Visualização de localização e contato da clínica

Formulário simplificado para cadastro

Para Psicólogos
Visualização de todos os agendamentos

Acesso a prontuários dos pacientes

Adição de observações e evoluções

Controle de sessões realizadas

Administrativas
Autenticação segura de profissionais

Gerenciamento de horários

Histórico completo de atendimentos

🛠️ Tecnologias Utilizadas
Front-end: HTML5, CSS3, JavaScript

Back-end: PHP 8+

Banco de Dados: MySQL/MariaDB

Servidor: Apache/Nginx

Segurança: CSRF protection, sanitização de inputs, prepared statements

⚙️ Requisitos do Sistema
PHP 8.0 ou superior

MySQL 5.7+ ou MariaDB 10.3+

Extensão PDO habilitada

Servidor web (Apache/Nginx recomendado)

Composer (para futuras atualizações)

🚀 Instalação
Clone o repositório:

bash
git clone https://github.com/seu-usuario/clinica-mentalize.git
Configure o banco de dados:

Importe o arquivo clinica_mentalize.sql para seu MySQL

Crie um arquivo includes/conexao.php com as credenciais do seu banco:

php
<?php
$host = 'localhost';
$db   = 'clinica_mentalize';
$user = 'seu_usuario';
$pass = 'sua_senha';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
Configurações iniciais:

Credenciais padrão:

Usuário: cicera.santana

Senha: senha123

📂 Estrutura de Arquivos
clinica-mentalize/
├── includes/
│   ├── conexao.php          # Configuração do banco de dados
├── assets/                  # Arquivos estáticos
│   ├── css/
│   ├── js/
│   ├── img/
├── sistema.php              # Página principal do sistema
├── index.php                # Página inicial com login
├── auth.php                 # Controle de autenticação
├── login.php                # Processamento de login
├── logout.php               # Logout do sistema
├── editar_agendamento.php   # Edição de agendamentos
├── excluir_agendamento.php  # Exclusão de agendamentos
├── profissionais.php        # Gerenciamento de profissionais
├── prontuario.php           # Gerenciamento de prontuários
├── README.md                # Este arquivo
└── .gitignore               # Arquivos ignorados pelo Git
🔒 Segurança
O sistema inclui:

Proteção contra CSRF

Sanitização de todos os inputs

Prepared statements para consultas SQL

Controle de sessão seguro

Timeout automático após inatividade

Senhas armazenadas com bcrypt

📝 Licença
Este projeto está sob a licença MIT. Consulte o arquivo LICENSE para mais detalhes.

🤝 Contribuição
Contribuições são bem-vindas! Siga estes passos:

Faça um fork do projeto

Crie uma branch para sua feature (git checkout -b feature/AmazingFeature)

Commit suas mudanças (git commit -m 'Add some AmazingFeature')

Push para a branch (git push origin feature/AmazingFeature)

Abra um Pull Request

📧 Contato
Para dúvidas ou suporte, entre em contato:

Email: contato@clinicamentalize.com.br

Telefone: (11) 96331-3561

Clínica Mentalize © 2025 - Todos os direitos reservados


# Clínica Mentalize - Sistema de Agendamentos

Sistema completo para agendamentos de sessões psicológicas.

## Requisitos
- PHP 7.4+
- MySQL 5.7+
- Apache ou Nginx

## Instalação
```bash
git clone https://github.com/motorola2019e5andre/projeto-int-1-.git
cd clinica-mentalize
composer install
cp src/config/env.example.php .env
## ✨ Funcionalidades

- Agendamento online de consultas
- Gerenciamento de pacientes e psicólogos
- Painel administrativo
- Notificações por e-mail

## 🚀 Instalação

1. Clonar o repositório:
bash
git clone https://github.com/seu-usuario/clinica-mentalize.git
cd clinica-mentalize
Instalar dependências:

bash
Copy
composer install
Configurar ambiente:

bash
Copy
cp src/config/env.example.php .env
# Editar o .env com suas credenciais
Banco de dados:

bash
Copy
mysql -u usuario -p database < migrations/initial.sql
Acessar:

Copy
http://localhost/clinica-mentalize/public

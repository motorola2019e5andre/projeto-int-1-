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

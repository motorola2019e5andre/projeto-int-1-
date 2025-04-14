// backend/server.js
const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
const app = express();

// Configuração do MySQL (ajuste conforme seu ambiente)
const pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',      // Host do Render ou local
  user: process.env.DB_USER || 'root',           // Usuário do Render ou local
  password: process.env.DB_PASSWORD || '',       // Senha do Render ou vazia
  database: process.env.DB_NAME || 'clinica_mentalize', // Nome do BD
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

// Middlewares
app.use(cors());
app.use(express.json());

// Rota de teste
app.get('/api/test', (req, res) => {
  res.json({ message: "Backend funcionando!", status: 200 });
});

// Rota para agendamentos (POST)
app.post('/api/agendamentos', async (req, res) => {
  try {
    const { nome, data, hora, profissional, email, telefone, observacoes } = req.body;

    // Validação básica
    if (!nome || !data || !hora || !profissional || !email || !telefone) {
      return res.status(400).json({ 
        error: "Preencha todos os campos obrigatórios!",
        camposFaltantes: {
          nome: !nome,
          data: !data,
          hora: !hora,
          profissional: !profissional,
          email: !email,
          telefone: !telefone
        }
      });
    }

    // Insere no banco de dados
    const [result] = await pool.execute(
      `INSERT INTO agendamentos 
       (nome_paciente, data_consulta, hora_consulta, profissional, email, telefone, observacoes) 
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [nome, data, hora, profissional, email, telefone, observacoes || null]
    );

    res.status(201).json({ 
      success: true,
      id: result.insertId,
      message: "Agendamento salvo com sucesso!",
      data: req.body
    });

  } catch (error) {
    console.error("Erro no servidor:", error);
    res.status(500).json({ 
      error: "Erro ao processar agendamento",
      detalhes: error.message
    });
  }
});

// Rota para listar agendamentos (GET)
app.get('/api/agendamentos', async (req, res) => {
  try {
    const [agendamentos] = await pool.query(
      `SELECT * FROM agendamentos 
       ORDER BY data_consulta DESC, hora_consulta DESC`
    );
    
    res.json(agendamentos);
  } catch (error) {
    res.status(500).json({ error: "Erro ao buscar agendamentos" });
  }
});

const PORT = process.env.PORT || 10000; // Usa a porta do Render ou 10000 local
app.listen(PORT, '0.0.0.0', () => {
  console.log(`\n🚀 Servidor rodando na porta ${PORT}`);
  console.log(`📌 Rotas disponíveis:`);
  console.log(`- GET  /api/test`);
  console.log(`- GET  /api/agendamentos`);
  console.log(`- POST /api/agendamentos\n`);
});
// backend/server.js
require('dotenv').config();
const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
const app = express();

// Configuração do MySQL
const pool = mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

// Middlewares
app.use(cors({
  origin: process.env.ALLOWED_ORIGINS.split(',')
}));
app.use(express.json());

// Health Check
app.get('/health', async (req, res) => {
  try {
    await pool.query('SELECT 1');
    res.json({ status: 'healthy', database: 'connected' });
  } catch (error) {
    res.status(500).json({ status: 'unhealthy', database: 'disconnected' });
  }
});

// Rota de Agendamentos (POST)
app.post('https://projeto-int-1-1.onrender.com/', async (req, res) => {
  console.log('Dados recebidos:', req.body);
  
  try {
    const { nome_paciente, data_consulta, hora_consulta, profissional, email, telefone, observacoes } = req.body;

    // Validação
    if (!nome_paciente || !data_consulta || !hora_consulta || !profissional || !email || !telefone) {
      return res.status(400).json({ error: "Campos obrigatórios faltando" });
    }

    // Insere no banco
    const [result] = await pool.execute(
      `INSERT INTO agendamentos 
       (nome_paciente, data_consulta, hora_consulta, profissional, email, telefone, observacoes) 
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [nome_paciente, data_consulta, hora_consulta, profissional, email, telefone, observacoes || null]
    );

    res.status(201).json({ 
      success: true,
      id: result.insertId,
      message: "Agendamento realizado com sucesso!"
    });

  } catch (error) {
    console.error("Erro detalhado:", error);
    res.status(500).json({ 
      error: "Erro no servidor",
      detalhes: process.env.NODE_ENV === 'development' ? error.message : undefined
    });
  }
});

// Inicia o servidor
const PORT = https://projeto-int-1-1.onrender.com/api/agendamentos;
app.listen(PORT, () => {
  console.log(`Servidor rodando na porta ${PORT}`);
  console.log(`Banco: ${process.env.DB_HOST}/${process.env.DB_NAME}`);
});

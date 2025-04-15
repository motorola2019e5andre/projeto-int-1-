app.get('/', (req, res) => {
  res.json({ message: 'API da Clínica Mentalize' });
});

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
const allowedOrigins = [
  'https://motorola2019e5andre.github.io',
  'http://localhost:1000',
  'https://seusitefrontend.com' // ADICIONE A URL DO SEU FRONTEND AQUI
];

app.use(cors({
  origin: function (origin, callback) {
    if (!origin || allowedOrigins.includes(origin)) {
      callback(null, true);
    } else {
      callback(new Error('Origem não permitida pelo CORS'));
    }
  }
}));

app.use(express.json());

// Rota raiz
app.get('/', (req, res) => {
  res.json({ message: 'API da Clínica Mentalize' });
});

// Rota para listar agendamentos (GET)
app.get('/api/agendamentos', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM agendamentos');
    res.json(rows);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Erro ao buscar agendamentos' });
  }
});

// Rota para criar agendamentos (POST)
app.post('/api/agendamentos', async (req, res) => {
  console.log('Dados recebidos:', req.body);

  try {
    const { nome_paciente, data_consulta, hora_consulta, profissional, email, telefone, observacoes } = req.body;

    if (!nome_paciente || !data_consulta || !hora_consulta || !profissional || !email || !telefone) {
      return res.status(400).json({ error: "Campos obrigatórios faltando" });
    }

    const [result] = await pool.execute(
      'INSERT INTO agendamentos (nome_paciente, data_consulta, hora_consulta, profissional, email, telefone, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?)',
      [nome_paciente, data_consulta, hora_consulta, profissional, email, telefone, observacoes || null]
    );

    res.status(201).json({ message: 'Agendamento criado com sucesso!', agendamentoId: result.insertId });
  } catch (error) {
    console.error('Erro ao salvar agendamento:', error);
    res.status(500).json({ error: 'Erro interno do servidor' });
  }
});

// Inicialização do servidor
const PORT = process.env.PORT || 1000;
app.listen(PORT, () => {
  console.log(`Servidor rodando na porta ${PORT}`);
});

require('dotenv').config();
const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');

// 1. Configuração inicial do servidor
const app = express();
const PORT = process.env.PORT || 10000;

// 2. Configuração do pool de conexões MySQL
const pool = mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    ssl: process.env.DB_SSL ? { rejectUnauthorized: false } : null,
    connectTimeout: 10000 // 10 segundos de timeout
});

// 3. Middlewares
app.use(cors({
    origin: [
        'https://motorola2019e5andre.github.io',
        'http://localhost:1000'
    ],
    methods: ['GET', 'POST', 'OPTIONS'],
    allowedHeaders: ['Content-Type'],
    credentials: true
}));

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// 4. Middleware de logs para debug
app.use((req, res, next) => {
    console.log(`[${new Date().toISOString()}] ${req.method} ${req.url}`);
    next();
});

// 5. Rotas

// Rota de status
app.get('/', (req, res) => {
    res.json({
        status: 'API Online',
        message: 'Bem-vindo ao sistema de agendamentos da Clínica Mentalize',
        environment: process.env.NODE_ENV || 'development',
        timestamp: new Date().toISOString()
    });
});

// Rota de listagem de agendamentos
app.get('/api/agendamentos', async (req, res) => {
    try {
        const [rows] = await pool.query(`
            SELECT 
                id, 
                nome_paciente, 
                DATE_FORMAT(data_consulta, '%Y-%m-%d') as data_consulta,
                TIME_FORMAT(hora_consulta, '%H:%i') as hora_consulta,
                profissional,
                email,
                telefone,
                observacoes,
                created_at
            FROM agendamentos
            ORDER BY data_consulta DESC, hora_consulta DESC
            LIMIT 50
        `);

        res.json({
            success: true,
            count: rows.length,
            data: rows
        });
    } catch (error) {
        console.error('Erro ao buscar agendamentos:', error);
        res.status(500).json({
            success: false,
            error: 'Erro interno ao buscar agendamentos',
            details: process.env.NODE_ENV === 'development' ? error.message : null
        });
    }
});

// Rota de criação de agendamentos
app.post('/api/agendamentos', async (req, res) => {
    console.log('Dados recebidos:', req.body);

    try {
        const { 
            nome_paciente, 
            data_consulta, 
            hora_consulta, 
            profissional, 
            email, 
            telefone, 
            observacoes 
        } = req.body;

        // Validação dos campos
        const errors = [];
        
        if (!nome_paciente?.trim()) errors.push('Nome do paciente é obrigatório');
        if (!data_consulta) errors.push('Data da consulta é obrigatória');
        if (!hora_consulta) errors.push('Hora da consulta é obrigatória');
        if (!profissional?.trim()) errors.push('Profissional é obrigatório');
        if (!email?.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) errors.push('E-mail inválido');
        if (!telefone?.match(/^\d{10,11}$/)) errors.push('Telefone inválido (deve ter 10 ou 11 dígitos)');

        if (errors.length > 0) {
            return res.status(400).json({ 
                success: false,
                error: 'Dados inválidos',
                details: errors
            });
        }

        // Formatação dos dados
        const agendamentoData = {
            nome_paciente: nome_paciente.trim(),
            data_consulta: data_consulta,
            hora_consulta: hora_consulta,
            profissional: profissional.trim(),
            email: email.toLowerCase().trim(),
            telefone: telefone.replace(/\D/g, ''),
            observacoes: observacoes?.trim() || null
        };

        // Inserção no banco de dados
        const connection = await pool.getConnection();
        
        try {
            const [result] = await connection.execute(
                `INSERT INTO agendamentos 
                (nome_paciente, data_consulta, hora_consulta, profissional, email, telefone, observacoes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)`,
                [
                    agendamentoData.nome_paciente,
                    agendamentoData.data_consulta,
                    agendamentoData.hora_consulta,
                    agendamentoData.profissional,
                    agendamentoData.email,
                    agendamentoData.telefone,
                    agendamentoData.observacoes
                ]
            );

            res.status(201).json({
                success: true,
                message: 'Agendamento criado com sucesso',
                agendamentoId: result.insertId,
                data: {
                    paciente: agendamentoData.nome_paciente,
                    data: agendamentoData.data_consulta,
                    hora: agendamentoData.hora_consulta,
                    profissional: agendamentoData.profissional
                }
            });
        } finally {
            connection.release();
        }

    } catch (error) {
        console.error('Erro ao processar agendamento:', {
            message: error.message,
            sqlMessage: error.sqlMessage,
            stack: error.stack
        });

        res.status(500).json({
            success: false,
            error: 'Erro interno ao processar agendamento',
            details: process.env.NODE_ENV === 'development' ? error.message : null
        });
    }
});

// Middleware de tratamento de erros
app.use((err, req, res, next) => {
    console.error('Erro não tratado:', err.stack);
    res.status(500).json({
        success: false,
        error: 'Erro interno no servidor',
        details: process.env.NODE_ENV === 'development' ? err.message : null
    });
});

// 6. Inicialização do servidor
app.listen(PORT, () => {
    console.log(`\n🚀 Servidor iniciado na porta ${PORT}`);
    console.log('📅', new Date().toLocaleString('pt-BR'));
    console.log('🔍 Variáveis de ambiente:');
    console.log('- DB_HOST:', process.env.DB_HOST ? '****' : 'não definido');
    console.log('- DB_USER:', process.env.DB_USER ? '****' : 'não definido');
    console.log('- DB_NAME:', process.env.DB_NAME || 'não definido');
    console.log('- PORT:', PORT);
    console.log('- NODE_ENV:', process.env.NODE_ENV || 'development');
    console.log('\n📌 Endpoints disponíveis:');
    console.log(`- GET  / → Status da API`);
    console.log(`- GET  /api/agendamentos → Listar agendamentos`);
    console.log(`- POST /api/agendamentos → Criar agendamento\n`);
});

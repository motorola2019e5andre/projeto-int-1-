/ backend/server.js
+const express = require('express');
+const mysql = require('mysql2/promise');
+const cors = require('cors');
+const app = express();
+
+// ConfiguraÃ§Ã£o do MySQL (ajuste conforme seu ambiente)
+const pool = mysql.createPool({
+  host: 'localhost',
+  user: 'root',       // UsuÃ¡rio padrÃ£o do XAMPP
+  password: '',       // Deixe vazio se nÃ£o tiver senha
+  database: 'clinica_mentalize',
+  waitForConnections: true,
+  connectionLimit: 10,
+  queueLimit: 0
+});
+
+// Middlewares
+app.use(cors());
+app.use(express.json());
+
+// Rota de teste
+app.get('/api/test', (req, res) => {
+  res.json({ message: "Backend funcionando!", status: 200 });
+});
+
+// Rota para agendamentos (POST)
+app.post('/api/agendamentos', async (req, res) => {
+  try {
+    const { nome, data, hora, profissional, email, telefone, observacoes } = req.body;
+
+    // ValidaÃ§Ã£o bÃ¡sica
+    if (!nome || !data || !hora || !profissional || !email || !telefone) {
+      return res.status(400).json({ 
+        error: "Preencha todos os campos obrigatÃ³rios!",
+        camposFaltantes: {
+          nome: !nome,
+          data: !data,
+          hora: !hora,
+          profissional: !profissional,
+          email: !email,
+          telefone: !telefone
+        }
+      });
+    }
+
+    // Insere no banco de dados
+    const [result] = await pool.execute(
+      `INSERT INTO agendamentos 
+       (nome_paciente, data_consulta, hora_consulta, profissional, email, telefone, observacoes) 
+       VALUES (?, ?, ?, ?, ?, ?, ?)`,
+      [nome, data, hora, profissional, email, telefone, observacoes || null]
+    );
+
+    res.status(201).json({ 
+      success: true,
+      id: result.insertId,
+      message: "Agendamento salvo com sucesso!",
+      data: req.body
+    });
+
+  } catch (error) {
+    console.error("Erro no servidor:", error);
+    res.status(500).json({ 
+      error: "Erro ao processar agendamento",
+      detalhes: error.message
+    });
+  }
+});
+
+// Rota para listar agendamentos (GET)
+app.get('/api/agendamentos', async (req, res) => {
+  try {
+    const [agendamentos] = await pool.query(
+      `SELECT * FROM agendamentos 
+       ORDER BY data_consulta DESC, hora_consulta DESC`
+    );
+    
+    res.json(agendamentos);
+  } catch (error) {
+    res.status(500).json({ error: "Erro ao buscar agendamentos" });
+  }
+});
+
+// Inicia o servidor
+const PORT = 3000;
+app.listen(PORT, () => {
+  console.log(`\nðŸš€ Servidor rodando em http://localhost:${PORT}`);
+  console.log(`ðŸ“ Teste a rota: http://localhost:${PORT}/api/test`);
+  console.log(`ðŸ“Œ Pronto para receber agendamentos!\n`);
+});

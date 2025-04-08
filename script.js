// Armazenamento de agendamentos
let agendamentos = JSON.parse(localStorage.getItem('agendamentos')) || [];

document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const formAgendamento = document.getElementById('formAgendamento');
    const corpoTabela = document.getElementById('corpoTabela');
    const agendamentoSucesso = document.getElementById('agendamentoSucesso');
    const agendamentoErro = document.getElementById('agendamentoErro');
    const modal = document.getElementById('loginModal');
    const loginLink = document.getElementById('loginLink');
    const logoutBtn = document.getElementById('logoutBtn');
    const closeBtn = document.querySelector('.close');
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('errorMessage');
    const areaPublica = document.getElementById('areaPublica');
    const areaProfissional = document.getElementById('areaProfissional');
    const userGreeting = document.getElementById('userGreeting');
    const profissionalNome = document.getElementById('profissionalNome');

    // Credenciais válidas
    const validUsers = {
        'cicera.santana': { password: 'senha123', nome: 'Dra. Cicera Santana' },
    };

    // Verificar login ao carregar
    checkLogin();

    // Evento de login
    loginLink.addEventListener('click', function(e) {
        e.preventDefault();
        modal.style.display = 'block';
    });

    // Fechar modal
    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    // Logout
    logoutBtn.addEventListener('click', function() {
        localStorage.removeItem('loggedInUser');
        checkLogin();
    });

    // Validação de login
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        if (validUsers[username] && validUsers[username].password === password) {
            localStorage.setItem('loggedInUser', JSON.stringify({
                username: username,
                nome: validUsers[username].nome
            }));
            closeModal();
            checkLogin();
        } else {
            errorMessage.textContent = 'Usuário ou senha incorretos!';
        }
    });

    // Formulário de agendamento
    formAgendamento.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const nomePaciente = document.getElementById('nomePaciente').value;
        const dataConsulta = document.getElementById('dataConsulta').value;
        const horaConsulta = document.getElementById('horaConsulta').value;
        const profissional = document.getElementById('profissional').value;
        const observacoes = document.getElementById('observacoes').value;
        
        // Validar data/hora
        const dataAtual = new Date();
        const dataAgendamento = new Date(dataConsulta + 'T' + horaConsulta);
        
        if (dataAgendamento < dataAtual) {
            mostrarErro('Não é possível agendar para datas/horários passados.');
            return;
        }
        
        // Criar novo agendamento
        const novoAgendamento = {
            id: Date.now(),
            paciente: nomePaciente,
            data: dataConsulta,
            hora: horaConsulta,
            profissional: profissional,
            observacoes: observacoes,
            status: 'agendado'
        };
        
        // Adicionar e salvar
        agendamentos.push(novoAgendamento);
        localStorage.setItem('agendamentos', JSON.stringify(agendamentos));
        
        // Feedback
        mostrarSucesso();
        formAgendamento.reset();
        
        // Atualizar tabela se visível
        if (areaProfissional.style.display === 'block') {
            atualizarTabela();
        }
    });

    function mostrarSucesso() {
        agendamentoSucesso.style.display = 'block';
        agendamentoErro.style.display = 'none';
        setTimeout(() => {
            agendamentoSucesso.style.display = 'none';
        }, 3000);
    }
    
    function mostrarErro(mensagem) {
        agendamentoErro.textContent = mensagem;
        agendamentoErro.style.display = 'block';
        agendamentoSucesso.style.display = 'none';
    }
    
    function atualizarTabela() {
        corpoTabela.innerHTML = '';
        
        // Ordenar por data/hora
        const agendamentosOrdenados = [...agendamentos].sort((a, b) => {
            return new Date(a.data + 'T' + a.hora) - new Date(b.data + 'T' + b.hora);
        });
        
        agendamentosOrdenados.forEach(agendamento => {
            const tr = document.createElement('tr');
            
            // Formatar data (DD/MM/AAAA)
            const [ano, mes, dia] = agendamento.data.split('-');
            const dataFormatada = `${dia}/${mes}/${ano}`;
            
            tr.innerHTML = `
                <td>${agendamento.paciente}</td>
                <td>${dataFormatada}</td>
                <td>${agendamento.hora}</td>
                <td>${agendamento.profissional === 'psicologo1' ? 'Dra. Cicera Santana' : agendamento.profissional}</td>
                <td>${agendamento.observacoes || '-'}</td>
                <td>
                    <button class="cancelar-btn" data-id="${agendamento.id}">Cancelar</button>
                </td>
            `;
            
            corpoTabela.appendChild(tr);
        });
        
        // Eventos para botões de cancelar
        document.querySelectorAll('.cancelar-btn').forEach(botao => {
            botao.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                cancelarAgendamento(id);
            });
        });
    }
    
    function cancelarAgendamento(id) {
        if (confirm('Tem certeza que deseja cancelar este agendamento?')) {
            agendamentos = agendamentos.filter(ag => ag.id !== id);
            localStorage.setItem('agendamentos', JSON.stringify(agendamentos));
            atualizarTabela();
        }
    }
    
    function checkLogin() {
        const user = JSON.parse(localStorage.getItem('loggedInUser'));
        if (user) {
            loginLink.style.display = 'none';
            logoutBtn.style.display = 'inline-block';
            userGreeting.textContent = `Olá, ${user.nome.split(' ')[1]}!`;
            profissionalNome.textContent = user.nome;
            areaPublica.style.display = 'none';
            areaProfissional.style.display = 'block';
            atualizarTabela();
        } else {
            loginLink.style.display = 'inline-block';
            logoutBtn.style.display = 'none';
            userGreeting.textContent = '';
            areaPublica.style.display = 'block';
            areaProfissional.style.display = 'none';
        }
    }
    
    function closeModal() {
        modal.style.display = 'none';
        errorMessage.textContent = '';
        loginForm.reset();
    }
});
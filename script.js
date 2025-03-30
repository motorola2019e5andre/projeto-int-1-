// Banco de dados simulado
const database = {
    profissionais: {
        'ana.silva': {
            nome: 'Dra. Ana Silva',
            senha: 'senha123',
            especialidade: 'Psicologia Clínica',
            foto: 'https://img.freepik.com/fotos-gratis/medica-psiquiatra-ou-psicologa-com-tablet-e-prancheta_23-2149091111.jpg'
        },
        'carlos.mendes': {
            nome: 'Dr. Carlos Mendes',
            senha: 'clinica2024',
            especialidade: 'Neuropsicologia',
            foto: 'https://img.freepik.com/fotos-gratis/homem-bonito-e-sorridente-com-camisa-branca_23-2148288210.jpg'
        }
    },
    // [Adicione o restante do seu banco de dados simulado]
};

// Estado da aplicação
let currentUser = null;
let pendingAction = null;

// Funções
function updateUI() {
    // [Implemente toda a lógica de atualização da interface]
}

function showConfirm(title, message, action) {
    // [Implemente a lógica do modal de confirmação]
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // [Adicione todos os event listeners do seu código original]
    
    // Exemplo:
    document.getElementById('loginBtn').addEventListener('click', function() {
        document.getElementById('loginModal').style.display = 'block';
    });
    
    // [Continue com os demais listeners...]
});

// [Adicione todas as outras funções auxiliares]
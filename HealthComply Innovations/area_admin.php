<?php
// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

// Verificar se a conexão foi estabelecida
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start(); // Inicia a sessão

// Verifique se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
    echo "Erro: Você não está logado como administrador.";
    exit;
}

// Verifique se o ID do administrador está definido
$admin_id = isset($_SESSION["admin_id"]) ? $_SESSION["admin_id"] : 'ID não disponível';

// Obter o ID do usuário da sessão
$id_usuario = $_SESSION["id_usuario"];

// Função para verificar se o usuário já existe
function usuario_ja_existe($username, $conn) {
    $query = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Função para criar um novo usuário
function criar_usuario($username, $password, $tipo_usuario, $nome, $email, $telefone, $conn) {
    if (usuario_ja_existe($username, $conn)) {
        return "Usuário já existe";
    }
    $query = "INSERT INTO usuarios (username, password, tipo_usuario, nome, email, telefone) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $username, $password, $tipo_usuario, $nome, $email, $telefone);
    $stmt->execute();
    $stmt->close();
    return $conn->insert_id; // Retorna o ID do usuário recém-criado
}

// Adicionar médico
if (isset($_POST["add_medico"])) {
    $username_medico = $_POST["username_medico"];
    $password_medico = $_POST["password_medico"];
    $confirm_password_medico = $_POST["confirm_password_medico"];
    $tipo_usuario_medico = "medico";
    $nome_medico = $_POST["nome_medico"];
    $email_medico = $_POST["email_medico"];
    $telefone_medico = $_POST["telefone_medico"] ?? '';
    $especialidade_medico = $_POST["especialidade_medico"];
    $crm_medico = $_POST["crm_medico"];

    // Verificar se as senhas correspondem
    if ($password_medico !== $confirm_password_medico) {
        echo "As senhas não correspondem.";
    } elseif (!empty($telefone_medico)) {
        // Adicionar usuário à tabela usuarios
        $id_usuario = criar_usuario($username_medico, $password_medico, $tipo_usuario_medico, $nome_medico, $email_medico, $telefone_medico, $conn);
        
        if (is_numeric($id_usuario)) {
            // Adicionar médico à tabela medicos
            $query = "INSERT INTO medicos (id_usuario, nome, especialidade, crm, email, telefone) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isssss", $id_usuario, $nome_medico, $especialidade_medico, $crm_medico, $email_medico, $telefone_medico);
            $stmt->execute();
            $stmt->close();
            echo "Médico adicionado com sucesso!";
        } else {
            echo $id_usuario; // Mensagem de erro se o usuário já existe
        }
    } else {
        echo "Por favor, preencha o campo telefone.";
    }
}

// Adicionar auditor
if (isset($_POST["add_auditor"])) {
    $username_auditor = $_POST["username_auditor"];
    $password_auditor = $_POST["password_auditor"];
    $confirm_password_auditor = $_POST["confirm_password_auditor"];
    $tipo_usuario_auditor = "auditor";
    $nome_auditor = $_POST["nome_auditor"];
    $email_auditor = $_POST["email_auditor"];
    $telefone_auditor = $_POST["telefone_auditor"] ?? '';
    $especialidade_auditor = $_POST["especialidade_auditor"];
    $crm_auditor = $_POST["crm_auditor"];

    // Verificar se as senhas correspondem
    if ($password_auditor !== $confirm_password_auditor) {
        echo "As senhas não correspondem.";
    } else {
        $resultado = criar_usuario($username_auditor, $password_auditor, $tipo_usuario_auditor, $nome_auditor, $email_auditor, $telefone_auditor, $conn );
        echo $resultado;
    }
}
// Adicionar enfermeira
if (isset($_POST["add_enfermeira"])) {
    $username_enfermeira = $_POST["username_enfermeira"];
    $password_enfermeira = $_POST["password_enfermeira"];
    $confirm_password_enfermeira = $_POST["confirm_password_enfermeira"];
    $tipo_usuario_enfermeira = "enfermeira";
    $nome_enfermeira = $_POST["nome_enfermeira"];
    $email_enfermeira = $_POST["email_enfermeira"];
    $telefone_enfermeira = $_POST["telefone_enfermeira"];

    // Verificar se as senhas correspondem
    if ($password_enfermeira !== $confirm_password_enfermeira) {
        echo "As senhas não correspondem.";
    } else {
        // Adicionar usuário à tabela usuarios
        $id_usuario = criar_usuario($username_enfermeira, $password_enfermeira, $tipo_usuario_enfermeira, $nome_enfermeira, $email_enfermeira, $telefone_enfermeira, $conn);
        
        if (is_numeric($id_usuario)) {
            echo "Enfermeira adicionada com sucesso!";
        } else {
            echo $id_usuario; // Mensagem de erro se o usuário já existe
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Usuário</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login2.css">
    <style>
        body {
            background-color: green; /* Altera a cor de fundo para verde */
            color: white; /* Altera a cor do texto para branco para melhor contraste */
            display: flex; /* Usando flexbox para centralizar */
            justify-content: center; /* Centraliza horizontalmente */
            align-items: center; /* Centraliza verticalmente */
            height: 100vh; /* Ocupa toda a altura da janela */
            text-align: center; /* Centraliza o texto dentro do contêiner */
        }
        .FormsBox {
            background-color: rgba(255, 255, 255, 0.8); /* Fundo branco semitransparente */
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px; /* Largura do formulário */
        }
        .user-details {
            display: none; /* Oculta inicialmente */
        }
    </style>
</head>
<body>
    <div class="FormsBox">
        <form action="" method="post" id="userForm">
            <h2>Adicionar Usuário</h2>
            <label for="tipo_usuario">Tipo de Usuário:</label>
            <select id="tipo_usuario" name="tipo_usuario" required>
                <option value="">Selecione um tipo de usuário</option>
                <option value="medico">Médico</option>
                <option value="auditor">Auditor</option>
                <option value="enfermeira">Enfermeira</option>
            </select><br><br>

            <div id="userDetails" class="user-details">
                <div id="medicoDetails" class="user-type" style="display: none;">
                    <label for="crm">CRM:</label>
                    <input type="text" id="crm" name="crm"><br><br>
                </div>

                <div id="auditorDetails" class="user-type" style="display: none;">
                    <label for="area_auditoria">Área de Auditoria:</label>
                    <input type="text" id="area_auditoria" name="area_auditoria"><br><br>
                </div>

                <div id="enfermeiraDetails" class="user-type" style="display: none;">
                    <label for="coren">COREN:</label>
                    <input type="text" id="coren" name="coren"><br><br>
                </div>

                <label for="username">Nome de usuário:</label>
                <input type="text" id="username" name="username" required><br><br>

                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required><br><br>

                <label for="confirm_password">Confirme a Senha:</label>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>
                <span id="senha_mensagem" style="color: red;"></span> <!-- Mensagem de verificação -->

                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="telefone">Telefone:</label>
                <input type="tel" id="telefone" name="telefone" required><br><br>
            </div>

            <input type="submit" name="add_usuario" class="login-btn" value="Adicionar Usuário">
        </form>
    </div>

    <script>
        // Função para verificar se as senhas são iguais
        document.getElementById('confirm_password').addEventListener('input', function() {
            var password = document.getElementById('password').value;
            var confirmPassword = this.value;
            var mensagem = document.getElementById('senha_mensagem');

            if (password === confirmPassword) {
                mensagem.textContent = "As senhas são iguais.";
                mensagem.style.color = "green"; // Mensagem em verde
            } else {
                mensagem.textContent = "As senhas não correspondem.";
                mensagem.style.color = "red"; // Mensagem em vermelho
            }
        });

        // Mostrar/ocultar campos com base no tipo de usuário selecionado
        document.getElementById('tipo_usuario').addEventListener('change', function() {
            var tipo = this.value;
            var userDetails = document.getElementById('userDetails');
            userDetails.style.display = 'block'; // Exibe os detalhes do usuário

            // Oculta todos os detalhes de tipo de usuário
            document.getElementById('medicoDetails').style.display = 'none';
            document.getElementById('auditorDetails').style.display = 'none';
            document.getElementById('enfermeiraDetails').style.display = 'none';

            // Exibe os campos correspondentes ao tipo de usuário selecionado
            if (tipo === 'medico') {
                document.getElementById('medicoDetails').style.display = 'block';
            } else if (tipo === 'auditor') {
                document.getElementById('auditorDetails').style.display = 'block';
            } else if (tipo === 'enfermeira') {
                document.getElementById('enfermeiraDetails').style.display = 'block';
            }
        });
    </script>
</body>
</html>
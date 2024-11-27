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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Área do Administrador</title>
</head>
<body>
<h1>Bem-vindo à Área do Administrador</h1>
<p>ID do Usuário Conectado: <?php echo htmlspecialchars($admin_id); ?></p>
    <h1>Adicionar Médico ou Auditor</h1>
    <div class="FormsBox-container">
        <div class="FormsBox">
            <form action="" method="post">
                <h2>Adicionar Médico</h2>
                <label for="username_medico">Nome de usuario do Médico:</label>
                <input type="text" id="username_medico" name="username_medico" required><br><br>
                <label for="password_medico">Senha do Médico:</label>
                <input type="password" id="password_medico" name="password_medico" required><br><br>
                <label for="confirm_password_medico">Confirme a Senha:</label>
                <input type="password" id="confirm_password_medico" name="confirm_password_medico" required><br><br>
                <label for="nome_medico">Nome do Médico:</label>
                <input type="text" id="nome_medico" name="nome_medico" required><br><br>
                <label for="email_medico">Email do Médico:</label>
                <input type="email" id="email_medico" name="email_medico" required><br><br>
                <label for="telefone_medico">Telefone do Médico:</label>
                <input type="tel" id="telefone_medico" name="telefone_medico" required><br><br>
                <label for="especialidade_medico">Especialidade do Médico:</label>
                <input type="text" id="especialidade_medico" name="especialidade_medico" required><br><br>
                <label for="crm_medico">CRM do Médico:</label>
                <input type="text" id="crm_medico" name="crm_medico" required><br><br>
                <input type="submit" name="add_medico" class="login-btn" value="Adicionar Médico">
            </form>
        </div>

        <div class="FormsBox">
            <form action="" method="post">
                <h2>Adicionar Auditor</h2>
                <label for="username_auditor">Nome de usuario do Auditor:</label>
                <input type="text" id="username_auditor" name="username_auditor" required><br><br>
                <label for="password_auditor">Senha do Auditor:</label>
                <input type="password" id="password_auditor" name="password_auditor" required><br><br>
                <label for="confirm_password_auditor">Confirme a Senha:</label>
                <input type="password" id="confirm_password_auditor" name="confirm_password_auditor" required><br><br>
                <label for="nome_auditor">Nome do Auditor:</label>
                <input type="text" id="nome_auditor" name="nome_auditor" required><br><br>
                <label for="email_auditor">Email do Auditor:</label>
                <input type="email" id="email_auditor" name="email_auditor" required><br><br>
                <label for="telefone_auditor">Telefone do Auditor:</label>
                <input type="tel" id="telefone_auditor" name="telefone_auditor" required><br><br>
                <label for="especialidade_auditor">Especialidade do Auditor:</label>
                <input type="text" id="especialidade_auditor" name="especialidade_auditor" required><br><br>
                <label for="crm_auditor">CRM do Auditor:</label>
                <input type="text" id="crm_auditor" name="crm_auditor" required><br><br>
                <input type="submit" name="add_auditor" class="login-btn" value="Adicionar Auditor">
            </form>
        </div>
    </div>

    <div class="FormsBox">
    <form action="" method="post">
        <h2>Adicionar Enfermeira</h2>
        <label for="username_enfermeira">Nome de usuário da Enfermeira:</label>
        <input type="text" id="username_enfermeira" name="username_enfermeira" required><br><br>
        <label for="password_enfermeira">Senha da Enfermeira:</label>
        <input type="password" id="password_enfermeira" name="password_enfermeira" required><br><br>
        <label for="confirm_password_enfermeira">Confirme a Senha:</label>
        <input type="password" id="confirm_password_enfermeira" name="confirm_password_enfermeira" required><br><br>
        <label for="nome_enfermeira">Nome da Enfermeira:</label>
        <input type="text" id="nome_enfermeira" name="nome_enfermeira" required><br><br>
        <label for="email_enfermeira">Email da Enfermeira:</label>
        <input type="email" id="email_enfermeira" name="email_enfermeira" required><br><br>
        <label for="telefone_enfermeira">Telefone da Enfermeira:</label>
        <input type="tel" id="telefone_enfermeira" name="telefone_enfermeira" required><br><br>
        <input type="submit" name="add_enfermeira" class="login-btn" value="Adicionar Enfermeira">
    </form>
</div>

    <div class="back-button">
        <a href="index.php" class="login-btn">Voltar à Página de Login</a>
    </div>
    <script>
    // Função para verificar se as senhas são iguais para Médico
    document.getElementById('confirm_password_medico').addEventListener('input', function() {
        var password = document.getElementById('password_medico').value;
        var confirmPassword = this.value;
        var mensagem = document.getElementById('senha_mensagem_medico');

        if (password === confirmPassword) {
            mensagem.textContent = "As senhas são iguais.";
            mensagem.style.color = "green"; 
        } else {
            mensagem.textContent = "As senhas não correspondem.";
            mensagem.style.color = "red"; 
        }
    });

    // Função para verificar se as senhas são iguais para Auditor
    document.getElementById('confirm_password_auditor').addEventListener('input', function() {
        var password = document.getElementById('password_auditor').value;
        var confirmPassword = this.value;
        var mensagem = document.getElementById('senha_mensagem_auditor');

        if (password === confirmPassword) {
            mensagem.textContent = "As senhas são iguais.";
            mensagem.style.color = "green"; 
        } else {
            mensagem.textContent = "As senhas não correspondem.";
            mensagem.style.color = "red"; 
        }
    });

    // Função para verificar se as senhas são iguais para Enfermeira
    document.getElementById('confirm_password_enfermeira').addEventListener('input', function() {
        var password = document.getElementById('password_enfermeira').value;
        var confirmPassword = this.value;
        var mensagem = document.getElementById('senha_mensagem_enfermeira');

        if (password === confirmPassword) {
            mensagem.textContent = "As senhas são iguais.";
            mensagem.style.color = "green"; 
        } else {
            mensagem.textContent = "As senhas não correspondem.";
            mensagem.style.color = "red"; 
        }
    });
</script>
</body>
</html>
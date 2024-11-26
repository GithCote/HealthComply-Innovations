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
<p>ID do Usuário Conectado: <?php echo htmlspecialchars($id_usuario); ?></p>
    <h1>Adicionar Médico ou Auditor</h1>
    <div class="FormsBox-container">
        <div class="FormsBox">
            <form action="" method="post">
                <h2>Adicionar Médico</h2>
                <label for="username_medico">Username do Médico:</label>
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
                <label for="username_auditor">Username do Auditor:</label>
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

    <div class="back-button">
        <a href="index.php" class="login-btn">Voltar à Página de Login</a>
    </div>
</body>
</html>
<?php
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start(); 
// Verifique se o usuário está logado
if (!isset($_SESSION["id_usuario"])) {
    echo "Erro: Você não está logado como administrador.";
    exit;
} 

$id_usuario = $_SESSION["id_usuario"];

function usuario_ja_existe($username, $conn) {
    $query = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function criar_usuario($username, $password, $tipo_usuario, $nome, $email, $telefone, $conn) {
    if (usuario_ja_existe($username, $conn)) {
        return "Usuário já existe";
    }
    $query = "INSERT INTO usuarios (username, password, tipo_usuario, nome, email, telefone) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $username, $password, $tipo_usuario, $nome, $email, $telefone);
    $stmt->execute();
    $stmt->close();
    return $conn->insert_id; 
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
   
    if ($password_medico !== $confirm_password_medico) {
        echo "As senhas não correspondem.";
    } elseif (!empty($telefone_medico)) {
        
        $id_usuario = criar_usuario($username_medico, $password_medico, $tipo_usuario_medico, $nome_medico, $email_medico, $telefone_medico, $conn);
        
        if (is_numeric($id_usuario)) {
            
            $query = "INSERT INTO medicos (id_usuario, nome, especialidade, crm, email, telefone) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isssss", $id_usuario, $nome_medico, $especialidade_medico, $crm_medico, $email_medico, $telefone_medico);
            $stmt->execute();
            $stmt->close();
            echo "Médico adicionado com sucesso!";
        } else {
            echo $id_usuario; 
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
   
    if ($password_enfermeira !== $confirm_password_enfermeira) {
        echo "As senhas não correspondem.";
    } else {
        
        $id_usuario = criar_usuario($username_enfermeira, $password_enfermeira, $tipo_usuario_enfermeira, $nome_enfermeira, $email_enfermeira, $telefone_enfermeira, $conn);
        
        if (is_numeric($id_usuario)) {
            echo "Enfermeira adicionada com sucesso!";
        } else {
            echo $id_usuario; 
        }
    }
}

// Adicionar farmaceutico
if (isset($_POST["add_farmaceutico"])) {
    $username_farmaceutico = $_POST["username_farmaceutico"];
    $password_farmaceutico = $_POST["password_farmaceutico"];
    $confirm_password_farmaceutico = $_POST["confirm_password_farmaceutico"];
    $tipo_usuario_farmaceutico = "farmaceutico";
    $nome_farmaceutico = $_POST["nome_farmaceutico"];
    $email_farmaceutico = $_POST["email_farmaceutico"];
    $telefone_farmaceutico = $_POST["telefone_farmaceutico"];
    
    if ($password_farmaceutico !== $confirm_password_farmaceutico) {
        echo "As senhas não correspondem.";
    } else {
        
        $id_usuario = criar_usuario($username_farmaceutico, $password_farmaceutico, $tipo_usuario_farmaceutico, $nome_farmaceutico, $email_farmaceutico, $telefone_farmaceutico, $conn);
        
        if (is_numeric($id_usuario)) {
            echo "farmaceutico adicionada com sucesso!";
        } else {
            echo $id_usuario; 
        }
    }
}
$conn->close();


//----------------------------------------------------------------------Heitor a parte do HTML esta aki em baixo------------------------------------------------------------------------------------------------------------------------------------------------
//============================================================Cuidado com a verificação de senhas compativeis que esta no final===========================================================================
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="area_admin.css">
    <link rel="stylesheet" href="style.css">
    
    <title>Área do Administrador</title>
</head>
<body>
<h1>Bem-vindo à Área do Administrador</h1>

    <div class="FormsBox-container">
        <div class="FormsBox">
            <form action="" method="post">
                <h2>Adicionar Médico</h2>
                <label for="username_medico">Nome</label>
                <input type="text" id="username_medico" name="username_medico" required><br><br>
                <label for="password_medico">Senha</label>
                <input type="password" id="password_medico" name="password_medico" required><br><br>
                <label for="confirm_password_medico">Confirme a Senha:</label>
                <input type="password" id="confirm_password_medico" name="confirm_password_medico" required><br><br>
                <label for="nome_medico">Nome</label>
                <input type="text" id="nome_medico" name="nome_medico" required><br><br>
                <label for="email_medico">Email</label>
                <input type="email" id="email_medico" name="email_medico" required><br><br>
                <label for="telefone_medico">Telefone</label>
                <input type="tel" id="telefone_medico" name="telefone_medico" required><br><br>
                <label for="especialidade_medico">Especialidade</label>
                <input type="text" id="especialidade_medico" name="especialidade_medico" required><br><br>
                <label for="crm_medico">CRM</label>
                <input type="text" id="crm_medico" name="crm_medico" required><br><br>
                <input type="submit" name="add_medico" class="login-btn" value="Adicionar Médico">
            </form>
        </div>
        <div class="FormsBox">
            <form action="" method="post">
                <h2>Adicionar Auditor</h2>
                <label for="username_auditor">Nome</label>
                <input type="text" id="username_auditor" name="username_auditor" required><br><br>
                <label for="password_auditor">Senha</label>
                <input type="password" id="password_auditor" name="password_auditor" required><br><br>
                <label for="confirm_password_auditor">Confirme a Senha</label>
                <input type="password" id="confirm_password_auditor" name="confirm_password_auditor" required><br><br>
                <label for="nome_auditor">Nome</label>
                <input type="text" id="nome_auditor" name="nome_auditor" required><br><br>
                <label for="email_auditor">Email</label>
                <input type="email" id="email_auditor" name="email_auditor" required><br><br>
                <label for="telefone_auditor">Telefone</label>
                <input type="tel" id="telefone_auditor" name="telefone_auditor" required><br><br>
                <label for="especialidade_auditor">Especialidade</label>
                <input type="text" id="especialidade_auditor" name="especialidade_auditor" required><br><br>
                <label for="crm_auditor">CRM</label>
                <input type="text" id="crm_auditor" name="crm_auditor" required><br><br>
                <input type="submit" name="add_auditor" class="login-btn" value="Adicionar Auditor">
            </form>
        </div>
        <div class="FormsBox">
        <form action="" method="post">
            <h2>Adicionar Enfermeiro</h2>
            <label for="username_enfermeira">Nome</label>
            <input type="text" id="username_enfermeira" name="username_enfermeira" required><br><br>
            <label for="password_enfermeira">Senha</label>
            <input type="password" id="password_enfermeira" name="password_enfermeira" required><br><br>
            <label for="confirm_password_enfermeira">Confirme a Senha</label>
            <input type="password" id="confirm_password_enfermeira" name="confirm_password_enfermeira" required><br><br>
            <label for="nome_enfermeira">Nome</label>
            <input type="text" id="nome_enfermeira" name="nome_enfermeira" required><br><br>
            <label for="email_enfermeira">Email</label>
            <input type="email" id="email_enfermeira" name="email_enfermeira" required><br><br>
            <label for="telefone_enfermeira">Telefone</label>
            <input type="tel" id="telefone_enfermeira" name="telefone_enfermeira" required><br><br>
            <input type="submit" name="add_enfermeira" class="login-btn" value="Adicionar Enfermeira">
        </form>
    </div>
    <div class="FormsBox">
        <form action="" method="post">
            <h2>Adicionar farmaceutico</h2>
            <label for="username_farmaceutico">Nome</label>
            <input type="text" id="username_farmaceutico" name="username_farmaceutico" required><br><br>
            <label for="password_farmaceutico">Senha</label>
            <input type="password" id="password_farmaceutico" name="password_farmaceutico" required><br><br>
            <label for="confirm_password_farmaceutico">Confirme a Senha</label>
            <input type="password" id="confirm_password_farmaceutico" name="confirm_password_farmaceutico" required><br><br>
            <label for="nome_farmaceutico">Nome</label>
            <input type="text" id="nome_farmaceutico" name="nome_farmaceutico" required><br><br>
            <label for="email_farmaceutico">Email</label>
            <input type="email" id="email_farmaceutico" name="email_farmaceutico" required><br><br>
            <label for="telefone_farmaceutico">Telefone</label>
            <input type="tel" id="telefone_farmaceutico" name="telefone_farmaceutico" required><br><br>
            <input type="submit" name="add_farmaceutico" class="login-btn" value="Adicionar farmaceutico">
        </form>
    </div>
    </div>
    
    <div class="back-button">
        <a href="index.php" class="login-btn">Voltar à Página de Login</a>
    </div>
    <script>
    //Verificar as senhas para Médico
    //================================================================================================================================================================================================
    document.getElementById('confirm_password_auditor').addEventListener('input', function() {
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
    //verificar as senhas para o Auditor
    //====================================================================================================================================================================================================
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
    //Verificas as senhas para a Enfermeira
    //=====================================================================================================================================================================================================
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
    //Verificar as senhas para o farmaceutico
    //==============================================================================================================================================================================================
    document.getElementById('confirm_password_farmaceutico').addEventListener('input', function() {
        var password = document.getElementById('password_farmaceutico').value;
        var confirmPassword = this.value;
        var mensagem = document.getElementById('senha_mensagem_farmaceutico');
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

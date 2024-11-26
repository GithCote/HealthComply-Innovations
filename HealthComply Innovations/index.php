<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_HealthComply_Innovations_User";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Função para verificar login
function verifyLogin($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc(); // Retorna todos os dados do usuário
    } else {
        return false;
    }
}

// Verificar se o formulário de login foi submetido
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Verificar login
    $usuario = verifyLogin($conn, $username, $password);

    if ($usuario) {
        // Iniciar sessão
        session_start();
        $_SESSION["username"] = $usuario["username"];
        $_SESSION["tipo_usuario"] = $usuario["tipo_usuario"];
        $_SESSION["id_usuario"] = $usuario["id_usuario"]; // Armazena o ID do usuário

        // Se o usuário for médico, armazena o CRM
        if ($usuario["tipo_usuario"] == "medico") {
            // Buscar CRM do médico
            $stmt = $conn->prepare("SELECT crm FROM medicos WHERE id_usuario = ?");
            $stmt->bind_param("i", $usuario["id_usuario"]);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $medico = $result->fetch_assoc();
                $_SESSION["crm"] = $medico["crm"]; // Armazena o CRM na sessão
            }
        }

        // Redirecionar para a área correspondente
        if ($usuario["tipo_usuario"] == "medico") {
            header("Location: crud_medico.php", true, 302);
            exit;
        } elseif ($usuario["tipo_usuario"] == "admin") {
            header("Location: area_admin.php", true, 302);
            exit;
        } elseif ($usuario["tipo_usuario"] == "auditor") {
            header("Location: conferencia_procedimentos.php", true, 302);
            exit;
        }
    } else {
        $erro = "Usuário ou senha inválidos";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="Login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .input-field {
            position: relative;
        }
        .input-field button {
            position: absolute;
            top: 50%;
            right: -14px;
            transform: translateY(-50%);
            background-color: transparent;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <div class="input-field">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-field">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="button" onclick="mostrarSenha()">
                    <i class="fas fa-eye-slash" id="olho"></i>
                </button>
            </div>
            <input type="submit" value="Login" class="login-btn">
        </form>
        <?php if (isset($erro)) { echo "<span style='color: red;'>$erro</span>"; } ?>
    </div>

    <script>
        function mostrarSenha() {
            var senha = document.getElementById("password");
            var olho = document.getElementById("olho");
            if (senha.type === "password") {
                senha.type = "text";
                olho.className = "fas fa-eye";
            } else {
                senha.type = "password";
                olho.className = "fas fa-eye-slash";
            }
        }
    </script>
</body>
</html>

<?php
// Fechar conexão com o banco de dados
$conn->close();
?>
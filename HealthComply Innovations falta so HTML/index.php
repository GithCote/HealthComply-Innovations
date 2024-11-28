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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Verificar login
    $usuario = verifyLogin($conn, $username, $password);

    // Verifique se o login foi bem-sucedido
    if ($usuario) {
        // Iniciar sessão
        session_start();
        $_SESSION["username"] = $usuario["username"];
        $_SESSION["tipo_usuario"] = $usuario["tipo_usuario"];
        $_SESSION["id_usuario"] = $usuario["id_usuario"]; // Armazena o ID do usuário
        
        // Obter o CRM e o nome do médico, se for um médico
        if ($usuario["tipo_usuario"] == "medico") {
            $query = "SELECT m.nome, m.crm FROM medicos m JOIN usuarios u ON m.id_usuario = u.id_usuario WHERE u.id_usuario = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $usuario["id_usuario"]);
            $stmt->execute();
            $stmt->bind_result($nome_medico, $crm_medico);
            
            if ($stmt->fetch()) {
                $_SESSION["nome_medico"] = $nome_medico; // Armazena o nome do médico na sessão
                $_SESSION["crm_medico"] = $crm_medico;
                $_SESSION["id_medico"] = $id_medico;   
            } else {
                echo "Erro ao buscar dados do médico: " . $stmt->error; // Mensagem de erro
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
        } elseif ($usuario["tipo_usuario"] == "enfermeira") {
            header("Location: enfermeira.php", true, 302);
            exit;
        } elseif ($usuario["tipo_usuario"] == "farmaceutico") {
            header("Location: crud_farmacia.php", true, 302);
            exit;
        }
    } else {
        $erro = "Usuário ou senha inválidos"; // Mensagem de erro
    }
}
// -------------------------------------------------------------------------HEiTOR Aqui acabou a parte logica do codigo ----------------------------------------------------------------------------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-image: url('bg-img.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="Logo.png" alt="logo" class="logo">
            <h2>Bem-vindo</h2>
        </div>
        <form class="login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="input-group">
                <label for="username">
                    <i class="fas fa-user"></i>
                </label>
                <input type="text" id="username" name="username" placeholder="Usuário" required>
            </div>
            <div class="input-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                </label>
                <input type="password" id="password" name="password" placeholder="Senha" required>
            </div>
            <div class="remember-group">
    </br>
    <br>
            </div>
            <button type="submit">Entrar</button>
        </form>
        <?php if (isset($erro)) { echo "<span style='color: red ;'>$erro</span>"; } ?>
    </div>
</body>
</html>
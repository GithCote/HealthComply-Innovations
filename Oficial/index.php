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
        $user = $result->fetch_assoc();
        $tipo_usuario = $user["tipo_usuario"];
        return $tipo_usuario;
    } else {
        return false;
    }
}

// Verificar se o formulário de login foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Verificar login
    $tipo_usuario = verifyLogin($conn, $username, $password);

    if ($tipo_usuario) {
        // Iniciar sessão
        session_start();
        $_SESSION["username"] = $username;
        $_SESSION["tipo_usuario"] = $tipo_usuario;

        // Redirecionar para a área correspondente
        if ($tipo_usuario == "medico") {
            header("Location: crud_medico.php", true, 302);
            exit;
        } elseif ($tipo_usuario == "admin") {
            header("Location: area_admin.php", true, 302);
            exit;
        } elseif ($tipo_usuario == "auditor") {
            header("Location: conferencia_procedimentos.php", true, 302);
            exit;
        }
    } else {
        $erro = "Usuário ou senha inválidos";
    }
}

// Fechar conexão com o banco de dados
$conn->close();
?>



<?php if (isset($erro)) { echo $erro; } ?>

<!DOCTYPE html>
<html>
<head>
<title>Login Page</title>
<link rel="stylesheet" type="text/css" href="Login.css">
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
</div>
<input type="submit" value="Login" class="login-btn">
</form>
</div>
</body>
</html>
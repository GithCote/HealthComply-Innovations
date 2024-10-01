<?php
session_start();

// Verifica se o usuário já está logado
if (isset($_SESSION['user_id'])) {
    header("Location: pagina_admin.php"); // Redireciona para a página do administrador se já estiver logado
    exit;
}

// Conexão com o banco de dados
$servername = "localhost"; // Altere se necessário
$username = "root"; // Altere se necessário
$password = ""; // Altere se necessário
$dbname = "db_HealthComply_Innovations_User"; // Nome do seu banco de dados

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Processa o login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepara a consulta
    $stmt = $conn->prepare("SELECT id, password, tipo_usuario FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    // Verifica se o usuário existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $user_type);
        $stmt->fetch();

        // Verifica a senha
        if (password_verify($password, $hashed_password)) {
            // Inicia a sessão
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_type'] = $user_type;

            // Redireciona para a página do administrador ou a página correspondente ao tipo de usuário
            if ($user_type === 'admin') {
                header("Location: pagina_admin.php");
            } elseif ($user_type === 'medico') {
                header("Location: pagina_medico.php"); // Aqui você pode criar a página do médico
            } elseif ($user_type === 'auditor') {
                header("Location: pagina_auditor.php"); // Aqui você pode criar a página do auditor
            }
            exit;
        } else {
            $error_message = "Usuário ou senha inválidos.";
        }
    } else {
        $error_message = "Usuário ou senha inválidos.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <!-- Mensagem de erro -->
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>

<?php
$conn->close(); // Fecha a conexão
?>

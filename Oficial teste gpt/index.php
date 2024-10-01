<?php
session_start();

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

// Processamento do login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query para buscar o usuário no banco de dados
    $query = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verifica a senha
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['tipo_usuario'];
            // Redireciona de acordo com o tipo de usuário
            switch ($user['tipo_usuario']) {
                case 'admin':
                    header("Location: pagina_admin.php");
                    break;
                case 'medico':
                    header("Location: pagina_medico.php");
                    break;
                case 'auditor':
                    header("Location: pagina_auditor.php");
                    break;
            }
            exit;
        } else {
            $error = "Usuário ou senha incorretos!";
        }
    } else {
        $error = "Usuário ou senha incorretos!";
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
    <form action="login.php" method="POST">
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>

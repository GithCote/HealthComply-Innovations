<?php
session_start();

// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
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

// Adiciona um novo usuário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_user'])) {
    $new_username = $_POST['new_username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $new_type = $_POST['new_type'];
    $new_name = $_POST['new_name'];
    $new_email = $_POST['new_email'];
    $new_phone = $_POST['new_phone'];
    
    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, tipo_usuario, nome, email, telefone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $new_username, $new_password, $new_type, $new_name, $new_email, $new_phone);
    
    if ($stmt->execute()) {
        $success_message = "Usuário criado com sucesso!";
    } else {
        $error_message = "Erro ao criar usuário: " . $stmt->error;
    }
}

// Consulta para listar os usuários
$query = "SELECT * FROM usuarios";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página do Administrador</title>
</head>
<body>
    <h2>Página do Administrador</h2>
    
    <!-- Mensagem de sucesso ou erro -->
    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <h3>Criar Novo Usuário</h3>
    <form action="pagina_admin.php" method="POST">
        <label for="new_username">Usuário:</label>
        <input type="text" id="new_username" name="new_username" required>
        <br>
        <label for="new_password">Senha:</label>
        <input type="password" id="new_password" name="new_password" required>
        <br>
        <label for="new_type">Tipo de Usuário:</label>
        <select id="new_type" name="new_type" required>
            <option value="admin">Admin</option>
            <option value="medico">Médico</option>
            <option value="auditor">Auditor</option>
        </select>
        <br>
        <label for="new_name">Nome:</label>
        <input type="text" id="new_name" name="new_name" required>
        <br>
        <label for="new_email">Email:</label>
        <input type="email" id="new_email" name="new_email" required>
        <br>
        <label for="new_phone">Telefone:</label>
        <input type="text" id="new_phone" name="new_phone" required>
        <br>
        <button type="submit" name="create_user">Criar Usuário</button>
    </form>

    <h3>Usuários Cadastrados</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Usuário</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo de Usuário</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['nome']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['tipo_usuario']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="logout.php">Sair</a> <!-- Adicione um link para logout -->
</body>
</html>

<?php
$conn->close(); // Fecha a conexão
?>

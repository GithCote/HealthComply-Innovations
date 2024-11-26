<?php
// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

session_start();

// Verifique se o ID do médico está definido
if (!isset($_SESSION["id_medico"])) {
    echo "Erro: Você não está logado como médico.";
    exit;
}

$id_medico = $_SESSION["id_medico"];

// Obter o ID do médico da sessão
session_start();
$id_medico = $_SESSION["id_medico"]; // Certifique-se de que o ID do médico está na sessão

// Contar quantas consultas o médico fez no dia atual
$data_atual = date('Y-m-d');
$query = "SELECT COUNT(*) as total_consultas FROM consulta WHERE id_medico = ? AND dt_consulta = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $id_medico, $data_atual);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_consultas = $row['total_consultas'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="dashboard.css">
    <title>Dashboard do Médico</title>
</head>
<body>
    <div class="dashboard">
        <h1>Dashboard do Médico</h1>
        <p>Você fez <?php echo $total_consultas; ?> consultas hoje.</p>
        <a href="crud_medico.php" class="login-btn">Criar Nova Consulta</a>
    </div>
</body>
</html>

<?php
// Fechar conexão com o banco de dados
$conn->close();
?>
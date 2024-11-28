<?php

$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start(); 


if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] != "enfermeira") {
    echo "Erro: Você não está logado como enfermeira.";
    exit;
}


$consultas = [];
$mensagem = '';


if (isset($_POST['buscar'])) {
    $crm = $_POST['crm'] ?? '';
    $nome_paciente = $_POST['nome_paciente'] ?? '';

   
    $query = "SELECT c.id_consulta, p.nome AS nome_paciente, m.crm, pr.nome_procedimento, 
                     cm.quantidade AS quantidade_receitada, 
                     rm.remedio_dado, rm.remedio_devolvido, med.nome AS nome_medicamento
              FROM consulta c 
              JOIN pacientes p ON c.id_paciente = p.id_paciente 
              JOIN medicos m ON c.id_medico = m.id_medico 
              JOIN procedimentos pr ON c.id_procedimento = pr.id_procedimento 
              LEFT JOIN consulta_medicamentos cm ON c.id_consulta = cm.id_consulta
              LEFT JOIN registro_medicamentos rm ON c.id_consulta = rm.id_consulta
              LEFT JOIN medicamentos med ON cm.id_medicamento = med.id_medicamento
              WHERE m.crm = ? OR p.nome LIKE ?";

    $stmt = $conn->prepare($query);
    $like_nome = '%' . $nome_paciente . '%';
    $stmt->bind_param("ss", $crm, $like_nome);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $consultas[] = $row;
    }
}


if (isset($_POST['atualizar'])) {
    $id_consulta = $_POST['id_consulta'];
    $remedio_dado = $_POST['remedio_dado'] ?? 0; 
    $remedio_devolvido = $_POST['remedio_devolvido'] ?? 0; 

    
    $query = "INSERT INTO registro_medicamentos (id_consulta, remedio_dado, remedio_devolvido) 
              VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $id_consulta, $remedio_dado, $remedio_devolvido);
    
    if ($stmt->execute()) {
        $mensagem = "Registro de medicamento adicionado com sucesso!";
    } else {
        $mensagem = "Erro ao adicionar registro: " . $stmt->error;
    }
}
?>

    //=======================================================Ja sabe ne aki começa o html toma cuidado================================================================================================================
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="enfermeira.css">
    <title>Buscar Consulta - Enfermeira</title>
</head>
<body>
<h1>Buscar Consulta</h1>

<form action="" method="post">
    <label for="crm">CRM do Médico:</label>
    <input type="text" id="crm" name="crm"><br><br>

    <label for="nome_paciente">Nome do Paciente:</label>
    <input type="text" id="nome_paciente" name="nome_paciente"><br><br>

    <button type="submit" name="buscar">Buscar</button>
</form>

<?php if ($mensagem): ?>
    <p><?php echo $mensagem; ?></p>
<?php endif; ?>

<?php if (!empty($consultas)): ?>
    <h2>Resultados da Busca</h2>
    <table>
        <tr>
            <th>ID da Consulta</th>
            <th>Nome do Paciente</th>
            <th>CRM do Médico</th>
            <th>Procedimento</th>
            <th>Nome do Remédio</th>
            <th>Quantidade Receitada</th>
            <th>Quantidade Dada</th>
            <th>Quantidade Devolvida</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($consultas as $consulta): ?>
            <tr>
                <td><?php echo $consulta['id_consulta']; ?></td>
                <td><?php echo $consulta['nome_paciente']; ?></td>
                <td><?php echo $consulta['crm']; ?></td>
                <td><?php echo $consulta['nome_procedimento']; ?></td>
                <td><?php echo $consulta['nome_medicamento'] ?? 'N/A'; ?></td>
                <td><?php echo $consulta['quantidade_receitada'] ?? 0; ?></td>
                <td><?php echo $consulta['remedio_dado'] ?? 0; ?></td>
                <td><?php echo $consulta['remedio_devolvido'] ?? 0; ?></td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="id_consulta" value="<?php echo $consulta['id_consulta']; ?>">
                        <label for="remedio_dado">Remédio Dado:</label>
                        <input type="number" name="remedio_dado" min="0" value="0">
                        <label for="remedio_devolvido">Remédio Devolvido:</label>
                        <input type="number" name="remedio_devolvido" min="0" value="0">
                        <button type="submit" name="atualizar">Atualizar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
<div class="back-button">
        <a href="index.php" class="login-btn">Voltar à Página de Login</a>
    </div>
</html>

<?php
// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

// Verificar se a conexão foi estabelecida
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start(); // Inicia a sessão

// Verifique se o usuário está logado e se é um farmacêutico
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] != "farmaceutico") {
    echo "Erro: Você não está logado como farmacêutico.";
    exit;
}

// Variáveis para armazenar resultados da busca
$consultas = [];
$mensagem = '';

// Processar a busca
if (isset($_POST['buscar'])) {
    $crm = $_POST['crm'] ?? '';
    $nome_paciente = $_POST['nome_paciente'] ?? '';

    // Montar a consulta SQL
    $query = "SELECT c.id_consulta, p.nome AS nome_paciente, m.crm, pr.nome_procedimento, 
                     cm.quantidade AS quantidade_receitada, 
                     rm.remedio_saida, rm.remedio_volta, med.nome AS nome_medicamento
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

// Processar a atualização do status do medicamento
if (isset($_POST['atualizar'])) {
    $id_consulta = $_POST['id_consulta'];
    $saida_remedio = $_POST['saida_remedio'] ?? 0; // Pega a quantidade de saída
    $devolucao_remedio = $_POST['devolucao_remedio'] ?? 0; // Pega a quantidade devolvida

    // Inserir o registro na tabela registro_medicamentos
    $query = "INSERT INTO registro_medicamentos (id_consulta, remedio_saida, remedio_volta) 
              VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $id_consulta, $saida_remedio, $devolucao_remedio);
    
    if ($stmt->execute()) {
        $mensagem = "Registro de saída e devolução de medicamento adicionado com sucesso!";
    } else {
        $mensagem = "Erro ao adicionar registro: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Buscar Consulta - Farmácia</title>
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
        <tr> <th>ID da Consulta</th>
            <th>Nome do Paciente</th>
            <th>CRM do Médico</th>
            <th>Procedimento</th>
            <th>Nome do Remédio</th>
            <th>Quantidade Receitada</th>
            <th>Saída do Remédio</th>
            <th>Devolução do Remédio</th>
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
                <td><?php echo $consulta['remedio_saida'] ?? 0; ?></td>
                <td><?php echo $consulta['remedio_volta'] ?? 0; ?></td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="id_consulta" value="<?php echo $consulta['id_consulta']; ?>">
                        <label for="saida_remedio">Saída do Remédio:</label>
                        <input type="number" name="saida_remedio" min="0" value="0">
                        <label for="devolucao_remedio">Devolução do Remédio:</label>
                        <input type="number" name="devolucao_remedio" min="0" value="0">
                        <button type="submit" name="atualizar">Atualizar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
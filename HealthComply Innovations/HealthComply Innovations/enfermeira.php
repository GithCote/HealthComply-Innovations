<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verificar se a enfermeira está logada
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] !== "enfermeira") {
    echo "Erro: Você não está logado como enfermeira.";
    exit;
}

// Lógica para buscar dados da consulta
$consultas = [];
if (isset($_POST["buscar_consulta"])) {
    $nome_paciente = $_POST["nome_paciente"];
    $query = "SELECT c.*, p.nome AS nome_paciente FROM consulta c JOIN pacientes p ON c.id_paciente = p.id_paciente WHERE p.nome LIKE '%$nome_paciente%'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $consultas[] = $row;
        }
    } else {
        echo "Nenhuma consulta encontrada para o paciente: $nome_paciente.";
    }
}

// Lógica para gerir medicamentos
if (isset($_POST["gerir_medicamentos"])) {
    $id_consulta = $_POST["id_consulta"];
    $medicamentos_geridos = $_POST["medicamentos_geridos"] ?? [];

    foreach ($medicamentos_geridos as $medicamento) {
        $query = "INSERT INTO medicamentos_geridos (id_consulta, medicamento) VALUES ('$id_consulta', '$medicamento')";
        $conn->query($query);
    }
    echo "Medicamentos geridos salvos com sucesso!";
}

// Lógica para devolver medicamentos
if (isset($_POST["devolver_medicamentos"])) {
    $id_consulta = $_POST["id_consulta"];
    $medicamentos_devolvidos = $_POST["medicamentos_devolvidos"] ?? [];

    foreach ($medicamentos_devolvidos as $medicamento) {
        $query = "INSERT INTO medicamentos_devolvidos (id_consulta, medicamento) VALUES ('$id_consulta', '$medicamento')";
        $conn->query($query);
    }
    echo "Medicamentos devolvidos salvos com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página da Enfermeira</title>
</head>
<body>
    <h1>Bem-vinda, Enfermeira!</h1>
    <form action="" method="post">
        <label for="nome_paciente">Nome do Paciente:</label>
        <input type="text" id="nome_paciente" name="nome_paciente" required>
        <input type="submit" name="buscar_consulta" value="Buscar Consulta">
    </form>

    <?php if (!empty($consultas)): ?>
        <h2>Consultas Encontradas:</h2>
        <table border="1">
            <tr>
                <th>ID Consulta</th>
                <th>Nome do Paciente</th>
                <th>Data da Consulta</th>
                <th>Gerir Medicamentos</th>
                <th>Devolver Medicamentos</th>
            </tr>
            <?php foreach ($consultas as $consulta): ?>
                <tr>
                    <td><?php echo $consulta['id_consulta']; ?></td>
                    <td><?php echo $consulta['nome_paciente']; ?></td>
                    <td><?php echo $consulta['data_consulta']; ?></td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="id_consulta" value="<?php echo $consulta['id_consulta']; ?>">
                            <input type="checkbox" name="medicamentos_geridos[]" value="medicamento1"> Medicamento 1
                            <input type="checkbox" name="medicamentos_geridos[]" value="medicamento2"> Medicamento 2
                            <input type="submit" name="gerir_medicamentos" value="Salvar Medicamentos Geridos">
                        </form>
                    </td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="id_consulta" value="<?php echo $consulta['id_consulta']; ?>">
                            <input type="checkbox" name="medicamentos_devolvidos[]" value="medicamento1"> Medicamento 1
                            <input type="checkbox" name="medicamentos_devolvidos[]" value="medicamento2"> Medicamento 2
                            <input type="submit" name="devolver_medicamentos" value="Salvar Medicamentos Devolvidos">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
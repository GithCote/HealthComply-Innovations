<?php
// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

// Verificar se a conexão foi estabelecida
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start(); // Inicia a sessão

// Verifique se o usuário está logado e se é um médico
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] != "medico") {
    echo "Erro: Você não está logado como médico.";
    exit;
}

// Adicionar nova consulta
if (isset($_POST['add_consulta'])) {
    $crm = $_POST['crm'];
    $nome_paciente = $_POST['nome_paciente'];
    $cpf_paciente = $_POST['cpf_paciente'];
    $data_nascimento = $_POST['data_nascimento'];
    $plano_saude = $_POST['plano_saude'];
    $id_procedimento = $_POST['id_procedimento']; // ID do procedimento
    $dt_consulta = $_POST['dt_consulta'];
    $medicamentos = $_POST['medicamentos']; // IDs dos medicamentos
    $quantidades = $_POST['quantidades']; // Quantidades dos medicamentos

    // Verificar se o paciente já existe
    $query = "SELECT * FROM pacientes WHERE cpf = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $cpf_paciente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Paciente não existe, criar novo paciente
        $query = "INSERT INTO pacientes (nome, sobrenome, data_nascimento, cpf, plano_saude) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $sobrenome = ''; // Supondo que o sobrenome não seja necessário
        $stmt->bind_param("sssss", $nome_paciente, $sobrenome, $data_nascimento, $cpf_paciente, $plano_saude);
        if (!$stmt->execute()) {
            echo "Erro ao inserir paciente: " . $stmt->error;
            exit;
        }
        $id_paciente = $conn->insert_id; // Obter o ID do novo paciente
    } else {
        // Paciente já existe, obter o ID do paciente
        $paciente = $result->fetch_assoc();
        $id_paciente = $paciente['id_paciente'];
    }

    // Obter o ID do médico com base no CRM
    $query = "SELECT id_medico FROM medicos WHERE crm = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $crm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $medico = $result->fetch_assoc();
        $id_medico = $medico['id_medico'];

        // Inserir a consulta
        $query = "INSERT INTO consulta (id_paciente, id_medico, crm, dt_consulta, id_procedimento) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissi", $id_paciente, $id_medico, $crm, $dt_consulta, $id_procedimento);
        if (!$stmt->execute()) {
            echo "Erro ao inserir consulta: " . $stmt->error;
            exit;
        }
        $id_consulta = $conn->insert_id; // Obter o ID da nova consulta

        // Inserir medicamentos utilizados na consulta
        foreach ($medicamentos as $index => $medicamento) {
            $quantidade = $quantidades[$index]; // Obter a quantidade correspondente
            $query = "INSERT INTO consulta_medicamentos (id_consulta, id_medicamento, quantidade) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $id_consulta, $medicamento, $quantidade);
            
            if (!$stmt->execute()) {
                echo "Erro ao inserir medicamento: " . $stmt->error; // Exibir erro
            }
        }

        echo " Consulta registrada com sucesso!";
    } else {
        echo "Erro: Médico com CRM informado não encontrado.";
    }
}

// Obter todos os procedimentos
$query_procedimentos = "SELECT * FROM procedimentos";
$result_procedimentos = $conn->query($query_procedimentos);

// Obter todos os planos de saúde
$query_planos = "SELECT nome_plano FROM plano_de_saude"; // ajuste o nome da tabela e coluna conforme necessário
$result_planos = $conn->query($query_planos);

// Obter todos os medicamentos
$query_medicamentos = "SELECT * FROM medicamentos";
$result_medicamentos = $conn->query($query_medicamentos);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Registrar Consulta Médica</title>
</head>
<body>
<h1>Registrar Consulta Médica</h1>

<form action="" method="post">
    <label for="crm">CRM:</label>
    <input type="text" id="crm" name="crm" required><br><br>

    <label for="nome_paciente">Nome do Paciente:</label>
    <input type="text" id="nome_paciente" name="nome_paciente" required><br><br>

    <label for="cpf_paciente">CPF do Paciente:</label>
    <input type="text" id="cpf_paciente" name="cpf_paciente" required><br><br>

    <label for="data_nascimento">Data de Nascimento:</label>
    <input type="date" id="data_nascimento" name="data_nascimento" required><br><br>

    <label for="plano_saude">Plano de Saúde:</label>
    <select id="plano_saude" name="plano_saude" required>
        <?php while ($row = $result_planos->fetch_assoc()): ?>
            <option value="<?php echo $row['nome_plano']; ?>"><?php echo $row['nome_plano']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="procedimento">Procedimento:</label>
    <select id="procedimento" name="id_procedimento" required>
        <?php while ($row = $result_procedimentos->fetch_assoc()): ?>
            <option value="<?php echo $row['id_procedimento']; ?>"><?php echo $row['nome_procedimento']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="dt_consulta">Data da Consulta:</label>
    <input type="date" id="dt_consulta" name="dt_consulta" required><br><br>

    <label for="medicamentos">Medicamentos:</label>
    <select id="medicamentos" name="medicamentos[]" multiple required>
        <?php while ($row = $result_medicamentos->fetch_assoc()): ?>
            <option value="<?php echo $row['id_medicamento']; ?>"><?php echo $row['nome']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="quantidades">Quantidades:</label>
    <input type="text" id="quantidades" name="quantidades[]" placeholder="Ex: 2, 1" required><br><br>

    <button type="submit" name="add_consulta">Registrar Consulta</button>
</form>

</body>
</html>
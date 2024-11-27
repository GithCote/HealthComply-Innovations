<?php
session_start(); // Inicia a sessão

// Verifique se o usuário está logado e se é um médico
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] !== "medico") {
    header("Location: index.php"); // Redireciona para a página de login se não for médico
    exit;
}

// Obter o ID e o nome do médico da sessão
$crm = $_SESSION["crm"];
$nome_medico = $_SESSION["nome_medico"]; // Nome do médico logado

// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Buscar planos de saúde
$query_planos = "SELECT * FROM plano_de_saude";
$result_planos = $conn->query($query_planos);

// Buscar procedimentos
$query_procedimentos = "SELECT * FROM procedimentos";
$result_procedimentos = $conn->query($query_procedimentos);

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["acao"]) && $_POST["acao"] == "registrar_atendimento") {

    // Verificar se os campos estão preenchidos
    $cpf_paciente = $_POST["cpf_paciente"];
    $nome = $_POST["nome"];
    $data_nascimento = $_POST["data_nascimento"];
    $plano_saude = $_POST["plano_saude"];
    $procedimento = $_POST["procedimento"];
    $data_procedimento = $_POST["data_procedimento"]; // Adicionado para capturar a data do procedimento

    if (empty($cpf_paciente) || empty($nome) || empty($data_nascimento) || empty($plano_saude) || empty($procedimento) || empty($data_procedimento)) {
        echo "Erro: Alguns campos obrigatórios estão faltando!";
        exit;
    }

   

    // Buscar paciente existente pelo CPF
    $sql_paciente = "SELECT * FROM pacientes WHERE cpf = ?";
    $stmt_paciente = $conn->prepare($sql_paciente);
    $stmt_paciente->bind_param("s", $cpf_paciente);
    $stmt_paciente->execute();
    $result = $stmt_paciente->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_paciente = $row["id_paciente"];
    } else {
        // Inserir novo paciente
        $sql_paciente = "INSERT INTO pacientes (nome, data_nascimento, plano_saude, cpf) VALUES (?, ?, ?, ?)";
        $stmt_paciente = $conn->prepare($sql_paciente);
        $stmt_paciente->bind_param("ssss", $nome, $data_nascimento, $plano_saude, $cpf_paciente);
        $stmt_paciente->execute();
        $id_paciente = $stmt_paciente->insert_id;
    }

    // Verificar se o médico existe
    $sql_verifica_medico = "SELECT * FROM medicos WHERE crm = ?";
    $stmt_verifica_medico = $conn->prepare($sql_verifica_medico);
    $stmt_verifica_medico->bind_param("i", $crm);
    $stmt_verifica_medico->execute();
    $result_medico = $stmt_verifica_medico->get_result();

    if ($result_medico->num_rows == 0) {
        echo "Erro: O ID do médico não existe.";
        exit;
    }

     // Captura de crm, cpf_paciente e dt_consulta
     $dt_consulta = $_POST['dt_consulta'];

    // Preparar a consulta SQL para inserir a consulta no banco de dados
    $stmt = $conn->prepare("INSERT INTO consulta (crm, id_paciente, id_procedimento, dt_consulta) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $crm, $id_paciente, $procedimento, $dt_consulta);

    // Execute a consulta
    if (!$stmt->execute()) {
        echo "Erro na inserção da consulta: " . $stmt->error;
    } else {
        // Redirecionar para o dashboard do médico
        header("Location: dashboard _medico.php", true, 302);
        exit;
    }

    $stmt->close();
}

// Fechar conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Crud-Medico.css">
    <title>Consulta</title>
</head>
<body>
    <div class="FormsBox">
        <h1>Bem-vindo, <?php echo $nome_medico; ?></h1>
        <h1>Consulta</h1>
        <form action="" method="post">
            <div class="input-field">
                <label for="crm"> CRM Médico:</label>
                <input type="text" id="crm" name="crm" value="<?php echo htmlspecialchars($crm); ?>" readonly>
            </div>
            <div class="input-field">
                <label for="cpf_paciente">CPF do Paciente:</label>
                <input type="text" id="cpf_paciente" name="cpf_paciente" required>
            </div>
            <div class="input-field">
                <label for="nome">Nome do Paciente:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="input-field">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required>
            </div>
            <div class="input-field">
                <label for="plano_saude">Plano de Saúde:</label>
                <select id="plano_saude" name="plano_saude" required>
                    <option value="">Selecione um plano de saúde</option>
                    <?php while ($row = $result_planos->fetch_assoc()): ?>
                        <option value="<?php echo $row['idPlano']; ?>"><?php echo $row['nome_plano']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="input-field">
                <label for="procedimento">Procedimento:</label>
                <select id="procedimento" name="procedimento" required>
                    <option value="">Selecione um procedimento</option>
                    <?php while ($row = $result_procedimentos->fetch_assoc()): ?>
                        <option value="<?php echo $row['id_procedimento']; ?>"><?php echo $row['nome_procedimento']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="input-field">
                <label for="data_procedimento">Data da Consulta:</label>
                <input type="date" id="data_procedimento" name="data_procedimento" required>
            </div>
            <div class="input-field">
                <label for="medicamentos">Medicamentos (separados por vírgula):</label>
                <input type="text" id="medicamentos" name="medicamentos">
            </div>
            <input type="hidden" name="acao" value="registrar_atendimento">
            <input type="submit" value="Registrar Atendimento" class="login-btn">
        </form>
    </div>
</body>
</html>
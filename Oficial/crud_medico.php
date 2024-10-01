<?php
// Ativar exceções para erros no MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["acao"]) && $_POST["acao"] == "registrar_atendimento") {
    $nome = $_POST["nome"];
    $data_nascimento = $_POST["data_nascimento"];
    $plano_saude = $_POST["plano_saude"];
    $procedimento = $_POST["procedimento"];
    $medicamentos = $_POST["medicamentos"];

    // Captura de id_medico, id_paciente e dt_consulta
    $id_medico = isset($_POST['id_medico']) ? $_POST['id_medico'] : null;
    $id_paciente = isset($_POST['id_paciente']) ? $_POST['id_paciente'] : null;
    $dt_consulta = isset($_POST['dt_consulta']) ? $_POST['dt_consulta'] : null;

    // Verificação para garantir que esses campos não sejam null
    if (is_null($id_medico) || is_null($id_paciente) || is_null($dt_consulta)) {
        echo "Erro: Alguns campos obrigatórios estão faltando!";
        exit;
    }

    // Preparar a consulta SQL para inserir a consulta no banco de dados
    $stmt = $conn->prepare("INSERT INTO consulta (id_medico, id_paciente, id_procedimento, dt_consulta) VALUES (?, ?, ?, ?)");

    // Certifique-se de que os parâmetros estão corretos
    $stmt->bind_param("iiis", $id_medico, $id_paciente, $procedimento, $dt_consulta);

    // Execute a consulta
    if (!$stmt->execute()) {
        echo "Erro na inserção da consulta: " . $stmt->error;
    } else {
        echo "Consulta registrada com sucesso!";
    }

    $stmt->close();

    // Inserir dados do paciente na tabela
    $sql_paciente = "INSERT INTO pacientes (nome, data_nascimento, plano_saude) VALUES (?, ?, ?)";
    $stmt_paciente = $conn->prepare($sql_paciente);
    $stmt_paciente->bind_param("sss", $nome, $data_nascimento, $plano_saude);

    if ($stmt_paciente->execute()) {
        $id_paciente = $stmt_paciente->insert_id;

        // Inserir o procedimento e medicamentos
        $sql_consulta = "INSERT INTO consulta (id_paciente, id_procedimento, dt_consulta) VALUES (?, ?, NOW())";
        $stmt_consulta = $conn->prepare($sql_consulta);
        $stmt_consulta->bind_param("ii", $id_paciente, $procedimento);
        $stmt_consulta->execute();

        // Tratamento para salvar os medicamentos
        $medicamentos_array = explode(",", $medicamentos);
        foreach ($medicamentos_array as $medicamento_nome) {
            $medicamento_nome = trim($medicamento_nome);
            $sql_medicamento = "SELECT id_medicamento FROM medicamentos WHERE nome = ?";
            $stmt_medicamento = $conn->prepare($sql_medicamento);
            $stmt_medicamento->bind_param("s", $medicamento_nome);
            $stmt_medicamento->execute();
            $result = $stmt_medicamento->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_medicamento = $row["id_medicamento"];

                // Inserir na tabela procedimentos_medicamentos
                $sql_procedimento_medicamento = "INSERT INTO procedimentos_medicamentos (id_procedimento, id_medicamento, quantidade) VALUES (?, ?, 1)";
                $stmt_proc_med = $conn->prepare($sql_procedimento_medicamento);
                $stmt_proc_med->bind_param("ii", $procedimento, $id_medicamento);
                $stmt_proc_med->execute();
            }
        }

        echo "Atendimento registrado com sucesso!";
    } else {
        echo "Erro ao registrar o atendimento.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Atendimento</title>
    <link rel="stylesheet" type="text/css" href="Crud-Medico.css">
</head>
<body>
    <div class="FormsBox">
        <h1>Registrar Atendimento</h1>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <div class="input-field">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome"><br><br>

                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento"><br><br>

                <label for="plano_saude">Plano de Saúde:</label>
                <select id="plano_saude" name="plano_saude" class="select">
                    <?php
                    $query = "SELECT * FROM plano_de_saude";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["idPlano"] . "'>" . $row["nome"] . "</option>";
                    }
                    ?>
                </select><br><br>

                <label for="procedimento">Procedimento:</label>
                <select id="procedimento" name="procedimento">
                    <?php
                    $query_procedimento = "SELECT * FROM procedimentos";
                    $result_proc = $conn->query($query_procedimento);
                    while ($row_proc = $result_proc->fetch_assoc()) {
                        echo "<option value='" . $row_proc["id_procedimento"] . "'>" . $row_proc["nome"] . "</option>";
                    }
                    ?>
                </select><br><br>

                <label for="medicamentos">Medicamentos:</label>
                <textarea id="medicamentos" name="medicamentos" placeholder="Separe os medicamentos com vírgulas" class="textarea1"></textarea><br><br>

                <!-- Adicionando os campos id_medico, id_paciente e dt_consulta -->
                <label for="id_medico">ID Médico:</label>
                <input type="text" id="id_medico" name="id_medico"><br><br>

                <label for="id_paciente">ID Paciente:</label>
                <input type="text" id="id_paciente" name="id_paciente"><br><br>

                <label for="dt_consulta">Data da Consulta:</label>
                <input type="date" id="dt_consulta" name="dt_consulta"><br><br>

                <input type="submit" value="Registrar Atendimento" class="login-btn">
                <input type="hidden" name="acao" value="registrar_atendimento">
            </div>
        </form>
    </div>
</body>
</html>

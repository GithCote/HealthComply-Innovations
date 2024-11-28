<?php

$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start(); 

// Verificar se e um médico
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] != "medico") {
    echo "Erro: Você não está logado como médico.";
    exit;
}

// criação de consulta
if (isset($_POST['add_consulta'])) {
    $crm = $_POST['crm'];
    $nome_paciente = $_POST['nome_paciente'];
    $cpf_paciente = $_POST['cpf_paciente'];
    $data_nascimento = $_POST['data_nascimento'];
    $plano_saude = $_POST['plano_saude'];
    $id_procedimento = $_POST['id_procedimento']; 
    $dt_consulta = $_POST['dt_consulta'];
    $medicamentos = $_POST['medicamentos']; 
    $quantidades = $_POST['quantidades']; 

    
    $query = "SELECT * FROM pacientes WHERE cpf = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $cpf_paciente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        
        $query = "INSERT INTO pacientes (nome, sobrenome, data_nascimento, cpf, plano_saude) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $sobrenome = ''; 
        $stmt->bind_param("sssss", $nome_paciente, $sobrenome, $data_nascimento, $cpf_paciente, $plano_saude);
        if (!$stmt->execute()) {
            echo "Erro ao inserir paciente: " . $stmt->error;
            exit;
        }
        $id_paciente = $conn->insert_id; 
    } else {
        
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

      
        $query = "INSERT INTO consulta (id_paciente, id_medico, crm, dt_consulta, id_procedimento) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissi", $id_paciente, $id_medico, $crm, $dt_consulta, $id_procedimento);
        if (!$stmt->execute()) {
            echo "Erro ao inserir consulta: " . $stmt->error;
            exit;
        }
        $id_consulta = $conn->insert_id; 

       
        foreach ($medicamentos as $index => $medicamento) {
            $quantidade = $quantidades[$index]; 
            $query = "INSERT INTO consulta_medicamentos (id_consulta, id_medicamento, quantidade) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $id_consulta, $medicamento, $quantidade);
            
            if (!$stmt->execute()) {
                echo "Erro ao inserir medicamento: " . $stmt->error; 
            }
        }

        echo " Consulta registrada com sucesso!";
    } else {
        echo "Erro: Médico com CRM informado não encontrado.";
    }
}

//parte para exibir as informações nas opções de preenchimento

$query_procedimentos = "SELECT * FROM procedimentos";
$result_procedimentos = $conn->query($query_procedimentos);


$query_planos = "SELECT nome_plano FROM plano_de_saude"; 
$result_planos = $conn->query($query_planos);


$query_medicamentos = "SELECT * FROM medicamentos";
$result_medicamentos = $conn->query($query_medicamentos);
?>

==================================================================Aqui acaba toma cuidado com o php no meio do html=========================================================================================================
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="medico.css">
    <title>Registrar Consulta Médica</title>
</head>
<body>
<div class="container">
<h2>Registrar Consulta Médica</h2>
<form action="" method="post">
    <label for="crm">CRM</label>
    <input type="text" id="crm" name="crm" required><br><br>

    <label for="nome_paciente">Nome do Paciente</label>
    <input type="text" id="nome_paciente" name="nome_paciente" required><br><br>

    <label for="cpf_paciente">CPF do Paciente</label>
    <input type="text" id="cpf_paciente" name="cpf_paciente" required><br><br>

    <label for="data_nascimento">Data de Nascimento</label>
    <input type="date" id="data_nascimento" name="data_nascimento" required><br><br>

    <label for="plano_saude">Plano de Saúde</label>
    <select id="plano_saude" name="plano_saude" required>
        <?php while ($row = $result_planos->fetch_assoc()): ?>
            <option value="<?php echo $row['nome_plano']; ?>"><?php echo $row['nome_plano']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="procedimento">Procedimento</label>
    <select id="procedimento" name="id_procedimento" required>
        <?php while ($row = $result_procedimentos->fetch_assoc()): ?>
            <option value="<?php echo $row['id_procedimento']; ?>"><?php echo $row['nome_procedimento']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="dt_consulta">Data da Consulta</label>
    <input type="date" id="dt_consulta" name="dt_consulta" required><br><br>

    <label for="medicamentos">Medicamentos</label>
    <select id="medicamentos" name="medicamentos[]">
        <?php while ($row = $result_medicamentos->fetch_assoc()): ?>
            <option value="<?php echo $row['id_medicamento']; ?>"><?php echo $row['nome']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="quantidades">Quantidades</label>
    <input type="text" id="quantidades" name="quantidades[]" placeholder="Ex: 2, 1" required><br><br>

    <button type="submit" name="add_consulta">REGISTRAR</button>
    <img class="cadastrarImg" src="medical-5459630.svg" alt="cadastrarImg">
</form>
</div>


</body>
</html>

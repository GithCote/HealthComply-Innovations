<?php
// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="ConferenciaDeProcedimento.css">
    <title>Conferência de Procedimentos</title>
</head>
<body>
    <div class="FormsBox">
        <h1>Conferência de Procedimento</h1>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="input-field">
            <label for="id_consulta">ID da Consulta:</label>
            <input type="text" id="id_consulta" name="id_consulta"><br><br>
            <label for="id_medico">ID do Médico:</label>
            <input type="text" id="id_medico" name="id_medico"><br><br>
            <label for="id_paciente">ID do Paciente:</label>
            <input type="text" id="id_paciente" name="id_paciente"><br><br>
            <label for="dt_consulta">Data da Consulta:</label>
            <input type="date" id="dt_consulta" name="dt_consulta"><br><br>
            <input type="submit" value="Buscar" name="buscar" class="login-btn">
        </form>
    </div>

    <?php
    if (isset($_POST["id_consulta"]) || isset($_POST["id_medico"]) || isset($_POST["id_paciente"]) || isset($_POST["dt_consulta"])) {
        $id_consulta = $_POST["id_consulta"];
        $id_medico = $_POST["id_medico"];
        $id_paciente = $_POST["id_paciente"];
        $data_consulta = $_POST["dt_consulta"];

        // Create the search query
        $query = "SELECT c.*, m.nome AS nome_medico, p.nome AS nome_procedimento, GROUP_CONCAT(med.nome SEPARATOR ', ') AS medicamentos
                  FROM consulta c
                  JOIN medicos m ON c.id_medico = m.id_medico
                  JOIN procedimentos p ON c.id_procedimento = p.id_procedimento
                  LEFT JOIN consulta_medicamentos cm ON c.id_consulta = cm.id_consulta
                  LEFT JOIN medicamentos med ON cm.id_medicamento = med.id_medicamento
                  WHERE 1=1";

        if (!empty($id_consulta)) {
            $query .= " AND c.id_consulta = '$id_consulta'";
        }
        if (!empty($id_medico)) {
            $query .= " AND c.id_medico = '$id_medico'";
        }
        if (!empty($id_paciente)) {
            $query .= " AND c.id_paciente = '$id_paciente'";
        }
        if (!empty($data_consulta)) {
            $query .= " AND c.dt_consulta = '$data_consulta'";
        }

        // Execute the query
        $result = mysqli_query($conn, $query);

        // Check if there are results
        if (mysqli_num_rows($result) > 0) {
            // Display the results in a table
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;background-color: #f0f0f0;'>";
            echo "<tr>";
            echo "<th>ID da Consulta</th>";
            echo "<th>ID do Médico</th>";
            echo "<th>Nome do Médico</th>";
            echo "<th>ID do Paciente</th>";
            echo "<th>Procedimento</th>";
            echo "<th>Data da Consulta</th>";
            echo "<th>Medicamentos Util izados</th>";
            echo "</tr>";

            // Fetch and display each row
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id_consulta'] . "</td>";
                echo "<td>" . $row['id_medico'] . "</td>";
                echo "<td>" . $row['nome_medico'] . "</td>";
                echo "<td>" . $row['id_paciente'] . "</td>";
                echo "<td>" . $row['nome_procedimento'] . "</td>";
                echo "<td>" . $row['dt_consulta'] . "</td>";
                echo "<td>" . $row['medicamentos'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Nenhum resultado encontrado.";
        }
    }

    // Close the database connection
    mysqli_close($conn);
    ?>
</body>
</html>
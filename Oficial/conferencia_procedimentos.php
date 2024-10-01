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
    <title>Document</title>
</head>
<body>
    <div class="FormsBox">

        <h1>Conferencia de Procedimento</h1>
        <form method="post" class="input-field">

        <label for="id_consulta">ID da Consulta:</label>
        <input type="text" id="id_consulta" name="id_consulta"><br><br>
        <label for="id_medico">ID do Medico:</label>
        <input type="text" id="id_medico" name="id_medico"><br><br>
        <label for="id_paciente">ID do Paciente:</label>
        <input type="text" id="id_paciente" name="id_paciente"><br><br>
        <label for="dt_consulta">Data da Consulta:</label>
        <input type="date" id="dt_consulta" name="dt_consulta"><br><br>

        </form>
        <input type="submit" value="Buscar" class="login-btn">
    </div>
</body>
</html>
    <?php
    if (isset($_POST["id_consulta"]) || isset($_POST["id_medico"]) || isset($_POST["id_paciente"]) || isset($_POST["dt_consulta"])) {
      $id_consulta = $_POST["id_consulta"];
      $id_medico = $_POST["id_medico"];
      $id_paciente = $_POST["id_paciente"];
      $data_consulta = $_POST["dt_consulta"];

      // Create the search query
      $query = "SELECT * FROM consulta WHERE 1=1";

      if (!empty($id_consulta)) {
        $query .= " AND id_consulta = '$id_consulta'";
      }
      if (!empty($id_medico)) {
        $query .= " AND id_medico = '$id_medico'";
      }
      if (!empty($id_paciente)) {
        $query .= " AND id_paciente = '$id_paciente'";
      }
      if (!empty($data_consulta)) {
        $query .= " AND dt_consulta = '$data_consulta'";
      }

      // Execute the query
      $result = mysqli_query($conn, $query);

      // Check if there are results
      if (mysqli_num_rows($result) > 0) {
        // Display the results
        while($row = mysqli_fetch_assoc($result)) {
          echo "Consulta #{$row["id_consulta"]}:<br>";
          echo "Médico: {$row["id_medico"]}<br>";
          echo "Paciente: {$row["id_paciente"]}<br>";
          echo "Procedimento: {$row["id_procedimento"]}<br>";
          echo "Data da Consulta: {$row["dt_consulta"]}<br><br>";
        }
      } else {
        echo "Nenhum resultado encontrado.";
      }
    }
    ?>

  </body>
</html>

<?php
// Close the connection
mysqli_close($conn);
?>
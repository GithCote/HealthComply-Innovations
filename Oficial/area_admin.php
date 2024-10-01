
<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Create the form to add a doctor
if (isset($_POST["add_medico"])) {
  $username_medico = $_POST["username_medico"];
  $password_medico = $_POST["password_medico"];
  $tipo_usuario_medico = "medico";
  $nome_medico = $_POST["nome_medico"];
  $email_medico = $_POST["email_medico"];
  $telefone_medico = $_POST["telefone_medico"];
  $especialidade_medico = $_POST["especialidade_medico"];
  $crm_medico = $_POST["crm_medico"];
  $cargo_medico = $_POST["cargo_medico"];

  // Hash the password
  $hashed_password_medico = password_hash($password_medico, PASSWORD_DEFAULT);

  // Insert the doctor into the database
  $query = "INSERT INTO usuarios (username, password, tipo_usuario, nome, email, telefone, especialidade, crm, cargo) VALUES ('$username_medico', '$hashed_password_medico', '$tipo_usuario_medico', '$nome_medico', '$email_medico', '$telefone_medico', '$especialidade_medico', '$crm_medico', '$cargo_medico')";
  mysqli_query($conn, $query);

  echo "Médico adicionado com sucesso!";
}

// Create the form to add an auditor
if (isset($_POST["add_auditor"])) {
  $username_auditor = $_POST["username_auditor"];
  $password_auditor = $_POST["password_auditor"];
  $tipo_usuario_auditor = "auditor";
  $nome_auditor = $_POST["nome_auditor"];
  $email_auditor = $_POST["email_auditor"];
  $telefone_auditor = $_POST["telefone_auditor"];
  $especialidade_auditor = $_POST["especialidade_auditor"];
  $crm_auditor = $_POST["crm_auditor"];
  $cargo_auditor = $_POST["cargo_auditor"];

  // Hash the password
  $hashed_password_auditor = password_hash($password_auditor, PASSWORD_DEFAULT);

  // Insert the auditor into the database
  $query = "INSERT INTO usuarios (username, password, tipo_usuario, nome, email, telefone, especialidade, crm, cargo) VALUES ('$username_auditor', '$hashed_password_auditor', '$tipo_usuario_auditor', '$nome_auditor', '$email_auditor', '$telefone_auditor', '$especialidade_auditor', '$crm_auditor', '$cargo_auditor')";
  mysqli_query($conn, $query);

  echo "Auditor adicionado com sucesso!";
}
?>

<h1>Adicionar Médico ou Auditor</h1>

<form action="add_medico.php" method="post">
  <h2>Adicionar Médico</h2>
  <label for="username_medico">Username do Médico:</label>
  <input type="text" id="username_medico" name="username_medico"><br><br>
  <label for="password_medico">Senha do Médico:</label>
  <input type="password" id="password_medico" name="password_medico"><br><br>
  <label for="nome_medico">Nome do Médico:</label>
  <input type="text" id="nome_medico" name="nome_medico"><br><br>
  <label for="email_medico">Email do Médico:</label>
  <input type="email" id="email_medico" name="email_medico"><br><br>
  <label for="telefone_medico">Telefone do Médico:</label>
  <input type="tel" id="telefone_medico" name="telefone_medico"><br><br>
  <label for="especialidade_medico">Especialidade do Médico:</label>
  <input type="text" id="especialidade_medico" name="especialidade_medico"><br><br>
  <label for="crm_medico">CRM do Médico:</label>
  <input type="text" id="crm_medico" name="crm_medico"><br><br>
  <label for="cargo_medico">Cargo do Médico:</label>
  <input type="text" id="cargo_medico" name="cargo_medico"><br><br>
  <input type="submit" name="add_medico" value="Adicionar Médico">
</form>

<form action="add_medico.php" method="post">
  <h2>Adicionar Auditor</h2>
  <label for="username_auditor">Username do Auditor:</label>
  <input type="text" id="username_auditor" name="username_auditor"><br><br>
  <label for="password_auditor">Senha do Auditor:</label>
  <input type="password" id="password_auditor" name="password_auditor"><br><br>
  <label for="nome_auditor">Nome do Auditor:</label>
  <input type="text" id="nome_auditor" name="nome_auditor"><br><br>
  <label for="email_auditor">Email do Auditor:</label>
  <input type="email" id="email_auditor" name="email_auditor"><br><br>
  <label for="telefone_auditor">Telefone do Auditor:</label>
  <input type="tel" id="telefone_auditor" name="telefone_auditor"><br><br>
  <label for="especialidade_auditor">Especialidade do Auditor:</label>
  <input type="text" id="especialidade_auditor" name="especialidade_auditor"><br><br>
  <label for="crm_auditor">CRM do Auditor:</label>
  <input type="text" id="crm_auditor" name="crm_auditor"><br><br>
  <label for="cargo_auditor">Cargo do Auditor:</label>
  <input type="text" id="cargo_auditor" name="cargo_auditor"><br><br>
  <input type="submit" name="add_auditor" value="Adicionar Auditor">
</form>
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

                <label for="id_paciente">Cpf Paciente:</label>
                <input type="text" id="cpf_paciente" name="cpf_paciente" required><br><br>

                <label for="dt_consulta">Data da Consulta:</label>
                <input type="date" id="dt_consulta" name="dt_consulta"><br><br>

                <input type="submit" value="Registrar Atendimento" class="login-btn">
                <input type="hidden" name="acao" value="registrar_atendimento">
            </div>
        </form>
    </div>
</body>
</html>
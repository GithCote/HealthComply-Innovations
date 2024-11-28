<?php
$conn = new mysqli("localhost", "root", "", "db_HealthComply_Innovations_User");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start(); 

// Verifique se o usuário e um auditor
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] != "auditor") {
    echo "Erro: Você não está logado como auditor.";
    exit;
}

// armazenar resultados da busca
$consultas_corretas = [];
$consultas_erradas = [];
$mensagem = '';


$query = "SELECT c.id_consulta, p.nome AS nome_paciente, m.crm, pr.nome_procedimento, 
                 rm.remedio_dado, rm.remedio_saida, rm.remedio_devolvido, rm.remedio_volta
          FROM consulta c 
          JOIN pacientes p ON c.id_paciente = p.id_paciente 
          JOIN medicos m ON c.id_medico = m.id_medico 
          JOIN procedimentos pr ON c.id_procedimento = pr.id_procedimento 
          LEFT JOIN registro_medicamentos rm ON c.id_consulta = rm.id_consulta";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    // verificar se existe alguma diferença entre os dados da enfermeira e do farmaceutico
    if (($row['remedio_dado'] - $row['remedio_saida']) != 0 || ($row['remedio_devolvido'] - $row['remedio_volta']) != 0) {
        $consultas_erradas[] = $row; 
    } else {
        $consultas_corretas[] = $row; 
    }
}


if (isset($_POST['corrigir'])) {
    $id_consulta = $_POST['id_consulta'];
    $remedio_dado = $_POST['remedio_dado'];
    $remedio_saida = $_POST['remedio_saida'];
    $remedio_devolvido = $_POST['remedio_devolvido'];
    $remedio_volta = $_POST['remedio_volta'];

    
    $query = "INSERT INTO consultas_verificadas (id_consulta, remedio_dado, remedio_saida, 
                                                  remedio_devolvido, remedio_volta) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiiii", $id_consulta, $remedio_dado, $remedio_saida, $remedio_devolvido, $remedio_volta);
    
    if ($stmt->execute()) {
        
        foreach ($consultas_erradas as $key => $consulta) {
            if ($consulta['id_consulta'] == $id_consulta) {
               
                $consultas_corretas[] = $consulta;
                
                unset($consultas_erradas[$key]);
                break;
            }
        }
        $mensagem = "Dados corrigidos e salvos com sucesso!";
    } else {
        $mensagem = "Erro ao salvar dados: " . $stmt->error;
    }
}
?>

//==========================================Aqui acaba a parte logica toma cuidado com as partes de php dentro do html=============================================================================================================

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="auditor.css">
    <title>Auditor - Consultas</title>
</head>
<body>
<h1>Auditor - Consultas</h1>

<?php if ($mensagem): ?>
    <p><?php echo $mensagem; ?></p>
<?php endif; ?>

<div class="123" style="display: flex; justify-content: space-between;">
    <div style="width: 48%;">
        <h2>Consultas Corretas</h2>
        <?php if (!empty($consultas_corretas)): ?>
            <table>
                <tr <th>ID da Consulta</th>
                    <th>Nome do Paciente</th>
                    <th>CRM do Médico</th>
                    <th>Procedimento</th>
                    <th>Remédio Dado</th>
                    <th>Saída do Remédio</th>
                    <th>Devolução do Remédio</th>
                    <th>Volta do Remédio</th>
                </tr>
                <?php foreach ($consultas_corretas as $consulta): ?>
                    <tr>
                        <td><?php echo $consulta['id_consulta']; ?></td>
                        <td><?php echo $consulta['nome_paciente']; ?></td>
                        <td><?php echo $consulta['crm']; ?></td>
                        <td><?php echo $consulta['nome_procedimento']; ?></td>
                        <td><?php echo $consulta['remedio_dado']; ?></td>
                        <td><?php echo $consulta['remedio_saida']; ?></td>
                        <td><?php echo $consulta['remedio_devolvido']; ?></td>
                        <td><?php echo $consulta['remedio_volta']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Nenhuma consulta correta encontrada.</p>
        <?php endif; ?>
    </div>

    <div style="width: 48%;">
        <h2>Consultas Erradas</h2>
        <?php if (!empty($consultas_erradas)): ?>
            <table>
                <tr>
                    <th>ID da Consulta</th>
                    <th>Nome do Paciente</th>
                    <th>CRM do Médico</th>
                    <th>Procedimento</th>
                    <th>Remédio Dado</th>
                    <th>Saída do Remédio</th>
                    <th>Devolução do Remédio</th>
                    <th>Volta do Remédio</th>
                    <th>Ações</th>
                </tr>
                <?php foreach ($consultas_erradas as $consulta): ?>
                    <tr>
                        <td><?php echo $consulta['id_consulta']; ?></td>
                        <td><?php echo $consulta['nome_paciente']; ?></td>
                        <td><?php echo $consulta['crm']; ?></td>
                        <td><?php echo $consulta['nome_procedimento']; ?></td>
                        <td style="color: red;"><?php echo $consulta['remedio_dado']; ?></td>
                        <td style="color: red;"><?php echo $consulta['remedio_saida']; ?></td>
                        <td style="color: red;"><?php echo $consulta['remedio_devolvido']; ?></td>
                        <td style="color: red;"><?php echo $consulta['remedio_volta']; ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="id_consulta" value="<?php echo $consulta['id_consulta']; ?>">
                                <input type="hidden" name="remedio_dado" value="<?php echo $consulta['remedio_dado']; ?>">
                                <input type="hidden" name="remedio_saida" value="<?php echo $consulta['remedio_saida']; ?>">
                                <input type="hidden" name="remedio_devolvido" value="<?php echo $consulta['remedio_devolvido']; ?>">
                                <input type="hidden" name="remedio_volta" value="<?php echo $consulta['remedio_volta']; ?>">
                                <button type="submit" name="corrigir">Corrigir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Nenhuma consulta errada encontrada.</p>
        <?php endif; ?>
    </div>
</div>
<div class="back-button">
        <a href="index.php" class="login-btn">Voltar à Página de Login</a>
    </div>
</body>
</html>

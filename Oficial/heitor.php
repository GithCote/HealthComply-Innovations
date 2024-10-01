<h1>Plano de Saúde</h1>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome"><br><br>
    <label for="descricao">Descrição:</label>
    <input type="text" id="descricao" name="descricao"><br><br>
    <label for="tipo">Tipo:</label>
    <input type="text" id="tipo" name="tipo"><br><br>
    <label for="valor">Valor:</label>
    <input type="number" id="valor" name="valor"><br><br>
    <label for="coberturaMedicamentos">Cobertura de Medicamentos:</label>
    <input type="checkbox" id="coberturaMedicamentos" name="coberturaMedicamentos"><br><br>
    <label for="coberturaConsultas">Cobertura de Consultas:</label>
    <input type="checkbox" id="coberturaConsultas" name="coberturaConsultas"><br><br>
    <label for="limiteConsultas">Limite de Consultas:</label>
    <input type="number" id="limiteConsultas" name="limiteConsultas"><br><br>
    <label for="limiteMedicamentos">Limite de Medicamentos:</label>
    <input type="number" id="limiteMedicamentos" name="limiteMedicamentos"><br><br>
    <input type="submit" value="Criar">
</form>

<?php
require_once 'PlanoDeSaudeController.php';
require_once 'Database.php';
$PlanoDeSaudeController = new PlanoDeSaudeController();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];
    $coberturaMedicamentos = $_POST['coberturaMedicamentos'] ? 1 : 0;
    $coberturaConsultas = $_POST['coberturaConsultas'] ? 1 : 0;
    $limiteConsultas = $_POST['limiteConsultas'];
    $limiteMedicamentos = $_POST['limiteMedicamentos'];
    $PlanoDeSaudeController->create($nome, $descricao, $tipo, $valor, $coberturaMedicamentos, $coberturaConsultas, $limiteConsultas, $limiteMedicamentos);
}
?>

<h2>Planos de Saúde</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Descrição</th>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Cobertura de Medicamentos</th>
        <th>Cobertura de Consultas</th>
        <th>Limite de Consultas</th>
        <th>Limite de Medicamentos</th>
        <th>Ações</th>
    </tr>
    <?php
    require_once 'PlanoDeSaudeController.php';
    $planosDeSaude = $PlanoDeSaudeController->readAll();
    foreach ($planosDeSaude as $planoDeSaude) {
        ?>
        <tr>
            <td><?php echo $planoDeSaude['idPlano']; ?></td>
            <td><?php echo $planoDeSaude['nome']; ?></td>
            <td><?php echo $planoDeSaude['descricao']; ?></td>
            <td><?php echo $planoDeSaude['tipo']; ?></td>
            <td><?php echo $planoDeSaude['valor']; ?></td>
            <td><?php echo $planoDeSaude['cobertura_medicamentos'] ? 'Sim' : 'Não'; ?></td>
            <td><?php echo $planoDeSaude['cobertura_consultas'] ? 'Sim' : 'Não'; ?></td>
            <td><?php echo $planoDeSaude['limite_consultas']; ?></td>
            <td><?php echo $planoDeSaude['limite_medicamentos']; ?></td>
            <td>
                <a href="update.php?idPlano=<?php echo $planoDeSaude['idPlano']; ?>">Editar</a>
                <a href="delete.php?idPlano=<?php echo $planoDeSaude['idPlano']; ?>">Excluir</a>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
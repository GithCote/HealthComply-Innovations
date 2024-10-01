<?php
require_once 'PlanoDeSaudeController.php';
require_once 'Database.php';

$PlanoDeSaudeController = new PlanoDeSaudeController();

if (isset($_GET['idPlano'])) {
    $idPlano = intval($_GET['idPlano']); // Obtém o ID do plano via GET

    // Busca o plano de saúde para pré-preencher o formulário
    $planoDeSaude = $PlanoDeSaudeController->readById($idPlano);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Recebe os dados do formulário para atualizar
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];
        $tipo = $_POST['tipo'];
        $valor = $_POST['valor'];
        $coberturaMedicamentos = $_POST['coberturaMedicamentos'] ? 1 : 0;
        $coberturaConsultas = $_POST['coberturaConsultas'] ? 1 : 0;
        $limiteConsultas = $_POST['limiteConsultas'];
        $limiteMedicamentos = $_POST['limiteMedicamentos'];

        // Atualiza o plano de saúde
        $PlanoDeSaudeController->update($idPlano, $nome, $descricao, $tipo, $valor, $coberturaMedicamentos, $coberturaConsultas, $limiteConsultas, $limiteMedicamentos);

        // Redireciona de volta para a página de listagem após a atualização
        header("Location: plano_de_saude_view.php");
        exit();
    }
} else {
    echo "ID do plano não fornecido.";
}
?>

<!-- Formulário de edição -->
<h1>Editar Plano de Saúde</h1>

<form action="update.php?idPlano=<?php echo $idPlano; ?>" method="post">
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" value="<?php echo $planoDeSaude['nome']; ?>"><br><br>
    <label for="descricao">Descrição:</label>
    <input type="text" id="descricao" name="descricao" value="<?php echo $planoDeSaude['descricao']; ?>"><br><br>
    <label for="tipo">Tipo:</label>
    <input type="text" id="tipo" name="tipo" value="<?php echo $planoDeSaude['tipo']; ?>"><br><br>
    <label for="valor">Valor:</label>
    <input type="number" id="valor" name="valor" value="<?php echo $planoDeSaude['valor']; ?>"><br><br>
    <label for="coberturaMedicamentos">Cobertura de Medicamentos:</label>
    <input type="checkbox" id="coberturaMedicamentos" name="coberturaMedicamentos" <?php echo $planoDeSaude['cobertura_medicamentos'] ? 'checked' : ''; ?>><br><br>
    <label for="coberturaConsultas">Cobertura de Consultas:</label>
    <input type="checkbox" id="coberturaConsultas" name="coberturaConsultas" <?php echo $planoDeSaude['cobertura_consultas'] ? 'checked' : ''; ?>><br><br>
    <label for="limiteConsultas">Limite de Consultas:</label>
    <input type="number" id="limiteConsultas" name="limiteConsultas" value="<?php echo $planoDeSaude['limite_consultas']; ?>"><br><br>
    <label for="limiteMedicamentos">Limite de Medicamentos:</label>
    <input type="number" id="limiteMedicamentos" name="limiteMedicamentos" value="<?php echo $planoDeSaude['limite_medicamentos']; ?>"><br><br>
    <input type="submit" value="Atualizar">
</form>

<?php
class PlanoDeSaudeController {
    private $database;

    public function __construct() {
        $this->database = new Database();
    }

    public function create($nome, $descricao, $tipo, $valor, $coberturaMedicamentos, $coberturaConsultas, $limiteConsultas, $limiteMedicamentos) {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("INSERT INTO plano_de_saude (nome, descricao, tipo, valor, cobertura_medicamentos, cobertura_consultas, limite_consultas, limite_medicamentos) VALUES (:nome, :descricao, :tipo, :valor, :coberturaMedicamentos, :coberturaConsultas, :limiteConsultas, :limiteMedicamentos)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':coberturaMedicamentos', $coberturaMedicamentos);
        $stmt->bindParam(':coberturaConsultas', $coberturaConsultas);
        $stmt->bindParam(':limiteConsultas', $limiteConsultas);
        $stmt->bindParam(':limiteMedicamentos', $limiteMedicamentos);
        $stmt->execute();
        return $conn->lastInsertId();
    }

    public function readAll() {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("SELECT * FROM plano_de_saude");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function update($idPlano, $nome, $descricao, $tipo, $valor, $coberturaMedicamentos, $coberturaConsultas, $limiteConsultas, $limiteMedicamentos) {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("UPDATE plano_de_saude SET nome = :nome, descricao = :descricao, tipo = :tipo, valor = :valor, cobertura_medicamentos = :coberturaMedicamentos, cobertura_consultas = :coberturaConsultas, limite_consultas = :limiteConsultas, limite_medicamentos = :limiteMedicamentos WHERE idPlano = :idPlano ");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':coberturaMedicamentos', $coberturaMedicamentos);
        $stmt->bindParam(':coberturaConsultas', $coberturaConsultas);
        $stmt->bindParam(':limiteConsultas', $limiteConsultas);
        $stmt->bindParam(':limiteMedicamentos', $limiteMedicamentos);
        $stmt->bindParam(':idPlano', $idPlano);
        $stmt->execute();
    }

    public function delete($idPlano) {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("DELETE FROM plano_de_saude WHERE idPlano = :idPlano");
        $stmt->bindParam(':idPlano', $idPlano);
        $stmt->execute();
    }
    public function readById($idPlano) {
        $conn = $this->database->connect();
        $stmt = $conn->prepare("SELECT * FROM plano_de_saude WHERE idPlano = :idPlano");
        $stmt->bindParam(':idPlano', $idPlano);
        $stmt->execute();
        return $stmt->fetch();
    }
}

?>
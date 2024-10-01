<?php 
class PlanoDeSaude {
    private $idPlano;
    private $nome;
    private $descricao;
    private $tipo;
    private $valor;
    private $coberturaMedicamentos;
    private $coberturaConsultas;
    private $limiteConsultas;
    private $limiteMedicamentos;

    public function __construct($idPlano = null, $nome = null, $descricao = null, $tipo = null, $valor = null, $coberturaMedicamentos = null, $coberturaConsultas = null, $limiteConsultas = null, $limiteMedicamentos = null) {
        $this->idPlano = $idPlano;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->tipo = $tipo;
        $this->valor = $valor;
        $this->coberturaMedicamentos = $coberturaMedicamentos;
        $this->coberturaConsultas = $coberturaConsultas;
        $this->limiteConsultas = $limiteConsultas;
        $this->limiteMedicamentos = $limiteMedicamentos;
    }

    // Getters and setters
    public function getIdPlano() { return $this->idPlano; }
    public function getNome() { return $this->nome; }
    public function getDescricao() { return $this->descricao; }
    public function getTipo() { return $this->tipo; }
    public function getValor() { return $this->valor; }
    public function getCoberturaMedicamentos() { return $this->coberturaMedicamentos; }
    public function getCoberturaConsultas() { return $this->coberturaConsultas; }
    public function getLimiteConsultas() { return $this->limiteConsultas; }
    public function getLimiteMedicamentos() { return $this->limiteMedicamentos; }

    public function setIdPlano($idPlano) { $this->idPlano = $idPlano; }
    public function setNome($nome) { $this->nome = $nome; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    public function setTipo($tipo) { $this->tipo = $tipo; }
    public function setValor($valor) { $this->valor = $valor; }
    public function setCoberturaMedicamentos($coberturaMedicamentos) { $this->coberturaMedicamentos = $coberturaMedicamentos; }
    public function setCoberturaConsultas($coberturaConsultas) { $this->coberturaConsultas = $coberturaConsultas; }
    public function setLimiteConsultas($limiteConsultas) { $this->limiteConsultas = $limiteConsultas; }
    public function setLimiteMedicamentos($limiteMedicamentos) { $this->limiteMedicamentos = $limiteMedicamentos; }
}

?>
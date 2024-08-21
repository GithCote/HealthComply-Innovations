create database db_HealthComply_Innovations;
use db_HealthComply_Innovations;

DROP TABLE IF EXISTS Planos_de_saude;
CREATE TABLE IF NOT EXISTS Planos_de_saude (
  idPlano int NOT NULL AUTO_INCREMENT,
  nome varchar(60) NOT NULL,
  modalidade varchar(35) NOT NULL,
  PRIMARY KEY (idPlano)

  DROP TABLE IF EXISTS Pacientes;
  CREATE TABLE IF NOT EXISTS Pacientes (
  idPaciente int NOT NULL AUTO_INCREMENT,
  email varchar(60) NOT NULL,
  cpf char(11) NOT NULL,
  primeiro e ultimo nome varchar(40) NOT NULL,
  FOREIGN KEY (modalidade) REFERENCES Planos_de_saude(modalidade)
  FOREIGN KEY (medicamento) REFERENCES Medicamentos(medicamento)
  

  DROP TABLE IF EXISTS Medicamentos;
CREATE TABLE IF NOT EXISTS Medicamentos (
  idMedicamneto int NOT NULL AUTO_INCREMENT,
  nome varchar(60) NOT NULL,
  preco varchar(35) NOT NULL,
  metodo_de_administracao varchar(35) NOT NULL,
  PRIMARY KEY (idPlano)

  PRIMARY KEY (idPlano)

) ENGINE=INNODB;

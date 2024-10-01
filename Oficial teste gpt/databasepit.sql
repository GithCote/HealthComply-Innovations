-- Criação do Banco de Dados
CREATE DATABASE db_HealthComply_Innovations_User;
USE db_HealthComply_Innovations_User;

-- Tabela de Tipos de Usuários
CREATE TABLE tipo_usuario (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(50) NOT NULL,
  PRIMARY KEY (id)
);

-- Inserção de Tipos de Usuários
INSERT INTO tipo_usuario (nome) VALUES ('admin'), ('medico'), ('auditor');

-- Tabela de Usuários
CREATE TABLE usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  tipo_usuario ENUM('admin', 'medico', 'auditor') NOT NULL,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  especialidade VARCHAR(50) NULL,  -- apenas para médicos
  crm VARCHAR(20) NULL,  -- apenas para médicos
  cargo VARCHAR(50) NULL,  -- apenas para secretários
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de Planos de Saúde
CREATE TABLE plano_de_saude (
  idPlano INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(60) NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  tipo VARCHAR(20) NOT NULL,  -- (ex: "Individual", "Familiar", "Empresarial")
  valor DECIMAL(10, 2) NOT NULL,
  cobertura_medicamentos BOOLEAN NOT NULL DEFAULT 1,  -- (1 = Sim, 0 = Não)
  cobertura_consultas BOOLEAN NOT NULL DEFAULT 1,  -- (1 = Sim, 0 = Não)
  limite_consultas INT NOT NULL DEFAULT 0,  -- (0 = Ilimitado)
  limite_medicamentos INT NOT NULL DEFAULT 0,  -- (0 = Ilimitado)
  PRIMARY KEY (idPlano)
);

-- Tabela de Procedimentos
CREATE TABLE procedimentos (
  id_procedimento INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(60) NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_procedimento)
);

-- Tabela para associar Planos de Saúde e Procedimentos Cobertos
CREATE TABLE plano_procedimentos (
  id INT NOT NULL AUTO_INCREMENT,
  id_plano INT NOT NULL,
  id_procedimento INT NOT NULL,
  coberto BOOLEAN NOT NULL DEFAULT 1,  -- (1 = coberto, 0 = não coberto)
  PRIMARY KEY (id),
  FOREIGN KEY (id_plano) REFERENCES plano_de_saude(idPlano),
  FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento)
);

-- Tabela de Pacientes
CREATE TABLE pacientes (
  id_paciente INT AUTO_INCREMENT,
  nome VARCHAR(255) NOT NULL,
  sobrenome VARCHAR(255) NOT NULL,
  data_nascimento DATE NOT NULL,
  sexo ENUM('Masculino', 'Feminino') NOT NULL,
  cpf VARCHAR(14) NOT NULL,
  rg VARCHAR(12) NOT NULL,
  endereco VARCHAR(255) NOT NULL,
  cidade VARCHAR(100) NOT NULL,
  estado VARCHAR(50) NOT NULL,
  cep VARCHAR(10) NOT NULL,
  telefone VARCHAR(15) NOT NULL,
  email VARCHAR(100) NOT NULL,
  plano_saude VARCHAR(100),
  PRIMARY KEY (id_paciente)
);

-- Tabela de Médicos
CREATE TABLE medicos (
  id_medico INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(255) NOT NULL,
  especialidade VARCHAR(100) NOT NULL,
  crm VARCHAR(20) NOT NULL,
  email VARCHAR(100) NOT NULL,
  telefone VARCHAR(20) NOT NULL
);

-- Tabela de Medicamentos
CREATE TABLE medicamentos (
  id_medicamento INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(60) NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  preco DECIMAL(10, 2) NOT NULL,
  tipo VARCHAR(20) NOT NULL,  -- (ex: "Remédio", "Vitamina", etc.)
  forma_administracao VARCHAR(20) NOT NULL,  -- (ex: "Oral", "Injetável", etc.)
  PRIMARY KEY (id_medicamento)
);

-- Tabela Procedimentos Medicamentos
CREATE TABLE procedimentos_medicamentos (
  id_procedimento INT NOT NULL,
  id_medicamento INT NOT NULL,
  quantidade INT NOT NULL,
  id_lote INT PRIMARY KEY AUTO_INCREMENT,
  FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento),
  FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id_medicamento)
);

-- Tabela Guia
CREATE TABLE guia (
  idGuia INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  dt_atendimento DATETIME NOT NULL,
  id_medico INT,
  id_paciente INT NOT NULL,
  id_procedimento INT,
  id_medicamento INT,
  FOREIGN KEY (id_medico) REFERENCES medicos(id_medico),
  FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente),
  FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento),
  FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id_medicamento)
);

-- Tabela de Consultas
CREATE TABLE consulta (
  id_consulta INT AUTO_INCREMENT,
  id_medico INT,
  id_paciente INT NOT NULL,
  id_procedimento INT NOT NULL,
  dt_consulta DATE NOT NULL,
  auditada_por INT NULL,  -- Auditor que revisou a consulta
  PRIMARY KEY (id_consulta),
  FOREIGN KEY (id_medico) REFERENCES medicos(id_medico),
  FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente),
  FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento),
  FOREIGN KEY (auditada_por) REFERENCES usuarios(id)
);

-- Inserção de Usuários
INSERT INTO usuarios (username, password, id_tipo_usuario, nome, email, telefone, especialidade, crm, cargo)
VALUES
('admin1', 'password123', 1, 'Administrador 1', 'admin1@example.com', '11987654321', NULL, NULL, NULL),
('medico1', 'password123', 2, 'Médico 1', 'medico1@example.com', '11987654321', 'Cardiologia', '123456', NULL),
('auditor1', 'password123', 3, 'Auditor 1', 'auditor1@example.com', '11987654321', NULL, NULL, 'Recepcionista');

-- Inserção de Médicos
INSERT INTO medicos (nome, especialidade, crm, email, telefone)
VALUES 
('Dr. João', 'Cardiologia', 'CRM12345', 'joao@example.com', '11987654321'),
('Dr. Maria', 'Pediatria', 'CRM54321', 'maria@example.com', '11987654322');

-- Inserção de Pacientes
INSERT INTO pacientes (nome, sobrenome, data_nascimento, sexo, cpf, rg, endereco, cidade, estado, cep, telefone, email, plano_saude)
VALUES 
('Carlos', 'Silva', '1980-05-10', 'Masculino', '12345678900', 'MG123456', 'Rua A, 123', 'São Paulo', 'SP', '01234-567', '11987654321', 'carlos@example.com', 'Plano Individual Básico');

-- Inserção de Procedimentos
INSERT INTO procedimentos (nome, descricao)
VALUES 
('Consulta Geral', 'Consulta médica geral para avaliação de saúde'),
('Exame de Sangue', 'Exame de sangue para detecção de doenças');

-- Inserção de Medicamentos
INSERT INTO medicamentos (nome, descricao, preco, tipo, forma_administracao)
VALUES
('Paracetamol', 'Analgésico e antipirético', 10.99, 'Remédio', 'Oral'),
('Ibuprofeno', 'Anti-inflamatório não esteroide', 15.99, 'Remédio', 'Oral');

-- Inserção de Consulta
INSERT INTO consulta (id_medico, id_paciente, id_procedimento, dt_consulta)
VALUES (1, 1, 1, '2024-09-20');

-- Inserção de Planos de Saúde
INSERT INTO plano_de_saude (nome, descricao, tipo, valor, cobertura_medicamentos, cobertura_consultas, limite_consultas, limite_medicamentos)
VALUES
('Plano Individual Básico', 'Plano de saúde individual básico', 'Individual', 150.00, 1, 1, 10, 5),
('Plano Familiar Avançado', 'Plano de saúde familiar avançado', 'Familiar', 500.00, 1, 1, 20, 10),
('Plano Empresarial Premium', 'Plano de saúde empresarial premium', 'Empresarial', 1000.00, 1, 1, 0, 0),
('Plano Individual Econômico', 'Plano de saúde individual econômico', 'Individual', 80.00, 0, 1, 5, 3),
('Plano Familiar Básico', 'Plano de saúde familiar básico', 'Familiar', 300.00, 1, 1, 15, 8);


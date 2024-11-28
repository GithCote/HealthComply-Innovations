create database db_HealthComply_Innovations_User;
use db_HealthComply_Innovations_User;

  -- (Tabela Planos de saude)
CREATE TABLE plano_de_saude (
  idPlano int NOT NULL AUTO_INCREMENT,
  nome_plano varchar(60) NOT NULL,
  descricao varchar(255) NOT NULL,
  tipo varchar(20) NOT NULL,  -- (ex: "Individual", "Familiar", "Empresarial")
  valor decimal(10, 2) NOT NULL,
  cobertura_medicamentos boolean NOT NULL DEFAULT 1,  -- (1 = Sim, 0 = Não)
  cobertura_consultas boolean NOT NULL DEFAULT 1,  -- (1 = Sim, 0 = Não)
  limite_consultas int NOT NULL DEFAULT 0,  -- (0 = Ilimitado)
  limite_medicamentos int NOT NULL DEFAULT 0,  -- (0 = Ilimitado)
  PRIMARY KEY (idPlano)
);

	 -- (Tabela Usuarios)
CREATE TABLE usuarios (
  id_usuario INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  tipo_usuario ENUM('admin', 'medico', 'auditor', 'enfermeira', 'farmaceutico') NOT NULL,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  especialidade VARCHAR(50) NULL,  -- apenas para médicos
  crm VARCHAR(20) NULL,  -- apenas para médicos
  cargo VARCHAR(50) NULL,  -- apenas para secretários
  coren VARCHAR(20) NULL,  -- apenas para enfermeiras
  crf VARCHAR(20) NULL, -- apenas para farmaceuticos
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

 -- (Tabela Pacientes)
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

 -- (Tabela Medicos)
CREATE TABLE medicos (
  id_medico INT PRIMARY KEY AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  nome VARCHAR(255) NOT NULL,
  especialidade VARCHAR(100) NOT NULL,
  crm VARCHAR(20) NOT NULL,
  email VARCHAR(100) NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

  -- (Tabela Procedimentos)
CREATE TABLE procedimentos (
  id_procedimento int NOT NULL AUTO_INCREMENT,
  nome_procedimento varchar(60) NOT NULL,
  descricao varchar(255) NOT NULL,
  PRIMARY KEY (id_procedimento)
);

-- (Tabela Medicamentos)
CREATE TABLE medicamentos (
  id_medicamento int NOT NULL AUTO_INCREMENT,
  nome varchar(60) NOT NULL,
  descricao varchar(255) NOT NULL,
  preco decimal(10, 2) NOT NULL,
  tipo varchar(20) NOT NULL,  -- (ex: "Remédio", "Vitamina", etc.)
  forma_administracao varchar(20) NOT NULL,  -- (ex: "Oral", "Injetável", etc.)
  quantidade int NOT NULL, 
  PRIMARY KEY (id_medicamento)
);

-- (Tabela Medicamentos utilizados nos procedimentos)
CREATE TABLE procedimentos_medicamentos (
  id_lote INT PRIMARY KEY AUTO_INCREMENT,
  id_procedimento int NOT NULL,
  id_medicamento int NOT NULL,
  quantidade int NOT NULL,
  FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento),
  FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id_medicamento)
);


 -- (Tabela Consulta)
CREATE TABLE consulta (
  id_consulta INT AUTO_INCREMENT,
  id_paciente INT NOT NULL,
  id_medico INT NOT NULL,
  crm INT NOT NULL,	
  cpf_paciente VARCHAR(14),
  id_procedimento INT NOT NULL,
  dt_consulta DATE NOT NULL,
  PRIMARY KEY (id_consulta),
  FOREIGN KEY (id_medico) REFERENCES medicos(id_medico),
  FOREIGN KEY (id_paciente) REFERENCES pacientes(id_paciente),
  FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento)
);

-- (Tabela para registrar os medicamentos que foram ou não ultilizados)
CREATE TABLE registro_medicamentos (
    id_registro INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT NOT NULL,
    remedio_dado TINYINT(1) DEFAULT 0,
    remedio_devolvido TINYINT(1) DEFAULT 0,
    remedio_saida TINYINT(1) DEFAULT 0,
    remedio_volta TINYINT(1) DEFAULT 0,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_consulta) REFERENCES consulta(id_consulta)
);

	-- (Tabela para armazenar os medicamentos usados na consulta)
CREATE TABLE consulta_medicamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT NOT NULL,
    id_medicamento INT NOT NULL,
    quantidade INT NOT NULL,
    FOREIGN KEY (id_consulta) REFERENCES consulta(id_consulta),
    FOREIGN KEY (id_medicamento) REFERENCES medicamentos(id_medicamento)
);

CREATE TABLE avisos (
    id_aviso INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT,
    nome_paciente VARCHAR(255),
    crm_medico VARCHAR(20),
    nome_medicamento VARCHAR(255),
    quantidade_receitada INT,
    remedio_dado INT,
    remedio_devolvido INT,
    saida_remedio INT,
    devolucao_remedio INT,
    data_aviso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_consulta) REFERENCES consulta(id_consulta)
);

CREATE TABLE consultas_verificadas (
    id_verificacao INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT,
    remedio_dado INT,
    remedio_saida INT,
    remedio_devolvido INT,
    remedio_volta INT,
    data_verificacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_consulta) REFERENCES consulta(id_consulta)
);

INSERT INTO medicamentos (nome, descricao, preco, tipo, forma_administracao, quantidade)
VALUES
  ('Paracetamol', 'Analgésico e antipirético', 10.99, 'Remédio', 'Oral',20),
  ('Ibuprofeno', 'Anti-inflamatório não esteroide', 15.99, 'Remédio', 'Oral',20),
  ('Amoxicilina', 'Antibiótico', 20.99, 'Remédio', 'Oral',20),
  ('Vitamina C', 'Vitamina essencial', 5.99, 'Vitamina', 'Oral',20),
  ('Omeprazol', 'Inibidor de bomba de prótons', 30.99, 'Remédio', 'Oral',20),
  ('Dipirona', 'Analgésico e antipirético', 12.99, 'Remédio', 'Oral',20),
  ('Ciprofloxacino', 'Antibiótico', 25.99, 'Remédio', 'Injetável',20),
  ('Azitromicina', 'Antibiótico', 18.99, 'Remédio', 'Oral',20),
  ('Ranitidina', 'Antagonista H2', 22.99, 'Remédio', 'Oral',20),
  ('Metformina', 'Medicamento para diabetes', 35.99, 'Remédio', 'Oral',20);

  INSERT INTO usuarios (username, password, tipo_usuario, nome, email, telefone, especialidade, crm, cargo) VALUES
  ('admin1', '123', 'admin', 'Administrador 1', 'admin1@examplo.com', '31987654321', NULL, NULL, NULL),
  ('admin2', '123', 'admin', 'Administrador 2', 'admin2@examplo.com', '31987654322', NULL, NULL, NULL);
  ('auditor1', '123', 'auditor', 'auditor', 'exemplo1@auditor.com', '319983030303', NULL, NULL, NULL);
  ('enfermeira1', '123', 'enfermeira', 'enfermeira', 'exemplo1@enfermeira.com', '319983030203', NULL, NULL, NULL);
  ('farmaceutico1', '123', 'farmaceutico', 'farmaceutico', 'exemplo1@farmaceutico.com', '319983020203', NULL, NULL, NULL);


  
  INSERT INTO plano_de_saude (nome_plano, descricao, tipo, valor, cobertura_medicamentos, cobertura_consultas, limite_consultas, limite_medicamentos)
VALUES
  ('Plano Individual Básico', 'Plano de saúde individual com cobertura básica', 'Individual', 150.00, 1, 1, 10, 5),
  ('Plano Familiar Completo', 'Plano de saúde familiar com cobertura completa', 'Familiar', 500.00, 1, 1, 0, 0),
  ('Plano Empresarial Premium', 'Plano de saúde empresarial com cobertura premium', 'Empresarial', 1000.00, 1, 1, 0, 0),
  ('Plano Individual Avançado', 'Plano de saúde individual com cobertura avançada', 'Individual', 300.00, 1, 1, 20, 10),
  ('Plano Familiar Básico', 'Plano de saúde familiar com cobertura básica', 'Familiar', 300.00, 1, 1, 10, 5);
  
  INSERT INTO procedimentos (nome_procedimento, descricao)	
VALUES
  ('Consulta Geral', 'Consulta médica geral para avaliação de saúde'),
  ('Exame de Sangue', 'Exame de sangue para detecção de doenças'),
  ('Raio-X', 'Exame de Raio-X para diagnóstico de lesões'),
  ('Eletrocardiograma', 'Exame de Eletrocardiograma para avaliação do coração'),
  ('Cirurgia Geral', 'Cirurgia geral para tratamento de lesões'),
  ('Tratamento de Dor', 'Tratamento de dor para alívio de sintomas'),
  ('Exame de Imagem', 'Exame de imagem para diagnóstico de doenças'),
  ('Análise de Urina', 'Análise de urina para detecção de doenças'),
  ('Vacinação', 'Vacinação para prevenção de doenças');
  
  INSERT INTO pacientes (nome, sobrenome, data_nascimento, sexo, cpf, rg, endereco, cidade, estado, cep, telefone, email, plano_saude)
VALUES
  ('João', 'Silva', '1990-01-01', 'Masculino', '123.456.789-10', '12.345.678-9', 'Rua dos Pinheiros, 123', 'São Paulo', 'SP', '01310-000', '(11) 1234-5678', 'joao.silva@email.com', 'Amil'),
  ('Maria', 'Rodrigues', '1985-06-15', 'Feminino', '987.654.321-20', '98.765.432-1', 'Avenida Paulista, 456', 'São Paulo', 'SP', '01310-000', '(11) 9876-5432', 'maria.rodrigues@email.com', 'Unimed'),
  ('Pedro', 'Henrique', '1995-03-20', 'Masculino', '741.852.963-40', '74.185.296-3', 'Rua dos Jardins, 789', 'Campinas', 'SP', '13010-000', '(19) 7418-5296', 'pedro.henrique@email.com', 'SulAmérica'),
  ('Ana', 'Luiza', '1992-09-12', 'Feminino', '963.852.741-60', '96.385.274-1', 'Avenida Brasil, 901', 'Rio de Janeiro', 'RJ', '20000-000', '(21) 9638-5274', 'ana.luiza@email.com', 'Bradesco Saúde'),
  ('Luís', 'Fernandes', '1980-02-28', 'Masculino', '852.963.741-80', '85.296.374-2', 'Rua dos Campos, 234', 'Belo Horizonte', 'MG', '30110-000', '(31) 8529-6374', 'luis.fernandes@email.com', 'Caixa Saúde'),
  ('Julia', 'Santos', '1998-05-18', 'Feminino', '741.963.852-90', '74.196.385-4', 'Avenida São João, 567', 'Curitiba', 'PR', '80010-000', '(41) 7419-6385', 'julia.santos@email.com', 'Porto Seguro');
  
  select * from plano_de_saude;
  select * from medicamentos;
  select * from medicos;
  select * from usuarios;
  select * from consulta_medicamentos;
  select * from pacientes;
  select * from consulta;
  

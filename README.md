#  Sistema de Gerenciamento de Funcionários e Setores (PHP + MySQL)

Um sistema **CRUD** completo e seguro para gerenciamento de funcionários, com controle de acesso para administradores, upload de fotos de perfil, banco de dados relacional e interface moderna desenvolvida com **Tailwind CSS**.

---

##  Funcionalidades

* **Sistema de Autenticação Seguro:** Tela de login para administradores com senhas criptografadas (`password_hash` / `password_verify`) e proteção de rotas via Sessões PHP (`auth.php`).
* **Gerenciamento de Funcionários:** Criar, listar, visualizar, editar e remover registros.
* **Relacionamento no Banco de Dados:** Vinculação entre Funcionários e Setores via **Chave Estrangeira** (`FOREIGN KEY`) exibida no painel com consultas otimizadas (`INNER JOIN`).
* **Upload de Fotos de Perfil:** Validação de extensões, substituição e remoção automática de arquivos antigos no diretório `uploads/`.
* **Segurança Reforçada:** Consultas preparadas (*Prepared Statements*) contra **SQL Injection** e sanitização de dados de saída.
* **Interface Responsiva:** Design elegante e fluido utilizando **Tailwind CSS** e ícones do **FontAwesome 6**.

---

##  Tecnologias Utilizadas

* **Linguagem:** PHP 8.x
* **Banco de Dados:** MySQL / MariaDB
* **Conexão:** MySQLi (Procedural / Prepared Statements)
* **Estilização:** Tailwind CSS (via CDN)
* **Ícones:** FontAwesome 6

---

##  Estrutura do Banco de Dados

O banco de dados é composto por três tabelas interligadas:

```sql
-- 1. Tabela de Administradores (Login)
CREATE TABLE IF NOT EXISTS administradores (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabela de Setores
CREATE TABLE IF NOT EXISTS setores (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL
);

-- 3. Tabela de Funcionários
CREATE TABLE IF NOT EXISTS funcionarios (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    salario INT(10) NOT NULL,
    foto VARCHAR(255) DEFAULT 'default-avatar.png',
    setor_id INT NOT NULL,
    FOREIGN KEY (setor_id) REFERENCES setores(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

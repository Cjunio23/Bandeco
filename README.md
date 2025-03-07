# 🍽️ Sistema de Alimentação - Bandeco

Este é um sistema web para gerenciamento de recargas, transações e cardápios de um restaurante universitário. O projeto foi desenvolvido utilizando PHP, MySQL e Tailwind CSS.

## 📌 Funcionalidades

- 📋 **Cadastro e Login**: Usuários podem se cadastrar e realizar login.
- 💳 **Recarga de Cartão**: Possibilidade de adicionar saldo ao cartão de alimentação.
- 🔄 **Transferências**: Enviar saldo para outros usuários.
- 🍽️ **Gerenciamento de Cardápios**: Nutricionistas podem criar e visualizar cardápios.
- 📊 **Dashboard do Administrador**: Controle de transações e gerenciamento do sistema.

## 🛠️ Tecnologias Utilizadas

- **Frontend**: HTML
- **Backend**: PHP (com MySQLi e PDO)
- **Banco de Dados**: MySQL
- **Servidor**: XAMPP (para ambiente local)

## 🚀 Como Rodar o Projeto

### 1️⃣ Clonar o Repositório
```bash
  git clone https://github.com/seu-usuario/nome-do-repositorio.git
  cd nome-do-repositorio
```

### 2️⃣ Configurar o Banco de Dados
- Importe o arquivo `database.sql` no MySQL.
- Atualize as credenciais no arquivo `config/database.php`.

### 3️⃣ Iniciar o Servidor Local
Se estiver usando o XAMPP, inicie o Apache e o MySQL e acesse o projeto via navegador:
```
http://localhost/sistema_alimentacao/
```

## 📂 Estrutura do Projeto
```
📦 sistema_alimentacao
├── 📁 config         # Configurações do banco de dados
├── 📁 public         # Arquivos públicos (CSS, JS)
├── 📁 views          # Páginas do sistema
├── 📁 controllers    # Lógica das funcionalidades
├── 📁 models         # Manipulação do banco de dados
└── index.php         # Arquivo inicial
```

## 📝 To-Do List
- [ ] Melhorar layout das telas
- [ ] Adicionar autenticação JWT para maior segurança
- [ ] Implementar testes unitários

## 🤝 Contribuição
Sinta-se à vontade para abrir uma *issue* ou enviar um *pull request*!

---
Desenvolvido com ❤️ por Custodio Junio (https://github.com/Cjunio23)


# 📦 Sistema de Controle de Estoque com Gráficos Dinâmicos

Bem-vindo ao sistema de controle de estoque desenvolvido em **PHP com PDO**, que oferece recursos avançados como entrada e saída de produtos, relatórios filtráveis, exportação para Excel e **gráficos dinâmicos (diário, semanal e mensal)** usando **Chart.js**.

## 🔍 Funcionalidades Principais

- ✅ Login de usuários com autenticação por token
- 📥 Registro de entrada de produtos
- 📤 Registro de saída de produtos
- 📊 Dashboard com gráficos dinâmicos:
  - Gráfico Diário: mostra movimentações de um dia específico
  - Gráfico Semanal: consolidado por semana
  - Gráfico Mensal: consolidação por mês
- 📆 Filtros por dia, mês e ano
- 📄 Exportação de dados para Excel
- 🔎 Pesquisa e paginação nas tabelas
- 📱 Layout responsivo com navegação simples




---

## 🛠️ Tecnologias Utilizadas

- **PHP (PDO)**
- **MySQL**
- **HTML5 + CSS3**
- **JavaScript (Chart.js)**
- **Bootstrap (opcional para o layout)**
- **Exportação com PHPSpreadsheet (para Excel)**

---

## 📁 Estrutura de Pastas

/config → Conexão com o banco de dados
/models → Classes de autenticação e entidades
/partials → Cabeçalho, menu e rodapé reutilizáveis
/assent → Scripts JS, imagens e estilos CSS
/dados_grafico.php → Arquivo responsável por gerar dados JSON para os gráficos
index.php → Página principal ou dashboard
entrada.php → Cadastro de entradas
saida.php → Cadastro de saídas
relatorio.php → Relatórios e exportações
<?php
require_once 'models/Estoque.php';

// Classe responsável por realizar operações no banco de dados relacionadas ao estoque
class EstoqueDaoMysql implements EstoqueDao {
    private $pdo;

    // Construtor: recebe a conexão PDO para uso nas operações com banco
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 🔹 MÉTODO: Inserir novo item no estoque
    // Recebe um objeto Estoque e grava seus dados na tabela estoque
    public function insert(Estoque $e) {
        $sql = $this->pdo->prepare("INSERT INTO estoque 
            (nome_produto, descricao, categoria, fornecedor, quantidade_em_estoque, preco_custo, data_entrada, data_saida, responsavel, observacoes) 
            VALUES (:nome_produto, :descricao, :categoria, :fornecedor, :quantidade_em_estoque, :preco_custo, :data_entrada, :data_saida, :responsavel, :observacoes)");

        // Atribui os valores do objeto Estoque aos parâmetros da query
        $sql->bindValue(':nome_produto', $e->nome_produto);
        $sql->bindValue(':descricao', $e->descricao);
        $sql->bindValue(':categoria', $e->categoria);
        $sql->bindValue(':fornecedor', $e->fornecedor);
        $sql->bindValue(':quantidade_em_estoque', $e->quantidade_em_estoque);
        $sql->bindValue(':preco_custo', $e->preco_custo);
        $sql->bindValue(':data_entrada', $e->data_entrada);
        $sql->bindValue(':data_saida', $e->data_saida);
        $sql->bindValue(':responsavel', $e->responsavel);
        $sql->bindValue(':observacoes', $e->observacoes);

        // Executa a inserção no banco de dados
        $sql->execute();
    }

    // 🔹 MÉTODO: Buscar item do estoque pelo ID
    // Retorna um objeto Estoque com os dados encontrados ou false se não existir
    public function findById($id) {
        $sql = $this->pdo->prepare("SELECT * FROM estoque WHERE id = :id");
        $sql->bindValue(':id', $id);
        $sql->execute();

        // Se encontrar o registro, monta um objeto Estoque com os dados
        if($sql->rowCount() > 0) {
            $data = $sql->fetch(PDO::FETCH_ASSOC);
            $e = new Estoque();
            $e->id = $data['id'];
            $e->nome_produto = $data['nome_produto'];
            $e->descricao = $data['descricao'];
            $e->categoria = $data['categoria'];
            $e->fornecedor = $data['fornecedor'];
            $e->quantidade_em_estoque = $data['quantidade_em_estoque'];
            $e->preco_custo = $data['preco_custo'];
            $e->data_entrada = $data['data_entrada'];
            $e->data_saida = $data['data_saida'];
            $e->responsavel = $data['responsavel'];
            $e->observacoes = $data['observacoes'];
            return $e;
        }

        // Caso não encontre, retorna false
        return false;
    }

    // 🔹 MÉTODO: Buscar itens do estoque filtrando por mês e ano com paginação
    // Retorna um array de objetos Estoque conforme filtro e limites de paginação
    public function findByMonthYear($month, $year, $offset = 0, $limit = 10) {
        $sql = $this->pdo->prepare("SELECT * FROM estoque 
            WHERE MONTH(data_entrada) = :month AND YEAR(data_entrada) = :year 
            LIMIT :offset, :limit");

        // Bind dos valores com tipos explícitos para segurança
        $sql->bindValue(':month', $month, PDO::PARAM_INT);
        $sql->bindValue(':year', $year, PDO::PARAM_INT);
        $sql->bindValue(':offset', $offset, PDO::PARAM_INT);
        $sql->bindValue(':limit', $limit, PDO::PARAM_INT);
        $sql->execute();

        $results = [];

        // Para cada linha retornada, cria um objeto Estoque preenchido
        while($data = $sql->fetch(PDO::FETCH_ASSOC)) {
            $e = new Estoque();
            foreach ($data as $key => $value) {
                $e->$key = $value; // Atribui dinamicamente cada campo do banco ao objeto
            }
            $results[] = $e;
        }

        // Retorna o array de objetos Estoque
        return $results;
    }

    // 🔹 MÉTODO: Contar total de registros de um mês e ano específico
    // Retorna a quantidade total de itens registrados para filtro específico
    public function countByMonthYear($month, $year) {
        $sql = $this->pdo->prepare("SELECT COUNT(*) as c FROM estoque 
            WHERE MONTH(data_entrada) = :month AND YEAR(data_entrada) = :year");
        
        $sql->bindValue(':month', $month, PDO::PARAM_INT);
        $sql->bindValue(':year', $year, PDO::PARAM_INT);
        $sql->execute();

        $row = $sql->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['c'] : 0; // Retorna o total de registros encontrados
    }

    // 🔹 MÉTODO: Atualizar um item do estoque
    // Atualiza os dados de um objeto Estoque no banco
    // Além disso, registra no histórico se a quantidade mudou
    public function update(Estoque $e) {
        // 1. Buscar quantidade atual antes de atualizar para comparar
        $sql = $this->pdo->prepare("SELECT quantidade_em_estoque FROM estoque WHERE id = :id");
        $sql->bindValue(':id', $e->id);
        $sql->execute();
        $quantidadeAtual = $sql->fetchColumn();

        // 2. Se a quantidade mudou, registrar essa alteração na tabela estoque_historico
        if ($quantidadeAtual !== false && $quantidadeAtual != $e->quantidade_em_estoque) {
            $sqlHist = $this->pdo->prepare("INSERT INTO estoque_historico (estoque_id, quantidade_anterior, quantidade_nova, responsavel, observacoes) VALUES (:estoque_id, :quantidade_anterior, :quantidade_nova, :responsavel, :observacoes)");
            $sqlHist->bindValue(':estoque_id', $e->id);
            $sqlHist->bindValue(':quantidade_anterior', $quantidadeAtual);
            $sqlHist->bindValue(':quantidade_nova', $e->quantidade_em_estoque);
            $sqlHist->bindValue(':responsavel', $e->responsavel);
            $sqlHist->bindValue(':observacoes', $e->observacoes);
            $sqlHist->execute();
        }

        // 3. Atualiza os dados na tabela estoque
        $sqlUpdate = $this->pdo->prepare("UPDATE estoque SET 
            nome_produto = :nome_produto, 
            descricao = :descricao, 
            categoria = :categoria, 
            fornecedor = :fornecedor, 
            quantidade_em_estoque = :quantidade_em_estoque, 
            preco_custo = :preco_custo, 
            data_entrada = :data_entrada, 
            data_saida = :data_saida, 
            responsavel = :responsavel, 
            observacoes = :observacoes 
            WHERE id = :id");

        $sqlUpdate->bindValue(':nome_produto', $e->nome_produto);
        $sqlUpdate->bindValue(':descricao', $e->descricao);
        $sqlUpdate->bindValue(':categoria', $e->categoria);
        $sqlUpdate->bindValue(':fornecedor', $e->fornecedor);
        $sqlUpdate->bindValue(':quantidade_em_estoque', $e->quantidade_em_estoque);
        $sqlUpdate->bindValue(':preco_custo', $e->preco_custo);
        $sqlUpdate->bindValue(':data_entrada', $e->data_entrada);
        $sqlUpdate->bindValue(':data_saida', $e->data_saida);
        $sqlUpdate->bindValue(':responsavel', $e->responsavel);
        $sqlUpdate->bindValue(':observacoes', $e->observacoes);
        $sqlUpdate->bindValue(':id', $e->id);

        // Executa a atualização
        $sqlUpdate->execute();

        return true;
    }

    // 🔹 MÉTODO: Buscar histórico de alterações de estoque para um determinado produto
    // Retorna um array com registros das alterações feitas no estoque (quantidade)
    public function findHistoricoByEstoqueId($estoque_id) {
        $sql = $this->pdo->prepare("SELECT * FROM estoque_historico WHERE estoque_id = :estoque_id ORDER BY data_alteracao DESC");
        $sql->bindValue(':estoque_id', $estoque_id);
        $sql->execute();

        $historicos = [];
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $historicos[] = $row;
        }
        return $historicos;
    }
}


?>

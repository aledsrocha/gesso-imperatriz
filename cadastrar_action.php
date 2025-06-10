<?php
// Importa os arquivos necessários para conexão, modelo e DAO
require_once 'config.php'; // Contém configuração da base e conexão PDO
require_once 'models/Estoque.php'; // Modelo da entidade Estoque
require_once 'dao/EstoqueDaoMysql.php'; // Classe que faz operações no banco para o Estoque

// Captura os dados enviados via formulário POST
$nome_produto = filter_input(INPUT_POST, 'nome_produto');
$descricao = filter_input(INPUT_POST, 'descricao');
$categoria = filter_input(INPUT_POST, 'categoria');
$fornecedor = filter_input(INPUT_POST, 'fornecedor');
$quantidade_em_estoque = filter_input(INPUT_POST, 'quantidade_em_estoque');
$preco_custo = filter_input(INPUT_POST, 'preco_custo');
$data_entrada = filter_input(INPUT_POST, 'data_entrada'); // Esperado no formato dd/mm/yyyy
$data_saida = filter_input(INPUT_POST, 'data_saida');     // Esperado no formato dd/mm/yyyy
$responsavel = filter_input(INPUT_POST, 'responsavel');
$observacoes = filter_input(INPUT_POST, 'observacoes');

// Verifica se os campos obrigatórios foram preenchidos
if($nome_produto && $descricao){
    // Cria nova instância do modelo e do DAO
    $estoque = new Estoque();
    $estoqueDao = new EstoqueDaoMysql($pdo);

    // ========== VALIDAÇÃO E CONVERSÃO DA DATA DE ENTRADA ==========
    $data_entrada = explode('/', $data_entrada); // Quebra a string em dia/mês/ano
    if (count($data_entrada) != 3) {
        $_SESSION['flash'] = 'Data de entrada inválida';
        header("Location:" .$base . "/cadastroestoque.php");
        exit;
    }

    // Reorganiza para o formato yyyy-mm-dd (padrão do MySQL)
    $data_entrada = $data_entrada[2]. '-'. $data_entrada[1]. '-'. $data_entrada[0];

    // Verifica se é uma data válida
    if (strtotime($data_entrada) === false) {
        $_SESSION['flash'] = 'Data de entrada inválida';
        header("Location:" .$base . "/cadastroestoque.php");
        exit;
    }
    // ==============================================================

    // ========== VALIDAÇÃO E CONVERSÃO DA DATA DE SAÍDA ===========
    $data_saida = explode('/', $data_saida);
    if (count($data_saida) != 3) {
        $_SESSION['flash'] = 'Data de saída inválida';
        header("Location:" .$base . "/cadastroestoque.php");
        exit;
    }

    $data_saida = $data_saida[2]. '-'. $data_saida[1]. '-'. $data_saida[0];

    if (strtotime($data_saida) === false) {
        $_SESSION['flash'] = 'Data de saída inválida';
        header("Location:" .$base . "/cadastroestoque.php");
        exit;
    }
    // ==============================================================

    // Atribui os valores capturados aos atributos do objeto Estoque
    $estoque->nome_produto = $nome_produto;
    $estoque->descricao = $descricao;
    $estoque->categoria = $categoria;
    $estoque->fornecedor = $fornecedor;
    $estoque->quantidade_em_estoque = $quantidade_em_estoque;
    $estoque->preco_custo = $preco_custo;
    $estoque->data_entrada = $data_entrada;
    $estoque->data_saida = $data_saida;
    $estoque->responsavel = $responsavel;
    $estoque->observacoes = $observacoes;

    // Insere os dados no banco de dados
    $estoqueDao->insert($estoque);

    // Redireciona de volta para a tela de cadastro após salvar
    header("Location:" .$base . "/cadastroestoque.php");
    exit;
}

?>

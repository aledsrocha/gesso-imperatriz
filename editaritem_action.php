<?php

// Carrega as dependências: conexão com banco, modelo e DAO
require_once 'config.php';
require_once 'models/Estoque.php';
require_once 'dao/EstoqueDaoMysql.php';

// Cria a instância do DAO para manipular o banco de dados
$dao = new EstoqueDaoMysql($pdo);

// ==================== RECEBE DADOS DO FORMULÁRIO ====================
// Captura e sanitiza os dados enviados via POST (vindo do formulário de edição)
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); // ID do produto (obrigatório)
$nome_produto = filter_input(INPUT_POST, 'nome_produto', FILTER_SANITIZE_STRING);
$descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
$categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING);
$fornecedor = filter_input(INPUT_POST, 'fornecedor', FILTER_SANITIZE_STRING);
$quantidade_em_estoque = filter_input(INPUT_POST, 'quantidade_em_estoque', FILTER_VALIDATE_INT);
$preco_custo = filter_input(INPUT_POST, 'preco_custo', FILTER_SANITIZE_STRING);
$data_entrada = filter_input(INPUT_POST, 'data_entrada', FILTER_SANITIZE_STRING);
$data_saida = filter_input(INPUT_POST, 'data_saida', FILTER_SANITIZE_STRING);
$responsavel = filter_input(INPUT_POST, 'responsavel', FILTER_SANITIZE_STRING);
$observacoes = filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_STRING);

// ==================== VALIDAÇÃO DE DADOS ====================
if (
    !$id || !$nome_produto || !$descricao || !$categoria || !$fornecedor ||
    $quantidade_em_estoque === false || !$preco_custo || !$responsavel
) {
    // Se algum campo obrigatório estiver ausente, mostra erro e para execução
    echo "Dados obrigatórios incompletos. <a href='javascript:history.back()'>Voltar</a>";
    exit;
}

// ==================== CONVERSÃO DE PREÇO ====================
// Converte valores com ponto e vírgula para float padrão do PHP (ex: 1.234,56 → 1234.56)
$preco_custo = str_replace(['.', ','], ['', '.'], $preco_custo);
$preco_custo = floatval($preco_custo);

// ==================== CONVERSÃO DE DATAS ====================
// Converte data_entrada do formato brasileiro (DD/MM/AAAA) para MySQL (AAAA-MM-DD)
if (!empty($data_entrada)) {
    $partes = explode('/', $data_entrada);
    if (count($partes) === 3) {
        $data_entrada = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    } else {
        $data_entrada = null; // Caso esteja mal formatada
    }
} else {
    $data_entrada = null;
}

// Converte data_saida também para o formato MySQL
if (!empty($data_saida)) {
    $partes = explode('/', $data_saida);
    if (count($partes) === 3) {
        $data_saida = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    } else {
        $data_saida = null;
    }
} else {
    $data_saida = null;
}

// ==================== CRIA OBJETO E PREENCHE ====================
// Cria uma instância de Estoque e preenche os dados com os valores recebidos do formulário
$produto = new Estoque();
$produto->id = $id;
$produto->nome_produto = $nome_produto;
$produto->descricao = $descricao;
$produto->categoria = $categoria;
$produto->fornecedor = $fornecedor;
$produto->quantidade_em_estoque = $quantidade_em_estoque;
$produto->preco_custo = $preco_custo;
$produto->data_entrada = $data_entrada;
$produto->data_saida = $data_saida;
$produto->responsavel = $responsavel;
$produto->observacoes = $observacoes;

// ==================== ATUALIZA NO BANCO DE DADOS ====================
// Chama o método update para salvar as alterações no banco
$dao->update($produto);

// ==================== REDIRECIONA ====================
// Após atualizar, redireciona para a listagem de produtos
header("Location: visualizarestoque.php");
exit;

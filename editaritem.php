<?php 
// Carrega arquivos essenciais de configuração e classes do projeto
require_once 'config.php'; // Conexão com o banco e constantes
require_once 'models/Auth.php'; // Classe de autenticação do usuário
require_once 'models/Estoque.php'; // Modelo da entidade Estoque
require_once 'dao/EstoqueDaoMysql.php'; // DAO com operações no banco

// Verifica se o usuário está autenticado
$auth = new Auth($pdo, $base);
$userInfo = $auth->checktoken();

// Instancia o DAO para acesso ao banco
$dao = new EstoqueDaoMysql($pdo);

// Recebe o ID do produto via GET e valida como número inteiro
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Se não houver ID válido, redireciona
if (!$id) {
    header("Location: ".$base."/editaritem.php");
    exit;
}

// Busca os dados do produto a ser editado pelo ID
$produto = $dao->findById($id);

// Se o produto não existir, exibe mensagem e interrompe
if (!$produto) {
    echo "<p>Produto não encontrado.</p>";
    exit;
}

// Função que converte datas do formato do banco (YYYY-MM-DD) para o formato brasileiro (DD/MM/YYYY)
function formatarData($data) {
    if ($data && $data !== '0000-00-00') {
        $dt = DateTime::createFromFormat('Y-m-d', $data);
        return $dt ? $dt->format('d/m/Y') : '';
    }
    return '';
}

// Aplica a conversão para as datas que virão no formulário
$data_entrada = formatarData($produto->data_entrada);
$data_saida = formatarData($produto->data_saida);

// Inclui o menu da interface
require_once 'partials/menu.php';
?>

<!-- Área principal de conteúdo -->
<div class="main-content">
    <!-- Estilo CSS específico da página de estoque -->
    <link rel="stylesheet" href="<?=$base;?>/assents/css/estoque.css">

    <h2>Editar Produto</h2>

    <!-- Formulário para edição dos dados do produto -->
    <form action="<?=$base;?>/editaritem_action.php" method="post">
        <!-- Campo oculto com o ID do produto -->
        <input type="hidden" name="id" value="<?=htmlspecialchars($produto->id)?>">

        <label for="nome_produto">Nome do Produto:</label>
        <input type="text" id="nome_produto" name="nome_produto" required value="<?=htmlspecialchars($produto->nome_produto)?>">

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" required><?=htmlspecialchars($produto->descricao)?></textarea>

        <label for="categoria">Categoria:</label>
        <input type="text" id="categoria" name="categoria" required value="<?=htmlspecialchars($produto->categoria)?>">

        <label for="fornecedor">Fornecedor:</label>
        <input type="text" id="fornecedor" name="fornecedor" required value="<?=htmlspecialchars($produto->fornecedor)?>">

        <label for="quantidade_em_estoque">Quantidade em Estoque:</label>
        <input type="number" id="quantidade_em_estoque" name="quantidade_em_estoque" min="0" required value="<?=htmlspecialchars($produto->quantidade_em_estoque)?>">

        <label for="preco_custo">Preço de Custo (R$):</label>
        <input type="text" id="preco_custo" name="preco_custo" required value="<?=htmlspecialchars($produto->preco_custo)?>">

        <label for="data_entrada">Data de Entrada:</label>
        <input type="text" id="data_entrada" name="data_entrada" value="<?=htmlspecialchars($data_entrada)?>">

        <label for="data_saida">Data de Saída:</label>
        <input type="text" id="data_saida" name="data_saida" value="<?=htmlspecialchars($data_saida)?>">

        <label for="responsavel">Responsável:</label>
        <input type="text" id="responsavel" name="responsavel" required value="<?=htmlspecialchars($produto->responsavel)?>">

        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes"><?=htmlspecialchars($produto->observacoes)?></textarea>

        <!-- Botão para salvar as alterações -->
        <button class="salvar" type="submit">Salvar Alterações</button>
    </form>
</div>

<!-- Máscara de data para campos de entrada -->
<script src="https://unpkg.com/imask"></script>
<script>
    IMask(document.getElementById("data_entrada"), {mask: '00/00/0000'});
    IMask(document.getElementById("data_saida"), {mask: '00/00/0000'});
</script>

<!-- Inclui rodapé da página -->
<?php require_once 'partials/footer.php'; ?>

<?php
// Inclui configurações do banco e classes necessárias para manipular o estoque
require_once 'config.php';
require_once 'models/Estoque.php';
require_once 'dao/EstoqueDaoMysql.php';

// Instancia o objeto DAO para acesso aos dados do estoque
$dao = new EstoqueDaoMysql($pdo);

// Obtém o mês e ano do filtro via GET, ou usa o mês e ano atuais como padrão
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

$limit = 10; // Quantidade de itens por página na listagem
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Página atual da paginação
$offset = ($page - 1) * $limit; // Offset para consulta SQL conforme a página

// Consulta para contar o total de registros de saída do estoque no mês e ano selecionados
$sqlCount = $pdo->prepare("SELECT COUNT(*) as c FROM estoque WHERE MONTH(data_saida) = :month AND YEAR(data_saida) = :year");
$sqlCount->bindValue(':month', $month, PDO::PARAM_INT);
$sqlCount->bindValue(':year', $year, PDO::PARAM_INT);
$sqlCount->execute();
$total = $sqlCount->fetch(PDO::FETCH_ASSOC)['c']; // Total de registros para paginação
$totalPages = ceil($total / $limit); // Calcula o total de páginas

// Consulta para buscar os produtos da saída do estoque com paginação
$sql = $pdo->prepare("SELECT * FROM estoque WHERE MONTH(data_saida) = :month AND YEAR(data_saida) = :year LIMIT :offset, :limit");
$sql->bindValue(':month', $month, PDO::PARAM_INT);
$sql->bindValue(':year', $year, PDO::PARAM_INT);
$sql->bindValue(':offset', $offset, PDO::PARAM_INT);
$sql->bindValue(':limit', $limit, PDO::PARAM_INT);
$sql->execute();
$produtos = $sql->fetchAll(PDO::FETCH_ASSOC); // Resultados para exibir na tabela

// Consulta para buscar todos os produtos do mês/ano para montar o resumo, sem limite
$sqlResumo = $pdo->prepare("SELECT * FROM estoque WHERE MONTH(data_saida) = :month AND YEAR(data_saida) = :year");
$sqlResumo->bindValue(':month', $month, PDO::PARAM_INT);
$sqlResumo->bindValue(':year', $year, PDO::PARAM_INT);
$sqlResumo->execute();
$todosProdutos = $sqlResumo->fetchAll(PDO::FETCH_ASSOC);

// Monta um array resumo com a soma de quantidade e valor total por produto na saída
$valoresPorProduto = [];
$valorTotalGeral = 0;

foreach ($todosProdutos as $p) {
    $nome = $p['nome_produto'];
    $subtotal = $p['preco_custo'] * $p['quantidade_em_estoque'];

    // Se o produto ainda não estiver no resumo, inicializa seus dados
    if (!isset($valoresPorProduto[$nome])) {
        $valoresPorProduto[$nome] = [
            'quantidade' => $p['quantidade_em_estoque'],
            'valor_total' => $subtotal
        ];
    } else {
        // Se já existir, acumula a quantidade e o valor total
        $valoresPorProduto[$nome]['quantidade'] += $p['quantidade_em_estoque'];
        $valoresPorProduto[$nome]['valor_total'] += $subtotal;
    }

    // Acumula o valor total geral de todos os produtos
    $valorTotalGeral += $subtotal;
}

// Inclui o menu da página
require_once 'partials/menu.php';
?>

<title>Estoque - Saída de Produtos</title>
<link rel="stylesheet" href="<?=$base;?>/assents/css/tabela.css">

<h1>Saída de Produtos - Estoque</h1>

<div class="main-content">
<!-- Formulário de filtro por mês e ano -->
<form method="get" action="">
    <label for="month">Mês:</label>
    <select name="month" id="month">
      <?php for($m=1; $m<=12; $m++): ?>
        <option value="<?=$m?>" <?=($m == $month) ? 'selected' : ''?>><?=str_pad($m,2,'0',STR_PAD_LEFT)?></option>
      <?php endfor; ?>
    </select>

    <label for="year">Ano:</label>
    <select name="year" id="year">
      <?php $currentYear = date('Y'); ?>
      <?php for($y=$currentYear-1; $y<=$currentYear+20; $y++): ?>
        <option value="<?=$y?>" <?=($y == $year) ? 'selected' : ''?>><?=$y?></option>
      <?php endfor; ?>
    </select>

    <button type="submit">Filtrar</button>
</form>

<!-- Tabela listando os produtos filtrados pela saída -->
<table>
    <thead>
        <tr>
            <th>Nome Produto</th>
            <th>Quantidade</th>
            <th>Preço Custo</th>
            <th>Data Saída</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($produtos) === 0): ?>
            <tr><td colspan="4" style="text-align:center;">Nenhum produto encontrado para o filtro selecionado.</td></tr>
        <?php else: ?>
            <?php foreach($produtos as $p): ?>
                <tr>
                    <td><?=htmlspecialchars($p['nome_produto'])?></td>
                    <td><?=htmlspecialchars($p['quantidade_em_estoque'])?></td>
                    <td>R$ <?=number_format($p['preco_custo'], 2, ',', '.')?></td>
                    <td><?=htmlspecialchars($p['data_saida'])?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Tabela resumo com valores totais por produto na saída -->
<h2>Resumo por Produto (Valores Totais da Saída)</h2>
<table>
    <thead>
        <tr>
            <th>Produto</th>
            <th>Quantidade Total</th>
            <th>Valor Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($valoresPorProduto as $nome => $info): ?>
            <tr>
                <td><?=htmlspecialchars($nome)?></td>
                <td><?=$info['quantidade']?></td>
                <td>R$ <?=number_format($info['valor_total'], 2, ',', '.')?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Exibe o valor total geral da saída -->
<h3>Valor Total Geral: R$ <?=number_format($valorTotalGeral, 2, ',', '.')?></h3>

<!-- Paginação para navegação entre páginas -->
<div class="pagination">
  <?php for($i = 1; $i <= $totalPages; $i++): ?>
    <a class="<?=($i == $page) ? 'active' : ''?>" href="?month=<?=$month?>&year=<?=$year?>&page=<?=$i?>"><?=$i?></a>
  <?php endfor; ?>
</div>
</div>

<?php 
// Inclui o rodapé da página
require_once 'partials/footer.php'; 
?>

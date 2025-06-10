<?php
// Inclui configurações de banco de dados e classes necessárias
require_once 'config.php';
require_once 'models/Estoque.php';
require_once 'dao/EstoqueDaoMysql.php';

// Instancia o DAO para manipular dados do estoque
$dao = new EstoqueDaoMysql($pdo);

// Define o mês e ano com base nos parâmetros GET ou usa o mês e ano atuais
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Define paginação
$limit = 10; // número de itens por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Conta o total de registros para o filtro selecionado
$total = $dao->countByMonthYear($month, $year);
$totalPages = ceil($total / $limit); // número total de páginas

// Busca os produtos paginados conforme filtro de mês/ano
$produtos = $dao->findByMonthYear($month, $year, $offset, $limit);

// Busca todos os produtos do mês para calcular totais (sem limite de paginação)
$todosProdutos = $dao->findByMonthYear($month, $year, 0, 10000);

// Inicializa arrays de resumo
$valoresPorProduto = [];
$valorTotalGeral = 0;

// Calcula totais por produto e total geral
foreach($todosProdutos as $p) {
    $nome = $p->nome_produto;
    $subtotal = $p->preco_custo * $p->quantidade_em_estoque;

    // Se o produto ainda não foi adicionado ao array, inicia ele
    if (!isset($valoresPorProduto[$nome])) {
        $valoresPorProduto[$nome] = [
            'quantidade' => $p->quantidade_em_estoque,
            'valor_total' => $subtotal
        ];
    } else {
        // Se já existe, acumula quantidade e valor
        $valoresPorProduto[$nome]['quantidade'] += $p->quantidade_em_estoque;
        $valoresPorProduto[$nome]['valor_total'] += $subtotal;
    }

    // Soma ao valor total geral
    $valorTotalGeral += $subtotal;
}

// Inclui o menu do sistema
require_once 'partials/menu.php';
?>

<!-- Título da página e link do CSS -->
<title>Estoque - Lista de Produtos</title>
<link rel="stylesheet" href="<?=$base;?>/assents/css/tabela.css">

<h1>Lista de Produtos - Estoque</h1>

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

<!-- Tabela principal com produtos paginados -->
<table>
    <thead>
        <tr>
            <th>Nome Produto</th>
            <th>Quantidade</th>
            <th>Preço Custo</th>
            <th>Data Entrada</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($produtos) === 0): ?>
            <!-- Caso nenhum produto seja encontrado -->
            <tr><td colspan="4" style="text-align:center;">Nenhum produto encontrado para o filtro selecionado.</td></tr>
        <?php else: ?>
            <!-- Exibe cada produto da página atual -->
            <?php foreach($produtos as $p): ?>
                <tr>
                    <td><?=htmlspecialchars($p->nome_produto)?></td>
                    <td><?=htmlspecialchars($p->quantidade_em_estoque)?></td>
                    <td>R$ <?=number_format($p->preco_custo, 2, ',', '.')?></td>
                    <td><?=htmlspecialchars($p->data_entrada)?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Resumo por produto: mostra total de quantidade e valor por item -->
<h2>Resumo por Produto (Valores Totais)</h2>
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

<!-- Valor total geral do mês/ano filtrado -->
<h3>Valor Total Geral: R$ <?=number_format($valorTotalGeral, 2, ',', '.')?></h3>

<!-- Paginação: links para navegar entre páginas -->
<div class="pagination">
  <?php for($i = 1; $i <= $totalPages; $i++): ?>
    <a class="<?=($i == $page) ? 'active' : ''?>" href="?month=<?=$month?>&year=<?=$year?>&page=<?=$i?>"><?=$i?></a>
  <?php endfor; ?>
</div>

</div>

<!-- Inclui o rodapé -->
<?php require_once 'partials/footer.php'; ?>

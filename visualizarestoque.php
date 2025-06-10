<?php
// Inclui configurações de banco de dados e classes necessárias
require_once 'config.php';
require_once 'models/Estoque.php';           // Modelo da entidade Estoque
require_once 'dao/EstoqueDaoMysql.php';      // DAO responsável por operações no banco

// Instancia o DAO para uso posterior
$dao = new EstoqueDaoMysql($pdo);

// Recebe o filtro de mês e ano via GET, ou define mês e ano atual como padrão
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Definições para paginação
$limit = 10; // número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit; // cálculo do deslocamento para SQL

// Busca total de registros com base no filtro atual (para calcular total de páginas)
$total = $dao->countByMonthYear($month, $year);
$totalPages = ceil($total / $limit); // número total de páginas

// Busca produtos com base no filtro, na página atual
$produtos = $dao->findByMonthYear($month, $year, $offset, $limit);

// Inclui o menu do sistema
require_once 'partials/menu.php';
?>

<!-- Título e estilo -->
<title>Estoque - Lista de Produtos</title>
<link rel="stylesheet" href="<?=$base;?>/assents/css/tabela.css">

<h1>Lista de Produtos - Estoque</h1>

<div class="main-content">
<!-- Formulário para filtragem por mês e ano -->
<form method="get" action="">
    <label for="month">Mês:</label>
    <select name="month" id="month">
      <?php 
        // Gera opções de 1 a 12 (meses)
        for($m=1; $m<=12; $m++) {
          $selected = ($m == $month) ? 'selected' : '';
          echo "<option value='$m' $selected>" . str_pad($m,2,'0',STR_PAD_LEFT) . "</option>";
        }
      ?>
    </select>

    <label for="year">Ano:</label>
    <select name="year" id="year">
      <?php 
        // Gera um intervalo de anos (últimos 5 e o próximo)
        $currentYear = date('Y');
        for($y = $currentYear - 5; $y <= $currentYear + 20; $y++) {
          $selected = ($y == $year) ? 'selected' : '';
          echo "<option value='$y' $selected>$y</option>";
        }
      ?>
    </select>

    <!-- Botão para aplicar o filtro -->
    <button type="submit">Filtrar</button>
</form>

<!-- Tabela com os dados do estoque -->
<table>
    <thead>
        <tr>
            <!-- Cabeçalhos das colunas -->
            <th>Nome Produto</th>
            <th>Descrição</th>
            <th>Categoria</th>
            <th>Fornecedor</th>
            <th>Quantidade</th>
            <th>Preço Custo</th>
            <th>Data Entrada</th>
            <th>Data Saída</th>
            <th>Responsável</th>
            <th>Observações</th>
            <th>Ações</th> <!-- Coluna para botões de ação -->
        </tr>
    </thead>
    <tbody>
        <?php if(count($produtos) === 0): ?>
            <!-- Caso não existam produtos filtrados -->
            <tr><td colspan="12" style="text-align:center;">Nenhum produto encontrado para o filtro selecionado.</td></tr>
        <?php else: ?>
            <!-- Itera sobre os produtos e exibe os dados -->
            <?php foreach($produtos as $p): ?>
                <tr>
                    <td><?=htmlspecialchars($p->nome_produto)?></td>
                    <td><?=htmlspecialchars($p->descricao)?></td>
                    <td><?=htmlspecialchars($p->categoria)?></td>
                    <td><?=htmlspecialchars($p->fornecedor)?></td>
                    <td><?=htmlspecialchars($p->quantidade_em_estoque)?></td>
                    <td>R$ <?=number_format($p->preco_custo, 2, ',', '.')?></td>
                    <td><?=htmlspecialchars($p->data_entrada)?></td>
                    <td><?=htmlspecialchars($p->data_saida)?></td>
                    <td><?=htmlspecialchars($p->responsavel)?></td>
                    <td><?=htmlspecialchars($p->observacoes)?></td>
                    <td>
                        <!-- Botões de editar e excluir -->
                        <a class="btn-action btn-edit" href="<?=$base;?>/editaritem.php?id=<?=urlencode($p->id)?>">Editar</a>
                        <a class="btn-action btn-delete" href="<?=$base;?>/excluiritem.php?id=<?=urlencode($p->id)?>" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Paginação -->
<div class="pagination">
  <?php 
    // Geração de links de página
    for($i = 1; $i <= $totalPages; $i++) {
      $active = ($i == $page) ? 'active' : '';
      echo "<a class='$active' href='?month=$month&year=$year&page=$i'>$i</a>";
    }
  ?>
</div>
</div>

<!-- Rodapé -->
<?php
require_once 'partials/footer.php';
?>

<?php
// Inclui o arquivo de configuração com conexão ao banco de dados
require_once 'config.php';

// Define o tipo de retorno da resposta como JSON
header('Content-Type: application/json');

// Pega os valores de dia, mês e ano passados por GET; se não houver, usa a data atual
$dia = isset($_GET['dia']) ? (int)$_GET['dia'] : (int)date('d');
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');

// Função que monta a cláusula WHERE de filtragem por mês e ano
function filtrosPeriodo($mes, $ano, $prefix = '') {
    $filtros = ["{$prefix}data_saida IS NOT NULL"]; // Garante que há data de saída
    if ($ano !== null) {
        $filtros[] = "YEAR({$prefix}data_saida) = :ano"; // Filtra pelo ano
    }
    if ($mes !== null) {
        $filtros[] = "MONTH({$prefix}data_saida) = :mes"; // Filtra pelo mês
    }
    return implode(" AND ", $filtros); // Retorna os filtros combinados por AND
}

//// ===================== GRÁFICO DIÁRIO ===================== ////

// Consulta os dados de saída de produtos para o dia específico
$sqlStr = "
    SELECT 
        nome_produto,
        DATE(data_saida) as dia,
        SUM(quantidade_em_estoque) as total_saida
    FROM estoque
    WHERE DAY(data_saida) = :dia AND MONTH(data_saida) = :mes AND YEAR(data_saida) = :ano
    GROUP BY nome_produto
    ORDER BY nome_produto
";

$sql = $pdo->prepare($sqlStr);
$sql->bindValue(':dia', $dia, PDO::PARAM_INT);
$sql->bindValue(':mes', $mes, PDO::PARAM_INT);
$sql->bindValue(':ano', $ano, PDO::PARAM_INT);
$sql->execute();

// Organiza os dados no formato: produto => [dia => total]
$diario = [];
while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    $diario[$row['nome_produto']][$row['dia']] = (int)$row['total_saida'];
}

//// ===================== GRÁFICO SEMANAL ===================== ////

// Gera os filtros para semana usando a função
$whereSemanal = filtrosPeriodo($mes, $ano);

// Consulta os dados agrupados por semana
$sqlStr = "
    SELECT 
        nome_produto,
        YEAR(data_saida) as ano,
        WEEK(data_saida, 1) as semana,
        SUM(quantidade_em_estoque) as total_saida
    FROM estoque
    WHERE $whereSemanal
    GROUP BY nome_produto, ano, semana
    ORDER BY ano, semana
";

$sqlSemanal = $pdo->prepare($sqlStr);
if ($ano !== null) $sqlSemanal->bindValue(':ano', $ano, PDO::PARAM_INT);
if ($mes !== null) $sqlSemanal->bindValue(':mes', $mes, PDO::PARAM_INT);
$sqlSemanal->execute();

// Organiza os dados no formato: produto => [ano-semana => total]
$semanal = [];
while ($row = $sqlSemanal->fetch(PDO::FETCH_ASSOC)) {
    $semana = "{$row['ano']}-S{$row['semana']}";
    $semanal[$row['nome_produto']][$semana] = (int)$row['total_saida'];
}

//// ===================== GRÁFICO MENSAL ===================== ////

// Gera os filtros para o gráfico mensal
$whereMensal = filtrosPeriodo($mes, $ano);

// Consulta os dados agrupados por mês (ano-mês)
$sqlStr = "
    SELECT 
        nome_produto,
        DATE_FORMAT(data_saida, '%Y-%m') as mes,
        SUM(quantidade_em_estoque) as total_saida
    FROM estoque
    WHERE $whereMensal
    GROUP BY nome_produto, mes
    ORDER BY mes
";

$sqlMensal = $pdo->prepare($sqlStr);
if ($ano !== null) $sqlMensal->bindValue(':ano', $ano, PDO::PARAM_INT);
if ($mes !== null) $sqlMensal->bindValue(':mes', $mes, PDO::PARAM_INT);
$sqlMensal->execute();

// Organiza os dados no formato: produto => [ano-mês => total]
$mensal = [];
while ($row = $sqlMensal->fetch(PDO::FETCH_ASSOC)) {
    $mensal[$row['nome_produto']][$row['mes']] = (int)$row['total_saida'];
}

// Retorna os dados organizados em formato JSON para uso nos gráficos
echo json_encode([
    'diario' => $diario,
    'semanal' => $semanal,
    'mensal' => $mensal
]);

// Conexão com banco
require_once 'config.php';

$sql = "SELECT nome_produto, SUM(quantidade) AS total_saida
        FROM saida_estoque
        GROUP BY nome_produto
        ORDER BY total_saida DESC";

$stmt = $pdo->query($sql);
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separa os nomes e valores em arrays para o gráfico
$nomes = [];
$quantidades = [];

foreach ($dados as $row) {
    $nomes[] = $row['nome_produto'];
    $quantidades[] = $row['total_saida'];
}
